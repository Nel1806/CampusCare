<?php

namespace App\Http\Controllers;

use App\Mail\MaintenanceRequestReceived;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MaintenanceRequestController extends Controller
{
    public function create()
    {
        $trackedRequest = session('tracked_request_id')
            ? MaintenanceRequest::query()->select(['reference', 'building', 'room', 'status'])->find(session('tracked_request_id'))
            : null;

        return view('requests.create', [
            'trackedRequest' => $trackedRequest,
            'trackingSearched' => session('tracking_searched', false),
        ]);
    }

    public function track(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:30'],
        ]);

        $trackedRequest = MaintenanceRequest::query()
            ->select(['id', 'reference', 'building', 'room', 'status', 'created_at', 'updated_at'])
            ->where('reference', strtoupper(trim($data['reference'])))
            ->first();

        return redirect()->route('request.create')->with([
            'tracking_searched' => true,
            'tracked_request_id' => $trackedRequest?->id,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'requester_name' => ['required', 'string', 'max:255'],
            'requester_email' => ['required', 'email', 'max:255'],
            'room' => ['required', 'string', 'max:100'],
            'building' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1500'],
            'photo' => ['required', 'image', 'max:5120'],
        ]);

        $photoPath = $request->file('photo')->store('maintenance-photos', 'public');
        do {
            $reference = 'CC-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
        } while (MaintenanceRequest::where('reference', $reference)->exists());

        $maintenanceRequest = MaintenanceRequest::create([
            'requester_name' => $data['requester_name'],
            'requester_email' => $data['requester_email'],
            'room' => $data['room'],
            'building' => $data['building'],
            'description' => $data['description'],
            'reference' => $reference,
            'photo_path' => $photoPath,
        ]);

        try {
            Mail::to($maintenanceRequest->requester_email)->send(new MaintenanceRequestReceived($maintenanceRequest));
            $maintenanceRequest->updateQuietly(['confirmation_email_sent_at' => now()]);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return redirect()->route('request.create')->with('success', $reference);
    }
}
