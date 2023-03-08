<?php

namespace EscolaLms\ConsultationAccess\Tests\Api;

use EscolaLms\ConsultationAccess\Database\Seeders\ConsultationAccessPermissionSeeder;
use EscolaLms\ConsultationAccess\Enum\ConsultationAccessPermissionEnum;
use EscolaLms\ConsultationAccess\Events\ConsultationAccessEnquiryAdminCreatedEvent;
use EscolaLms\ConsultationAccess\Models\Consultation;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiryProposedTerm;
use EscolaLms\ConsultationAccess\Tests\TestCase;
use EscolaLms\Core\Tests\CreatesUsers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

class ConsultationAccessEnquiryDeleteApiTest extends TestCase
{
    use CreatesUsers;

    private $enquiry;
    private $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConsultationAccessPermissionSeeder::class);
        $this->student = $this->makeStudent();
        $this->enquiry = ConsultationAccessEnquiry::factory()
            ->state(['user_id' => $this->student->getKey()])
            ->has(ConsultationAccessEnquiryProposedTerm::factory()->count(4))
            ->create();
    }

    public function testConsultationAccessEnquiryDeleteUnauthorized(): void
    {
        $this->deleteJson('api/consultation-access-enquiries/' . $this->enquiry->getKey())
            ->assertUnauthorized();
    }

    public function testConsultationAccessEnquiryDelete(): void
    {
        $this->actingAs($this->student, 'api')
            ->deleteJson('api/consultation-access-enquiries/' . $this->enquiry->getKey())
            ->assertOk();

        $this->assertDatabaseMissing('consultation_access_enquiries', [
            'id' => $this->enquiry->getKey(),
        ]);

        $this->assertDatabaseMissing('consultation_access_enquiry_proposed_terms', [
            'consultation_access_enquiry_id' => $this->enquiry->getKey(),
        ]);
    }
}
