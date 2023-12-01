<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentDocument extends Model
{
    use HasFactory;

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(AgentBankAccount::class);
    }
}
