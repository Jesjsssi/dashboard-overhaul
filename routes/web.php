<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Superadmin\MasterDisiplinController;
use App\Http\Controllers\Superadmin\MasterSubDisiplinController;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\HistoriController;
use App\Http\Controllers\Superadmin\ProjectController;
use App\Http\Controllers\Superadmin\EPSController;
use App\Http\Controllers\Superadmin\PlanStepDurationController;
use App\Http\Controllers\Superadmin\JasaController;
use App\Http\Controllers\Superadmin\MasterTahapanController;
use App\Http\Controllers\Superadmin\DetailProgressController;
use App\Http\Controllers\Superadmin\JasrelController;
use App\Http\Controllers\Superadmin\MatrelController;



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


Route::get('/', [LoginController::class, 'showLoginForm'])->name('formlogin');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard route (redirect to superadmin dashboard)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');


Route::group(['prefix' => 'superadmin', 'middleware' => 'auth'], function () {
    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');

    //EPS
    Route::get('/eps', [EPSController::class, 'index'])->name('superadmin.eps');
    Route::get('/eps/create', [EPSController::class, 'create'])->name('superadmin.eps.create');
    Route::post('/eps', [EPSController::class, 'store'])->name('superadmin.eps.store');
    Route::get('/eps/{id}/edit', [EPSController::class, 'edit'])->name('superadmin.eps.edit');
    Route::put('/eps/{id}', [EPSController::class, 'update'])->name('superadmin.eps.update');
    Route::delete('/eps/{id}', [EPSController::class, 'destroy'])->name('superadmin.eps.destroy');
    Route::post('/superadmin/eps/{id}/set-default', [EPSController::class, 'setDefault'])->name('superadmin.eps.setDefault');

    //Project CRUD - Resource Route with parameter customization
    Route::resource('project', ProjectController::class)
        ->parameters(['project' => 'id']) // This maps {project} to {id} in your controller methods
        ->names([
            'index' => 'superadmin.project',
            'create' => 'superadmin.project.create',
            'store' => 'superadmin.project.store',
            'edit' => 'superadmin.project.edit',
            'update' => 'superadmin.project.update',
            'destroy' => 'superadmin.project.destroy',
            'show' => 'superadmin.project.show'
        ]);

    //Jasa CRUD
    Route::get('/jasa', [JasaController::class, 'index'])->name('superadmin.jasa');
    Route::get('/jasa/create', [JasaController::class, 'create'])->name('superadmin.jasa.create');
    Route::post('/jasa', [JasaController::class, 'store'])->name('superadmin.jasa.store');
    Route::get('/jasa/{id}/edit', [JasaController::class, 'edit'])->name('superadmin.jasa.edit');
    Route::put('/jasa/{id}', [JasaController::class, 'update'])->name('superadmin.jasa.update');
    Route::delete('/jasa/{id}', [JasaController::class, 'destroy'])->name('superadmin.jasa.destroy');
    Route::get('/jasa/{id}/show', [JasaController::class, 'show'])->name('superadmin.jasa.show');
    Route::put('/jasa/{id}/actual', [DetailProgressController::class, 'updateActual'])->name('superadmin.jasa.update-actual');
    Route::put('/jasa/{id}/plan', [DetailProgressController::class, 'updatePlan'])->name('superadmin.jasa.update-plan');

    //Jasa Import
    Route::get('/jasa/import', [JasaController::class, 'import'])->name('superadmin.jasa.import');
    Route::post('/jasa/import', [JasaController::class, 'importStore'])->name('superadmin.jasa.import.store');
    Route::get('/jasa/download-template', [JasaController::class, 'downloadTemplate'])->name('superadmin.jasa.download-template');

    //Jasa Export
    Route::get('/jasa/export', [JasaController::class, 'export'])->name('superadmin.jasa.export');
    Route::post('/jasa/export', [JasaController::class, 'exportStore'])->name('superadmin.jasa.export.store');
    Route::get('/jasa/download-template', [JasaController::class, 'downloadTemplate'])->name('superadmin.jasa.download-template');

    //Jasrel 
    Route::get('/jasrel', [JasrelController::class, 'index'])->name('superadmin.jasrel');

    //Matrel 
    Route::get('/matrel', [MatrelController::class, 'index'])->name('superadmin.matrel');

    //Import Data
    Route::get('/import-data', [ImportController::class, 'index'])->name('superadmin.import-data');
    Route::post('/import-data', [ImportController::class, 'import'])->name('superadmin.import-data.import');
    Route::get('/download-template', [ImportController::class, 'downloadTemplate'])->name('superadmin.download-template');

    //Master Data CRUD
    Route::get('/master-disiplin', [MasterDisiplinController::class, 'index'])->name('superadmin.master-disiplin');
    Route::get('/master-disiplin/create', [MasterDisiplinController::class, 'create'])->name('superadmin.master-disiplin.create');
    Route::post('/master-disiplin', [MasterDisiplinController::class, 'store'])->name('superadmin.master-disiplin.store');
    Route::get('/master-disiplin/{id}/edit', [MasterDisiplinController::class, 'edit'])->name('superadmin.master-disiplin.edit');
    Route::put('/master-disiplin/{id}', [MasterDisiplinController::class, 'update'])->name('superadmin.master-disiplin.update');
    Route::delete('/master-disiplin/{id}', [MasterDisiplinController::class, 'destroy'])->name('superadmin.master-disiplin.destroy');

    //Master Tahapan CRUD
    Route::get('/master-tahapan', [MasterTahapanController::class, 'index'])->name('superadmin.master-tahapan');
    Route::get('/master-tahapan/create', [MasterTahapanController::class, 'create'])->name('superadmin.master-tahapan.create');
    Route::post('/master-tahapan', [MasterTahapanController::class, 'store'])->name('superadmin.master-tahapan.store');
    Route::get('/master-tahapan/{id}/edit', [MasterTahapanController::class, 'edit'])->name('superadmin.master-tahapan.edit');
    Route::put('/master-tahapan/{id}', [MasterTahapanController::class, 'update'])->name('superadmin.master-tahapan.update');
    Route::delete('/master-tahapan/{id}', [MasterTahapanController::class, 'destroy'])->name('superadmin.master-tahapan.destroy');
    Route::post('/master-tahapan/{id}/move-up', [MasterTahapanController::class, 'moveUp'])->name('superadmin.master-tahapan.move-up');
    Route::post('/master-tahapan/{id}/move-down', [MasterTahapanController::class, 'moveDown'])->name('superadmin.master-tahapan.move-down');

    //Master Sub Disiplin CRUD
    Route::get('/master-sub-disiplin', [MasterSubDisiplinController::class, 'index'])->name('superadmin.master-sub-disiplin');
    Route::get('/master-sub-disiplin/create', [MasterSubDisiplinController::class, 'create'])->name('superadmin.master-sub-disiplin.create');
    Route::post('/master-sub-disiplin', [MasterSubDisiplinController::class, 'store'])->name('superadmin.master-sub-disiplin.store');
    Route::get('/master-sub-disiplin/{id}/edit', [MasterSubDisiplinController::class, 'edit'])->name('superadmin.master-sub-disiplin.edit');
    Route::put('/master-sub-disiplin/{id}', [MasterSubDisiplinController::class, 'update'])->name('superadmin.master-sub-disiplin.update');
    Route::delete('/master-sub-disiplin/{id}', [MasterSubDisiplinController::class, 'destroy'])->name('superadmin.master-sub-disiplin.destroy');
    Route::get('/master-sub-disiplin/get-by-disiplin/{id_disiplin}', [MasterSubDisiplinController::class, 'getByDisiplin'])->name('superadmin.master-sub-disiplin.get-by-disiplin');
});
