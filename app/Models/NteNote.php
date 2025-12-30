<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NteNote extends Model
{
    use HasFactory;

    protected $table = 'nte_notes';
    protected $guarded = [];

    // Cast date_served to Carbon instance
    protected $casts = [
        'date_served' => 'datetime',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(NteNote::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(NteNote::class, 'parent_id', 'id');
    }
}
