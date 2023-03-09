<?php

namespace EscolaLms\ConsultationAccess\Dtos;

use EscolaLms\ConsultationAccess\Enum\EnquiryStatusEnum;
use EscolaLms\Core\Dtos\Contracts\DtoContract;
use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use Illuminate\Http\Request;

class UpdateConsultationAccessEnquiryDto implements DtoContract, InstantiateFromRequest
{
    protected array $proposedTerms;

    public function __construct(array $proposedTerms)
    {
        $this->proposedTerms = $proposedTerms;
    }

    public function getProposedTerms(): array
    {
        return $this->proposedTerms;
    }

    public function toArray(): array
    {
        return [];
    }

    public static function instantiateFromRequest(Request $request): self
    {
        return new static(
            $request->input('proposed_terms')
        );
    }
}
