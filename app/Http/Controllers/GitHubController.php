<?php

namespace App\Http\Controllers;

use App\Models\GitHubActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class GitHubController extends Controller
{
    private $token;

    public function __construct()
    {
        $this->token = config('services.github.token');
    }

    public function getUser()
    {
        $response = Http::withToken($this->token)->get('https://api.github.com/user');
        return $response->json();
    }

    public function getActivities()
    {
        $response = Http::withToken($this->token)
            ->get('https://api.github.com/users/'.config('services.github.username').'/events');

        $activities = $response->json();

        foreach ($activities as $activity) {
            GitHubActivity::updateOrCreate(
                ['id' => $activity['id']],
                [
                    'event_type' => $activity['type'],
                    'repo_name' => $activity['repo']['name'],
                    'payload' => $activity['payload'],
                    'github_created_at' => $activity['created_at'],
                ]
            );
        }

        $activities = GitHubActivity::orderBy('github_created_at', 'desc')->get();

        return Inertia::render('GitHubActivities', [
            'activities' => $activities
        ]);
    }

    public function getCommitDiff($owner, $repo, $commitSha)
    {
        try {
            $url = "https://api.github.com/repos/{$owner}/{$repo}/commits/{$commitSha}";
            $response = Http::withToken($this->token)
                ->get($url);

            if ($response->failed()) {
                Log::error('GitHub API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'owner' => $owner,
                    'repo' => $repo,
                    'commitSha' => $commitSha,
                    'url' => $url,
                ]);
                return Inertia::render('CommitDiff', [
                    'error' => "Failed to fetch commit data from GitHub: {$response->status()} {$response->body()}",
                    'debug' => [
                        'owner' => $owner,
                        'repo' => $repo,
                        'commitSha' => $commitSha,
                        'url' => $url,
                    ]
                ]);
            }

            $commitData = $response->json();

            return Inertia::render('CommitDiff', [
                'commitData' => $commitData
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getCommitDiff', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'owner' => $owner,
                'repo' => $repo,
                'commitSha' => $commitSha,
            ]);
            return Inertia::render('CommitDiff', [
                'error' => 'An error occurred while processing the request: ' . $e->getMessage(),
                'debug' => [
                    'owner' => $owner,
                    'repo' => $repo,
                    'commitSha' => $commitSha,
                ]
            ]);
        }
    }
}
