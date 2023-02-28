<?php

namespace EscolaLms\ConsultationAccess\Policies;

use EscolaLms\Auth\Models\User;
use EscolaLms\ConsultationAccess\Enum\ConsultationAccessPermissionEnum;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultationAccessEnquiryPolicy
{
    use HandlesAuthorization;

    public function createOwn(User $user): bool
    {
        return $user->can(ConsultationAccessPermissionEnum::CREATE_OWN_CONSULTATION_ACCESS_ENQUIRY);
    }

    public function listOwn(User $user): bool
    {
        return $user->can(ConsultationAccessPermissionEnum::LIST_OWN_CONSULTATION_ACCESS_ENQUIRY);
    }

    public function list(User $user): bool
    {
        return $user->can(ConsultationAccessPermissionEnum::LIST_OWN_CONSULTATION_ACCESS_ENQUIRY);
    }

    public function approve(User $user): bool
    {
        return $user->can(ConsultationAccessPermissionEnum::APPROVE_CONSULTATION_ACCESS_ENQUIRY);
    }

    public function disapprove(User $user): bool
    {
        return $user->can(ConsultationAccessPermissionEnum::DISAPPROVE_CONSULTATION_ACCESS_ENQUIRY);
    }
}
