<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 * @method static whereHas(string $string, \Closure $param)
 */
class Comment extends Model
{
    use HasFactory;

    public function task(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    protected $fillable = ['content', 'user_id', 'task_id'];
}
