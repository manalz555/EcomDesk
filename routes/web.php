<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
})->name('home');

// Live chat widget — public routes (a website visitor has no account).
// Protected by rate limiting + the per-visitor token, not by auth.
Route::get('widget/frame', [WidgetController::class, 'frame'])->name('widget.frame');
Route::post('widget/messages', [WidgetController::class, 'send'])->middleware('throttle:30,1')->name('widget.send');
Route::get('widget/messages', [WidgetController::class, 'messages'])->middleware('throttle:120,1')->name('widget.messages');

Route::middleware(['auth', 'verified', 'actif'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    // Clients : consultation + creation ouvertes aux agents et admin ; modif/suppression protegees dans le controleur.
    Route::resource('clients', ClientController::class);
    Route::patch('clients/{client}/custom-fields', [ClientController::class, 'updateCustomFields'])->name('clients.custom-fields.update');

    // Conversations
    Route::resource('conversations', ConversationController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('conversations/{conversation}/assign', [ConversationController::class, 'assign'])->name('conversations.assign');
    Route::patch('conversations/{conversation}/statut', [ConversationController::class, 'updateStatut'])->name('conversations.statut');
    Route::patch('conversations/{conversation}/priorite', [ConversationController::class, 'updatePriorite'])->name('conversations.priorite');
    Route::post('conversations/{conversation}/reponses', [ConversationController::class, 'storeReponse'])->name('conversations.reponses.store');
    Route::post('conversations/{conversation}/drafts/{draft}/approve', [ConversationController::class, 'approveDraft'])->name('conversations.drafts.approve');
    Route::delete('conversations/{conversation}/drafts/{draft}', [ConversationController::class, 'discardDraft'])->name('conversations.drafts.discard');
    Route::post('conversations/{conversation}/tags', [ConversationController::class, 'storeTag'])->name('conversations.tags.store');
    Route::delete('conversations/{conversation}/tags/{tag}', [ConversationController::class, 'removeTag'])->name('conversations.tags.destroy');
    Route::post('conversations/{conversation}/notes', [ConversationController::class, 'storeNote'])->name('conversations.notes.store');
    Route::patch('conversations/{conversation}/team', [ConversationController::class, 'updateTeam'])->name('conversations.team');
    Route::patch('conversations/{conversation}/satisfaction', [ConversationController::class, 'updateSatisfaction'])->name('conversations.satisfaction');
    Route::get('conversations/{conversation}/attachments/{attachment}', [ConversationController::class, 'downloadAttachment'])->name('conversations.attachments.download');

    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Equipes : administrateurs et managers
    Route::middleware('manage-team')->group(function () {
        Route::resource('teams', TeamController::class)->except(['show']);
    });

    // Agents, entreprises et champs personnalises : reserves a l'administrateur
    Route::middleware('admin')->group(function () {
        Route::resource('agents', AgentController::class)->except(['show', 'destroy']);
        Route::resource('companies', CompanyController::class)->except(['show']);
        Route::resource('custom-fields', CustomFieldController::class)->only(['index', 'store', 'destroy']);
        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.index');
        Route::patch('settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('integrations', [IntegrationController::class, 'index'])->name('integrations.index');
        Route::patch('integrations/{integration}', [IntegrationController::class, 'updateChannel'])->name('integrations.update');
        Route::delete('integrations/{integration}', [IntegrationController::class, 'disconnectChannel'])->name('integrations.disconnect');
        Route::post('automation-rules', [IntegrationController::class, 'storeRule'])->name('automation-rules.store');
        Route::post('automation-rules/{rule}/toggle', [IntegrationController::class, 'toggleRule'])->name('automation-rules.toggle');
        Route::delete('automation-rules/{rule}', [IntegrationController::class, 'destroyRule'])->name('automation-rules.destroy');
    });
});

require __DIR__.'/auth.php';
