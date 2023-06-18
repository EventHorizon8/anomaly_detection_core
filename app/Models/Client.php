<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'name',
        'hostname',
        'type',
        'access_token',
        'last_communication_at',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_communication_at' => 'datetime',
    ];

    public function lastLog(): HasOne
    {
        return $this->hasOne(
                AwsSystemLog::class,
                'client_id',
                'id'
            )->orderBy('timestamp', 'desc');
    }

    public function systemLogs(): HasMany
    {
        return $this->hasMany(
            AwsSystemLog::class,
            'client_id',
            'id'
        )->orderBy('timestamp', 'desc');
    }

    public function clientStats(): HasMany
    {
        return $this->hasMany(
            ClientStats::class,
            'client_id',
            'id'
        )->orderBy('created_at', 'desc');
    }
}
