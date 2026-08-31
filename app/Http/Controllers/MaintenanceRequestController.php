<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaintenanceRequestController extends Controller
{
    public function create()
    {
        return view('requests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room' => ['required', 'string', 'max:100'],
            'building' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1500'],
            'photo' => ['required', 'image', 'max:5120'],
        ]);

        $photoPath = $request->file('photo')->store('maintenance-photos', 'public');
        $reference = 'CC-'.now()->format('ymd').'-'.strtoupper(Str::random(4));

        MaintenanceRequest::create([
            ...$data,
            'reference' => $reference,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('request.create')->with('success', $reference);
    }
}
