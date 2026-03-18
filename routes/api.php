<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SettingsController;
use App\Models\CommunityEvent;
use App\Models\CommunityNotification;
use App\Models\CommunityPost;
use App\Models\JobListing;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name', 'Khdev Community API'),
        'status' => 'ok',
        'version' => 1,
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::post('/register', [AuthTokenController::class, 'register']);
Route::post('/login', [AuthTokenController::class, 'login']);

Route::get('/home', function () {
    return [
        'stats' => [
            'developers' => User::count(),
            'projects' => Project::count(),
            'events' => CommunityEvent::count(),
            'posts' => CommunityPost::count(),
            'jobs' => JobListing::where('status', 'active')->count(),
        ],
        'featuredPost' => CommunityPost::with('user')->latest('published_at')->first(),
        'featuredProjects' => Project::with('user')->orderByDesc('stars_count')->take(3)->get(),
        'featuredEvents' => CommunityEvent::with('host')->orderBy('starts_at')->take(3)->get(),
        'featuredJobs' => JobListing::with('user')->where('status', 'active')->latest('published_at')->take(3)->get(),
        'developers' => User::query()->withCount(['posts', 'projects', 'hostedEvents', 'followers'])->orderByDesc('followers_count')->take(4)->get(),
    ];
});

Route::get('/feed', [PostController::class, 'index']);
Route::get('/feed/{post}', [PostController::class, 'show']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job:slug}', [JobController::class, 'show']);
Route::get('/developers', [ProfileController::class, 'index']);
Route::get('/developers/{username}', [ProfileController::class, 'show']);
Route::get('/search', [SearchController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthTokenController::class, 'me']);
    Route::post('/logout', [AuthTokenController::class, 'logout']);
    Route::put('/me/profile', [ProfileController::class, 'update']);
    Route::post('/me/avatar', [ProfileController::class, 'updateAvatar']);
    Route::get('/me/settings', [SettingsController::class, 'show']);
    Route::put('/me/settings', [SettingsController::class, 'update']);
    Route::get('/me/bookmarks', [BookmarkController::class, 'index']);

    Route::post('/feed', [PostController::class, 'store']);
    Route::put('/feed/{post}', [PostController::class, 'update']);
    Route::delete('/feed/{post}', [PostController::class, 'destroy']);
    Route::post('/feed/{post}/comments', [PostController::class, 'comment']);
    Route::post('/feed/{post}/like', [PostController::class, 'toggleLike']);
    Route::post('/feed/{post}/bookmark', [BookmarkController::class, 'togglePost']);
    Route::post('/feed/{post}/report', [PostController::class, 'report']);

    Route::post('/projects', [ProjectController::class, 'store']);
    Route::post('/projects/{project}/bookmark', [BookmarkController::class, 'toggleProject']);

    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
    Route::post('/events/{event}/respond', [EventController::class, 'respond']);
    Route::post('/events/{event}/comments', [EventController::class, 'comment']);
    Route::post('/events/{event}/bookmark', [BookmarkController::class, 'toggleEvent']);
    Route::post('/events/{event}/report', [EventController::class, 'report']);

    Route::post('/jobs', [JobController::class, 'store']);
    Route::get('/me/jobs', [JobController::class, 'mine']);
    Route::get('/me/job-applications', [JobController::class, 'applied']);
    Route::put('/jobs/{job}', [JobController::class, 'update']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
    Route::post('/jobs/{job}/apply', [JobController::class, 'apply']);
    Route::get('/jobs/{job}/applicants', [JobController::class, 'applicants']);
    Route::post('/jobs/{job}/bookmark', [BookmarkController::class, 'toggleJob']);
    Route::post('/jobs/{job}/report', [JobController::class, 'report']);

    Route::post('/users/{user}/follow', [FollowController::class, 'store']);
    Route::delete('/users/{user}/follow', [FollowController::class, 'destroy']);
    Route::post('/reports', [ReportController::class, 'store']);

    Route::get('/notifications', fn (Request $request) => CommunityNotification::where('user_id', $request->user()->id)->latest('sent_at')->get());
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/unread-count', [ConversationController::class, 'unreadCount']);
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'send']);
    Route::post('/conversations/{conversation}/read', [ConversationController::class, 'markRead']);
    Route::get('/messages', [ConversationController::class, 'index']);
});

Route::prefix('v1')->group(function () {
    Route::get('/feed', [PostController::class, 'index']);
    Route::get('/feed/{post}', [PostController::class, 'show']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{job:slug}', [JobController::class, 'show']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/feed', [PostController::class, 'store']);
        Route::put('/feed/{post}', [PostController::class, 'update']);
        Route::delete('/feed/{post}', [PostController::class, 'destroy']);
        Route::post('/feed/{post}/comments', [PostController::class, 'comment']);
        Route::post('/feed/{post}/like', [PostController::class, 'toggleLike']);
        Route::post('/feed/{post}/bookmark', [BookmarkController::class, 'togglePost']);
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
        Route::post('/events/{event}/respond', [EventController::class, 'respond']);
        Route::post('/jobs', [JobController::class, 'store']);
        Route::get('/me/jobs', [JobController::class, 'mine']);
        Route::get('/me/job-applications', [JobController::class, 'applied']);
        Route::put('/jobs/{job}', [JobController::class, 'update']);
        Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
        Route::post('/jobs/{job}/apply', [JobController::class, 'apply']);
        Route::get('/jobs/{job}/applicants', [JobController::class, 'applicants']);
        Route::post('/jobs/{job}/bookmark', [BookmarkController::class, 'toggleJob']);
        Route::get('/conversations', [ConversationController::class, 'index']);
        Route::post('/conversations', [ConversationController::class, 'store']);
        Route::get('/conversations/unread-count', [ConversationController::class, 'unreadCount']);
        Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
        Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'send']);
        Route::post('/conversations/{conversation}/read', [ConversationController::class, 'markRead']);
    });
});
