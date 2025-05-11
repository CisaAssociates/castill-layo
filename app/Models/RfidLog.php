<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidLog extends Model
{
    protected $table = 'rfid_logs';
    protected $fillable = ['rfid', 'photo_path', 'byte_block_data'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    use HasFactory;
}
