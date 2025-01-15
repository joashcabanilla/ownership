<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AdminController;

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

//Guest Route
Route::middleware(['guest'])->group(
    function(){
        //get route
        Route::get('/', [GuestController::class, 'Index'])->name('guest.index');

        //post route
        Route::post('searchAccount', [GuestController::class, 'searchAccount']);
        Route::post('saveQrCode', [GuestController::class, 'saveQrCode']);
    }
);

Route::prefix('admin')->middleware(['auth'])->group(
    function(){
        //get route
        Route::get('/users', [AdminController::class, 'Users'])->name('admin.users');
        Route::get('/members', [AdminController::class, 'Members'])->name('admin.members');
        Route::get('/timedeposit', [AdminController::class, 'Timedeposit'])->name('admin.timedeposit');
        Route::get('/maintenance', [AdminController::class, 'Maintenance'])->name('admin.maintenance');

        //post route
        Route::post('/logout', [AdminController::class, 'Logout']);
        Route::post('/userTable', [AdminController::class, 'UserTable']);
        Route::post('/createUpdateUser', [AdminController::class, 'createUpdateUser']);
        Route::post('/getUser', [AdminController::class, 'getUser']);
        Route::post('/deactivateUser', [AdminController::class, 'deactivateUser']);
        Route::post('/batchInsertData', [AdminController::class, 'batchInsertData']);
        Route::post('/memberTable', [AdminController::class, 'memberTable']);
        Route::post('/createUpdateMember', [AdminController::class, 'createUpdateMember']);
        Route::post('/deleteMember', [AdminController::class, 'deleteMember']);
        Route::post('/getMember', [AdminController::class, 'getMember']);
        Route::post('/generateReport', [AdminController::class, 'generateReport'])->name('admin.report');
        Route::post('/updatePbNoMemId', [AdminController::class, 'updatePbNoMemId']);
        Route::post('/receivedGiveaway', [AdminController::class, 'receivedGiveaway']);
        Route::post('/timedepositTable', [AdminController::class, 'timedepositTable']);
        Route::post('/addTimedepositMember', [AdminController::class, 'addTimedepositMember']);
        Route::post('/getTimedepositMember', [AdminController::class, 'getTimedepositMember']);
        Route::post('/updateMemberStatus', [AdminController::class, 'updateMemberStatus']);
    }
);