<?php

//   Dashboard
	Breadcrumbs::register('dashboard', function ($breadcrumbs) {
	    $breadcrumbs->push('Dashboard', route('dashboard'));
	});


//   User
	Breadcrumbs::register('users', function ($trail) {
	    $trail->parent('dashboard');
	    $trail->push('Users', route('user/index'));
	});

	Breadcrumbs::register('add-user', function ($trail) {
	    $trail->parent('users');
	    $trail->push('Add');
	});

	Breadcrumbs::register('edit-user', function ($trail) {
	    $trail->parent('users');
	    $trail->push('Edit');
	});

	Breadcrumbs::register('user', function ($trail) {
	    $trail->parent('users');
	    $trail->push('User');
	});

//  Role
	Breadcrumbs::register('roles', function ($trail) {
	    $trail->parent('dashboard');
	    $trail->push('User Roles', route('role/index'));
	});

	Breadcrumbs::register('add-role', function ($trail) {
	    $trail->parent('roles');
	    $trail->push('Add');
	});

	Breadcrumbs::register('edit-role', function ($trail) {
	    $trail->parent('roles');
	    $trail->push('Edit');
	});

// form
	Breadcrumbs::register('forms', function ($trail) {
	    $trail->parent('dashboard');
	    $trail->push('Forms', route('form/index'));
	});

	Breadcrumbs::register('add-form', function ($trail) {
	    $trail->parent('forms');
	    $trail->push('Add');
	});

	Breadcrumbs::register('edit-form', function ($trail) {
	    $trail->parent('forms');
	    $trail->push('Edit');
	});
   
   Breadcrumbs::register('template', function ($trail) {
	    $trail->parent('forms');
	    $trail->push('Template');
	});

 //data

   Breadcrumbs::register('data', function ($trail,$data) {
	    $trail->parent('forms');
	    $trail->push($data ,route('form/index'));
	});

    Breadcrumbs::register('add-data', function ($trail,$data) {
	    $trail->parent('data',$data);
	    // $trail->push('Add');
	});

	 Breadcrumbs::register('edit-data', function ($trail,$data) {
	    $trail->parent('data',$data);
	    $trail->push('Edit');
	});

	  Breadcrumbs::register('show-data', function ($trail,$data) {
	    $trail->parent('data',$data);
	    $trail->push('Show');
	});
        
        Breadcrumbs::register('users_request',function ($trail){
	    $trail->parent('dashboard');
	    $trail->push('Users Request', route('users_request/index'));
	});


//  form group's
	Breadcrumbs::register('form-groups', function ($trail) {
	    $trail->parent('dashboard');
	    $trail->push('Form Groups', route('formGroup/index'));
	});

//  User group's
	Breadcrumbs::register('role-groups', function ($trail) {
	    $trail->parent('dashboard');
	    $trail->push('User Role Groups', route('formGroup/index'));
	});

	//  Schedule Forms
	Breadcrumbs::register('schedule', function ($trail) {
	    $trail->parent('dashboard');
	    $trail->push('Schedule Forms', route('schedule/index'));
	});	
	//  Schedule Deatils
	Breadcrumbs::register('schedule-details', function ($trail) {
	    $trail->parent('schedule');
	    $trail->push('Details', route('schedule/show'));
	});
	//  Users Report
	Breadcrumbs::register('users-report', function ($trail) {
	    $trail->parent('dashboard');
	    $trail->push('Users Report', route('user/userReport'));
	});
	
	//  Submitted Form Data
	Breadcrumbs::register('submitted-form-data', function ($trail) {
	    $trail->parent('forms');
	    $trail->push('Submitted Form', route('form/index'));
	});

	//  Submitted Form Data
	Breadcrumbs::register('schedule-form', function ($trail) {
	    $trail->parent('forms');
	    $trail->push('Schedule Form', route('form/index'));
	});

?>