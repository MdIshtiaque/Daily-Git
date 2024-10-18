<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\GitHubController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [GitHubController::class, 'getActivities']);
Route::get('/github/user', [GitHubController::class, 'getUser']);
Route::get('/github/activities', [GitHubController::class, 'getActivities']);
Route::get('/github/commit/{owner}/{repo}/{commitSha}', [GitHubController::class, 'getCommitDiff']);
