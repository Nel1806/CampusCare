<?php

namespace App\Http\Controllers;

use App\Mail\MaintenanceRequestResolved;
use App\Models\MaintenancePersonnel;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:new,assigned,in_progress,resolved'],
        ]);
        $query = MaintenanceRequest::query()
            ->select(['id', 'reference', 'room', 'building', 'description', 'photo_path', 'status', 'maintenance_personnel_id', 'created_at'])
            ->with('assignee:id,name')
            ->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('reference', 'like', "%{$search}%")->orWhere('room', 'like', "%{$search}%")->orWhere('building', 'like', "%{$search}%"));
        }

        return view('admin.dashboard', [
            'requests' => $query->paginate(10)->withQueryString(),
            'counts' => MaintenanceRequest::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'recentActivity' => MaintenanceRequest::query()->select(['id', 'description', 'status', 'updated_at'])->latest('updated_at')->take(4)->get(),
            'personnel' => MaintenancePersonnel::query()->select(['id', 'name', 'expertise', 'availability'])->withCount('assignedRequests')->orderBy('name')->take(5)->get(),
        ]);
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        return view('admin.show', ['requestItem' => $maintenanceRequest->load('assignee'), 'personnel' => MaintenancePersonnel::where('employment_status', 'active')->orderBy('name')->get()]);
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $wasResolved = $maintenanceRequest->status === 'resolved';
        $data = $request->validate([
            'maintenance_personnel_id' => ['nullable', 'exists:maintenance_personnel,id'],
            'status' => ['required', 'in:new,assigned,in_progress,resolved'],
        ]);
        if (! empty($data['maintenance_personnel_id']) && $data['status'] === 'new') {
            $data['status'] = 'assigned';
        }
        $maintenanceRequest->update($data);

        if (! $wasResolved && $maintenanceRequest->status === 'resolved' && $maintenanceRequest->requester_email && ! $maintenanceRequest->resolved_email_sent_at) {
            try {
                Mail::to($maintenanceRequest->requester_email)->send(new MaintenanceRequestResolved($maintenanceRequest));
                $maintenanceRequest->updateQuietly(['resolved_email_sent_at' => now()]);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return back()->with('success', 'Request updated successfully.');
    }
}
