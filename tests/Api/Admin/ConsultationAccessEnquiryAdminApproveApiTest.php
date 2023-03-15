<?php

namespace EscolaLms\ConsultationAccess\Tests\Api\Admin;

use EscolaLms\ConsultationAccess\Database\Seeders\ConsultationAccessPermissionSeeder;
use EscolaLms\ConsultationAccess\Enum\EnquiryStatusEnum;
use EscolaLms\Consultations\Enum\ConsultationTermStatusEnum;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiryProposedTerm;
use EscolaLms\ConsultationAccess\Tests\TestCase;
use EscolaLms\Consultations\Models\ConsultationUserPivot;
use EscolaLms\Core\Tests\CreatesUsers;

class ConsultationAccessEnquiryAdminApproveApiTest extends TestCase
{
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConsultationAccessPermissionSeeder::class);
    }

    public function testConsultationAccessEnquiryAdminApproveUnauthorized(): void
    {
        $this->postJson('api/admin/consultation-access-enquiries/approve/1')
            ->assertUnauthorized();
    }

    public function testConsultationAccessEnquiryAdminApprove(): void
    {
        /** @var ConsultationAccessEnquiryProposedTerm $proposedTerm */
        $proposedTerm = ConsultationAccessEnquiryProposedTerm::factory()->create();
        $meetingLink = $this->faker->url;
        $this->actingAs($this->makeAdmin(), 'api')
            ->postJson('api/admin/consultation-access-enquiries/approve/' . $proposedTerm->getKey(), [
                'meeting_link' => $meetingLink,
            ])
            ->assertOk();

        $proposedTerm->refresh();

        $this->assertDatabaseHas('consultation_access_enquiries', [
            'status' => EnquiryStatusEnum::APPROVED,
            'consultation_id' => $proposedTerm->consultationAccessEnquiry->consultation_id,
            'meeting_link' => $meetingLink,
        ]);

        $this->assertDatabaseHas('consultation_user', [
            'user_id' => $proposedTerm->consultationAccessEnquiry->user_id,
            'consultation_id' => $proposedTerm->consultationAccessEnquiry->consultation_id,
            'executed_at' => $proposedTerm->proposed_at,
            'executed_status' => ConsultationTermStatusEnum::APPROVED,
        ]);
    }

    public function testConsultationAccessEnquiryAdminAlreadyApprovedException(): void
    {
        $enquiry = ConsultationAccessEnquiry::factory()
            ->state(['status' => EnquiryStatusEnum::APPROVED])
            ->create();

        $proposedTerm = ConsultationAccessEnquiryProposedTerm::factory()
            ->state(['consultation_access_enquiry_id' => $enquiry->getKey()])
            ->create();

        $this->actingAs($this->makeAdmin(), 'api')
            ->postJson('api/admin/consultation-access-enquiries/approve/' . $proposedTerm->getKey())
            ->assertStatus(400)
            ->assertJsonFragment([
                'message' => __('Enquiry already approved'),
            ]);
    }

    public function testConsultationAccessEnquiryAdminApproveProposedTermIsBusyException(): void
    {
        /** @var ConsultationAccessEnquiryProposedTerm $proposedTerm */
        $proposedTerm = ConsultationAccessEnquiryProposedTerm::factory()
            ->create();

        ConsultationUserPivot::factory()
            ->state([
                'user_id' => $this->makeStudent()->getKey(),
                'consultation_id' => $proposedTerm->consultationAccessEnquiry->consultation_id,
                'executed_at' => $proposedTerm->proposed_at,
                'executed_status' => ConsultationTermStatusEnum::APPROVED,
            ])->create();

        $this->actingAs($this->makeAdmin(), 'api')
            ->postJson('api/admin/consultation-access-enquiries/approve/' . $proposedTerm->getKey())
            ->assertStatus(400)
            ->assertJsonFragment([
                'message' => __('Term is busy'),
            ]);
    }
}
