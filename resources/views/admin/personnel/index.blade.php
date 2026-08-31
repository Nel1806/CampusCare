@extends('layouts.app', ['title' => 'Maintenance Personnel | CampusCare'])

@push('styles')
<style>
    .personnel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin: 34px 0 22px;
    }

    .personnel-head h1 {
        margin: 0;
        font-size: 30px;
        letter-spacing: -.7px;
    }

    .personnel-head p { margin: 7px 0 0; color: var(--muted); }
    .personnel-list { display: grid; gap: 14px; }

    .person-card {
        display: grid;
        grid-template-columns: minmax(220px, 1.2fr) auto auto;
        align-items: center;
        gap: 24px;
        padding: 20px;
    }

    .person-name { font-size: 17px; font-weight: 800; }
    .person-email { margin-top: 3px; color: var(--muted); font-size: 13px; }
    .workload { margin-top: 9px; color: var(--muted); font-size: 13px; }

    .employment-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .employment-status::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        content: '';
    }

    .employment-status-active { color: #087443; background: #ecfdf3; }
    .employment-status-inactive { color: #b42318; background: #fef3f2; }

    .person-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .person-actions form { display: flex; margin: 0; }

    .person-actions .btn,
    .btn-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        white-space: nowrap;
    }

    .btn-delete {
        padding: 9px 12px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: transparent;
        color: #b42318;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-delete:hover { background: #fff1f1; }

    @media (max-width: 850px) {
        .person-card { grid-template-columns: 1fr; gap: 14px; }
        .personnel-head { align-items: flex-start; flex-direction: column; }
        .person-actions { justify-content: flex-start; }
    }

    @media (max-width: 520px) {
        .person-actions { flex-wrap: wrap; }
    }
</style>
@endpush

@section('body')
    @include('layouts.admin-header')

    <main class="shell">
        <div class="personnel-head">
            <div>
                <p style="margin: 0 0 7px; color: var(--brand); font-size: 11px; font-weight: 800; letter-spacing: .8px; text-transform: uppercase">Team management</p>
                <h1>Maintenance personnel</h1>
                <p>Keep your team’s availability and working status current.</p>
            </div>

            <a class="btn" href="{{ route('admin.personnel.create') }}">Add Maintenance Personnel</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="personnel-list">
            @forelse ($personnel as $person)
                <article class="card person-card">
                    <div>
                        <div class="person-name">{{ $person->name }}</div>
                        <div class="person-email">{{ $person->email }}</div>
                        <div class="person-email">Expertise: {{ $person->expertise ?? 'Not set' }}</div>
                        <div class="workload">
                            {{ $person->assigned_requests_count }} assigned
                            {{ Str::plural('request', $person->assigned_requests_count) }}
                        </div>
                    </div>

                    <div>
                        <span class="employment-status employment-status-{{ $person->employment_status }}">
                            {{ $person->employment_status }}
                        </span>
                    </div>

                    <div class="person-actions">
                        <a class="btn btn-secondary" href="{{ route('admin.personnel.edit', $person) }}" style="padding: 9px 12px">Edit</a>

                        <form method="POST" action="{{ route('admin.personnel.destroy', $person) }}" onsubmit="return confirm('Delete {{ $person->name }} from maintenance personnel?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-delete" type="submit">Delete Personnel</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="card" style="padding: 42px; color: var(--muted); text-align: center">
                    No maintenance personnel have been added yet.
                </div>
            @endforelse
        </section>
    </main>
@endsection
