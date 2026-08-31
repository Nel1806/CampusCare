<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePersonnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index()
    {
        return view('admin.personnel.index', ['personnel' => MaintenancePersonnel::withCount('assignedRequests')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.personnel.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:maintenance_personnel,email'],
            'expertise' => ['required', 'in:Electrician,IT,Carpenter,Cleaner'],
            'employment_status' => ['required', 'in:active,inactive'],
        ]);
        $data['availability'] = 'available';
        MaintenancePersonnel::create($data);

        return redirect()->route('admin.personnel.index')->with('success', 'Maintenance personnel added successfully.');
    }

    public function edit(MaintenancePersonnel $personnel)
    {
        return view('admin.personnel.edit', compact('personnel'));
    }

    public function update(Request $request, MaintenancePersonnel $personnel)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:maintenance_personnel,email,'.$personnel->id],
            'expertise' => ['required', 'in:Electrician,IT,Carpenter,Cleaner'],
            'employment_status' => ['required', 'in:active,inactive'],
        ]);
        $personnel->update($data);

        return redirect()->route('admin.personnel.index')->with('success', "{$personnel->name}'s account has been updated.");
    }

    public function destroy(MaintenancePersonnel $personnel)
    {
        $name = $personnel->name;
        $personnel->delete();

        return back()->with('success', "{$name} has been removed from maintenance personnel.");
    }

    public function updateDuty(Request $request, MaintenancePersonnel $personnel)
    {
        $personnel->update([
            'availability' => $request->boolean('on_duty') ? 'available' : 'off_duty',
        ]);

        return back();
    }
}
