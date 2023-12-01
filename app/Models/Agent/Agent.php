<?php

namespace App\Models\Agent;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'job_title',
        'gender',
        'aadhaar_number',
        'pan_number',
        'photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_changed_at',
        'email_verified_at',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(AgentBankAccount::class);
    }

    public function documents()
    {
        return $this->hasMany(AgentDocument::class);
    }
}
