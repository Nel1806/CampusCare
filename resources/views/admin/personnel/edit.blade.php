@extends('layouts.app', ['title' => 'Edit Maintenance Personnel | CampusCare'])

@push('styles')
<style>
    .form-page {
        max-width: 740px;
        margin: 34px auto 50px;
    }

    .form-page h1 {
        margin: 18px 0 0;
        font-size: 30px;
        letter-spacing: -.7px;
    }

    .form-page > p {
        color: var(--muted);
        margin: 7px 0 22px;
    }

    .staff-form {
        padding: 25px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .staff-form .full {
        grid-column: 1 / -1;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 4px;
    }

    @media (max-width: 600px) {
        .staff-form {
            grid-template-columns: 1fr;
            padding: 18px;
        }

        .staff-form .full {
            grid-column: auto;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions .btn {
            text-align: center;
        }
    }
</style>
@endpush

@section('body')
@include('layouts.admin-header')

<main class="shell">
    <div class="form-page">
        <a
            class="muted"
            href="{{ route('admin.personnel.index') }}"
            style="font-weight:700;font-size:13px"
        >
            ← Back to personnel
        </a>

        <h1>Edit maintenance personnel</h1>

        <p>
            Update this team member’s account, expertise, and employment status.
        </p>

        <form
            method="POST"
            action="{{ route('admin.personnel.update', $personnel) }}"
            class="card staff-form"
        >
            @csrf
            @method('PATCH')

            <div class="field full">
                <label for="name">Full name</label>

                <input
                    id="name"
                    name="name"
                    value="{{ old('name', $personnel->name) }}"
                    required
                >

                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field full">
                <label for="email">Email address</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $personnel->email) }}"
                    required
                >

                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="expertise">Expertise</label>

                <select
                    id="expertise"
                    name="expertise"
                    required
                >
                    @foreach(['Electrician', 'IT', 'Carpenter', 'Cleaner'] as $expertise)
                        <option
                            value="{{ $expertise }}"
                            @selected(old('expertise', $personnel->expertise) === $expertise)
                        >
                            {{ $expertise }}
                        </option>
                    @endforeach
                </select>

                @error('expertise')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="employment_status">Employment status</label>

                <select
                    id="employment_status"
                    name="employment_status"
                >
                    @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected(old('employment_status', $personnel->employment_status) === $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="full form-actions">
                <a
                    class="btn btn-secondary"
                    href="{{ route('admin.personnel.index') }}"
                >
                    Cancel
                </a>

                <button
                    class="btn"
                    type="submit"
                >
                    Save changes
                </button>
            </div>
        </form>
    </div>
</main>
@endsection