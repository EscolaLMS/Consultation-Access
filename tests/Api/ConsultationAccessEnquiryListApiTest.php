<?php

namespace EscolaLms\ConsultationAccess\Tests\Api;

use EscolaLms\ConsultationAccess\Database\Seeders\ConsultationAccessPermissionSeeder;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiryProposedTerm;
use EscolaLms\ConsultationAccess\Tests\TestCase;
use EscolaLms\Core\Tests\CreatesUsers;

class ConsultationAccessEnquiryListApiTest extends TestCase
{
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConsultationAccessPermissionSeeder::class);
    }

    public function testConsultationAccessEnquiryListUnauthorized(): void
    {
        $this->getJson('api/consultation-access-enquiries')
            ->assertUnauthorized();
    }

    public function testConsultationAccessEnquiryList(): void
    {
        $student = $this->makeStudent();

        ConsultationAccessEnquiry::factory()
            ->state(['user_id' => $student->getKey()])
            ->count(3)
            ->has(ConsultationAccessEnquiryProposedTerm::factory())
            ->create();

        ConsultationAccessEnquiry::factory()->count(2)->create();

        $this->actingAs($student, 'api')->getJson('api/consultation-access-enquiries')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'created_at',
                    'status',
                    'consultation' => [
                        'id',
                        'name',
                    ],
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'proposed_terms' => [
                        [
                            'id',
                            'proposed_at',
                        ],
                    ],
                ]],
            ]);
    }
}
