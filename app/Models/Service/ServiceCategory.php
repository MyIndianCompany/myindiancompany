<?php

namespace App\Models\Service;

use App\Models\Agent\Agent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ServiceCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function boot() {
        parent::boot();
        static::creating(function($model)
        {
            $user = Auth::user();
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
        });
        static::updating(function($model)
        {
            $user = Auth::user();
            $model->updated_by = $user->id;
            $model->updated_at = Carbon::now();
        });
        static::deleting(function($model)
        {
            $user = Auth::user();
            $model->deleted_by = $user->id;
            $model->save();
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_category_id',
        'name',
        'description',
        'slug',
        'remark',
        'type',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'status',
        'deleted_by',
        'created_by',
        'updated_by',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function files()
    {
        return $this->hasMany(ServiceCategoryFile::class, 'category_id');
    }
}
