<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePersonnel extends Model
{
    protected $table = 'maintenance_personnel';

    protected $fillable = [
        'name',
        'email',
        'expertise',
        'availability',
        'employment_status',
    ];

    public function assignedRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'maintenance_personnel_id');
    }
}
