<?php

namespace EscolaLms\ConsultationAccess\Http\Resources;

use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\Consultations\Http\Resources\ConsultationTermsResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ConsultationAccessEnquiryResource",
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          type="number"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="consultation",
 *          ref="#/components/schemas/ConsultationShortResource"
 *      ),
 *      @OA\Property(
 *          property="user",
 *          ref="#/components/schemas/ConsultationUserShortResource"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          description="status",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="meeting_link",
 *          description="meeting_link",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="proposed_terms",
 *          ref="#/components/schemas/ConsultationAccessEnquiryProposedTermsResource"
 *      ),
 *      @OA\Property(
 *          property="consultation_term",
 *          ref="#/components/schemas/ConsultationTerm"
 *      ),
 *      @OA\Property(
 *          property="related_type",
 *          description="related_type",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="related_id",
 *          description="related_id",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 * )
 *
 */

/**
 * @mixin ConsultationAccessEnquiry
 */
class ConsultationAccessEnquiryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'consultation' => ConsultationShortResource::make($this->consultation),
            'user' => UserShortResource::make($this->user),
            'proposed_terms' => ConsultationAccessEnquiryProposedTermsResource::collection($this->consultationAccessEnquiryProposedTerms),
            'description' => $this->description,
            'consultation_term' => $this->consultationUser ? ConsultationTermsResource::make($this->consultationUser) : null,
            'meeting_link' => $this->meeting_link,
            'related_type' => $this->related_type,
            'related_id' => $this->related_id,
            'title' => $this->title,
        ];
    }
}
