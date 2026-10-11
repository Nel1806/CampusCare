<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class ChatSupportController extends Controller
{
    public function open(Request $request)
    {
        $data = $request->validate([
            'chat_reference' => ['required', 'string', 'max:30'],
            'chat_email' => ['required', 'email', 'max:255'],
        ]);

        $maintenanceRequest = $this->matchingRequest($data['chat_reference'], $data['chat_email']);

        if (! $maintenanceRequest) {
            return back()->withErrors(['chat_reference' => 'The Request ID and email do not match our records.'])->withInput();
        }

        $request->session()->put('chat_request_id', $maintenanceRequest->id);
        ChatMessage::where('maintenance_request_id', $maintenanceRequest->id)
            ->where('sender', 'admin')->whereNull('read_at')->update(['read_at' => now()]);

        return redirect()->route('request.create', ['chat' => 1])->with('chat_opened', true);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);
        $maintenanceRequest = MaintenanceRequest::find($request->session()->get('chat_request_id'));

        abort_unless($maintenanceRequest, 403);

        $maintenanceRequest->chatMessages()->create(['sender' => 'user', 'message' => trim($data['message'])]);

        return redirect()->route('request.create', ['chat' => 1])->with('chat_sent', 'Your message was sent to the administrator.');
    }

    public function close(Request $request)
    {
        $request->session()->forget('chat_request_id');

        return redirect()->route('request.create');
    }

    private function matchingRequest(string $reference, string $email): ?MaintenanceRequest
    {
        return MaintenanceRequest::where('reference', strtoupper(trim($reference)))
            ->whereRaw('LOWER(requester_email) = ?', [strtolower(trim($email))])->first();
    }
}
