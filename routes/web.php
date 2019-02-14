<?php

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

Route::get('/admin-user-login','Auth\AdminUserLoginController@login')->name('admin-user-login');
Route::post('/admin-user-logout','Auth\AdminUserLoginController@logout')->name('admin-user-logout');

Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');

Route::get('/not-found',function(){
     return view('not_found');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {

Route::prefix('admin')->group(function(){
// Role Routes
Route::name('role/')->group(function(){
    Route::get('/roles','Web\RoleController@index')->name('index');
    Route::get('/ajax_Role','Web\RoleController@ajax_Role')->name('ajax_Role');
    Route::get('role/add','Web\RoleController@create')->name('create');
    Route::post('role/add','Web\RoleController@store')->name('store');
    Route::get('role/edit/{id}','Web\RoleController@edit')->name('edit');
    Route::put('role/update','Web\RoleController@update')->name('update');
    Route::get('role/delete/{id}','Web\RoleController@delete')->name('delete');
    Route::post('role/status','Web\RoleController@status')->name('status');
    });
// Form Routes
Route::name('form/')->group(function(){

   Route::get('form/{group_id?}/','Web\FormController@index')->name('index');
   Route::get('submissions/{form_id?}/','Web\FormController@submission')->name('submission');
    Route::get('/ajax_Form','Web\FormController@ajax_Form')->name('ajax_Form');
    Route::get('create-form','Web\FormController@createForm')->name('create');
    Route::post('form/add','Web\FormController@store')->name('store');
    Route::get('form/edit/{id}','Web\FormController@edit')->name('edit');

    Route::get('form/delete/{id?}','Web\FormController@delete')->name('delete');
    Route::get('form/delete/{id?}','Web\FormController@delete')->name('delete');
    Route::post('form/update/','Web\FormController@update')->name('update');
    Route::post('form/updateField/','Web\FormController@updateformField')->name('updateField');
    Route::get('form-submit','Web\FormController@submit')->name('submit');
    Route::get('form-template','Web\FormController@template')->name('template');
    Route::post('form/saveTableFormData/','Web\FormController@saveTableFormData')->name('saveTableFormData');
    Route::post('form/saveMultiTableFormData/','Web\FormController@saveMultiTableFormData')->name('saveMultiTableFormData');
    Route::post('form/updateTabularRowSetting/','Web\FormController@updateTabularRowSetting')
                                                                ->name('RowSetting');
    Route::post('form/updateTabularSetting/','Web\FormController@updateTabularSetting')
                                                                ->name('tableSetting');
                                                                
    Route::post('getValidationRules' , 'Web\FormController@getValidationRules')->name('validation-rules');
   
    Route::post('form/add_vertical_form_feild','Web\FormController@storeVerticalFormField')->name('storeVerticalFormField');
    Route::post('form/update_vertical_form_feild','Web\FormController@updateVerticalFormField')->name('updateVerticalFormField');
    Route::delete('form/delete_vertical_form_feild','Web\FormController@deleteVerticalFormField')->name('deleteVerticalFormField');
    Route::get('edit_vertical_form_feild','Web\FormController@editVerticalFormField')->name('editVerticalFormField');
    //Tabular Setting route
    Route::post('form/addTabularFormField','Web\FormController@storeTabularFormField')->name('storeTabularFormField');
    Route::post('form/updateTabularFormField','Web\FormController@updateTabularFormField')->name('updateTabularFormField');
    Route::get('form/deleteTable/{form_id}/{table_id}','Web\FormController@deleteTable')
                                                                ->name('deleteTable');
    Route::delete('form/delete_table_form_feild','Web\FormController@deleteTableFormField')->name('deleteTableFormField');
    Route::get('/edit_table_form_feild','Web\FormController@editTableFormField')->name('editTableFormField');
    Route::get('form/publish/{id}','Web\FormController@publish')->name('publish');
    Route::post('form/publish/store','Web\FormController@publishStore')->name('publish-store');
    // Route::post('form/schedule','Web\FormController@schedule')->name('schedule');
    Route::get('form/assign/form/users','Web\FormController@assignFormUsers')->name('assignFormUsers');
    Route::get('form/assign/form/users','Web\FormController@assignFormUsers')->name('schedule');
    // change Request
    Route::get('form/change_request/{form_id}','Web\FormController@showChangeRequest')->name('showChangeRequest');
    Route::post('form/changeRequest/','Web\FormController@storeChangeRequest')->name('changeRequest');
    Route::get('form/viewTabularData/{form_id}','Web\FormController@viewTabularData')->name('viewTabularData');
    // user  scheduled forms
     Route::get('form/schedule/{form_id}/','Web\FormController@schedule')->name('schedule');

     //User request for resubmission of form data
      Route::get('user-resubmission-request','Web\FormController@userResubmissionRequest')->name('userResubmissionRequest');

    //Admin Resubmission Request
      Route::get('admin-resubmission-request','Web\FormController@adminResubmissionRequest')->name('adminResubmissionRequest');
      
     //User record data accept 
      Route::get('record-accept','Web\FormController@acceptRecord')->name('acceptRecord');
     //User record data accept submission
      Route::get('request-accept','Web\FormController@acceptSubmissionRequest')->name('acceptSubmissionRequest');
    });
    
    Route::name('schedule/')->group(function(){
        Route::get('/schedules','Web\SchedulerController@index')->name('index');
        Route::get('/schedule-details','Web\SchedulerController@show')->name('show');
        Route::get('/create-schedule','Web\SchedulerController@create')->name('create');
        Route::post('/store-schedule','Web\SchedulerController@store')->name('store');
        Route::get('/edit-schedule','Web\SchedulerController@edit')->name('edit');
        Route::post('/update-schedule','Web\SchedulerController@update')->name('update');
        Route::put('/user-schedule','Web\SchedulerController@status')->name('status');
        Route::delete('/delete-schedule','Web\SchedulerController@destroy')->name('delete');
        Route::get('/get-forms','Web\SchedulerController@getForms')->name('getForms');
        Route::get('/schedule-forms','Web\SchedulerController@scheduleForms')->name('schedule-forms');
        Route::get('/submission-list','Web\SchedulerController@submittedFormsList')->name('submission-list');
    });

});

Route::get('/dashboard', 'Web\DashboardController@index')->name('dashboard');
Route::get('/get-notifications','Web\DashboardController@getNotifications')->name('getNotifications');

Route::name('profile/')->group(function (){
    Route::get('/profile','Web\ProfileController@show')->name('show');
    Route::put('/update-profile','Web\ProfileController@update')->name('update');
    Route::get('/change-password','Web\ProfileController@changePassword')->name('change-password');
    Route::put('/update-password','Web\ProfileController@updatePassword')->name('update-password');
});
Route::name('user/')->group(function(){
    Route::get('/users','Web\UserController@index')->name('index');
    Route::get('/user-details','Web\UserController@show')->name('show');
    Route::get('/create-user','Web\UserController@create')->name('create');
    Route::post('/store-user','Web\UserController@store')->name('store');
    Route::get('/edit-user','Web\UserController@edit')->name('edit');
    Route::put('/store-user','Web\UserController@update')->name('update');
    Route::post('/user-status','Web\UserController@status')->name('status');
    Route::delete('/delete-user','Web\UserController@destroy')->name('delete');
    Route::get('/get-group-users','Web\UserController@getGroupUsers')->name('getGroupUsers');
    Route::get('/get-user-report','Web\UserController@getUserReport')->name('userReport');
    Route::get('/export-user-report','Web\UserController@exportUserReport')->name('exportUserReport');
    Route::get('/export-users','Web\UserController@exportUsers')->name('exportUsers');
    Route::get('/get-role-by-group','Web\UserController@getRolesByGroup')->name('getRolesByGroup');
    
});
Route::name('data/')->group(function(){
    Route::get('/data-list','Web\FormDataController@index')->name('index');
    Route::get('/create-data','Web\FormDataController@create')->name('create');
    Route::post('/store-data','Web\FormDataController@store')->name('store');
    Route::get('/edit-data','Web\FormDataController@edit')->name('edit');
    Route::post('/update-data','Web\FormDataController@update')->name('update');
    Route::get('/show-data','Web\FormDataController@show')->name('show');
    Route::get('/delete-data','Web\FormDataController@destroy')->name('delete');
    Route::get('/export-vertical-data','Web\FormDataController@export')->name('export');
});
Route::name('tabularData/')->group(function(){
    Route::get('/tabularData-list','Web\FormTabularDataController@index')->name('index');
    Route::get('/tabularData-edit','Web\FormTabularDataController@edit')->name('edit');
    Route::post('/tabularData-update','Web\FormTabularDataController@update')->name('update');
    Route::get('/tabularData-show','Web\FormTabularDataController@show')->name('show');
    Route::get('/tabularData-delete','Web\FormTabularDataController@destroy')->name('delete');
    Route::get('/tabularData-export','Web\FormTabularDataController@export')->name('export');
    Route::get('/tabularData-print','Web\FormTabularDataController@printTable')->name('print');
    Route::get('/tabularData-changeStatus','Web\FormTabularDataController@updateStatus')->name('updateStatus');
});

Route::name('roleGroup/')->group(function(){
    Route::get('/role-groups','Web\RoleGroupController@index')->name('index');
    Route::get('/create-role-group','Web\RoleGroupController@create')->name('create');
    Route::post('/store-role-group','Web\RoleGroupController@store')->name('store');
    Route::get('/edit-role-group','Web\RoleGroupController@edit')->name('edit');
    Route::post('/update-role-group','Web\RoleGroupController@update')->name('update');
    Route::get('/show-role-group','Web\RoleGroupController@show')->name('show');
    Route::delete('/delete-role-group','Web\RoleGroupController@destroy')->name('delete');
    Route::get('/get-role-groups','Web\RoleGroupController@getGroups')->name('getGroups');
    Route::post('/group-role-status','Web\RoleGroupController@status')->name('status');
});

Route::name('formGroup/')->group(function(){
    Route::get('/form-groups','Web\FormGroupController@index')->name('index');
    Route::get('/create-form-group','Web\FormGroupController@create')->name('create');
    Route::post('/store-form-group','Web\FormGroupController@store')->name('store');
    Route::get('/edit-form-group','Web\FormGroupController@edit')->name('edit');
    Route::post('/update-form-group','Web\FormGroupController@update')->name('update');
    Route::get('/show-form-group','Web\FormGroupController@show')->name('show');
    Route::delete('/delete-form-group','Web\FormGroupController@destroy')->name('delete');
    Route::get('/get-form-groups','Web\FormGroupController@getGroups')->name('getGroups');
    Route::post('/form-group-status','Web\FormGroupController@status')->name('status');
    Route::post('/get-form-groups','Web\FormGroupController@getGroups')->name('getGroups');
    });
//users request
Route::name('users_request/')->group(function(){
  Route::get('/users-request','Web\UserRequestController@index')->name('index');
  });
Route::get('test','Web\FormController@createForm');
});

Route::prefix('cron')->group(function(){
   Route::get('/scheduled-form-mail','Web\CroneJobController@scheduleFormMail')->name('scheduleFormMail');
   Route::get('/schedule-authority-email','Web\SchedulerController@scheduleAuthorityEmail');
});






