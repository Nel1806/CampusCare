@extends('layouts.app', ['title' => 'Admin Sign In | CampusCare'])

@push('styles')
<style>
    .login-page {
        display: grid;
        min-height: 100vh;
        place-items: center;
        padding: 28px;
        background-image:
            linear-gradient(120deg, rgb(5 70 68 / .18), rgb(5 70 68 / .04)),
            url('{{ asset('images/admin-login-background-v2.png') }}');
        background-position: center;
        background-size: cover;
    }

    .login-card {
        width: min(100%, 430px);
        padding: 34px;
        border: 1px solid rgb(255 255 255 / .55);
        border-radius: 22px;
        background: rgb(255 255 255 / .32);
        box-shadow: 0 22px 60px rgb(4 49 56 / .24);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
    }

    .login-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 31px;
        color: #073b40;
        font-size: 21px;
        font-weight: 800;
        letter-spacing: -.3px;
    }

    .login-brand .brand-logo {
        width: 42px;
        height: 42px;
        filter: drop-shadow(0 8px 17px rgb(15 118 110 / .28));
    }

    .login-card h1 {
        margin: 0;
        color: #073b40;
        font-size: 27px;
        letter-spacing: -.6px;
    }

    .login-card .intro {
        margin: 7px 0 25px;
        color: #174e54;
        font-weight: 600;
    }

    .login-card .field label {
        color: #103f45;
    }

    .login-card .field input {
        border-color: rgb(255 255 255 / .75);
        background: rgb(255 255 255 / .68);
    }

    .login-card .field input:focus {
        background: rgb(255 255 255 / .88);
    }

    .password-control {
        position: relative;
    }

    .password-control input {
        padding-right: 48px !important;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        bottom: 8px;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 7px;
        background: transparent;
        color: #0f5e62;
        cursor: pointer;
    }

    .password-toggle svg {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 1.8;
    }

    .password-toggle:hover {
        background: rgb(15 118 110 / .12);
    }

    .login-card .remember {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #174e54;
        font-size: 13px;
        font-weight: 600;
    }

    .login-card .btn {
        width: 100%;
        margin-top: 3px;
        box-shadow: 0 8px 16px rgb(15 118 110 / .25);
    }

    .login-note {
        margin: 19px 0 0;
        color: #174e54;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
    }

    @media (max-width: 480px) {
        .login-page {
            padding: 18px;
        }

        .login-card {
            padding: 27px 22px;
        }
    }
</style>
@endpush

@section('body')
<main class="login-page">
    <section class="login-card">
        <a href="{{ route('request.create') }}" class="login-brand">
            <img
                class="brand-logo"
                src="{{ asset('images/campuscare-logo.png') }}"
                alt="CampusCare logo"
            >
            CampusCare
        </a>

        <h1>Administrator Sign In</h1>

        <p class="intro">
            Access maintenance requests, assignments, and team operations.
        </p>

        <form
            method="POST"
            action="{{ route('admin.login.store') }}"
            class="form-stack"
        >
            @csrf

            <div class="field">
                <label for="email">Email</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
            </div>

            <div class="field password-control">
                <label for="password">Password</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                >

                <button
                    class="password-toggle"
                    id="password-toggle"
                    type="button"
                    aria-label="Show password"
                    title="Show password"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                        <circle cx="12" cy="12" r="2.5"/>
                    </svg>
                </button>
            </div>

            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror

            <label class="remember">
                <input
                    name="remember"
                    type="checkbox"
                >
                Keep me signed in
            </label>

            <button class="btn" type="submit">
                Sign in
            </button>
        </form>

        <p class="login-note">
    CampusCare administration<br>
    Email: admin@campuscare.test<br>
    Password: password
</p>
    </section>
</main>

<script>
    const passwordInput = document.getElementById('password'),
        passwordToggle = document.getElementById('password-toggle');

    passwordToggle.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';

        passwordInput.type = isHidden ? 'text' : 'password';

        passwordToggle.setAttribute(
            'aria-label',
            isHidden ? 'Hide password' : 'Show password'
        );

        passwordToggle.setAttribute(
            'title',
            isHidden ? 'Hide password' : 'Show password'
        );
    });
</script>
@endsection
