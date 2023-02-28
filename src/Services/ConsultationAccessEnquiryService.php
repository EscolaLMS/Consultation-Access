<?php

namespace EscolaLms\ConsultationAccess\Services;

use EscolaLms\Auth\Models\User;
use EscolaLms\ConsultationAccess\Dtos\ConsultationAccessEnquiryDto;
use EscolaLms\ConsultationAccess\Dtos\CriteriaDto;
use EscolaLms\ConsultationAccess\Enum\ConsultationAccessPermissionEnum;
use EscolaLms\ConsultationAccess\Enum\EnquiryStatusEnum;
use EscolaLms\ConsultationAccess\Events\ConsultationAccessEnquiryAdminCreatedEvent;
use EscolaLms\ConsultationAccess\Events\ConsultationAccessEnquiryDisapprovedEvent;
use EscolaLms\ConsultationAccess\Exceptions\ConsultationAccessException;
use EscolaLms\ConsultationAccess\Exceptions\EnquiryAlreadyApprovedException;
use EscolaLms\ConsultationAccess\Exceptions\TermIsBusyException;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiryProposedTerm;
use EscolaLms\ConsultationAccess\Repositories\Contracts\ConsultationAccessEnquiryProposedTermRepositoryContract;
use EscolaLms\ConsultationAccess\Repositories\Contracts\ConsultationAccessEnquiryRepositoryContract;
use EscolaLms\ConsultationAccess\Services\Contracts\ConsultationAccessEnquiryServiceContract;
use EscolaLms\Consultations\Enum\ConsultationTermStatusEnum;
use EscolaLms\Consultations\Repositories\Contracts\ConsultationUserRepositoryContract;
use EscolaLms\Consultations\Services\Contracts\ConsultationServiceContract;
use EscolaLms\Core\Repositories\Criteria\Primitives\EqualCriterion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use EscolaLms\Core\Dtos\PaginationDto;
use Illuminate\Support\Facades\DB;

class ConsultationAccessEnquiryService implements ConsultationAccessEnquiryServiceContract
{
    private ConsultationAccessEnquiryRepositoryContract $accessEnquiryRepository;
    private ConsultationAccessEnquiryProposedTermRepositoryContract $proposedTermRepository;
    private ConsultationUserRepositoryContract $consultationUserRepository;
    private ConsultationServiceContract $consultationService;

    public function __construct(
        ConsultationAccessEnquiryRepositoryContract $accessEnquiryRepository,
        ConsultationAccessEnquiryProposedTermRepositoryContract $proposedTermRepository,
        ConsultationUserRepositoryContract $consultationUserRepository,
        ConsultationServiceContract $consultationService
    ) {
        $this->accessEnquiryRepository = $accessEnquiryRepository;
        $this->proposedTermRepository = $proposedTermRepository;
        $this->consultationUserRepository = $consultationUserRepository;
        $this->consultationService = $consultationService;
    }

    public function findByUser(CriteriaDto $criteriaDto, PaginationDto $paginationDto, int $userId): LengthAwarePaginator
    {
        $criteria = $criteriaDto->toArray();
        $criteria[] = new EqualCriterion('user_id', $userId);

        return $this->accessEnquiryRepository->findByCriteria($criteria, $paginationDto->getLimit());
    }

    public function findAll(CriteriaDto $criteriaDto, PaginationDto $paginationDto, int $userId): LengthAwarePaginator
    {
        return $this->accessEnquiryRepository->findByCriteria($criteriaDto->toArray(), $paginationDto->getLimit());
    }

    public function create(ConsultationAccessEnquiryDto $dto): ConsultationAccessEnquiry
    {
        return DB::transaction(function () use ($dto) {
            /** @var ConsultationAccessEnquiry $enquiry */
            $enquiry = $this->accessEnquiryRepository->create($dto->toArray());

            foreach ($dto->getProposedTerms() as $term) {
                $this->proposedTermRepository->create([
                    'consultation_access_enquiry_id' => $enquiry->getKey(),
                    'proposed_at' => $term,
                ]);
            }

            $this->dispatchEventToAdminsAfterCreateEnquiry($enquiry);

            return $enquiry;
        });
    }

    /**
     * @throws ConsultationAccessException
     */
    public function approveByProposedTerm(int $proposedTermId): void
    {
        $proposedTerm = $this->proposedTermRepository->findById($proposedTermId);
        $enquiry = $proposedTerm->consultationAccessEnquiry;

        if ($enquiry->status === EnquiryStatusEnum::APPROVED) {
            throw new EnquiryAlreadyApprovedException();
        }

        if ($this->consultationService->termIsBusy($enquiry->consultation_id, $proposedTerm->proposed_at)) {
            throw new TermIsBusyException();
        }

        DB::transaction(function () use ($enquiry, $proposedTerm) {
            $this->createConsultationUser($proposedTerm);

            $this->accessEnquiryRepository->update([
                'status' => EnquiryStatusEnum::APPROVED,
            ], $enquiry->getKey());
        });
    }

    public function disapprove(int $id, ?string $message): void
    {
        $enquiry = $this->accessEnquiryRepository->findById($id);
        $this->accessEnquiryRepository->remove($enquiry);
        event(new ConsultationAccessEnquiryDisapprovedEvent($enquiry->user, $enquiry->consultation->name, $message));
    }

    private function dispatchEventToAdminsAfterCreateEnquiry(ConsultationAccessEnquiry $enquiry): void
    {
        User::permission(ConsultationAccessPermissionEnum::APPROVE_CONSULTATION_ACCESS_ENQUIRY)
            ->get()
            ->each(fn (User $admin) => event(new ConsultationAccessEnquiryAdminCreatedEvent($admin, $enquiry)));
    }

    private function createConsultationUser(ConsultationAccessEnquiryProposedTerm $proposedTerm): void
    {
        $enquiry = $proposedTerm->consultationAccessEnquiry;

        $consultationUser = $this->consultationUserRepository->create([
            'consultation_id' => $enquiry->consultation_id,
            'user_id' => $enquiry->user_id,
            'executed_at' => $proposedTerm->proposed_at,
            'executed_status' => ConsultationTermStatusEnum::REPORTED,
        ]);

        $this->consultationService->approveTerm($consultationUser->getKey());
    }
}
