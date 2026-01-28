<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrainingModuleController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\SimulationEventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LessonCompletionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\EvaluationController;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Participant auth routes
Route::get('/participant/login', [AuthController::class, 'showParticipantLogin'])->name('participant.login');
Route::post('/participant/login', [AuthController::class, 'participantLogin'])->name('participant.login.post');

Route::get('/participant/register', [AuthController::class, 'showParticipantRegister'])->name('participant.register');
Route::post('/participant/register/start', [AuthController::class, 'participantRegisterStart'])->name('participant.register.start');

Route::get('/participant/register/verify', [AuthController::class, 'showParticipantRegisterVerify'])->name('participant.register.verify');
Route::post('/participant/register/verify', [AuthController::class, 'participantRegisterVerify'])->name('participant.register.verify.post');
Route::get('/participant/register/verify-email/{token}', [AuthController::class, 'participantRegisterVerifyEmail'])->name('participant.register.verify.email');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected app routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('app', ['section' => 'dashboard']);
    })->name('dashboard');

    Route::get('/training-modules', [TrainingModuleController::class, 'index'])
        ->name('training.modules');

    Route::get('/training-modules/create', [TrainingModuleController::class, 'create'])
        ->name('training.modules.create');

    Route::post('/training-modules', [TrainingModuleController::class, 'store'])
        ->name('training.modules.store');

    Route::get('/training-modules/{trainingModule}', [TrainingModuleController::class, 'show'])
        ->name('training.modules.show');

    // Lesson completion (participants)
    Route::post('/training-modules/{trainingModule}/lessons/{lesson}/completion', [LessonCompletionController::class, 'toggle'])
        ->name('training.lessons.completion.toggle');

    Route::post('/training-modules/{trainingModule}/lessons', [TrainingModuleController::class, 'storeLesson'])
        ->name('training.modules.lessons.store');

    Route::put('/training-modules/{trainingModule}/lessons/{lesson}', [TrainingModuleController::class, 'updateLesson'])
        ->name('training.modules.lessons.update');

    Route::delete('/training-modules/{trainingModule}/lessons/{lesson}', [TrainingModuleController::class, 'destroyLesson'])
        ->name('training.modules.lessons.destroy');

    Route::post('/training-modules/{trainingModule}/lessons/{lesson}/materials', [TrainingModuleController::class, 'storeMaterial'])
        ->name('training.modules.materials.store');

    Route::delete('/training-modules/{trainingModule}/lessons/{lesson}/materials/{material}', [TrainingModuleController::class, 'destroyMaterial'])
        ->name('training.modules.materials.destroy');

    Route::get('/training-modules/{trainingModule}/edit', [TrainingModuleController::class, 'edit'])
        ->name('training.modules.edit');

    Route::put('/training-modules/{trainingModule}', [TrainingModuleController::class, 'update'])
        ->name('training.modules.update');

    Route::post('/training-modules/{trainingModule}/archive', [TrainingModuleController::class, 'archive'])
        ->name('training.modules.archive');
    Route::post('/training-modules/{trainingModule}/publish', [TrainingModuleController::class, 'publish'])
        ->name('training.modules.publish');
    Route::delete('/training-modules/{trainingModule}', [TrainingModuleController::class, 'destroy'])
        ->name('training.modules.destroy');

    // Scenarios
    Route::get('/scenarios', [ScenarioController::class, 'index'])->name('scenarios.index');
    Route::get('/scenarios/create', [ScenarioController::class, 'create'])->name('scenarios.create');
    Route::post('/scenarios', [ScenarioController::class, 'store'])->name('scenarios.store');
    Route::post('/scenarios/generate-ai', [ScenarioController::class, 'generateAiScenario'])->name('scenarios.generate-ai');
    Route::get('/scenarios/{scenario}', [ScenarioController::class, 'show'])->name('scenarios.show');
    Route::get('/scenarios/{scenario}/edit', [ScenarioController::class, 'edit'])->name('scenarios.edit');
    Route::put('/scenarios/{scenario}', [ScenarioController::class, 'update'])->name('scenarios.update');
    Route::post('/scenarios/{scenario}/publish', [ScenarioController::class, 'publish'])->name('scenarios.publish');
    Route::post('/scenarios/{scenario}/archive', [ScenarioController::class, 'archive'])->name('scenarios.archive');
    Route::delete('/scenarios/{scenario}', [ScenarioController::class, 'destroy'])->name('scenarios.destroy');

    // Scenario Injects
    Route::post('/scenarios/{scenario}/injects', [ScenarioController::class, 'storeInject'])->name('scenarios.injects.store');
    Route::delete('/scenarios/{scenario}/injects/{inject}', [ScenarioController::class, 'destroyInject'])->name('scenarios.injects.destroy');

    // Scenario Expected Actions
    Route::post('/scenarios/{scenario}/expected-actions', [ScenarioController::class, 'storeExpectedAction'])->name('scenarios.expected-actions.store');
    Route::delete('/scenarios/{scenario}/expected-actions/{expectedAction}', [ScenarioController::class, 'destroyExpectedAction'])->name('scenarios.expected-actions.destroy');

    // Simulation Events
    Route::get('/simulation-events', [SimulationEventController::class, 'index'])->name('simulation.events.index');
    Route::get('/simulation-events/create', [SimulationEventController::class, 'create'])->name('simulation.events.create');
    Route::post('/simulation-events', [SimulationEventController::class, 'store'])->name('simulation.events.store');
    Route::get('/simulation-events/{simulationEvent}', [SimulationEventController::class, 'show'])->name('simulation.events.show');
    Route::get('/simulation-events/{simulationEvent}/edit', [SimulationEventController::class, 'edit'])->name('simulation.events.edit');
    Route::put('/simulation-events/{simulationEvent}', [SimulationEventController::class, 'update'])->name('simulation.events.update');
    Route::post('/simulation-events/{simulationEvent}/publish', [SimulationEventController::class, 'publish'])->name('simulation.events.publish');
    Route::post('/simulation-events/{simulationEvent}/unpublish', [SimulationEventController::class, 'unpublish'])->name('simulation.events.unpublish');
    Route::post('/simulation-events/{simulationEvent}/cancel', [SimulationEventController::class, 'cancel'])->name('simulation.events.cancel');
    Route::post('/simulation-events/{simulationEvent}/archive', [SimulationEventController::class, 'archive'])->name('simulation.events.archive');
    Route::post('/simulation-events/{simulationEvent}/start', [SimulationEventController::class, 'start'])->name('simulation.events.start');
    Route::post('/simulation-events/{simulationEvent}/complete', [SimulationEventController::class, 'complete'])->name('simulation.events.complete');
    Route::delete('/simulation-events/{simulationEvent}', [SimulationEventController::class, 'destroy'])->name('simulation.events.destroy');
    
    // Participant Event Registration
    Route::post('/simulation-events/{simulationEvent}/register', [SimulationEventController::class, 'register'])->name('simulation.events.register');
    Route::post('/simulation-events/{simulationEvent}/cancel-registration', [SimulationEventController::class, 'cancelRegistration'])->name('simulation.events.cancel.registration');

    // Participants
    Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('/participants/{user}', [ParticipantController::class, 'show'])->name('participants.show');
    Route::put('/participants/{user}', [ParticipantController::class, 'update'])->name('participants.update');
    Route::post('/participants/{user}/deactivate', [ParticipantController::class, 'deactivate'])->name('participants.deactivate');
    Route::post('/participants/{user}/reactivate', [ParticipantController::class, 'reactivate'])->name('participants.reactivate');
    Route::get('/participants/export/csv', [ParticipantController::class, 'export'])->name('participants.export');

    // Participant self-service attendance
    Route::get('/my-attendance', [ParticipantController::class, 'myAttendance'])->name('participants.self.attendance');

    // Event Registrations
    Route::get('/simulation-events/{simulationEvent}/registrations', [EventRegistrationController::class, 'index'])->name('simulation.events.registrations');
    Route::post('/event-registrations/{eventRegistration}/approve', [EventRegistrationController::class, 'approve'])->name('event.registrations.approve');
    Route::post('/event-registrations/{eventRegistration}/reject', [EventRegistrationController::class, 'reject'])->name('event.registrations.reject');
    Route::post('/event-registrations/{eventRegistration}/cancel', [EventRegistrationController::class, 'cancel'])->name('event.registrations.cancel');

    // Attendance
    Route::get('/simulation-events/{simulationEvent}/attendance', [AttendanceController::class, 'index'])->name('simulation.events.attendance');
    Route::post('/event-registrations/{eventRegistration}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::post('/simulation-events/{simulationEvent}/attendance/lock', [AttendanceController::class, 'lock'])->name('attendance.lock');
    Route::get('/simulation-events/{simulationEvent}/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::post('/simulation-events/{simulationEvent}/attendance/mark-present', [AttendanceController::class, 'markPresentByQR'])->name('attendance.mark.present');

    // Settings
    Route::get('/settings/auto-approval', [SettingController::class, 'getAutoApproval'])->name('settings.auto.approval.get');
    Route::post('/settings/auto-approval', [SettingController::class, 'toggleAutoApproval'])->name('settings.auto.approval.toggle');

    // Resources & Equipment Inventory
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
    Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');
    Route::get('/resources/{resource}/edit', [ResourceController::class, 'edit'])->name('resources.edit');
    Route::put('/resources/{resource}', [ResourceController::class, 'update'])->name('resources.update');
    Route::post('/resources/{resource}/assign-to-event', [ResourceController::class, 'assignToEvent'])->name('resources.assign.event');
    Route::post('/resources/{resource}/mark-in-use', [ResourceController::class, 'markInUse'])->name('resources.mark.inuse');
    Route::post('/resources/{resource}/mark-unused', [ResourceController::class, 'markUnused'])->name('resources.mark.unused');
    Route::post('/resources/{resource}/report-damage', [ResourceController::class, 'reportDamage'])->name('resources.report.damage');
    Route::post('/resources/{resource}/return-from-event', [ResourceController::class, 'returnFromEvent'])->name('resources.return.event');
    Route::post('/resources/{resource}/schedule-maintenance', [ResourceController::class, 'scheduleMaintenance'])->name('resources.schedule.maintenance');
    Route::post('/resources/{resource}/complete-maintenance', [ResourceController::class, 'completeMaintenance'])->name('resources.complete.maintenance');
    Route::get('/resources/{resource}/maintenance-logs', [ResourceController::class, 'maintenanceLogs'])->name('resources.maintenance.logs');
    Route::get('/resources/export/csv', [ResourceController::class, 'export'])->name('resources.export');
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');

    // Evaluation & Scoring System
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/simulation-events/{simulationEvent}/evaluation', [EvaluationController::class, 'show'])->name('evaluations.show');
    Route::get('/simulation-events/{simulationEvent}/evaluation/{userId}', [EvaluationController::class, 'evaluate'])->name('evaluations.evaluate');
    Route::post('/simulation-events/{simulationEvent}/evaluation/{userId}', [EvaluationController::class, 'storeEvaluation'])->name('evaluations.store');
    Route::put('/evaluations/{evaluation}/status', [EvaluationController::class, 'updateStatus'])->name('evaluations.update.status');
    Route::post('/evaluations/{evaluation}/lock', [EvaluationController::class, 'lock'])->name('evaluations.lock');
    Route::get('/simulation-events/{simulationEvent}/evaluation/summary', [EvaluationController::class, 'summary'])->name('evaluations.summary');
    Route::get('/simulation-events/{simulationEvent}/evaluation/export/{format?}', [EvaluationController::class, 'export'])->name('evaluations.export');

    // Barangay Profile (Admin only)
    Route::get('/barangay-profile', [App\Http\Controllers\BarangayProfileController::class, 'index'])
        ->name('barangay.profile');
    Route::post('/barangay-profile', [App\Http\Controllers\BarangayProfileController::class, 'store'])
        ->name('barangay.profile.store');
    Route::put('/barangay-profile/{barangayProfile}', [App\Http\Controllers\BarangayProfileController::class, 'update'])
        ->name('barangay.profile.update');

    Route::get('/certification', function () {
        return view('app', ['section' => 'certification']);
    })->name('certification');
}
);

