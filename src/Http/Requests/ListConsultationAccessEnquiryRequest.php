<?php

namespace EscolaLms\ConsultationAccess\Http\Requests;

use EscolaLms\ConsultationAccess\Dtos\CriteriaDto;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\Core\Dtos\PaginationDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ListConsultationAccessEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('listOwn', ConsultationAccessEnquiry::class);
    }

    public function rules(): array
    {
        return [];
    }

    public function getCriteriaDto(): CriteriaDto
    {
        return CriteriaDto::instantiateFromRequest($this);
    }

    public function getPaginationDto(): PaginationDto
    {
        return PaginationDto::instantiateFromRequest($this);
    }
}
