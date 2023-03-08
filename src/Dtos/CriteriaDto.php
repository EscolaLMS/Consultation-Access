<?php

namespace EscolaLms\ConsultationAccess\Dtos;

use EscolaLms\Consultations\Repositories\Criteria\Primitives\OrderCriterion;
use EscolaLms\Core\Dtos\Contracts\DtoContract;
use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Core\Dtos\CriteriaDto as BaseCriteriaDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\EqualCriterion;
use EscolaLms\Core\Repositories\Criteria\Primitives\HasCriterion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CriteriaDto extends BaseCriteriaDto implements DtoContract, InstantiateFromRequest
{
    public static function instantiateFromRequest(Request $request): self
    {
        $criteria = new Collection();

        if ($request->get('consultation_id')) {
            $criteria->push(new EqualCriterion('consultation_id', $request->get('consultation_id')));
        }
        if ($request->get('user_id')) {
            $criteria->push(new EqualCriterion('user_id', $request->get('user_id')));
        }
        if ($request->get('status')) {
            $criteria->push(new EqualCriterion('status', $request->get('status')));
        }
        if ($request->get('proposed_at_from')) {
            $criteria->push(new HasCriterion('consultationAccessEnquiryProposedTerms', function (Builder $query) use ($request) {
                $query->whereDate('proposed_at', '>=', Carbon::make($request->get('proposed_at_from')));
            }));
        }
        if ($request->get('proposed_at_to')) {
            $criteria->push(new HasCriterion('consultationAccessEnquiryProposedTerms', function (Builder $query) use ($request) {
                $query->whereDate('proposed_at', '<=', Carbon::make($request->get('proposed_at_to')));
            }));
        }

        $criteria->push(new OrderCriterion($request->get('order_by') ?? 'id', $request->get('order') ?? 'desc'));

        return new static($criteria);
    }
}
