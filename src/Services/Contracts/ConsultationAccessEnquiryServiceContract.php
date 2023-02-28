<?php

namespace EscolaLms\ConsultationAccess\Services\Contracts;

use EscolaLms\ConsultationAccess\Dtos\ConsultationAccessEnquiryDto;
use EscolaLms\ConsultationAccess\Dtos\CriteriaDto;
use EscolaLms\ConsultationAccess\Exceptions\ConsultationAccessException;
use EscolaLms\ConsultationAccess\Exceptions\EnquiryAlreadyApprovedException;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\Core\Dtos\PaginationDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ConsultationAccessEnquiryServiceContract
{
    public function findAll(CriteriaDto $criteriaDto, PaginationDto $paginationDto, int $userId): LengthAwarePaginator;

    /**
     * @throws ConsultationAccessException
     */
    public function approveByProposedTerm(int $proposedTermId): void;

    public function disapprove(int $id, ?string $message): void;

    public function findByUser(CriteriaDto $criteriaDto, PaginationDto $paginationDto, int $userId): LengthAwarePaginator;

    public function create(ConsultationAccessEnquiryDto $dto): ConsultationAccessEnquiry;
}
