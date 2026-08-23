<?php

use App\Http\Middleware\EnsureUserIsHr;
use App\Livewire\Activities;
use App\Livewire\ActivityAllocation\Create as ActivityAllocationCreate;
use App\Livewire\ActivityAllocation\Edit as ActivityAllocationEdit;
use App\Livewire\ActivityAllocation\Index as ActivityAllocationIndex;
use App\Livewire\BankAccounts;
use App\Livewire\Departments;
use App\Livewire\Donors;
use App\Livewire\EducationTraining\Create as EducationTrainingCreate;
use App\Livewire\EducationTraining\Edit as EducationTrainingEdit;
use App\Livewire\EducationTraining\Index as EducationTrainingIndex;
use App\Livewire\EmailSetup;
use App\Livewire\EmpTypes;
use App\Livewire\LeaveInformation;
use App\Livewire\Locations;
use App\Livewire\MonthlyActivities\Create as MonthlyActivitiesCreate;
use App\Livewire\MonthlyActivities\Edit as MonthlyActivitiesEdit;
use App\Livewire\MonthlyActivities\Index as MonthlyActivitiesIndex;
use App\Livewire\ParCalendars;
use App\Livewire\ParReport;
use App\Livewire\PersonalDetail\Create as PersonalDetailCreate;
use App\Livewire\PersonalDetail\Edit as PersonalDetailEdit;
use App\Livewire\PersonalDetail\Index as PersonalDetailIndex;
use App\Livewire\Project\Create as ProjectCreate;
use App\Livewire\Project\Edit as ProjectEdit;
use App\Livewire\Project\Index as ProjectIndex;
use App\Livewire\Roles;
use App\Livewire\Salary\Create as SalaryCreate;
use App\Livewire\Salary\Edit as SalaryEdit;
use App\Livewire\Salary\Index as SalaryIndex;
use App\Livewire\SalaryAllocation\Create as SalaryAllocationCreate;
use App\Livewire\SalaryAllocation\Edit as SalaryAllocationEdit;
use App\Livewire\SalaryAllocation\Index as SalaryAllocationIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('/donors', Donors::class)->name('donors.index');
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', ProjectIndex::class)->name('index');
        Route::get('/create', ProjectCreate::class)->name('create');
        Route::get('/{id}/edit', ProjectEdit::class)->name('edit');
    });
    Route::get('/departments', Departments::class)->name('departments.index');
    Route::get('/activities', Activities::class)->name('activities.index');
    Route::get('/locations', Locations::class)->name('locations.index');
    Route::get('/emp-types', EmpTypes::class)->name('emp-types.index');
    Route::get('/roles', Roles::class)->name('roles.index');
    Route::get('/bank-accounts', BankAccounts::class)->name('bank-accounts.index');
    Route::get('/par-calendars', ParCalendars::class)->name('par-calendars.index');
    Route::get('/par-report', ParReport::class)->name('par-report.index');
    Route::get('/leave-information', LeaveInformation::class)->name('leave-information.index');
    Route::get('/email-setup', EmailSetup::class)
        ->middleware(EnsureUserIsHr::class)
        ->name('email-setup');

    Route::prefix('salaries')->name('salaries.')->group(function () {
        Route::get('/', SalaryIndex::class)->name('index');
        Route::get('/create', SalaryCreate::class)->name('create');
        Route::get('/{id}/edit', SalaryEdit::class)->name('edit');
    });

    Route::prefix('salary-allocations')->name('salary-allocations.')->group(function () {
        Route::get('/', SalaryAllocationIndex::class)->name('index');
        Route::get('/create', SalaryAllocationCreate::class)->name('create');
        Route::get('/{id}/edit', SalaryAllocationEdit::class)->name('edit');
    });

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', \App\Livewire\Employee\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Employee\Create::class)->name('create');
        Route::get('/{id}/edit', \App\Livewire\Employee\Edit::class)->name('edit');
    });

    // Route::prefix('locations')->name('locations.')->group(function () {
    //     Route::get('/', LocationIndex::class)->name('index');
    //     Route::get('/create', LocationCreate::class)->name('create');
    //     Route::get('/{location}/edit', LocationEdit::class)->name('edit');
    // });

    Route::prefix('personal-details')->name('personal-details.')->group(function () {
        Route::get('/', PersonalDetailIndex::class)->name('index');
        Route::get('/create', PersonalDetailCreate::class)->name('create');
        Route::get('/{id}/edit', PersonalDetailEdit::class)->name('edit');
    });

    Route::prefix('education-trainings')->name('education-trainings.')->group(function () {
        Route::get('/', EducationTrainingIndex::class)->name('index');
        Route::get('/create', EducationTrainingCreate::class)->name('create');
        Route::get('/{id}/edit', EducationTrainingEdit::class)->name('edit');
    });

    Route::prefix('monthly-activities')->name('monthly-activities.')->group(function () {
        Route::get('/', MonthlyActivitiesIndex::class)->name('index');
        Route::get('/create', MonthlyActivitiesCreate::class)->name('create');
        Route::get('/{id}/edit', MonthlyActivitiesEdit::class)->name('edit');
    });

    Route::prefix('activity-allocations')->name('activity-allocations.')->group(function () {
        Route::get('/', ActivityAllocationIndex::class)->name('index');
        Route::get('/create', ActivityAllocationCreate::class)->name('create');
        Route::get('/{id}/edit', ActivityAllocationEdit::class)->name('edit');
    });

});
require __DIR__.'/auth.php';
