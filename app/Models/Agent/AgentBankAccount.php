<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentBankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agent_id',
        'account_holder_name',
        'bank_name',
        'account_number',
        'type',
        'ifsc',
        'swift_code'
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

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function documents()
    {
        return $this->hasMany(AgentDocument::class);
    }
}
