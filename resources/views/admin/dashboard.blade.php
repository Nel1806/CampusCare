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

    .request-date {
        color: var(--muted);
        font-size: 12px;
        white-space: nowrap;
    }

    .request-pagination {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: flex-end;
        padding: 16px 20px;
        border-top: 1px solid var(--line);
    }

    .request-pagination a,
    .request-pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        min-height: 32px;
        padding: 0 9px;
        border: 1px solid var(--line);
        border-radius: 7px;
        background: var(--paper);
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }

    .request-pagination .active {
        border-color: var(--brand);
        background: var(--brand);
        color: #fff;
    }

    .requests-panel {
        transition: opacity .18s ease, transform .18s ease;
    }

    .requests-panel.is-updating {
        opacity: .72;
        transform: translateY(3px);
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

    .request-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .request-delete {
        padding: 0;
        border: 0;
        background: none;
        color: var(--red);
        font: inherit;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
    }

    .delete-modal[hidden] { display: none; }
    .delete-modal {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgb(15 23 42 / 55%);
        backdrop-filter: blur(3px);
    }
    .delete-dialog {
        width: min(420px, 100%);
        padding: 28px;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: var(--paper);
        box-shadow: 0 24px 70px rgb(15 23 42 / 25%);
        text-align: center;
        animation: delete-dialog-in .16s ease-out;
    }
    @keyframes delete-dialog-in {
        from { opacity: 0; transform: translateY(8px) scale(.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .delete-dialog-icon {
        display: grid;
        width: 52px;
        height: 52px;
        margin: 0 auto 16px;
        place-items: center;
        border-radius: 50%;
        background: #fef2f2;
        color: var(--red);
        font-size: 25px;
        font-weight: 800;
    }
    .delete-dialog-icon.is-success {
        background: #ecfdf3;
        color: #067647;
    }
    .delete-dialog h2 { margin: 0; font-size: 20px; }
    .delete-dialog p { margin: 10px 0 24px; color: var(--muted); line-height: 1.55; }
    .delete-dialog-reference { color: var(--ink); font-weight: 800; }
    .delete-dialog-actions { display: flex; justify-content: center; gap: 10px; }
    .delete-dialog-actions button {
        min-height: 42px;
        padding: 0 17px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--paper);
        color: var(--ink);
        font: inherit;
        font-weight: 700;
        cursor: pointer;
    }
    .delete-dialog-actions .delete-confirm {
        border-color: var(--red);
        background: var(--red);
        color: #fff;
    }
    .delete-dialog-actions .delete-confirm.is-notice {
        border-color: var(--brand);
        background: var(--brand);
    }
    .request-delete-loading[hidden] { display: none; }
    .request-delete-loading {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgb(15 23 42 / 55%);
        backdrop-filter: blur(3px);
    }
    .request-delete-loading-card {
        width: min(330px, 100%);
        padding: 28px 24px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: var(--paper);
        box-shadow: 0 20px 60px rgb(15 23 42 / 22%);
        text-align: center;
    }
    .request-delete-spinner {
        width: 42px;
        height: 42px;
        margin: 0 auto 15px;
        border: 4px solid color-mix(in srgb, var(--brand) 18%, var(--paper));
        border-top-color: var(--brand);
        border-radius: 50%;
        animation: request-delete-spin .75s linear infinite;
    }
    @keyframes request-delete-spin { to { transform: rotate(360deg); } }
    .request-delete-loading p { margin: 0; font-weight: 800; }
    .dashboard-delete-error {
        margin: 0 0 18px;
        padding: 13px 16px;
        border: 1px solid #fecdca;
        border-radius: 9px;
        background: #fef3f2;
        color: #b42318;
        font-size: 13px;
        font-weight: 700;
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
        @if (session('delete_error'))
            <div class="dashboard-delete-error" role="alert">{{ session('delete_error') }}</div>
        @endif
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
                <section class="card requests-panel" id="requests-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Recent Requests</h2>
                            <span>Latest reports from campus</span>
                        </div>

                        <form method="GET" id="status-filter-form">
                            <select
                                id="status-filter"
                                name="status"
                                aria-label="Filter requests by status"
                            >
                                <option value="">All</option>

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
                                    <th>Date Submitted</th>
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
                                        <td class="request-date">
                                            {{ $requestItem->created_at->timezone('Asia/Manila')->format('M j, Y') }}
                                        </td>

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
                                                    loading="lazy"
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
                                            <div class="request-actions">
                                                <a
                                                    class="action-link"
                                                    href="{{ route('admin.requests.show', $requestItem) }}"
                                                >
                                                    Manage
                                                </a>
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.requests.destroy', $requestItem) }}"
                                                    data-request-delete-form
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="request-delete" type="button" data-delete-request="{{ $requestItem->reference }}" data-request-resolved="{{ $requestItem->status === 'resolved' ? 'true' : 'false' }}">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="8"
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

                    @if ($requests->hasPages())
                        <nav class="request-pagination" aria-label="Request table pages">
                            @for ($page = 1; $page <= $requests->lastPage(); $page++)
                                @if ($page === $requests->currentPage())
                                    <span class="active">{{ $page }}</span>
                                @else
                                    <a href="{{ $requests->url($page) }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                                @endif
                            @endfor
                        </nav>
                    @endif
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

    <div class="delete-modal" id="delete-modal" hidden>
        <section class="delete-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-dialog-title" aria-describedby="delete-dialog-description">
            <div class="delete-dialog-icon" aria-hidden="true">!</div>
            <h2 id="delete-dialog-title">Delete maintenance request?</h2>
            <p id="delete-dialog-description">Are you sure you want to permanently delete <span class="delete-dialog-reference" id="delete-request-reference"></span>? This action cannot be undone.</p>
            <div class="delete-dialog-actions">
                <button type="button" id="delete-cancel">Cancel</button>
                <button type="button" class="delete-confirm" id="delete-confirm">Yes, delete</button>
            </div>
        </section>
    </div>

    <div class="request-delete-loading" id="request-delete-loading" role="status" aria-live="polite" aria-busy="true" hidden>
        <div class="request-delete-loading-card">
            <div class="request-delete-spinner" aria-hidden="true"></div>
            <p>Deleting request...</p>
        </div>
    </div>

    @if (session('request_deleted'))
        <div class="delete-modal" id="delete-success-modal">
            <section class="delete-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-success-title" aria-describedby="delete-success-description">
                <div class="delete-dialog-icon is-success" aria-hidden="true">✓</div>
                <h2 id="delete-success-title">Request deleted</h2>
                <p id="delete-success-description">Request <span class="delete-dialog-reference">{{ session('request_deleted') }}</span> was successfully deleted.</p>
                <div class="delete-dialog-actions">
                    <button type="button" class="delete-confirm is-notice" id="delete-success-close">Done</button>
                </div>
            </section>
        </div>
    @endif

    <script>
        const searchForm = document.getElementById('live-search-form');
        const searchInput = document.getElementById('live-search');
        let searchDelay;
        let activeRequest;
        const deleteModal = document.getElementById('delete-modal');
        const deleteReference = document.getElementById('delete-request-reference');
        const deleteCancel = document.getElementById('delete-cancel');
        const deleteConfirm = document.getElementById('delete-confirm');
        const deleteLoading = document.getElementById('request-delete-loading');
        let pendingDeleteForm = null;
        const deleteSuccessModal = document.getElementById('delete-success-modal');

        if (deleteSuccessModal) {
            const closeSuccessModal = () => { deleteSuccessModal.hidden = true; };
            document.getElementById('delete-success-close').addEventListener('click', closeSuccessModal);
            deleteSuccessModal.addEventListener('click', (event) => {
                if (event.target === deleteSuccessModal) closeSuccessModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeSuccessModal();
            });
            document.getElementById('delete-success-close').focus();
        }

        const closeDeleteModal = () => {
            deleteModal.hidden = true;
            pendingDeleteForm = null;
        };

        document.addEventListener('click', (event) => {
            const deleteButton = event.target.closest('[data-delete-request]');

            if (deleteButton) {
                const isResolved = deleteButton.dataset.requestResolved === 'true';
                pendingDeleteForm = isResolved ? deleteButton.closest('form') : null;
                deleteReference.textContent = deleteButton.dataset.deleteRequest;
                document.getElementById('delete-dialog-title').textContent = isResolved
                    ? 'Delete maintenance request?'
                    : 'Request not resolved';
                document.getElementById('delete-dialog-description').textContent = isResolved
                    ? `Are you sure you want to permanently delete request ${deleteButton.dataset.deleteRequest}? This action cannot be undone.`
                    : `Request ${deleteButton.dataset.deleteRequest} cannot be deleted until it is resolved.`;
                deleteCancel.hidden = !isResolved;
                deleteConfirm.textContent = isResolved ? 'Yes, delete' : 'Okay';
                deleteConfirm.classList.toggle('is-notice', !isResolved);
                deleteModal.hidden = false;
                (isResolved ? deleteCancel : deleteConfirm).focus();
                return;
            }

            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });

        deleteCancel.addEventListener('click', closeDeleteModal);
        deleteConfirm.addEventListener('click', () => {
            if (pendingDeleteForm) {
                deleteConfirm.disabled = true;
                deleteCancel.disabled = true;
                deleteModal.hidden = true;
                deleteLoading.hidden = false;
                pendingDeleteForm.requestSubmit();
            } else {
                closeDeleteModal();
            }
        });
        document.addEventListener('campuscare:ajax-form-error', (event) => {
            if (!event.detail?.form?.hasAttribute('data-request-delete-form')) return;

            deleteLoading.hidden = true;
            deleteModal.hidden = false;
            deleteConfirm.disabled = false;
            deleteCancel.disabled = false;
            document.getElementById('delete-dialog-description').textContent = 'The request could not be deleted. Please try again.';
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !deleteModal.hidden) {
                closeDeleteModal();
            }
        });

        const updateRequestTable = async (pageUrl = null) => {
            const currentPanel = document.getElementById('requests-panel');
            const filter = document.getElementById('status-filter');
            const url = pageUrl ? new URL(pageUrl, window.location.origin) : new URL(window.location.href);

            if (!pageUrl) {
                url.searchParams.set('search', searchInput.value.trim());
                url.searchParams.set('status', filter.value);
                url.searchParams.delete('page');
            }

            if (activeRequest) {
                activeRequest.abort();
            }

            activeRequest = new AbortController();
            currentPanel.classList.add('is-updating');

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: activeRequest.signal,
                });

                if (!response.ok) {
                    throw new Error('Unable to load requests');
                }

                const documentResponse = new DOMParser().parseFromString(await response.text(), 'text/html');
                const updatedPanel = documentResponse.getElementById('requests-panel');

                if (updatedPanel) {
                    currentPanel.innerHTML = updatedPanel.innerHTML;
                    window.history.replaceState({}, '', url);
                    requestAnimationFrame(() => currentPanel.classList.remove('is-updating'));
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    currentPanel.classList.remove('is-updating');
                }
            }
        };

        searchForm.addEventListener('submit', (event) => event.preventDefault());

        searchInput.addEventListener('input', () => {
            clearTimeout(searchDelay);
            searchDelay = setTimeout(updateRequestTable, 180);
        });

        document.addEventListener('change', (event) => {
            if (event.target.id === 'status-filter') {
                updateRequestTable();
            }
        });

        document.addEventListener('click', (event) => {
            const pageLink = event.target.closest('#requests-panel .request-pagination a');

            if (pageLink) {
                event.preventDefault();
                updateRequestTable(pageLink.href);
            }
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
