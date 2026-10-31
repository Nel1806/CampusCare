@extends('layouts.app', ['title' => $requestItem->reference.' | CampusCare'])

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(300px, .8fr);
        gap: 20px;
        margin-bottom: 40px;
    }

    .detail-card {
        padding: 24px;
    }

    .detail-card h2 {
        font-size: 17px;
        margin: 0 0 18px;
    }

    .issue-photo {
        width: 100%;
        max-height: 480px;
        object-fit: contain;
        background: #f1f5f9;
        border-radius: 6px;
    }

    .details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .details small {
        display: block;
        text-transform: uppercase;
        color: var(--muted);
        font-weight: 700;
        font-size: 11px;
        letter-spacing: .5px;
        margin-bottom: 3px;
    }

    .description {
        white-space: pre-wrap;
        margin: 0;
        color: #344054;
    }

    .update-form {
        display: grid;
        gap: 18px;
    }

    .reference-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--brand);
        margin-bottom: 5px;
    }

    .manage-loading[hidden],
    .manage-result-overlay[hidden] { display: none; }
    .manage-loading,
    .manage-result-overlay {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgb(15 23 42 / 55%);
        backdrop-filter: blur(3px);
    }
    .manage-loading-card,
    .manage-result-card {
        width: min(430px, 100%);
        padding: 30px;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: var(--paper);
        box-shadow: 0 24px 70px rgb(15 23 42 / 25%);
        text-align: center;
    }
    .manage-spinner {
        width: 42px;
        height: 42px;
        margin: 0 auto 15px;
        border: 4px solid color-mix(in srgb, var(--brand) 18%, var(--paper));
        border-top-color: var(--brand);
        border-radius: 50%;
        animation: manage-spin .75s linear infinite;
    }
    @keyframes manage-spin { to { transform: rotate(360deg); } }
    .manage-loading p { margin: 0; font-weight: 800; }
    .manage-result-icon {
        display: grid;
        width: 54px;
        height: 54px;
        margin: 0 auto 16px;
        place-items: center;
        border-radius: 50%;
        background: #ecfdf3;
        color: #067647;
        font-size: 27px;
        font-weight: 800;
    }
    .manage-result-icon.is-error { background: #fef3f2; color: #b42318; }
    .manage-result-card h2 { margin: 0; font-size: 21px; }
    .manage-result-card p { margin: 9px 0 22px; color: var(--muted); line-height: 1.55; }
    .manage-result-errors { margin: -8px 0 22px; padding-left: 20px; color: #b42318; text-align: left; font-size: 13px; }
    .manage-result-done { min-height: 43px; padding: 0 20px; border: 0; border-radius: 8px; background: var(--brand); color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
    .update-form button:disabled { cursor: wait; opacity: .7; }

    @media (max-width: 750px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .details {
            grid-template-columns: 1fr;
        }

        .detail-card {
            padding: 19px;
        }
    }
</style>
@endpush

@section('body')
@include('layouts.admin-header')

<main class="shell">
    <div class="page-head">
        <div>
            <div class="reference-title">
                {{ $requestItem->reference }}
            </div>

            <h1>Request details</h1>

            <p>
                Reported {{ $requestItem->created_at->timezone('Asia/Manila')->format('F j, Y \a\t g:i A') }}
            </p>
        </div>

        <a
            class="btn btn-secondary"
            href="{{ route('admin.dashboard') }}"
        >
            Back to requests
        </a>
    </div>

    <div class="detail-grid">
        <section class="card detail-card">
            <h2>Reported issue</h2>

            @if($requestItem->photo_path)
                <img
                    class="issue-photo"
                    src="{{ asset('storage/'.$requestItem->photo_path) }}"
                    alt="Photo submitted with request"
                >
            @else
                <div class="muted">
                    No photo was attached.
                </div>
            @endif

            <div class="details" style="margin-top:22px">
                <div>
                    <small>Building</small>
                    <strong>{{ $requestItem->building }}</strong>
                </div>

                <div>
                    <small>Room / Area</small>
                    <strong>{{ $requestItem->room }}</strong>
                </div>
            </div>

            <div style="margin-top:22px">
                <small
                    style="display:block;text-transform:uppercase;color:var(--muted);font-weight:700;font-size:11px;letter-spacing:.5px;margin-bottom:6px"
                >
                    Description
                </small>

                <p class="description">
                    {{ $requestItem->description }}
                </p>
            </div>
        </section>

        <aside class="card detail-card">
            <h2>Assignment & status</h2>

            <form
                id="request-update-form"
                data-request-update-form
                class="update-form"
                method="POST"
                action="{{ route('admin.requests.update', $requestItem) }}"
            >
                @csrf
                @method('PATCH')

                <div class="field">
                    <label for="maintenance_personnel_id">
                        Maintenance personnel
                    </label>

                    <select
                        id="maintenance_personnel_id"
                        name="maintenance_personnel_id"
                    >
                        <option value="">Not assigned</option>

                        @foreach($personnel as $person)
                            <option
                                value="{{ $person->id }}"
                                @selected(old('maintenance_personnel_id', $requestItem->maintenance_personnel_id) == $person->id)
                            >
                                {{ $person->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="status">
                        Current status
                    </label>

                    <select
                        id="status"
                        name="status"
                    >
                        @foreach(['new' => 'New', 'assigned' => 'Assigned', 'in_progress' => 'In progress', 'resolved' => 'Resolved'] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(old('status', $requestItem->status) === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <span class="status status-{{ $requestItem->status }}">
                        {{ str_replace('_', ' ', $requestItem->status) }}
                    </span>
                </div>

                <button
                    id="request-update-button"
                    type="submit"
                    class="btn"
                >
                    Save changes
                </button>
            </form>
        </aside>
    </div>
</main>

<div class="manage-loading" id="manage-loading" role="status" aria-live="polite" aria-busy="true" hidden>
    <div class="manage-loading-card">
        <div class="manage-spinner" aria-hidden="true"></div>
        <p>Saving changes...</p>
    </div>
</div>

<div class="manage-result-overlay" id="manage-result-overlay" @if(!session('success') && !$errors->any()) hidden @endif>
    <section class="manage-result-card" role="dialog" aria-modal="true" aria-labelledby="manage-result-title" aria-describedby="manage-result-description">
        @if(session('success'))
            <div class="manage-result-icon" id="manage-result-icon" aria-hidden="true">✓</div>
            <h2 id="manage-result-title">Changes saved</h2>
            <p id="manage-result-description">{{ session('success') }}</p>
            <ul class="manage-result-errors" id="manage-result-errors" hidden></ul>
        @elseif($errors->any())
            <div class="manage-result-icon is-error" id="manage-result-icon" aria-hidden="true">!</div>
            <h2 id="manage-result-title">Couldn't save changes</h2>
            <p id="manage-result-description">Please correct the following and try again.</p>
            <ul class="manage-result-errors" id="manage-result-errors">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @else
            <div class="manage-result-icon" id="manage-result-icon" aria-hidden="true">✓</div>
            <h2 id="manage-result-title">Changes saved</h2>
            <p id="manage-result-description"></p>
            <ul class="manage-result-errors" id="manage-result-errors" hidden></ul>
        @endif
        <button type="button" class="manage-result-done" id="manage-result-done">Done</button>
    </section>
</div>

<script>
    const requestUpdateForm = document.getElementById('request-update-form');
    const requestUpdateButton = document.getElementById('request-update-button');
    const manageLoading = document.getElementById('manage-loading');
    const manageResultOverlay = document.getElementById('manage-result-overlay');
    const manageResultTitle = document.getElementById('manage-result-title');
    const manageResultDescription = document.getElementById('manage-result-description');
    const manageResultErrors = document.getElementById('manage-result-errors');
    const manageResultIcon = document.getElementById('manage-result-icon');

    requestUpdateForm.addEventListener('submit', (event) => {
        if (requestUpdateForm.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        requestUpdateForm.dataset.submitting = 'true';
        requestUpdateButton.disabled = true;
        manageLoading.hidden = false;
        manageResultOverlay.hidden = true;
    });

    document.addEventListener('campuscare:ajax-form-error', (event) => {
        if (event.detail?.form !== requestUpdateForm) return;

        requestUpdateForm.dataset.submitting = 'false';
        requestUpdateButton.disabled = false;
        manageLoading.hidden = true;
        manageResultIcon.classList.add('is-error');
        manageResultIcon.textContent = '!';
        manageResultTitle.textContent = 'Unable to save changes';
        manageResultDescription.textContent = 'A connection or server error stopped the update. Please try again.';
        manageResultErrors.hidden = true;
        manageResultOverlay.hidden = false;
        document.getElementById('manage-result-done').focus();
    });

    const closeManageResult = () => { manageResultOverlay.hidden = true; };
    document.getElementById('manage-result-done').addEventListener('click', closeManageResult);
    manageResultOverlay.addEventListener('click', (event) => {
        if (event.target === manageResultOverlay) closeManageResult();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !manageResultOverlay.hidden) closeManageResult();
    });
    if (!manageResultOverlay.hidden) document.getElementById('manage-result-done').focus();
</script>
@endsection
