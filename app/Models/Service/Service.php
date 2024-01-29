<?php

namespace App\Models\Service;

use App\Models\CustomerEnquiry;
use App\Models\ServiceDetailFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Service extends Model
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
        'name',
        'service_code',
        'description',
        'price',
        'slug',
        'remark',
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
        'updated_at',
        'pivot'
    ];

    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class);
    }
    public function variants()
    {
        return $this->hasMany(ServiceVariant::class);
    }

    public function files()
    {
        return $this->hasMany(ServiceFile::class, 'service_id');
    }

    public function detailFiles()
    {
        return $this->hasMany(ServiceDetailFile::class, 'service_id');
    }

    public function CustomerEnquiries()
    {
        return $this->hasMany(CustomerEnquiry::class, 'service');
    }
}
