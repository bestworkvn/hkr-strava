<?php

use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostCommentsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StravaController;
use App\Http\Controllers\TracklogController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Services\StravaWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CourseController::class, 'index'])->name('home');

//Route::get('posts/{post:slug}', [PostController::class, 'show']);
//Route::post('posts/{post:slug}/comments', [PostCommentsController::class, 'store']);
//
//Route::post('newsletter', NewsletterController::class);

Route::get('register', [RegisterController::class, 'create'])->middleware('guest');
Route::post('register', [RegisterController::class, 'store'])->middleware('guest');

Route::get('login', [SessionsController::class, 'create'])->middleware('guest')->name('login');
Route::post('login', [SessionsController::class, 'store'])->middleware('guest');

Route::post('logout', [SessionsController::class, 'destroy'])->middleware('auth');
Route::get('profile', [SessionsController::class, 'profile'])->middleware('auth')->name('userprofile');
Route::get('profile/strava/auth', [StravaController::class, 'stravaAuth'])->middleware('auth');
Route::get('profile/strava/token', [StravaController::class, 'getToken'])->middleware('auth');
Route::get('user/activities', [UserController::class, 'getLatestActivities'])->middleware('auth');
Route::get('profile/activities', [TracklogController::class, 'index'])->middleware('auth');
Route::get('profile/activities/{tracklog:id}', [TracklogController::class, 'show'])->middleware('auth');

// Admin Section
Route::middleware('can:admin')->group(function () {
    Route::resource('admin/posts', AdminPostController::class)->except('show');

});

Route::get('/webhook', function (Request $request) {
    $mode = $request->query('hub_mode'); // hub.mode
    $token = $request->query('hub_verify_token'); // hub.verify_token
    $challenge = $request->query('hub_challenge'); // hub.challenge

    return app(StravaWebhookService::class)->validate($mode, $token, $challenge);
});

Route::post('/webhook', function (Request $request) {
    $aspect_type = $request['aspect_type']; // "create" | "update" | "delete"
    $event_time = $request['event_time']; // time the event occurred
    $object_id = $request['object_id']; // activity ID | athlete ID
    $object_type = $request['object_type']; // "activity" | "athlete"
    $owner_id = $request['owner_id']; // athlete ID
    $subscription_id = $request['subscription_id']; // push subscription ID receiving the event
    $updates = $request['updates']; // activity update: {"title" | "type" | "private": true/false} ; app deauthorization: {"authorized": false}

    Log::channel('strava')->info(json_encode($request->all()));

    return response('EVENT_RECEIVED', Response::HTTP_OK);
})->withoutMiddleware(VerifyCsrfToken::class);

