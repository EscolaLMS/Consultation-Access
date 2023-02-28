<?php

namespace EscolaLms\ConsultationAccess\Repositories;

use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\ConsultationAccess\Repositories\Contracts\ConsultationAccessEnquiryRepositoryContract;
use EscolaLms\Core\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ConsultationAccessEnquiryRepository extends BaseRepository implements ConsultationAccessEnquiryRepositoryContract
{
    public function model(): string
    {
        return ConsultationAccessEnquiry::class;
    }

    public function getFieldsSearchable(): array
    {
        return [
            'consultation_id',
            'user_id',
            'status',
        ];
    }

    public function findByCriteria(array $criteria, int $perPage): LengthAwarePaginator
    {
        return $this->queryWithAppliedCriteria($criteria)
            ->paginate($perPage);
    }

    public function findById(int $id): ConsultationAccessEnquiry
    {
        /** @var ConsultationAccessEnquiry */
        return $this->model->newQuery()->findOrFail($id);
    }
}
