<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    use HasFactory;

    public function province() {
        $this->belongsTo(Province::class);
    }
    public function region() {
        $this->BelongsTo(Region::class);
    }
}
