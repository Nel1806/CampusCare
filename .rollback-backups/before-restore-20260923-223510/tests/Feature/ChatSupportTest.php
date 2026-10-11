<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class ChatSupportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function maintenanceRequest(): MaintenanceRequest
    {
        return MaintenanceRequest::create([
            'reference' => 'CC-260910-ABCDE',
            'requester_name' => 'Maria Santos',
            'requester_email' => 'maria@example.com',
            'room' => '204',
            'building' => 'Science Building',
            'description' => 'Broken light',
        ]);
    }

    public function test_user_must_match_request_reference_and_email_to_open_chat(): void
    {
        $this->maintenanceRequest();

        $this->post(route('chat.open'), [
            'chat_reference' => 'CC-260910-ABCDE',
            'chat_email' => 'wrong@example.com',
        ])->assertRedirect()->assertSessionHasErrors('chat_reference');

        $this->post(route('chat.open'), [
            'chat_reference' => 'cc-260910-abcde',
            'chat_email' => 'MARIA@example.com',
        ])->assertSessionHas('chat_request_id');
    }

    public function test_user_and_admin_can_exchange_messages_and_unread_state_is_updated(): void
    {
        $maintenanceRequest = $this->maintenanceRequest();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->withSession(['chat_request_id' => $maintenanceRequest->id])
            ->post(route('chat.store'), ['message' => 'When will this be repaired?'])
            ->assertRedirect();

        $userMessage = ChatMessage::first();
        $this->assertSame('user', $userMessage->sender);
        $this->assertNull($userMessage->read_at);

        $this->actingAs($admin)->get(route('admin.chat.index', ['request' => $maintenanceRequest->id]))
            ->assertOk()->assertSee('Maria Santos')->assertSee('When will this be repaired?');
        $this->assertNotNull($userMessage->fresh()->read_at);

        $this->actingAs($admin)->post(route('admin.chat.store', $maintenanceRequest), [
            'message' => 'A technician is on the way.',
        ]);

        $this->withSession(['chat_request_id' => $maintenanceRequest->id])->get(route('request.create'))
            ->assertOk()->assertSee('A technician is on the way.');
    }

    public function test_non_admin_cannot_access_admin_messages(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $maintenanceRequest = $this->maintenanceRequest();

        $this->actingAs($user)->get(route('admin.chat.index'))->assertForbidden();
        $this->actingAs($user)->delete(route('admin.chat.conversations.destroy'), [
            'conversation_ids' => [$maintenanceRequest->id],
        ])->assertForbidden();
    }

    public function test_administrator_can_delete_an_entire_conversation(): void
    {
        $maintenanceRequest = $this->maintenanceRequest();
        $admin = User::factory()->create(['role' => 'admin']);
        $message = $maintenanceRequest->chatMessages()->create([
            'sender' => 'user',
            'message' => 'Please provide an update.',
        ]);
        $maintenanceRequest->chatMessages()->create([
            'sender' => 'admin',
            'message' => 'A technician is assigned.',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.chat.conversations.destroy'), [
                'conversation_ids' => [$maintenanceRequest->id],
            ])
            ->assertRedirect(route('admin.chat.index'));

        $this->assertDatabaseMissing('chat_messages', ['id' => $message->id]);
        $this->assertDatabaseCount('chat_messages', 0);
        $this->assertDatabaseHas('maintenance_requests', ['id' => $maintenanceRequest->id]);
    }

    public function test_administrator_can_delete_only_selected_conversations(): void
    {
        $request = $this->maintenanceRequest();
        $admin = User::factory()->create(['role' => 'admin']);
        $first = $request->chatMessages()->create(['sender' => 'user', 'message' => 'First']);
        $second = $request->chatMessages()->create(['sender' => 'admin', 'message' => 'Second']);
        $other = $request->replicate();
        $other->reference = 'CC-260910-OTHER';
        $other->save();
        $kept = $other->chatMessages()->create(['sender' => 'user', 'message' => 'Keep']);

        $this->actingAs($admin)->delete(route('admin.chat.conversations.destroy'), [
            'conversation_ids' => [$request->id],
        ])->assertRedirect(route('admin.chat.index'));

        $this->assertDatabaseMissing('chat_messages', ['id' => $first->id]);
        $this->assertDatabaseMissing('chat_messages', ['id' => $second->id]);
        $this->assertDatabaseHas('chat_messages', ['id' => $kept->id]);
        $this->assertDatabaseHas('maintenance_requests', ['id' => $request->id]);
    }

    public function test_selected_conversation_deletion_requires_admin_and_valid_selection(): void
    {
        $request = $this->maintenanceRequest();
        $message = $request->chatMessages()->create(['sender' => 'user', 'message' => 'Keep']);
        $other = $request->replicate();
        $other->reference = 'CC-260910-OTHER';
        $other->save();
        $other->chatMessages()->create(['sender' => 'user', 'message' => 'Other chat']);
        $url = route('admin.chat.conversations.destroy');

        $this->delete($url, ['conversation_ids' => [$request->id]])->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->delete($url, ['conversation_ids' => [$request->id]])->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->delete($url, ['conversation_ids' => []])->assertSessionHasErrors('conversation_ids');
        $this->delete($url, ['conversation_ids' => [$request->id, 999999]])
            ->assertSessionHasErrors('conversation_ids.1');
        $this->assertDatabaseCount('chat_messages', 2);
    }
}
