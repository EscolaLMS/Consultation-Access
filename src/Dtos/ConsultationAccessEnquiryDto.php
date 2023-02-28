<?php

namespace EscolaLms\ConsultationAccess\Dtos;

use EscolaLms\ConsultationAccess\Enum\EnquiryStatusEnum;
use EscolaLms\Core\Dtos\Contracts\DtoContract;
use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use Illuminate\Http\Request;

class ConsultationAccessEnquiryDto implements DtoContract, InstantiateFromRequest
{

    protected int $consultationId;
    protected int $userId;
    protected string $status;
    protected array $proposedTerms;

    public function __construct(int $consultationId, int $userId, string $status, array $proposedTerms)
    {
        $this->consultationId = $consultationId;
        $this->userId = $userId;
        $this->status = $status;
        $this->proposedTerms = $proposedTerms;
    }

    public function getConsultationId(): int
    {
        return $this->consultationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getProposedTerms(): array
    {
        return $this->proposedTerms;
    }

    public function toArray(): array
    {
        return [
            'consultation_id' => $this->getConsultationId(),
            'user_id' => $this->getUserId(),
            'status' => $this->status,
        ];
    }

    public static function instantiateFromRequest(Request $request): self
    {
        return new static(
            $request->input('consultation_id'),
            auth()->id(),
            EnquiryStatusEnum::PENDING,
            $request->input('proposed_terms')
        );
    }
}
