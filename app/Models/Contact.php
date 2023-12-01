<?php

namespace App\Models;

use App\Models\Agent\Agent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'address',
        'city',
        'district',
        'landmark',
        'state',
        'zip_code',
        'country',
        'phone',
        'alt_phone',
        'email',
        'is_primary'
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


    public function agents()
    {
        return $this->belongsToMany(Agent::class);
    }
}
