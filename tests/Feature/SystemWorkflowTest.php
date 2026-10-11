<?php

namespace Tests\Feature;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_ignores_removed_pages_and_admin_pages_load(): void
    {
        User::factory()->create(['email' => 'admin@campuscare.test', 'password' => 'password', 'role' => 'admin']);
        $this->withSession(['url.intended' => url('/admin/chat')])
            ->post(route('admin.login.store'), ['email' => 'admin@campuscare.test', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        foreach (['admin.dashboard', 'admin.personnel.index', 'admin.personnel.create'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_request_submission_tracking_and_status_only_update(): void
    {
        Mail::fake();
        Storage::fake('public');
        $this->post(route('request.store'), [
            'requester_name' => 'Test Requester', 'requester_email' => 'requester@example.com',
            'room' => '101', 'building' => 'Science', 'description' => 'Broken light',
            'photo' => UploadedFile::fake()->image('issue.jpg'),
        ])->assertRedirect(route('request.create'))->assertSessionHasNoErrors();
        $report = MaintenanceRequest::firstOrFail();
        Storage::disk('public')->assertExists($report->photo_path);
        $this->post(route('request.track'), ['reference' => strtolower($report->reference)])
            ->assertSessionHas('tracked_request_id', $report->id);
        $this->get(route('request.create'))->assertOk()->assertSee($report->reference);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('admin.requests.show', $report))->assertOk();
        $this->patch(route('admin.requests.update', $report), ['status' => 'in_progress'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('in_progress', $report->fresh()->status);
    }

    public function test_invalid_dashboard_filters_are_validation_errors(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->getJson(route('admin.dashboard', ['search' => ['invalid']]))->assertUnprocessable();
        $this->getJson(route('admin.dashboard', ['status' => 'invalid']))->assertUnprocessable();
    }

    public function test_request_can_be_submitted_without_a_photo_even_when_mail_fails(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Mail server unavailable'));
        $this->post(route('request.store'), [
            'requester_name' => 'Test Requester', 'requester_email' => 'requester@example.com',
            'room' => '101', 'building' => 'Science', 'description' => 'Broken light',
        ])->assertRedirect(route('request.create'))->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertDatabaseHas('maintenance_requests', ['requester_email' => 'requester@example.com', 'photo_path' => null]);
    }
}
