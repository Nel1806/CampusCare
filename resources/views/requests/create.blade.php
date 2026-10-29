@extends('layouts.app', ['title' => 'Report an Issue | CampusCare'])

@push('styles')
<style>
    .public-hero {
        background: linear-gradient(125deg, #0b3d3d, #0f766e);
        color: #fff;
        padding: 38px 0 82px;
    }

    .public-hero h1 {
        font-size: 34px;
        line-height: 1.15;
        margin: 21px 0 9px;
        max-width: 620px;
    }

    .public-hero p {
        color: #d1fae5;
        margin: 0;
        max-width: 560px;
    }

    .public-content {
        max-width: 720px;
        margin: -48px auto 50px;
        padding: 0 20px;
    }

    .report-card {
        padding: 28px;
    }

    .report-card h2 {
        font-size: 20px;
        margin: 0 0 4px;
    }

    .report-card > p {
        margin: 0 0 24px;
        color: var(--muted);
    }

    .report-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 17px;
    }

    .upload {
        border: 1.5px dashed #90a4b5;
        border-radius: 7px;
        padding: 22px;
        text-align: center;
        background: #f8fafc;
        position: relative;
    }

    .upload strong {
        display: block;
    }

    .upload span {
        font-size: 13px;
        color: var(--muted);
    }

    .photo-choice-trigger {
        width: 100%;
        padding: 0;
        border: 0;
        background: transparent;
        color: var(--ink);
        font: inherit;
        cursor: pointer;
    }

    .field-label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 700;
    }

    .photo-choice-trigger:focus-visible,
    .photo-choice-actions button:focus-visible {
        outline: 3px solid #5eead4;
        outline-offset: 3px;
    }

    .photo-choice-overlay[hidden] { display: none; }
    .photo-choice-overlay {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgb(15 23 42 / 58%);
        backdrop-filter: blur(4px);
    }

    .photo-choice-dialog {
        width: min(420px, 100%);
        padding: 28px;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: var(--paper);
        box-shadow: 0 24px 70px rgb(15 23 42 / 25%);
        text-align: center;
    }

    .photo-choice-dialog h2 { margin: 0; font-size: 20px; }
    .photo-choice-dialog > p { margin: 8px 0 20px; color: var(--muted); }
    .photo-choice-actions { display: grid; gap: 10px; }
    .photo-choice-actions button {
        min-height: 46px;
        padding: 0 16px;
        border: 1px solid var(--line);
        border-radius: 9px;
        background: var(--paper);
        color: var(--ink);
        font: inherit;
        font-weight: 700;
        cursor: pointer;
    }
    .photo-choice-actions .use-camera { border-color: #0f766e; background: #0f766e; color: #fff; }

    .preview {
        display: none;
        max-width: 170px;
        max-height: 130px;
        margin: 10px auto 0;
        border-radius: 6px;
    }

    .tracking-card {
        margin-top: 20px;
        padding: 28px;
    }

    .tracking-card h2 {
        margin: 0 0 4px;
        font-size: 20px;
    }

    .tracking-card > p {
        margin: 0 0 17px;
        color: var(--muted);
    }

    .tracking-form {
        display: flex;
        gap: 10px;
    }

    .tracking-form input { flex: 1; }

    .tracking-result {
        margin-top: 18px;
        padding: 14px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #f8fafc;
    }

    .tracking-result strong { display: block; }
    .tracking-result p { margin: 4px 0 0; color: var(--muted); font-size: 13px; }

    .tracking-status {
        display: inline-flex;
        margin-top: 9px;
        padding: 4px 9px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 800;
    }

    .tracking-pending { background: #eff8ff; color: #175cd3; }
    .tracking-progress { background: #fffaeb; color: #b54708; }
    .tracking-resolved { background: #ecfdf3; color: #067647; }

    .submission-overlay {
        position: fixed;
        inset: 0;
        z-index: 1100;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgb(15 23 42 / 58%);
        backdrop-filter: blur(4px);
    }

    .submission-confirmation {
        width: min(460px, 100%);
        padding: 34px;
        border: 1px solid var(--line);
        border-radius: 18px;
        background: var(--paper);
        box-shadow: 0 26px 80px rgb(15 23 42 / 28%);
        text-align: center;
        animation: confirmation-appear .2s ease-out;
    }

    @keyframes confirmation-appear {
        from { opacity: 0; transform: translateY(10px) scale(.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .submission-check {
        display: grid;
        width: 64px;
        height: 64px;
        margin: 0 auto 18px;
        place-items: center;
        border-radius: 50%;
        background: #ecfdf3;
        color: #067647;
        font-size: 32px;
        font-weight: 800;
    }

    .submission-confirmation h2 { margin: 0; font-size: 23px; }
    .submission-confirmation > p { margin: 9px 0 22px; color: var(--muted); line-height: 1.55; }
    .submission-reference-label { display: block; color: var(--muted); font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; }
    .submission-reference {
        display: block;
        margin: 8px 0 18px;
        padding: 15px 12px;
        border: 1px dashed #8bbdb5;
        border-radius: 10px;
        background: #f0fdfa;
        color: #0f766e;
        font-size: clamp(20px, 6vw, 26px);
        font-weight: 800;
        letter-spacing: 1px;
        overflow-wrap: anywhere;
    }

    .submission-note { margin: 0 0 22px; color: var(--muted); font-size: 13px; }
    .submission-actions { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
    .submission-actions button {
        min-height: 44px;
        padding: 0 18px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--paper);
        color: var(--ink);
        font: inherit;
        font-weight: 700;
        cursor: pointer;
    }
    .submission-actions .submission-done { border-color: #0f766e; background: #0f766e; color: #fff; }

    .helper {
        font-size: 12px;
        color: var(--muted);
        margin-top: 5px;
    }

    .footer-note {
        text-align: center;
        color: var(--muted);
        font-size: 13px;
        margin-top: 18px;
    }

    @media (max-width: 550px) {
        .public-hero {
            padding: 27px 0 67px;
        }

        .public-hero h1 {
            font-size: 29px;
        }

        .report-card,
        .tracking-card {
            padding: 20px;
        }

        .report-grid {
            grid-template-columns: 1fr;
        }

        .tracking-form {
            flex-direction: column;
        }

        .tracking-form .btn { width: 100%; }
    }
</style>
@endpush

@section('body')
<header class="public-hero">
    <div class="shell">
        <a href="{{ route('request.create') }}" class="brand">
            <img class="brand-logo" src="{{ asset('images/campuscare-logo.png') }}" alt="CampusCare logo"> CampusCare
        </a>

        <h1>Report a Campus Facility Issue</h1>

        <p>
            Help us keep your learning spaces safe, comfortable, and ready to use.
        </p>
    </div>
</header>

<main class="public-content">
    <section class="card report-card">
        <h2>Maintenance Request</h2>

        <p>
            Include enough detail to help our team find and resolve the problem.
        </p>

        <form
            method="POST"
            action="{{ route('request.store') }}"
            enctype="multipart/form-data"
            class="form-stack"
        >
            @csrf

            <div class="report-grid">
                <div class="field">
                    <label for="requester_name">Name</label>

                    <input
                        id="requester_name"
                        name="requester_name"
                        value="{{ old('requester_name') }}"
                        placeholder="Your full name"
                        required
                    >

                    @error('requester_name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="requester_email">Email address</label>

                    <input
                        id="requester_email"
                        name="requester_email"
                        type="email"
                        value="{{ old('requester_email') }}"
                        placeholder="name@gmail.com"
                        required
                    >

                    @error('requester_email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="building">Building</label>

                    <input
                        id="building"
                        name="building"
                        value="{{ old('building') }}"
                        placeholder="e.g. Science Building"
                        required
                    >

                    @error('building')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="room">Room / Area</label>

                    <input
                        id="room"
                        name="room"
                        value="{{ old('room') }}"
                        placeholder="e.g. Room 204"
                        required
                    >

                    @error('room')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="field">
                <label for="description">What needs attention?</label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe the issue..."
                    required
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <span class="field-label">Photo of the issue (optional)</span>

                <div class="upload">
                    <button class="photo-choice-trigger" type="button" id="photo-choice-open" aria-haspopup="dialog">
                        <strong id="photo-choice-label">Tap to take or choose a photo</strong>
                        <span>JPG, PNG, or WEBP, up to 5 MB</span>
                    </button>

                    <input class="photo-file-input" id="camera-photo" type="file" accept="image/*" capture="environment" hidden>
                    <input class="photo-file-input" id="upload-photo" type="file" accept="image/*" hidden>

                    <img
                        id="preview"
                        class="preview"
                        alt="Selected issue photo"
                    >
                </div>

                @error('photo')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button class="btn" type="submit">
                Submit maintenance request
            </button>
        </form>
    </section>

    <section class="card tracking-card">
        <h2>Request tracking</h2>
        <p>Enter your Request ID to check the latest status of your maintenance request.</p>

        <form class="tracking-form" method="POST" action="{{ route('request.track') }}">
            @csrf
            <input
                id="reference"
                name="reference"
                value=""
                placeholder="e.g. CC-250101-ABCDE"
                aria-label="Request ID"
                required
            >
            <button class="btn" type="submit">Track request</button>
        </form>

        @error('reference')
            <p class="error">{{ $message }}</p>
        @enderror

        @if ($trackingSearched)
            @if ($trackedRequest)
                @php
                    $trackingStatus = match ($trackedRequest->status) {
                        'resolved' => ['Resolved', 'resolved'],
                        'in_progress' => ['In Progress', 'progress'],
                        default => ['Pending', 'pending'],
                    };
                @endphp

                <div class="tracking-result">
                    <strong>{{ $trackedRequest->reference }}</strong>
                    <p>{{ $trackedRequest->building }} · {{ $trackedRequest->room }}</p>
                    <span class="tracking-status tracking-{{ $trackingStatus[1] }}">{{ $trackingStatus[0] }}</span>
                </div>
            @else
                <div class="tracking-result">
                    <strong>Request not found</strong>
                    <p>Please check the Request ID and try again.</p>
                </div>
            @endif
        @endif

    <p class="footer-note">
        CampusCare Facility Support &middot;
        <a href="{{ url('/admin') }}">Administrator sign in</a>
    </p>
</main>

<div class="photo-choice-overlay" id="photo-choice-overlay" hidden>
    <section class="photo-choice-dialog" role="dialog" aria-modal="true" aria-labelledby="photo-choice-title" aria-describedby="photo-choice-description">
        <h2 id="photo-choice-title">Add a photo</h2>
        <p id="photo-choice-description">Choose how you want to attach a photo of the issue.</p>
        <div class="photo-choice-actions">
            <button type="button" class="use-camera" id="use-camera">Use camera</button>
            <button type="button" id="upload-file">Upload a file</button>
            <button type="button" id="photo-choice-cancel">Cancel</button>
        </div>
    </section>
</div>

@if(session('success'))
    <div class="submission-overlay" id="submission-overlay">
        <section class="submission-confirmation" role="dialog" aria-modal="true" aria-labelledby="submission-title" aria-describedby="submission-description">
            <div class="submission-check" aria-hidden="true">✓</div>
            <h2 id="submission-title">Submission completed</h2>
            <p id="submission-description">Your maintenance request has been received. Keep this unique code to check its status later.</p>
            <span class="submission-reference-label">Your request ID</span>
            <strong class="submission-reference" id="submission-reference">{{ session('success') }}</strong>
            <p class="submission-note">You can also find this code in the confirmation email.</p>
            <div class="submission-actions">
                <button type="button" id="copy-request-id">Copy request ID</button>
                <button type="button" class="submission-done" id="submission-done">Done</button>
            </div>
        </section>
    </div>
@endif

<script>
    const photoChoice = document.getElementById('photo-choice-overlay');
    const photoChoiceOpen = document.getElementById('photo-choice-open');
    const photoChoiceCancel = document.getElementById('photo-choice-cancel');
    const photoChoiceClose = () => { photoChoice.hidden = true; };

    photoChoiceOpen.addEventListener('click', () => {
        photoChoice.hidden = false;
        document.getElementById('use-camera').focus();
    });
    photoChoiceCancel.addEventListener('click', photoChoiceClose);
    photoChoice.addEventListener('click', (event) => {
        if (event.target === photoChoice) photoChoiceClose();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !photoChoice.hidden) photoChoiceClose();
    });

    const choosePhoto = (inputId) => {
        const input = document.getElementById(inputId);
        photoChoiceClose();
        input.click();
    };

    document.getElementById('use-camera').addEventListener('click', () => choosePhoto('camera-photo'));
    document.getElementById('upload-file').addEventListener('click', () => choosePhoto('upload-photo'));

    document.querySelectorAll('.photo-file-input').forEach((input) => {
        input.addEventListener('change', () => {
            if (input.files.length) {
                document.querySelectorAll('.photo-file-input').forEach((otherInput) => {
                    if (otherInput !== input) {
                        otherInput.value = '';
                        otherInput.removeAttribute('name');
                    }
                });
                input.name = 'photo';
                previewPhoto(input);
            }
        });
    });

    function previewPhoto(input) {
        const file = input.files[0];
        const image = document.getElementById('preview');

        if (file) {
            image.src = URL.createObjectURL(file);
            image.style.display = 'block';
            document.getElementById('photo-choice-label').textContent = file.name;
        }
    }

    document.getElementById('reference').addEventListener('input', (event) => {
        if (!event.target.value.trim()) {
            window.location.href = '{{ route('request.create') }}';
        }
    });

    const submissionOverlay = document.getElementById('submission-overlay');
    if (submissionOverlay) {
        const doneButton = document.getElementById('submission-done');
        const copyButton = document.getElementById('copy-request-id');
        const closeConfirmation = () => { submissionOverlay.style.display = 'none'; };

        doneButton.addEventListener('click', closeConfirmation);
        submissionOverlay.addEventListener('click', (event) => {
            if (event.target === submissionOverlay) closeConfirmation();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeConfirmation();
        });
        copyButton.addEventListener('click', async () => {
            const requestId = document.getElementById('submission-reference').textContent.trim();
            try {
                await navigator.clipboard.writeText(requestId);
                copyButton.textContent = 'Copied';
            } catch (error) {
                copyButton.textContent = requestId;
            }
        });
        doneButton.focus();
    }
</script>
@endsection
