<?php

namespace App\Models\Agent;

use App\Models\Contact;
use App\Models\Service\ServiceCategory;
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
        'service_category_id',
        'gender',
        'aadhaar_number',
        'pan_number',
        'pan_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function AgentBankAccounts()
    {
        return $this->hasMany(AgentBankAccount::class, 'agent_id');
    }
}
