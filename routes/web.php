<?php

use App\Http\Controllers\DashboardController;
use App\Livewire\CollegeLabSummary;
use App\Livewire\DivisionCollegeReport;
use App\Livewire\IctTrainingSummary;
use App\Livewire\TeacherManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('/teacher-management', TeacherManagement::class)->name('teachers.manage');
    Route::get('/lab-summary', CollegeLabSummary::class)->name('lab.summary');
    Route::get('/ict-training-summary', IctTrainingSummary::class)->name('ict.summary');
    Route::get('/reports/division-colleges', DivisionCollegeReport::class)->name('reports.division-colleges');
});

require __DIR__.'/settings.php';
