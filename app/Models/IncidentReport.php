<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reported_by',
        'position',
        'date_time_report',
        'incident_no',
        'incident_type',
        'date_incident',
        'location',
        'incident_description',
        'name_involved',
        'name_witness',
        'recommended_action',
    ];

    protected $casts = [
        'date_time_report' => 'datetime',
        'date_incident' => 'datetime',
    ];

    // Relationships
    public function reportedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'reported_by', 'id');
    }

    public function involvedEmployee()
    {
        return $this->belongsTo(Employee::class, 'name_involved', 'id');
    }

    public function witnessEmployee()
    {
        return $this->belongsTo(Employee::class, 'name_witness', 'id');
    }
}
