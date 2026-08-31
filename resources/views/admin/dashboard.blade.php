@extends('layouts.app', ['title' => 'Dashboard | CampusCare'])

@push('styles')
<style>
    .dashboard {
        padding-top: 28px;
        padding-bottom: 42px;
    }

    .dashboard-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 26px;
    }

    .dashboard-title h1 {
        margin: 0;
        font-size: 27px;
        letter-spacing: -.7px;
    }

    .dashboard-title p {
        margin: 5px 0 0;
        color: var(--muted);
    }

    .dashboard-search {
        width: min(320px, 100%);
    }

    .dashboard-search input {
        padding: 10px 13px;
    }

    .metrics {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .metric {
        min-height: 122px;
        padding: 19px;
        position: relative;
        overflow: hidden;
    }

    .metric::after {
        content: '';
        position: absolute;
        inset: 0 0 0 auto;
        width: 72px;
        opacity: .15;
        background: radial-gradient(
            circle,
            var(--metric-color) 0 5px,
            transparent 6px
        );
        background-size: 18px 18px;
    }

    .metric-label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
    }

    .metric-value {
        margin-top: 10px;
        font-size: 31px;
        font-weight: 800;
        letter-spacing: -1.2px;
    }

    .metric-icon {
        display: grid;
        place-items: center;
        position: absolute;
        right: 18px;
        bottom: 18px;
        width: 31px;
        height: 31px;
        border-radius: 9px;
        color: var(--metric-color);
        background: color-mix(
            in srgb,
            var(--metric-color) 13%,
            var(--paper)
        );
        font-weight: 800;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.75fr) minmax(250px, .65fr);
        gap: 20px;
    }

    .panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 19px 20px 14px;
    }

    .panel-heading h2 {
        margin: 0;
        font-size: 16px;
        letter-spacing: -.25px;
    }

    .panel-heading span {
        color: var(--muted);
        font-size: 12px;
    }

    .request-table {
        overflow-x: auto;
    }

    .request-table table {
        width: 100%;
        min-width: 680px;
        border-collapse: collapse;
    }

    .request-table th {
        padding: 11px 20px;
        color: var(--muted);
        background: color-mix(
            in srgb,
            var(--surface) 68%,
            var(--paper)
        );
        border-top: 1px solid var(--line);
        border-bottom: 1px solid var(--line);
        text-align: left;
        font-size: 11px;
        letter-spacing: .45px;
        text-transform: uppercase;
    }

    .request-table td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--line);
        font-size: 13px;
        vertical-align: middle;
    }

    .request-photo {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--surface);
    }

    .request-photo-empty {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: var(--surface);
        color: var(--muted);
        font-size: 16px;
    }

    .request-table tr:last-child td {
        border-bottom: 0;
    }

    .request-table tr:hover td {
        background: color-mix(
            in srgb,
            var(--brand) 3%,
            var(--paper)
        );
    }

    .request-ref {
        font-weight: 800;
    }

    .request-location {
        display: block;
        margin-top: 2px;
        color: var(--muted);
        font-size: 12px;
    }

    .action-link {
        color: var(--brand);
        font-size: 12px;
        font-weight: 800;
    }

    .activity {
        display: grid;
        gap: 10px;
        padding: 0 14px 14px;
    }

    .activity-item {
        display: flex;
        gap: 10px;
        padding: 12px;
        border-radius: 9px;
        background: color-mix(
            in srgb,
            var(--surface) 68%,
            var(--paper)
        );
    }

    .activity-icon {
        flex: 0 0 25px;
        display: grid;
        place-items: center;
        height: 25px;
        border-radius: 6px;
        color: var(--brand);
        background: #d9f3ee;
        font-size: 13px;
    }

    .activity-item strong {
        display: block;
        font-size: 12px;
    }

    .activity-item small {
        display: block;
        margin-top: 2px;
        color: var(--muted);
        font-size: 11px;
    }

    .team-panel {
        margin-top: 20px;
    }

    .team-list {
        padding: 0 20px 6px;
    }

    .team-member {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 12px 0;
        border-top: 1px solid var(--line);
    }

    .team-member strong {
        display: block;
        font-size: 13px;
    }

    .team-member span {
        color: var(--muted);
        font-size: 11px;
    }

    .duty-control {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .duty-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .duty-label-on { color: #087443 !important; }
    .duty-label-off { color: #b42318 !important; }

    .duty-switch {
        position: relative;
        width: 38px;
        height: 21px;
    }

    .duty-switch input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
    }

    .duty-slider {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: #cbd5e1;
        cursor: pointer;
        transition: background-color .28s ease;
    }

    .duty-slider::after {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 3px rgb(15 23 42 / .25);
        content: '';
        transition: transform .28s cubic-bezier(.34, 1.56, .64, 1), box-shadow .2s ease;
    }

    .duty-switch input:checked + .duty-slider { background: #087443; }
    .duty-switch input:checked + .duty-slider::after { transform: translateX(17px); }
    .duty-control.is-saving { opacity: .65; pointer-events: none; }

    @media (max-width: 950px) {
        .metrics {
            grid-template-columns: repeat(2, 1fr);
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .dashboard-title {
            align-items: flex-start;
            flex-direction: column;
        }

        .metrics {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('body')
    @include('layouts.admin-header')

    <main class="shell dashboard">
        <div class="dashboard-title">
            <div>
                <h1>Admin Overview</h1>
                <p>Monitor maintenance operations and team workload in one place.</p>
            </div>

            <form
                class="dashboard-search"
                method="GET"
                id="live-search-form"
            >
                <input
                    id="live-search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search requests"
                    aria-label="Search requests"
                    autocomplete="off"
                >

                @if (request('status'))
                    <input
                        type="hidden"
                        name="status"
                        value="{{ request('status') }}"
                    >
                @endif
            </form>
        </div>

        <section class="metrics">
            <article
                class="card metric"
                style="--metric-color:var(--brand)"
            >
                <div class="metric-label">Total Requests</div>
                <div class="metric-value">{{ $counts->sum() }}</div>
                <span class="metric-icon">▦</span>
            </article>

            <article
                class="card metric"
                style="--metric-color:#2563eb"
            >
                <div class="metric-label">Open Requests</div>
                <div class="metric-value">
                    {{ ($counts['new'] ?? 0) + ($counts['assigned'] ?? 0) }}
                </div>
                <span class="metric-icon">◫</span>
            </article>

            <article
                class="card metric"
                style="--metric-color:#d97706"
            >
                <div class="metric-label">In Progress</div>
                <div class="metric-value">
                    {{ $counts['in_progress'] ?? 0 }}
                </div>
                <span class="metric-icon">◌</span>
            </article>

            <article
                class="card metric"
                style="--metric-color:#16a34a"
            >
                <div class="metric-label">Resolved</div>
                <div class="metric-value">
                    {{ $counts['resolved'] ?? 0 }}
                </div>
                <span class="metric-icon">✓</span>
            </article>
        </section>

        <div class="dashboard-grid">
            <div>
                <section class="card">
                    <div class="panel-heading">
                        <div>
                            <h2>Recent Requests</h2>
                            <span>Latest reports from campus</span>
                        </div>

                        <form method="GET">
                            <select
                                name="status"
                                onchange="this.form.submit()"
                                aria-label="Filter requests by status"
                            >
                                <option value="">All statuses</option>

                                @foreach([
                                    'new'=>'New',
                                    'assigned'=>'Assigned',
                                    'in_progress'=>'In progress',
                                    'resolved'=>'Resolved'
                                ] as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(request('status') === $value)
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @if(request('search'))
                                <input
                                    type="hidden"
                                    name="search"
                                    value="{{ request('search') }}"
                                >
                            @endif
                        </form>
                    </div>

                    <div class="request-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Issue</th>
                                    <th>Location</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Assigned to</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($requests as $requestItem)
                                    <tr>
                                        <td class="request-ref">
                                            {{ $requestItem->reference }}
                                        </td>

                                        <td>
                                            {{ str($requestItem->description)->limit(32) }}
                                        </td>

                                        <td>
                                            {{ $requestItem->building }}
                                            <span class="request-location">
                                                {{ $requestItem->room }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($requestItem->photo_path)
                                                <img
                                                    class="request-photo"
                                                    src="{{ asset('storage/'.$requestItem->photo_path) }}"
                                                    alt="Issue photo for {{ $requestItem->reference }}"
                                                >
                                            @else
                                                <span
                                                    class="request-photo-empty"
                                                    title="No image uploaded"
                                                >
                                                    —
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="status status-{{ $requestItem->status }}">
                                                {{ str_replace('_', ' ', $requestItem->status) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $requestItem->assignee?->name ?? 'Unassigned' }}
                                        </td>

                                        <td>
                                            <a
                                                class="action-link"
                                                href="{{ route('admin.requests.show', $requestItem) }}"
                                            >
                                                Manage
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="7"
                                            class="muted"
                                            style="padding:38px;text-align:center"
                                        >
                                            No maintenance requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card team-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Support Team Status</h2>
                            <span>Maintenance personnel and current workload</span>
                        </div>

                        <a
                            class="action-link"
                            href="{{ route('admin.personnel.index') }}"
                        >
                            Manage team
                        </a>
                    </div>

                    <div class="team-list">
                        @forelse($personnel as $person)
                            @php($onDuty = $person->availability !== 'off_duty')
                            <div class="team-member">
                                <div>
                                    <strong>{{ $person->name }}</strong>

                                    <span>
                                        {{ $person->expertise ?? 'Expertise not set' }}
                                        ·
                                        {{ $person->assigned_requests_count }} assigned
                                    </span>
                                </div>

                                <form class="duty-control" method="POST" action="{{ route('admin.personnel.duty.update', $person) }}" data-duty-form>
                                    @csrf
                                    @method('PATCH')
                                    <span class="duty-label {{ $onDuty ? 'duty-label-on' : 'duty-label-off' }}" data-duty-label>
                                        {{ $onDuty ? 'On Duty' : 'Off Duty' }}
                                    </span>
                                    <label class="duty-switch">
                                        <input type="checkbox" name="on_duty" value="1" @checked($onDuty) onchange="this.form.requestSubmit()" aria-label="Toggle {{ $person->name }} duty status">
                                        <span class="duty-slider"></span>
                                    </label>
                                </form>
                            </div>
                        @empty
                            <div
                                class="muted"
                                style="padding:14px 0 20px"
                            >
                                No personnel have been added yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="card">
                <div class="panel-heading">
                    <div>
                        <h2>Last Activity</h2>
                        <span>Recent request changes</span>
                    </div>
                </div>

                <div class="activity">
                    @forelse($recentActivity as $activity)
                        <div class="activity-item">
                            <span class="activity-icon">✓</span>

                            <div>
                                <strong>
                                    {{ str($activity->description)->limit(28) }}
                                </strong>

                                <small>
                                    {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                    ·
                                    {{ $activity->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div
                            class="muted"
                            style="padding:15px"
                        >
                            No recent activity.
                        </div>
                    @endforelse
                </div>
            </aside>
        </div>
    </main>

    <script>
        const searchForm = document.getElementById('live-search-form'),
            searchInput = document.getElementById('live-search');

        let searchDelay;

        searchInput.addEventListener('input', () => {
            clearTimeout(searchDelay);

            searchDelay = setTimeout(
                () => searchForm.submit(),
                350
            );
        });

        document.querySelectorAll('[data-duty-form]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const toggle = form.querySelector('input[name="on_duty"]');
                const label = form.querySelector('[data-duty-label]');
                const previousState = !toggle.checked;

                form.classList.add('is-saving');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });

                    if (!response.ok) {
                        throw new Error('Unable to save duty status');
                    }

                    label.textContent = toggle.checked ? 'On Duty' : 'Off Duty';
                    label.classList.toggle('duty-label-on', toggle.checked);
                    label.classList.toggle('duty-label-off', !toggle.checked);
                } catch (error) {
                    toggle.checked = previousState;
                    alert('Could not update duty status. Please try again.');
                } finally {
                    form.classList.remove('is-saving');
                }
            });
        });
    </script>
@endsection
