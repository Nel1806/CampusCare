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

    .upload input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .upload strong {
        display: block;
    }

    .upload span {
        font-size: 13px;
        color: var(--muted);
    }

    .preview {
        display: none;
        max-width: 170px;
        max-height: 130px;
        margin: 10px auto 0;
        border-radius: 6px;
    }

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

        .report-card {
            padding: 20px;
        }

        .report-grid {
            grid-template-columns: 1fr;
        }
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
    @if(session('success'))
        <div class="alert alert-success">
            <strong>Report submitted.</strong>
            Your reference number is
            <strong>{{ session('success') }}</strong>.
            The maintenance team has been notified.
        </div>
    @endif

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
                <label>Photo of the issue</label>

                <div class="upload">
                    <input
                        id="photo"
                        type="file"
                        name="photo"
                        accept="image/*"
                        capture="environment"
                        required
                        onchange="previewPhoto(this)"
                    >

                    <strong>Tap to take or choose a photo</strong>

                    <span>JPG, PNG, or WEBP, up to 5 MB</span>

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

    <p class="footer-note">
        CampusCare Facility Support &middot;
        <a href="{{ url('/admin') }}">Administrator sign in</a>
    </p>
</main>

<script>
    function previewPhoto(input) {
        const file = input.files[0],
            image = document.getElementById('preview');

        if (file) {
            image.src = URL.createObjectURL(file);
            image.style.display = 'block';
        }
    }
</script>
@endsection
