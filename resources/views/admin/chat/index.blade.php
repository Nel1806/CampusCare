@extends('layouts.app', ['title' => 'Chat Messages | CampusCare'])

@push('styles')
<style>
    .chat-page { padding-top: 28px; padding-bottom: 42px; }
    .chat-page h1 { margin: 0; font-size: 28px; }
    .chat-page > p { margin: 5px 0 22px; color: var(--muted); }
    .admin-chat { display: grid; grid-template-columns: 330px minmax(0, 1fr); min-height: 620px; overflow: hidden; }
    .conversation-list { border-right: 1px solid var(--line); }
    .conversation-header { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 14px 12px 18px; border-bottom: 1px solid var(--line); }
    .conversation-header h2 { font-size: 16px; margin: 0; }
    .conversation-row { position: relative; display: flex; align-items: center; border-bottom: 1px solid var(--line); }
    .conversation { display: block; flex: 1; min-width: 0; padding: 15px 18px; position: relative; }
    .conversation:hover, .conversation.active { background: var(--surface); }
    .conversation strong { display: block; padding-right: 28px; }
    .conversation small { display: block; color: var(--muted); margin-top: 3px; }
    .unread-dot { position: absolute; right: 17px; top: 18px; min-width: 21px; height: 21px; padding: 0 6px; border-radius: 11px; display: grid; place-items: center; background: #ef4444; color: #fff; font-size: 11px; font-weight: 800; }
    .conversation-empty { color: var(--muted); padding: 25px 18px; }
    .conversation-select { display: none; width: 18px; height: 18px; flex: 0 0 auto; margin-left: 15px; accent-color: var(--brand); cursor: pointer; }
    .conversation-list.is-selecting .conversation-select { display: block; }
    .conversation-list.is-selecting .conversation { padding-left: 12px; }
    .conversation-list.is-selecting .conversation { pointer-events: none; }
    .conversation-delete-toggle, .delete-selected-conversations { border: 0; border-radius: 7px; padding: 7px 10px; cursor: pointer; font: inherit; font-size: 12px; font-weight: 800; }
    .conversation-delete-toggle { display: grid; width: 34px; height: 34px; place-items: center; padding: 0; background: #fef3f2; color: var(--red); }
    .conversation-delete-toggle svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.9; }
    .conversation-delete-toggle.is-active { background: var(--surface); color: var(--ink); }
    .delete-selected-conversations { width: calc(100% - 28px); margin: 14px; background: var(--red); color: #fff; }
    .admin-thread { display: flex; flex-direction: column; min-width: 0; }
    .thread-header { padding: 17px 22px; border-bottom: 1px solid var(--line); }
    .thread-header strong { display: block; font-size: 17px; }
    .thread-header span { color: var(--muted); font-size: 13px; }
    .messages { flex: 1; max-height: 475px; overflow-y: auto; padding: 22px; background: var(--surface); display: flex; flex-direction: column; gap: 11px; }
    .message-row { display: flex; align-items: end; gap: 8px; }
    .message-row-admin { justify-content: flex-end; }
    .bubble { max-width: 72%; padding: 10px 13px; border: 1px solid var(--line); border-radius: 17px 17px 17px 4px; background: var(--paper); box-shadow: 0 1px 2px rgb(15 23 42 / 6%); }
    .bubble-admin { align-self: flex-end; background: var(--brand); border-color: var(--brand); color: #fff; }
    .message-row-admin .bubble { border-radius: 17px 17px 4px 17px; }
    .bubble b, .bubble small { display: block; }
    .bubble b { font-size: 11px; margin-bottom: 2px; }
    .bubble small { opacity: .7; margin-top: 4px; font-size: 11px; }
    .admin-reply { display: flex; gap: 10px; padding: 16px 22px; border-top: 1px solid var(--line); }
    .admin-reply textarea { flex: 1; min-height: 64px; resize: vertical; border: 1px solid var(--line); border-radius: 8px; padding: 10px; background: var(--paper); color: var(--ink); font: inherit; }
    .select-prompt { margin: auto; color: var(--muted); }
    @media(max-width: 780px) { .admin-chat { grid-template-columns: 1fr; } .conversation-list { border-right: 0; border-bottom: 1px solid var(--line); max-height: 260px; overflow-y: auto; } }
</style>
@endpush

@section('body')
@include('layouts.admin-header')
<main class="shell chat-page">
    <h1>Chat Messages</h1>
    <p>View and respond to questions about maintenance requests.</p>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <section class="card admin-chat">
        <aside class="conversation-list">
            <div class="conversation-header">
                <h2>Conversations</h2>
                <button class="conversation-delete-toggle" id="conversation-delete-toggle" type="button" aria-label="Select conversations to delete" title="Delete conversations" aria-expanded="false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 7h16M10 11v6M14 11v6M9 7l1-2h4l1 2M6 7l1 13h10l1-13"/>
                    </svg>
                </button>
            </div>
            <form id="conversation-delete-form" method="POST" action="{{ route('admin.chat.conversations.destroy') }}" onsubmit="return confirm('Delete the selected conversation(s) permanently? All messages in the selected chats will be removed.');">
                @csrf
                @method('DELETE')
                @forelse($conversations as $conversation)
                    <div class="conversation-row">
                        <input class="conversation-select" type="checkbox" name="conversation_ids[]" value="{{ $conversation->id }}" aria-label="Select conversation with {{ $conversation->requester_name }}">
                        <a class="conversation {{ $activeRequest?->id === $conversation->id ? 'active' : '' }}" href="{{ route('admin.chat.index', ['request' => $conversation->id]) }}">
                            <strong>{{ $conversation->requester_name }}</strong>
                            <small>{{ $conversation->reference }} &middot; {{ $conversation->building }}</small>
                            <small>{{ \Illuminate\Support\Carbon::parse($conversation->chat_messages_max_created_at)->timezone('Asia/Manila')->diffForHumans() }}</small>
                            @if($conversation->unread_count)<span class="unread-dot">{{ $conversation->unread_count }}</span>@endif
                        </a>
                    </div>
                @empty
                    <div class="conversation-empty">No user messages yet.</div>
                @endforelse
                <button class="delete-selected-conversations" id="delete-selected-conversations" type="submit" hidden disabled>Delete selected</button>
            </form>
        </aside>
        <div class="admin-thread">
            @if($activeRequest)
                <header class="thread-header">
                    <strong>{{ $activeRequest->requester_name }}</strong>
                    <span>{{ $activeRequest->requester_email }} &middot; {{ $activeRequest->reference }} &middot; {{ $activeRequest->building }}, {{ $activeRequest->room }}</span>
                </header>
                <div class="messages" id="admin-messages">
                    @foreach($activeRequest->chatMessages as $chatMessage)
                        <div class="message-row message-row-{{ $chatMessage->sender }}">
                            <div class="bubble bubble-{{ $chatMessage->sender }}">
                                <b>{{ $chatMessage->sender === 'user' ? $activeRequest->requester_name : 'Administrator' }}</b>
                                {{ $chatMessage->message }}
                                <small>{{ $chatMessage->created_at->timezone('Asia/Manila')->format('M j, Y g:i A') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <form class="admin-reply" method="POST" action="{{ route('admin.chat.store', $activeRequest) }}">
                    @csrf
                    <textarea name="message" placeholder="Reply to {{ $activeRequest->requester_name }}..." required maxlength="2000">{{ old('message') }}</textarea>
                    <button class="btn" type="submit">Send reply</button>
                </form>
                @error('message')<p class="error" style="padding:0 22px">{{ $message }}</p>@enderror
            @else
                <p class="select-prompt">Messages from users will appear here.</p>
            @endif
        </div>
    </section>
</main>
<script>
    const messages = document.getElementById('admin-messages');
    if (messages) messages.scrollTop = messages.scrollHeight;

    const conversationList = document.querySelector('.conversation-list');
    const deleteToggle = document.getElementById('conversation-delete-toggle');
    const selectedDeleteButton = document.getElementById('delete-selected-conversations');

    const updateConversationSelection = () => {
        const selectedCount = document.querySelectorAll('.conversation-select:checked').length;
        selectedDeleteButton.hidden = selectedCount === 0;
        selectedDeleteButton.disabled = selectedCount === 0;
        selectedDeleteButton.textContent = `Delete ${selectedCount} selected`;
    };

    deleteToggle.addEventListener('click', () => {
        const isSelecting = conversationList.classList.toggle('is-selecting');
        deleteToggle.classList.toggle('is-active', isSelecting);
        deleteToggle.title = isSelecting ? 'Cancel selection' : 'Delete conversations';
        deleteToggle.setAttribute('aria-label', deleteToggle.title);
        deleteToggle.setAttribute('aria-expanded', String(isSelecting));

        if (!isSelecting) {
            document.querySelectorAll('.conversation-select').forEach(checkbox => checkbox.checked = false);
        }

        updateConversationSelection();
    });

    conversationList.addEventListener('change', event => {
        if (event.target.matches('.conversation-select')) updateConversationSelection();
    });
</script>
@endsection
