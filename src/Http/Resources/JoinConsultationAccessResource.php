<?php

namespace EscolaLms\ConsultationAccess\Http\Resources;

use EscolaLms\ConsultationAccess\Enum\MeetingLinkTypeEnum;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\PencilSpaces\Facades\PencilSpace;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *      schema="JoinConsultationAccessResource",
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          type="number"
 *      ),
 *      @OA\Property(
 *          property="meeting_url",
 *          description="meeting_url",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="meeting_url_type",
 *          description="meeting_url_type",
 *          type="string"
 *      ),
 * )
 *
 */

/** @mixin ConsultationAccessEnquiry */
class JoinConsultationAccessResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'meeting_url_type' => $this->meeting_link_type,
            'meeting_url' => $this->meeting_link_type === MeetingLinkTypeEnum::PENCIL_SPACES
                ? PencilSpace::getDirectLoginUrl(Auth::id(), $this->meeting_link)
                : $this->meeting_link,
        ];
    }
}
