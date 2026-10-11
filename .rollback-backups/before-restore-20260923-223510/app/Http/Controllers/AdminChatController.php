<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminChatController extends Controller
{
    public function index(Request $request)
    {
        $conversations = MaintenanceRequest::query()
            ->whereHas('chatMessages')
            ->withCount(['chatMessages as unread_count' => fn ($q) => $q->where('sender', 'user')->whereNull('read_at')])
            ->withMax('chatMessages', 'created_at')
            ->orderByDesc('chat_messages_max_created_at')->get();

        $activeRequest = $request->filled('request')
            ? MaintenanceRequest::with('chatMessages')->findOrFail($request->integer('request'))
            : $conversations->first()?->load('chatMessages');

        if ($activeRequest) {
            ChatMessage::where('maintenance_request_id', $activeRequest->id)
                ->where('sender', 'user')->whereNull('read_at')->update(['read_at' => now()]);
        }

        return view('admin.chat.index', compact('conversations', 'activeRequest'));
    }

    public function store(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless($maintenanceRequest->chatMessages()->exists(), 404);
        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);
        $maintenanceRequest->chatMessages()->create(['sender' => 'admin', 'message' => trim($data['message'])]);

        return redirect()->route('admin.chat.index', ['request' => $maintenanceRequest->id])->with('success', 'Reply sent.');
    }

    public function destroyConversations(Request $request)
    {
        $data = $request->validate([
            'conversation_ids' => ['required', 'array', 'min:1'],
            'conversation_ids.*' => ['required', 'integer', 'distinct', 'exists:maintenance_requests,id'],
        ]);

        [$conversationCount, $messageCount] = DB::transaction(function () use ($data): array {
            $conversationIds = MaintenanceRequest::query()
                ->whereIn('id', $data['conversation_ids'])
                ->whereHas('chatMessages')
                ->pluck('id');

            $messageCount = ChatMessage::whereIn('maintenance_request_id', $conversationIds)->delete();

            return [$conversationIds->count(), $messageCount];
        });

        return redirect()->route('admin.chat.index')
            ->with('success', "{$conversationCount} conversation(s) deleted ({$messageCount} message(s) removed).");
    }
}
