<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitHubActivity extends Model
{
    use HasFactory;
    protected $table = 'github_activities';
    protected $fillable = ['event_type', 'repo_name', 'payload', 'github_created_at'];

    protected $casts = [
        'payload' => 'array',
        'github_created_at' => 'datetime',
    ];
}
