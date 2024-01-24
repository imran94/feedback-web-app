<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackPost(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }
}
