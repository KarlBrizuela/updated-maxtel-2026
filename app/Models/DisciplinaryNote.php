<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryNote extends Model
{
    use HasFactory;

    protected $table = 'disciplinary_notes';

    protected $fillable = [
        'employee_id',
        'case_details',
        'remarks',
        'date_served',
        'sanction',
        'attachment_path',
        'parent_id'
    ];

    protected $casts = [
        'date_served' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Get all replies/children to this disciplinary note
    public function replies()
    {
        return $this->hasMany(DisciplinaryNote::class, 'parent_id', 'id');
    }

    // Get the parent disciplinary note if this is a reply
    public function parent()
    {
        return $this->belongsTo(DisciplinaryNote::class, 'parent_id', 'id');
    }
}
