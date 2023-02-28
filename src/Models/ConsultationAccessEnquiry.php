<?php

namespace EscolaLms\ConsultationAccess\Models;

use EscolaLms\Auth\Models\User;
use EscolaLms\ConsultationAccess\Database\Factories\ConsultationAccessEnquiryFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry
 *
 * @property-read int $id
 * @property int $consultation_id
 * @property int $user_id
 * @property string $status
 *
 * @property-read User $user
 * @property-read Consultation $consultation
 * @property-read Collection|ConsultationAccessEnquiryProposedTerm[] $consultationAccessEnquiryProposedTerms
 */
class ConsultationAccessEnquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'user_id',
        'status',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultationAccessEnquiryProposedTerms(): HasMany
    {
        return $this->hasMany(ConsultationAccessEnquiryProposedTerm::class);
    }

    public static function newFactory(): ConsultationAccessEnquiryFactory
    {
        return ConsultationAccessEnquiryFactory::new();
    }
}
