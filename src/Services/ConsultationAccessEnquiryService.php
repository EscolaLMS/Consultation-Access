<?php

namespace EscolaLms\ConsultationAccess\Services;

use EscolaLms\ConsultationAccess\Dtos\ConsultationAccessEnquiryDto;
use EscolaLms\ConsultationAccess\Dtos\CriteriaDto;
use EscolaLms\ConsultationAccess\Dtos\PageDto;
use EscolaLms\ConsultationAccess\Dtos\UpdateConsultationAccessEnquiryDto;
use EscolaLms\ConsultationAccess\Enum\EnquiryStatusEnum;
use EscolaLms\ConsultationAccess\Events\ConsultationAccessEnquiryAdminCreatedEvent;
use EscolaLms\ConsultationAccess\Events\ConsultationAccessEnquiryAdminUpdatedEvent;
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
use EscolaLms\Consultations\Models\ConsultationUserPivot;
use EscolaLms\Consultations\Repositories\Contracts\ConsultationUserRepositoryContract;
use EscolaLms\Consultations\Services\Contracts\ConsultationServiceContract;
use EscolaLms\Core\Repositories\Criteria\Primitives\EqualCriterion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public function findByUser(CriteriaDto $criteriaDto, PageDto $paginationDto, int $userId): LengthAwarePaginator
    {
        $criteria = $criteriaDto->toArray();
        $criteria[] = new EqualCriterion('user_id', $userId);

        return $this->accessEnquiryRepository->findByCriteria($criteria, $paginationDto->getPerPage());
    }

    public function findAll(CriteriaDto $criteriaDto, PageDto $paginationDto, int $userId): LengthAwarePaginator
    {
        return $this->accessEnquiryRepository->findByCriteria($criteriaDto->toArray(), $paginationDto->getPerPage());
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

            event(new ConsultationAccessEnquiryAdminCreatedEvent($enquiry->consultation->author, $enquiry));

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
            $consultationUser = $this->createConsultationUser($proposedTerm);
            $this->accessEnquiryRepository->update([
                'consultation_user_id' => $consultationUser->getKey(),
                'status' => EnquiryStatusEnum::APPROVED,
            ], $enquiry->getKey());
        });
    }

    public function disapprove(int $id, ?string $message): void
    {
        $enquiry = $this->accessEnquiryRepository->findById($id);
        $this->delete($enquiry->getKey());
        event(new ConsultationAccessEnquiryDisapprovedEvent($enquiry->user, $enquiry->consultation->name, $message));
    }

    public function delete(int $id): void
    {
        $enquiry = $this->accessEnquiryRepository->findById($id);
        $consultationUser = $enquiry->consultationUser;
        $this->accessEnquiryRepository->remove($enquiry);

        if ($consultationUser) {
            $consultationUser->delete();
        }
    }

    public function update(int $id, UpdateConsultationAccessEnquiryDto $dto): ConsultationAccessEnquiry
    {
        return DB::transaction(function () use ($id, $dto) {
            $enquiry = $this->accessEnquiryRepository->findById($id);
            $consultationUser = $enquiry->consultationUser;

            /** @var ConsultationAccessEnquiry $enquiry */
            $enquiry = $this->accessEnquiryRepository->update(array_merge($dto->toArray(), [
                'consultation_user_id' => null,
                'status' => EnquiryStatusEnum::PENDING,
            ]), $enquiry->getKey());

            if ($consultationUser) {
                $consultationUser->delete();
            }

            $this->syncProposedTerms($enquiry, $dto->getProposedTerms());
            event(new ConsultationAccessEnquiryAdminUpdatedEvent($enquiry->consultation->author, $enquiry));

            return $enquiry;
        });
    }

    private function createConsultationUser(ConsultationAccessEnquiryProposedTerm $proposedTerm): ConsultationUserPivot
    {
        $enquiry = $proposedTerm->consultationAccessEnquiry;

        $consultationUser = $this->consultationUserRepository->create([
            'consultation_id' => $enquiry->consultation_id,
            'user_id' => $enquiry->user_id,
            'executed_at' => $proposedTerm->proposed_at,
            'executed_status' => ConsultationTermStatusEnum::REPORTED,
        ]);

        $this->consultationService->approveTerm($consultationUser->getKey());

        return $consultationUser;
    }

    private function syncProposedTerms(ConsultationAccessEnquiry $enquiry, array $proposedTerms): void
    {
        $enquiry->consultationAccessEnquiryProposedTerms()
            ->whereNotIn('proposed_at', $proposedTerms)
            ->delete();

        foreach ($proposedTerms as $term) {
            $this->proposedTermRepository->firstOrCreate([
                'consultation_access_enquiry_id' => $enquiry->getKey(),
                'proposed_at' => $term,
            ]);
        }
    }
}
