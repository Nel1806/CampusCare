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

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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
                                @selected($requestItem->maintenance_personnel_id === $person->id)
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
                                @selected($requestItem->status === $value)
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
                    type="submit"
                    class="btn"
                >
                    Save changes
                </button>
            </form>
        </aside>
    </div>
</main>
@endsection
