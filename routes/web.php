<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Chat\ChatController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
Route::get('chat', [ChatController::class, 'index'])->name('chat.index');

Route::get('chat/open/{user}', [ChatController::class, 'openChat'])->name('chat.open');

Route::post('chat/store-message', [ChatController::class, 'storeMessage'])->name('chat.message.store');
});
// web.php
Route::get('/fire', function () {
    event(new \App\Events\MyEvent('hello from Laravel'));
    return view('index');
});

// Route::post('/chat/mark-as-read/{conversation}', [ChatController::class, 'markAsRead']);
Route::post('/messages/{message}/mark-read', [ChatController::class, 'markAsRead'])->name('chat.mark-as-read');


Route::get('/test-upload', function () {
    return view('test');
});

Route::post('/test-upload', function (\Illuminate\Http\Request $request) {
    $message = \App\Models\Message::create([
        'conversation_id' => 1,
        'sender_id' => 1,
        'message' => 'تجربة',
        'is_read' => false,
    ]);
    
    if ($request->hasFile('file')) {
$message->addMedia($request->file->getRealPath())
    ->usingName($request->file->getClientOriginalName())  // الاسم الأصلي (بدون الامتداد)
    ->usingFileName($request->file->getClientOriginalName()) // الاسم الأصلي بالامتداد
    ->toMediaCollection('attachments');

    }

    return 'done';
})->name('messages.store');
