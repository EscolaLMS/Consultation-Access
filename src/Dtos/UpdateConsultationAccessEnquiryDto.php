<?php

namespace EscolaLms\ConsultationAccess\Dtos;

use EscolaLms\ConsultationAccess\Enum\EnquiryStatusEnum;
use EscolaLms\Core\Dtos\Contracts\DtoContract;
use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UpdateConsultationAccessEnquiryDto implements DtoContract, InstantiateFromRequest
{
    protected array $proposedTerms;
    protected ?string $description;

    public function __construct(array $proposedTerms, ?string $description)
    {
        $this->proposedTerms = $proposedTerms;
        $this->description = $description;
    }

    public function getProposedTerms(): array
    {
        return collect($this->proposedTerms)
            ->map(fn($term) => Carbon::make($term))
            ->toArray();
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
        ];
    }

    public static function instantiateFromRequest(Request $request): self
    {
        return new static(
            $request->input('proposed_terms'),
            $request->input('description')
        );
    }
}
