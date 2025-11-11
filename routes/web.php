<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ChatAdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VideoStreamController;
use Illuminate\Support\Facades\Storage;



Route::get('/clear',function (){
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    return 'Done';
});
Route::get('/set-language/{locale}', [LanguageController::class, 'setLanguage'])->name('set.language');

Route::post('/initiate-konnect-payment', [PaymentController::class, 'initiatePayment'])->name('konnect.initiate');
Route::get('/konnect-webhook', [PaymentController::class, 'handleWebhook'])->name('konnect.webhook');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');

Route::get('/payment/cancelled', function () {
    return 'payment.cancelled'; 
})->name('payment.cancelled');




Route::get('/', function () {
    return view('welcome');
});
Route::get('/landing', function () {
    return view('landing.index');
});


// Login routes with redirect.authenticated middleware
Route::middleware('redirect.authenticated')->group(function () {
    // Route::get('/inscription', function(){
    //     return view('auth.signup');
    // })->name('signup');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login_submit');
    Route::get('/login_admin', [AuthController::class, 'showLoginForm'])->name('login_admin');
    Route::post('/login_admin', [AuthController::class, 'login'])->name('login_admin_submit');
    Route::get('/client/login', [AuthController::class, 'showLoginFormClient'])->name('loginClient');
    Route::get('/verify-email', [AuthController::class, 'showLoginFormVerifyEmailClient'])->name('loginVerifyEmailClient');
    Route::get('/forgot-password', [AuthController::class, 'showLoginFormForgotPasswordClient'])->name('loginForgotPasswordClient');
    Route::get('/reset-password', [AuthController::class, 'showLoginFormResetPasswordClient'])->name('loginResetPasswordClient');

    // Public routes
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
});


Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout_get');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::delete('/chats/{chatId}', function ($chatId) {
        \App\Models\Chat::findOrFail($chatId)->delete();
        return response()->json(['message' => 'Chat deleted']);
    })->name('delete-chat');

    Route::name('v1.admin.')->prefix('v1/chat')->group(function() {
        Route::get('/', [ChatAdminController::class, 'index'])->name('chat');
        Route::get('', [ChatAdminController::class, 'index'])->name('index');
        Route::get('/{chatId}/messages', [ChatAdminController::class, 'getMessages'])->name('messages');
        Route::post('/send', [ChatAdminController::class, 'sendMessage'])->name('chat.send');
        Route::post('/message/{messageId}/read', [ChatAdminController::class, 'markAsRead'])->name('chat.read');
        Route::post('/upload', [ChatAdminController::class, 'uploadFile'])->name('chat.upload');
    });
    

    Route::get('/users', [ClientController::class, 'users'])->name('users');
    Route::get('/user/{id}/edit', [GlobalController::class, 'editUser'])->name('update_profil_user');
    Route::post('/user/{id}/update', [GlobalController::class, 'storeEditUser'])->name('update_store_profil_user');
    Route::post('/users/create', [GlobalController::class, 'createAgent'])->name('create_agent');
    Route::post('/users/{id}', [GlobalController::class, 'destroyUser'])->name('delete_profil_user');

    Route::get('/dashboard', [GlobalController::class, 'index'])->name('admin.index');
  
    Route::get('/get-chat-count', [GlobalController::class, 'getChatCount']);
    Route::get('/cours', [GlobalController::class, 'cours'])->name('admin.cours');
    Route::get('/ajout_cour', [GlobalController::class, 'ajout_cour'])->name('admin.ajout_cour');
    Route::get('/chapitres', [GlobalController::class, 'chapitres'])->name('admin.chapitres');
    Route::post('/chapitres/{id}/toggle-status', [GlobalController::class, 'toggleStatus']);

    Route::get('/users', [GlobalController::class, 'users'])->name('admin.users');
    Route::post('/ajout_cour', [GlobalController::class, 'ajout_cour']);
    Route::post('/ajout_chapitre', [GlobalController::class, 'ajout_chapitre'])->name('admin.addNewChapitre');   
    Route::post('/details_chapitre/{id}', [GlobalController::class, 'details_chapitre'])->name('details_chapitre');
    Route::post('/save-chapter', [GlobalController::class, 'saveChapter'])->name('save.chapter');
    Route::post('/save-course', [GlobalController::class, 'saveCourse']);
    Route::get('/get-course/{id}', [GlobalController::class, 'getCourse']);
    Route::get('/get-chapters/{courseId}', [GlobalController::class, 'getChapters']);
    Route::get('/get-certifica-test/{courseId}', [GlobalController::class, 'getCertificaTest']);
    Route::get('/get-course-details/{courseId}', [GlobalController::class, 'getCourseDetails']);
   // client / Apprenants
    //Route::get('/clients', [GlobalController::class, 'clients'])->name('admin.clients');

    Route::get('/clients', [GlobalController::class, 'getClients'])->name('admin.clients');
    
   // Route::get('/chats', [GlobalController::class, 'chatIndex'])->name('chats.index');

    Route::post('/ajout-Lecon', [GlobalController::class, 'ajout_lesson']);
    Route::post('/delete-course/{id}',[GlobalController::class,'delete_cour'])->name('admin.delete_cour');
    Route::delete('/delete_chapitre/{id}',[GlobalController::class,'delete_chapitre'])->name('admin.delete_chapitre');
    Route::delete('/delete_lesson_projet/{id}',[GlobalController::class,'delete_lesson_projet'])->name('admin.delete_lesson_projet');

    Route::get('/chats', [AdminChatController::class, 'index'])->name('admin.chats.index');
    Route::get('/chats/{chat}', [AdminChatController::class, 'show'])->name('admin.chats.show');
    Route::post('/chats/{chat}/send', [AdminChatController::class, 'sendMessage'])->name('admin.chats.send');
    Route::get('/chats/{chat}/poll', [AdminChatController::class, 'pollMessages'])->name('admin.chats.poll');
    Route::post('/chats/{chat}/read', [AdminChatController::class, 'markAsRead'])->name('admin.chats.read');
    Route::post('/lessons/reorder', [GlobalController::class, 'reorder_lessons']);
    Route::get('/get-lesson-details/{id}', [GlobalController::class, 'getLessonDetails'])->name('admin.lesson.details');


});
    Route::post('/chapitres/reorder', [GlobalController::class, 'reorder_chapitres']);
// Agent routes
Route::middleware(['auth', 'role:agent'])->prefix('agent')->group(function () {
    Route::get('/cours', [GlobalController::class, 'cours'])->name('agent.cours');
    Route::get('/ajout_cour', [GlobalController::class, 'ajout_cour'])->name('agent.ajout_cour');
    Route::get('/chapitres', [GlobalController::class, 'chapitres'])->name('agent.chapitres');
    Route::get('/users', [GlobalController::class, 'users'])->name('agent.users');
    Route::get('/clients', [GlobalController::class, 'getClients'])->name('agent.clients');
    Route::post('/lessons/reorder', [GlobalController::class, 'reorder_lessons']);


    Route::get('/chats', [AdminChatController::class, 'index'])->name('agent.chats.index');
    Route::get('/chats/{chat}', [AdminChatController::class, 'show'])->name('agent.chats.show');
    Route::post('/chats/{chat}/send', [AdminChatController::class, 'sendMessage'])->name('agent.chats.send');
    Route::get('/chats/{chat}/poll', [AdminChatController::class, 'pollMessages'])->name('agent.chats.poll');
    Route::post('/chats/{chat}/read', [AdminChatController::class, 'markAsRead'])->name('agent.chats.read');
});

// Client routes
    Route::get('/cours', [ClientController::class, 'cours'])->name('cours');
    Route::get('/formation/payment/{id}', [ClientController::class, 'courPay'])->name('courPay');
    Route::post('/payment/confirm-manual', [PaymentController::class, 'handleManualPayment'])->name('payment.confirm_manual');

Route::middleware(['auth', 'role:client|admin|agent'])->group(function () {
    Route::get('/cours', [ClientController::class, 'cours'])->name('cours');
    Route::get('/chapitres', [ClientController::class, 'chapitres'])->name('chapitres');
  
    Route::get('/clients', [ClientController::class, 'clients'])->name('clients');

    Route::get('/formation/cour/{id}', [ClientController::class, 'courById'])->name('courById');


    Route::get('/formation/chapitre/{id}', [ClientController::class, 'chapitreById'])->name('chapitreById');

    Route::get('/chat', [ChatController::class, 'getChat'])->name('chat.get');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/poll', [ChatController::class, 'pollMessages'])->name('chat.poll');
    Route::post('/chat/read', [ChatController::class, 'markAsRead'])->name('chat.read');

    Route::get('/ably/token', [ChatController::class, 'getAblyToken'])->name('ably.token');
    Route::get('/chat', [ChatController::class, 'getChat'])->name('chat.get');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::post('/client/profil/update', [GlobalController::class, 'storeEditClient'])->name('update_store_profil_client');

    Route::post('/client/profil/update-number', [GlobalController::class, 'storeEditClientNumber'])->name('update_store_profil_client_phone');

    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::get('/notifications/check-new', [NotificationController::class, 'checkNewNotifications']);
    
    Route::get('/videos/hls-key/{basename}', function ($basename) {
        $keyPath = 'hls/' . $basename . '/secret.key';
        if (!Storage::disk('local')->exists($keyPath)) {
            abort(404, 'Clé non trouvée.');
        }
        $keyContent = Storage::disk('local')->get($keyPath);
        return response($keyContent)->header('Content-Type', 'application/octet-stream');

    })->name('video.hls.key');
    
    Route::get('/videos/lessons_video/{filename}', [VideoStreamController::class, 'stream'])->name('video.stream')->where('filename', '.*');
});

Route::post('/newMessage', [ClientController::class, 'newMessage'])->name('newMessage');
Route::post('/upload', [ClientController::class, 'upload'])->name('upload');

Route::post('/uploadFile', [ClientController::class, 'uploadFile'])->name('uploadFile');
Route::post('/updateLessonId', [ClientController::class, 'updateLessonId']);

 
Route::post('/formation/chapitre/{chapitre_id}', [ClientController::class, 'chapitreById'])->name('formation.chapitre.submit');
Route::prefix('api/file-upload')->group(function () {
    
    // Upload multiple files
    Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');
    
    // Delete a specific file
    Route::delete('/delete', [FileUploadController::class, 'delete'])->name('file.delete');
    
    // Get upload progress
    Route::get('/progress', [FileUploadController::class, 'getProgress'])->name('file.progress');
    
    // Validate file before upload
    Route::post('/validate', [FileUploadController::class, 'validateFile'])->name('file.validate');
});

// Alternative routes if you prefer RESTful approach
Route::prefix('api')->group(function () {
    Route::resource('files', FileUploadController::class)->only(['store', 'destroy']);
    Route::post('files/validate', [FileUploadController::class, 'validateFile']);
    Route::get('files/progress/{uploadId}', [FileUploadController::class, 'getProgress']);
});