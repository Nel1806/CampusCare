<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'room', 'building', 'description', 'photo_path', 'status', 'assigned_to', 'maintenance_personnel_id',
    ];

    public function assignee()
    {
        return $this->belongsTo(MaintenancePersonnel::class, 'maintenance_personnel_id');
    }
}
