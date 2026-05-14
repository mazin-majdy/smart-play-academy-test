<?php

use App\Jobs\GenerateWeeklyReportJob;
use App\Models\Child;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // الأهل / المعلمين
    Route::get('/login',    [App\Http\Controllers\Auth\LoginController::class, 'showForm'])->name('login');
    Route::post('/login',   [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

    // الطفل — واجهة منفصلة
    Route::get('/child/login',  [App\Http\Controllers\Auth\ChildLoginController::class, 'showForm'])->name('child.login');
    Route::post('/child/login', [App\Http\Controllers\Auth\ChildLoginController::class, 'login']);
});

Route::post('/logout',       [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::post('/child/logout', [App\Http\Controllers\Auth\ChildLoginController::class, 'logout'])->name('child.logout');

// ── CHILD ZONE ────────────────────────────────────────────────────
Route::middleware('child.auth')->prefix('play')->name('child.')->group(function () {
    Route::get('/',                [App\Http\Controllers\Child\HomeController::class, 'index'])->name('home');
    Route::get('/subject/{subject}', [App\Http\Controllers\Child\GameController::class, 'subject'])->name('subject');
    Route::get('/game/{game}',     [App\Http\Controllers\Child\GameController::class, 'play'])->name('game');
    Route::post('/session/start',  [App\Http\Controllers\Child\GameController::class, 'startSession'])->name('session.start');
    Route::post('/session/{session}/answer', [App\Http\Controllers\Child\GameController::class, 'submitAnswer'])->name('session.answer');
    Route::post('/session/{session}/end', [App\Http\Controllers\Child\GameController::class, 'endSession'])->name('session.end');
    Route::get('/achievements',    [App\Http\Controllers\Child\HomeController::class, 'achievements'])->name('achievements');
    Route::post('/tutor/chat',     [App\Http\Controllers\Child\TutorController::class, 'chat'])->name('tutor.chat');
});

// ── PARENT / TEACHER DASHBOARD ────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/',                   [App\Http\Controllers\Dashboard\HomeController::class, 'index'])->name('home');
    Route::resource('children',        App\Http\Controllers\Dashboard\ChildrenController::class);
    Route::get('/reports/{child}',    [App\Http\Controllers\Dashboard\ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{child}/weekly', [App\Http\Controllers\Dashboard\ReportController::class, 'weekly'])->name('reports.weekly');
    Route::get('/notifications',      [App\Http\Controllers\Dashboard\NotificationController::class, 'index'])->name('notifications');
    Route::patch('/notifications/{n}/read', [App\Http\Controllers\Dashboard\NotificationController::class, 'markRead']);
    Route::get('/settings',           [App\Http\Controllers\Dashboard\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings',           [App\Http\Controllers\Dashboard\SettingsController::class, 'update']);
});

// ── ADMIN ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin|content_manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                   [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('home');
    Route::resource('subjects',        App\Http\Controllers\Admin\SubjectController::class);
    Route::resource('topics',          App\Http\Controllers\Admin\TopicController::class);
    Route::resource('games',           App\Http\Controllers\Admin\GameController::class);
    Route::resource('questions',       App\Http\Controllers\Admin\QuestionController::class);
    Route::get('/analytics',          [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/users',              [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
});
Route::get('/usersshow', function () {
    return "users show";
})->name('admin.users.show');

use App\Http\Controllers\PlaySessionController;

Route::middleware(['auth'])->prefix('play')->name('play.')->group(function () {
    Route::post('/child/{child}/game/{game}/start', [PlaySessionController::class, 'start'])->name('start');
    Route::post('/session/{session}/answer', [PlaySessionController::class, 'submitAnswer'])->name('answer');
    Route::post('/session/{session}/finish', [PlaySessionController::class, 'finish'])->name('finish');
});

// // Route لعرض صفحة اللعبة
// Route::get('/play/{child}/{game}', function ($childId, $gameId) {
//     $child = App\Models\Child::findOrFail($childId);
//     $game = App\Models\Game::findOrFail($gameId);

//     // تأكد إن الطفل تابع للمستخدم
//     abort_unless(auth()->user()->children->contains($child), 403);

//     return view('play.game', compact('child', 'game'));
// })->middleware('auth')->name('play.view');
