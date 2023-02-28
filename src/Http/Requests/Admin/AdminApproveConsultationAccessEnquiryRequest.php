<?php

namespace EscolaLms\ConsultationAccess\Http\Requests\Admin;

use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class AdminApproveConsultationAccessEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('approve', ConsultationAccessEnquiry::class);
    }

    public function rules(): array
    {
        return [
            'proposed_term_id' => ['required', 'integer', 'exists:consultation_access_enquiry_proposed_terms,id'],
        ];
    }

    public function getProposedTermId(): int
    {
        return $this->route('proposedTermId');
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['proposed_term_id' => $this->getProposedTermId()]);
    }
}
