<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwsSystemLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'timestamp',
        'process_id',
        'thread_id',
        'parent_process_id',
        'user_id',
        'mount_namespace',
        'process_name',
        'host_name',
        'event_id',
        'event_name',
        'stack_address',
        'args_num',
        'return_value',
        'args',
        'hash',
        'outlier_anomaly_score',
        'anomaly_detected',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
