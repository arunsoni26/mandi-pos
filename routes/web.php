<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDocumentController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\GstYearController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('dashboard', action: [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile')->middleware('permission:profile,can_view');
        Route::post('/settings/update', [ProfileController::class, 'updateProfile'])->name('settings.update')->middleware('permission:profile,can_edit');
        Route::post('/settings/password', [ProfileController::class, 'updatePassword'])->name('settings.password')->middleware('permission:profile,can_edit');

        //news
        Route::middleware(['role.superadmin'])->group(function () {
            Route::post('/news/add', [ProfileController::class, 'addNews'])->name('news.add');
            Route::put('/news/{id}', [ProfileController::class, 'update'])->name('news.update');
            Route::delete('/news/{id}', [ProfileController::class, 'destroy'])->name('news.destroy');
            Route::patch('/news/{id}/restore', [ProfileController::class, 'restore'])->name('news.restore');
    
            //gallery
            Route::post('/banner/add', [ProfileController::class, 'addBanner'])->name('banner.add');
            Route::delete('/banner/delete/{id}', [ProfileController::class, 'deleteBanner'])->name('banner.delete');
            Route::patch('/banner/restore/{id}', [ProfileController::class, 'restoreBanner'])->name('banner.restore');
            
            // roles & permissions
            Route::get('role-permissions', [RolePermissionController::class, 'index'])->name('role-permissions')->middleware('permission:permissions,can_view');
            Route::post('role-permission-form', [RolePermissionController::class, 'rolePermissionForm'])->name('role-permission-form')->middleware('permission:permissions,can_add');
            Route::post('update-role-permission', [RolePermissionController::class, 'update'])->name('update-role-permission')->middleware('permission:permissions,can_add');
            Route::get('role/{id}/permissions', [RolePermissionController::class, 'getPermissions'])->name('role.get-permissions')->middleware('permission:permissions,can_view');
    
            // Users Module
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [App\Http\Controllers\UserController::class,'index'])->name('index');
                Route::any('/list', [App\Http\Controllers\UserController::class,'list'])->name('list'); // Ajax JSON
                Route::post('/form', [App\Http\Controllers\UserController::class,'form'])->name('form');
                Route::post('/save', [App\Http\Controllers\UserController::class,'save'])->name('save');
                Route::post('/toggle-status/{id}', [App\Http\Controllers\UserController::class,'toggleStatus'])->name('toggle-status');
                Route::delete('/delete/{id}', [App\Http\Controllers\UserController::class,'destroy'])->name('delete');
            });
        });

        Route::middleware(['permission:customers,can_view'])->group(function () {
            Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
                Route::get('/', [CustomerController::class, 'index'])->name('index');
                Route::any('/list', [CustomerController::class, 'list'])->name('list');
                Route::get('/create', [CustomerController::class, 'create'])->name('create')->middleware('permission:customers,can_add');
                Route::post('/store', [CustomerController::class, 'store'])->name('store')->middleware('permission:customers,can_add');
                Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit')->middleware('permission:customers,can_edit');
                Route::post('/update/{id}', [CustomerController::class, 'update'])->name('update')->middleware('permission:customers,can_edit');
                Route::post('/toggle-status/{id}', [CustomerController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:customers,can_edit');
                Route::post('/toggle-dashboard/{id}', [CustomerController::class, 'toggleDashboard'])->name('toggle-dashboard')->middleware('permission:customers,can_edit');
                // Customer form load (Add / Edit)
                Route::post('form', [CustomerController::class, 'form'])->name('form')->middleware('permission:customers,can_edit');

                // Customer save (Add / Edit)
                Route::post('save', [CustomerController::class, 'save'])->name('save')->middleware('permission:customers,can_edit');

                Route::any('/view', [CustomerController::class, 'view'])->name('view')->middleware('permission:customers,can_view');
            });
        });
        
        Route::group(['prefix' => 'customers/groups', 'as' => 'customers.groups.'], function () {
            Route::get('list', [CustomerController::class, 'groupList'])->name('list');
            // Customer group form load (Add / Edit)
            Route::post('form', [CustomerController::class, 'groupForm'])->name('form');
            
            // Customer group save (Add / Edit)
            Route::post('save', [CustomerController::class, 'groupSave'])->name('save');
            Route::post('delete', [CustomerController::class, 'groupDelete'])->name('delete');
        });

        Route::group(['prefix' => 'customers/{customer}/docs', 'as' => 'customers.docs.'], function () {
            Route::get('/', [CustomerDocumentController::class,'index'])->name('index'); // page
            Route::any('/list', [CustomerDocumentController::class,'list'])->name('list'); // JSON
    
            // modal form (Add multiple docs for a GST year)
            Route::post('/form', [CustomerDocumentController::class,'form'])->name('form');
            Route::post('/save', [CustomerDocumentController::class,'save'])->name('save');
    
            // download / delete a single doc
            Route::get('/download/{id}', [CustomerDocumentController::class,'download'])->name('download');
            Route::delete('/delete/{id}', [CustomerDocumentController::class,'destroy'])->name('delete');
        });
    
        // GST Years quick-manage
        Route::group(['prefix' => 'gst-years', 'as' => 'gst_years.'], function () {
            Route::get('/list', [GstYearController::class,'list'])->name('list');      // JSON for dropdown / table
            Route::post('/save', [GstYearController::class,'save'])->name('save');     // add/edit
            Route::delete('/{id}', [GstYearController::class,'destroy'])->name('delete');
        });
    
        // GST Years quick-manage
        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::get('/main', [POSController::class,'index'])->name('main');
        });
    });
    
});

//Frontend
Route::get('/homepage', [FrontendController::class, 'home'])->name('homepage');
Route::get('/news', [FrontendController::class, 'news'])->name('news');
Route::get('/news/load-more', [FrontendController::class, 'loadMore'])->name('news.loadMore');
Route::get('/news/{id}', [FrontendController::class, 'show'])->name('news.show');