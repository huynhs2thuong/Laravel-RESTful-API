<?php

use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login','AuthController@login');
Route::post('resetPassword','ChangePasswordController@AdminResetPassword');

// Login Mobile

Route::post('mobile/login','AuthController@loginmobile');
Route::post('mobile/resetPassword','ChangePasswordController@sendPasswordResetEmail');
Route::post('mobile/restorePassword','ChangePasswordController@resetPassword');

// Route::post('resetPassword','ChangePasswordController@register');
// Route::post('register','AuthController@register');


//Get City
Route::get('city_us','LocationController@index');
    
// Get Weather
Route::get('weather_status','WeatherController@index');

// Get Material
Route::get('materials','MaterialController@index');

//Get Time of day
Route::get('time_of_day','TimeOfDayController@index');
//Store type
Route::apiResource('store_type','StoreTypeController');
//Store climate region
Route::apiResource('climate_region','Climate_regionController');
//Photo Tag type
Route::apiResource('photo_elevations','Photo_ElevationController');
//Photo Tag type
Route::apiResource('photo_tag','Photo_TagController');
//API cardinal direction
Route::apiResource('cardinal_direction','DirectionController');
//Get version app
Route::get('app_version','VersionAppController@index');
Route::get('testwedigi','VersionAppController@testwedigi');
Route::get('keep','VersionAppController@keep_session');
//Setting
Route::get('setting','SettingAppController@index');

Route::group(['middleware'=> 'auth.jwt'],function(){

    //API Mobile
    //Route::put('mobile/reset-password','EmployeeController@set_new_password');

    //API Mobile JsonStructure
    Route::get('mobile/jsonstructure/{id}','JsonStructureController@show');

    // API  Profile
    Route::get('mobile/profile','EmployeeController@profile');
    Route::put('mobile/profile/updateInfo','EmployeeController@updateInfo');
    Route::put('mobile/profile/change-password','EmployeeController@change_password');
    Route::post('mobile/profile/upload-avatar','EmployeeController@upload_avatar');

    
    Route::post('mobile/register_fcm_token','EmployeeController@register_fcm_token');
    Route::get('mobile/employee/jobs','Employee_to_Company_Controller@employee_list_jobs_mobile');
    Route::get('mobile/employee/stores','Employee_to_Company_Controller@employee_list_stores_mobile');
    Route::get('mobile/job/{sid}/store','JobController@job_to_stores_mobile');
    Route::get('mobile/job/plan/{sid}','JobController@plan_data');
    Route::post('mobile/job/plan/{sid}','JobController@plan_data_upload');
    Route::get('mobile/job/plan/{sid}/photo','JobController@plan_to_photos');

    //API Admin
    //Export Data
    Route::post('export_data','PlanController@exportExcel');
    // API Auth set new Password
    Route::post('auth/set-new-password','EmployeeController@set_new_password');
    // API  Profile
    Route::get('profile','EmployeeController@profile');
    Route::put('profile/updateInfo','EmployeeController@updateInfo');
    Route::put('profile/change-password','EmployeeController@change_password');
    Route::post('profile/upload-avatar','EmployeeController@upload_avatar');

    //Company theo Territory
    Route::get('companies/{sid}/territories','CompanyController@company_to_territory');
    //Job theo Company
    Route::get('companies/{sid}/jobs','CompanyController@company_to_jobs');
    //Employee theo Company
    Route::get('list_companies','Employee_to_Company_Controller@list_company');
    Route::get('employees/{sid}/companies','Employee_to_Company_Controller@show');
    Route::post('employees/{sid}/companies','Employee_to_Company_Controller@store');
    Route::put('employees/{employee_sid}/companies/{id}','Employee_to_Company_Controller@update');
    Route::delete('employees_to_company/{id}','Employee_to_Company_Controller@destroy');
    Route::get('employee/{sid}/job','Employee_to_Company_Controller@employee_list_jobs');
    Route::get('employee/{sid}/store','Employee_to_Company_Controller@employee_list_stores');
    //Employee Full CRUD
    Route::apiResource('employees','EmployeeController');
    //employee update_avatar
    Route::post('employees/{sid}/update-avatar','EmployeeController@update_avatar');
    //employee logout
    Route::delete('employee/{sid}/logout', 'EmployeeController@logout_admin');

    //Route::apiResource('employee_to_company','Employee_to_Company_Controller');

    //JsonStructure Full CRUD
    Route::apiResource('jsonstructures','JsonStructureController');

    //Company Full CRUD
    Route::apiResource('companies','CompanyController');

    //Territory Full CRUD
    Route::apiResource('territories','TerritoryController');
    //Job theo Territory
    Route::get('territory/jobs','TerritoryController@territory_list_job');
    Route::post('territory_by_job','TerritoryController@territory_by_job');

    //Store theo Territory
    Route::get('territory/stores','TerritoryController@territory_list_stores');
    Route::get('territory/chart','TerritoryController@chart');

    //Store Full CRUD
    Route::apiResource('stores','StoreController');
    Route::post('stores/{sid}/photo','StoreController@store_photo');
    Route::post('stores/{sid}/file','StoreController@store_file');
    Route::get('store/photos','StoreController@store_list_photos');

    //JOB Full CRUD
    Route::apiResource('jobs','JobController');
    Route::get('job/{sid}/store','JobController@job_to_stores');
    Route::get('job/plan/{sid}','JobController@plan_data');
    Route::get('job/plan/{sid}/photos','JobController@plan_to_photos');
    Route::get('job/photos','JobController@jobs_list_photos');
    Route::get('job/store/photos','JobController@jobs_store_list_photos');
    //Plan Full CRUD
    Route::apiResource('plans','PlanController');

    //Plan actual Full CRUD
    Route::apiResource('plan_actual','Plan_ActualController');

    //Plan data Full CRUD
    Route::apiResource('plan_data','Plan_DataController');

    //Plan info Full CRUD
    Route::apiResource('plan_info','Plan_InfoController');

    //Photo Job Full CRUD
    Route::apiResource('photos','Photo_JobController');
    Route::get('photos/{sid}','Photo_JobController@show');
    Route::get('customer/company/photos','Employee_to_Company_Controller@customer_company_list_photos');
    Route::delete('photos', 'Photo_JobController@destroy');
    Route::post('photos/{sid}/upload-photo','Photo_JobController@upload_photo');
    Route::post('photos/download','Photo_JobController@download_photo');
    Route::post('mobile/photo','Photo_JobController@store_photo_mobile');

   
    Route::get('activities', 'ActivityController@index');
    Route::get('activity/employee/{sid}', 'ActivityController@filter_activity_employee');
    
    Route::get('mobile/activities', 'ActivityController@show');

    Route::delete('mobile/logout', 'EmployeeController@logout');

    Route::post('material_mass_create','MaterialController@mass_create');
    
    Route::post('dashboard/territory','DashboardController@territory');
    Route::post('dashboard/month','DashboardController@month');
    Route::post('dashboard/store','DashboardController@store');
});


