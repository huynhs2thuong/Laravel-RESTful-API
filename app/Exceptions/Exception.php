<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Exception extends ExceptionHandler
{
    //Exception Company
    const SHOW_COMPANY = 'Information of company.';
    const ADD_COMPANY_SUCCESS = 'Create company successfully.';
    const ADD_COMPANY_FAILED_NAME = 'Company name already exists.';
    const ADD_COMPANY_FAILED_EMAIL = 'Company email already exists.';
    const UPDATE_COMPANY_SUCCESS = 'Edit information successfully.';
    const UPDATE_COMPANY_FAILED_NAME = 'Company name already exists.';
    const UPDATE_COMPANY_FAILED_EMAIL = 'Company email already exists.';
    const DESTROY_COMPANY_SUCCESS = 'Delete company successfully.';
    const DESTROY_COMPANY_FAILED = 'Delete unsuccessfully.';
    const GET_COMPANY_TO_TERRITORY = 'List territories of company.';
    const GET_COMPANY_TO_JOB = 'List jobs of company.';
    //Exception Store
    const SHOW_STORE = 'Information of store.';
    const ADD_STORE_SUCCESS = 'Create store successfully.';
    const ADD_STORE_FAILED_NAME = 'Store name already exists.';
    const ADD_STORE_FILE_PHOTO_SUCCESS = 'Upload file photo successfully';
    const ADD_STORE_FILE_PHOTO_FAILED = 'Upload file photo failes';
    const ADD_STORE_FILE_SUCCESS = 'Upload file successfully';
    const ADD_STORE_FILE_FAILED = 'Upload file failes';
    const UPDATE_STORE_SUCCESS = 'Edit information successfully';
    const UPDATE_STORE_FAILED = 'Store name already exists.';
    const DESTROY_STORE_SUCCESS = 'Delete Store successfully';
    const DESTROY_STORE_FAILED = 'This story has been deleted';
    //Employee
    const CREATED_EMPLOYEE_SUCCESS = 'Create employee successfully';
    const CREATED_FCM_TOKEN_SUCCESS = 'Create fcm_token successfully';
    const DELETE_EMPLOYEE_SUCCESS = 'Delete employee successfully';
    const SHOW_EMPLOYEE = 'Information of employee';
    const USERNAME_DUPLICATED = 'Username already exists.';
    const EMAIL_DUPLICATED = 'Email already exists.';
    const UPDATED_EMPLOYEE_SUCCESS = 'Edit information successfully';
    const UPDATED_AVATAR_SUCCESS = 'You have successfully updated your avatar';
    const UPDATED_FILE_FAILED = 'File upload failed.';
    const DELETED_EMPLOYEE_FAILED = 'Delete unsuccessfully';
    const CREATED_NEWPASSWORD_SUCCESS = 'You have successfully created a new password';
    const PASSWORD_CREATEDBEFORE = 'Password has been created before';
    const CHANGE_PASSWORD_SUCCESS = 'Change password successfully';
    const Current_password_incorrect = 'Incorrect password';
    const LOGOUT_SUCCESS = 'Logout Success';
    const LOGOUT_FAILED = 'No permission.';
    //Territory
    const COMPANY_DUPLICATED = 'Territory already have company';
    const TERRITORY_DUPLICATED = 'Territory name and code already exists.';
    const TERRITORYNAME_DUPLICATED = 'Territory name already exists.';
    const CREATED_TERRITORY_SUCCESS = 'Create territory successfully';
    const SHOW_TERRITOTY = 'Information of territory';
    const UPDATED_TERRITORY_SUCCESS = 'Edit information successfully';
    const DELETED_TERRITORY_SUCCESS = 'Delete territory successfully';
    const DELETED_TERRITORY_FAILED = 'Delete unsuccessfully';
    const LIST_JOB_OF_TERRITORY = 'List jobs of terriitory';
    const LIST_STORE_OF_TERRITORY = 'List stores of territory';
    //Plan
    const UPDATED_PLAN_SUCCESS = 'Edit information successfully';
    const EXPORT_SUCCESS = 'Export file successfully';
    const EXPORT_FAILED = 'Export file unsuccessfully';
    //Photo_tag
    const LIST_PHOTO_TAG = 'List Photo Tag';
    //Photo job
    const CREATED_PHOTO_SUCCESS = 'Create photo successfully';
    const CREATED_PHOTO_FAILED = 'Not failed job store';
    const SHOW_PHOTO = 'Information of Photo';
    const no_plan_actual = 'No plan_actual';
    const data_json_invalid = 'Invalid json structure of data';
    const UPDATED_PHOTO_SUCCESS = 'Upload file photo successfully';
    const UPDATED_FILEPHOTO_FAILED = 'Upload file photo failes';
    const DELETE_PHOTO_SUCCESS = 'Delete photo successfully';
    //activity
    const SHOW_ACTIVITY = 'Information of activities';
    //Auth
    const LOGIN_FAILED = 'Invalid Username or Password';
    const CUSTOMER_LOGIN_FAILED = 'No permission login';
    const PERMISSION = 'No permission';
    const EMPLOYEE_LOGIN_FAILED = 'No permission login';

    //Change password
    const EMAIL_INCORRECT = 'Email does not exist.';
    const ADMIN_EMAIL_OR_USERNAME_INCORRECT = 'Email or username is not of admin.';
    const SENDMAIL_SUCCESS = 'Check your inbox, we have sent a code to reset password.';
    const SENDMAIL_ADMIN_SUCCESS = 'Please check your email to get new password!';
    const CODE_EXPIRED = 'This password reset token is invalid.';
    const RESET_PASSWORD_SUCCESS = 'Password reset was successful';
    const CODE_INCORRECT = 'The code is incorrect.';
    //Employee to company
    const SHOW_EMPLOYEE_TO_COMPANY = 'Information of company';
    const LIST_COMPANY_OF_EMPLOYEE = 'List companies of employee';
    const INVALID_ID = 'Invalid id';
    const EMPLOYEE_NOT_FOUND = 'Employee not found';
    const CHECK_EMPLOYEE_AGAIN = 'Please check that only 1 territory of 1 company is assigned to 1 employee';
    const CHECK_CUSTOMER_AGAIN = 'Please check that 1 company is assigned to 1 customer';
    const UNASSIGN_JOBS_BEFORE = 'Please unassign related jobs before doing deletion.';
    const DELETE_SUCCESS = 'Delete information successfully';
    const DELETE_FAILED = 'Delete unsuccessfully';
    const LIST_JOB_EMPLOYEE = 'List jobs of employees';
    const LIST_STORE_EMPLOYEE = 'List stores of employees';
    //Direction
    const LIST_DIRECTION = 'List Cardinal Direction';
    const DELETED_DIRECTION_SUCCESS = 'Successfully deleted the direction';
    const DELETED_DIRECTION_FAILED = 'Directional delete failed';
    //Location
    const LIST_CITY = 'List City';
    //Material
    const LIST_MATERIAL = 'List material';
    //Photo_Elevation
    const LIST_PHOTO_ELEVATION = 'List Photo Elevation';
    //Store style
    const LIST_STORETYPE = 'List Store type';
    const DELETED_STORETYPE_SUCCESS = 'Successfully delete the store type';
    const DELETED_STORETYPE_FAILED = 'Store type delete failed ';
    //Time of date
    const LIST_TIMEOFDATE = 'List time of day';
    //Climate region
    const LIST_CLIMATE = 'List Climate region';
    //Weather
    const LIST_WEATHER = 'List weather';
    //Job
    const SHOW_JOB = 'Information of job';
    const CHECK_JOB_AGAIN = 'Please check that only 1 territory of 1 company is assigned to 1 job';
    const JOB_NAME_DUPLICATED = 'Job name already exists.';
    const NO_EMPLOYEE = 'There are no employees in the territory';
    const NO_STORE = 'There are no stores in the territory';
    const SELECT_EMPLOYEE = 'Please add employee to selected companies and territories first';
    const SELECT_STORE = 'Please add store to selected companies and territories first';
    const CREATED_JOB_SUCCESS = 'Create job successfully';
    const STORE_MUST_NEW = 'All stores must be NEW';
    const STORE_MUST_DONE = 'All stores must be DONE';
    const UPDATED_INFO_SUCCESS = 'Edit information successfully';
    const NOT_ALLOW = 'Not allow update status';
    const LIST_STORE_JOB = 'List stores of job';
    const LIST_PHOTO_JOB = 'List photos of job';
    const STORE_JOB_DONE = 'Store of the job was done. Please try again.';
    const NOT_FOUND_JOB = 'Not found job';
    const NOT_FOUND_DATA1 = 'Not found data 1';
    const DATA_INVALID = 'Invalid data';
    const DATA_EMPTY = 'Empty data';
    const UPLOAD_TEST_DATA_SUCCESS = 'Upload test data';
    const API_THROTTLE = 'Server is busy. Please wait in 5 minutes for the synchronization'; // Server đang bận bạn vui lòng thực hiện đồng bộ lại trong 5-10p tiếp theo
    const STORE_LINKED_TO_PLAN_ERROR = 'Not allow update store linked to running job';

    
    public static function checkJobAgain(){
        return [
            'status' => 'Failed',
            'message' => Exception::CHECK_JOB_AGAIN,
            'error_code' =>"CHECK_JOB_AGAIN",
        ];
    }
    public static function noEmployee(){
        return [
            'status' => 'Failed',
            'message' => Exception::NO_EMPLOYEE,
            'error_code' =>"NO_EMPLOYEE",
        ];
    }
    public static function noStore(){
        return [
            'status' => 'Failed',
            'message' => Exception::NO_STORE,
            'error_code' =>"NO_STORE",
        ];
    }
    public static function storeJobDone(){
        return [
            'status' => 'Failed',
            'message' => Exception::STORE_JOB_DONE,
            'error_code' => "STORE_DONE"
        ];
    }
    public static function notFoundJob(){
        return [
            'status' => 'Failed',
            'message' => Exception::NOT_FOUND_JOB,
            'error_code' => "NOT_FOUND_JOB"
        ];
    }
    public static function notFoundData(){
        return [
            
            'status' => 'Failed',
            'message' => Exception::NOT_FOUND_DATA1,
            'error_code' => "NOT_FOUND_DATA1"
        ];
    }
    public static function dataInvalid(){
        return [
            'status' => 'Failed',
            'message' => Exception::DATA_INVALID,
            'error_code' => "DATA_INVALID"
        ];
    }
    public static function selectEmployee(){
        return [
            'status' => 'Failed',
            'message' => Exception::SELECT_EMPLOYEE,
            'error_code' => "EMPLOYEE_NOT_FOUND"
        ];
    }
    public static function selectStore(){
        return [
            'status' => 'Failed',
            'message' => Exception::SELECT_STORE,
            'error_code' => "STORE_NOT_FOUND"
        ];
    }
    public static function storeMustNew(){
        return [
            'status' => 'Failed',
            'message' => Exception::STORE_MUST_NEW,
            'error_code' => "PLAN_STATUS_NOT_ALLOW_INACTIVE_TO_NEW"
        ];
    }
    public static function storeMustDone(){
        return [
            'status' => 'Failed',
            'message' => Exception::STORE_MUST_DONE,
            'error_code' => "PLAN_STATUS_NOT_ALLOW_IN_PROGRESS_TO_DONE"
        ];
    }
    
    public static function dataEmpty(){
        return [
            'status' => 'Failed',
            'message' => Exception::DATA_EMPTY,
            'error_code' => "DATA_EMPTY"
        ];
    }

    public static function notAllow(){
        return [
            'status' => 'Failed',
            'message' => Exception::NOT_ALLOW,
            'error_code' => "PLAN_STATUS_NOT_ALLOW"
        ];
    }
    
    public static function job_name_duplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::JOB_NAME_DUPLICATED,
            'error_code' => "JOB_NAME_DUPLICATED"
        ];
    }

    public static function store_linked_to_plan_error(){
        return [
            'status' => 'Failed',
            'message' => Exception::STORE_LINKED_TO_PLAN_ERROR,
            'error_code' => "STORE_LINKED_TO_PLAN_ERROR"
        ];
    }
    
    //Auth
    public static function LoginFailed(){
        return [
            'status' => false,
            'message' => Exception::LOGIN_FAILED,
            'error_code' => "LOGIN_FAILED"
        ];
    }
    public static function CustomerLoginFailed(){
        return [
            'status' => false,
            'message' => Exception::CUSTOMER_LOGIN_FAILED,
            'error_code' => "USER_CUSTOMER_OR_ADMIN"
        ];
    }
    
    public static function EmployeeLoginFailed(){
        return [
            'status' => false,
            'message' => Exception::EMPLOYEE_LOGIN_FAILED,
            'error_code' => "USER_EMPLOYEE"
        ];
    }
    //ChangePassword
    public static function emailAminIncorrect(){
        return [
            'status' => 'Failed',
            'message' => Exception::ADMIN_EMAIL_OR_USERNAME_INCORRECT,
            'error_code' => "EMAIL_OR_USERNAME_INCORRECT"
        ];
    }
    public static function codeIncorrect(){
        return [
            'status' => 'Failed',
            'message' =>Exception::CODE_INCORRECT,
            'error_code' => "CODE_INCORRECT"
        ];
    }
    public static function emailIncorrect(){
        return [
            'status' => 'Failed',
            'message' => Exception::EMAIL_INCORRECT,
            'error_code' => "EMAIL_INCORRECT"
        ];
    }
    public static function codeExpired(){
        return [
            'status' => 'Failed',
            'message' => Exception::CODE_EXPIRED,
            'error_code' => "CODE_EXPIRED"
        ];
    }
    //Company
    public static function companyDuplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::ADD_COMPANY_FAILED_NAME,
            'error_code' => "COMPANY_NAME_DUPLICATED"
        ];
    }
    public static function emailCompanyDuplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::ADD_COMPANY_FAILED_EMAIL,
            'error_code' => "COMPANY_EMAIL_DUPLICATED"
        ];
    }
    //Employee to company
    public static function invalid_id(){
        return [
            'status' => 'Failed',
            'message' => Exception::INVALID_ID,
            'error_code' => "INVALID_ID"
        ];
    }
    public static function employeeNotFound(){
        return [
            'status' => 'Failed',
            'message' => Exception::EMPLOYEE_NOT_FOUND,
            'error_code' => "EMPLOYEE_NOT_FOUND"
        ];
    }
    public static function checkEmployee(){
        return [
            'status' => 'Failed',
            'message' => Exception::CHECK_EMPLOYEE_AGAIN,
            'error_code' => "CHECK_EMPLOYEE"
        ];
    }
    public static function checkCustomer(){
        return [
            'status' => 'Failed',
            'message' => Exception::PERMISSION,
            'error_code' => "CHECK_CUSTOMER"
        ];
    }
    public static function unassignJobBefore(){
        return [
            'status' => 'Failed',
            'message' => Exception::UNASSIGN_JOBS_BEFORE,
            'error_code' => "UNASSIGN_JOBS_BEFORE"
        ];
    }
    public static function deleteFailed(){
        return [
            'status' => 'Failed',
            'message' => Exception::DELETE_FAILED,
            'error_code' => "DELETE_FAILED"
        ];
    }
    //Employee
    public static function usernameDuplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::USERNAME_DUPLICATED,
            'error_code' => "USERNAME_DUPLICATED"
        ];
    }
    public static function emailDuplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::EMAIL_DUPLICATED,
            'error_code' => "EMAIL_DUPLICATED"
        ];
    }
    public static function fileUploadFailed(){
        return [
            'status' => 'Failed',
            'message' => Exception::UPDATED_FILE_FAILED,
            'error_code' => "FILE_UPLOAD_FAILED"
        ];
    }
    public static function passwordCreatedBefore(){
        return [
            'status' => 'Failed',
            'message' => Exception::PASSWORD_CREATEDBEFORE,
            'error_code' => "PASSWORD_CREATEDBEFORE"
        ];
    }
    public static function currentPasswordIncorrect(){
        return [
            'status' => 'Failed',
            'message' => Exception::Current_password_incorrect,
            'error_code' => "CURRENT_PASSWORD_INCORRECT"
        ];
    }
    public static function logoutFailed(){
        return [
            'status' => 'Failed',
            'message' => Exception::Current_password_incorrect,
            'error_code' => "CURRENT_PASSWORD_INCORRECT"
        ];
    }
    //Photo_job
    public static function createdPhotoFailed(){
        return [
                'status' => 'Failed',
                'message' => Exception::CREATED_PHOTO_FAILED,
                'error_code' => "CREATED_PHOTO_FAILED"
            ];
    }

    public static function noPlanActual(){
        return [
            'status' => 'Failed',
            'message' => Exception::no_plan_actual,
            'error_code' => "NO_PLAN_ACTUAL"
        ];
    }
    public static function dataJsonInvalid(){
        return [
            'status' => 'Failed',
            'message' => Exception::data_json_invalid,
            'error_code' => "DATA_JSON_INVALID"
        ];
    }
    public static function uploadedPhotoFileFailed(){
        return [
            'status' => 'Failed',
            'message' => Exception::UPDATED_FILEPHOTO_FAILED,
            'error_code' => "UPDATED_FILEPHOTO_FAILED"
        ];
    }
    //Plan
    public static function exportFailed(){
        return [
            'status' => 'Failed',
            'message' => Exception::EXPORT_FAILED,
            'error_code' => "EXPORT_FAILED"
        ];
    }
    //store
    public static function addStoreFilePhotoFailed(){
        return [
            'status' => 'Failed',
            'message' => Exception::ADD_STORE_FILE_PHOTO_FAILED,
            'error_code' => "ADD_STORE_FILE_PHOTO_FAILED"
        ];
    }
    public static function addStoreFileFailed(){
        return [
            'status' => 'Failed',
            'message' => Exception::ADD_STORE_FILE_FAILED,
            'error_code' => "ADD_STORE_FILE_FAILED"
        ];
    }
    public static function storeNameDuplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::UPDATE_STORE_FAILED,
            'error_code' => "STORE_NAME_DUPLICATED"
        ];
    }
    //territory
    public static function territoryNameDuplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::TERRITORYNAME_DUPLICATED,
            'error_code' => "TERRITORYNAME_DUPLICATED"
        ];
    }
    public static function territoryDuplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::TERRITORY_DUPLICATED,
            'error_code' => "TERRITORY_DUPLICATED"
        ];
    }
    public static function companytDuplicated(){
        return [
            'status' => 'Failed',
            'message' => Exception::COMPANY_DUPLICATED,
            'error_code' => "COMPANY_DUPLICATED"
        ];
    }
    public static function deletedTerritoryFailed(){
        return [
            'status' => 'Failed',
            'message' => Exception::DELETED_TERRITORY_FAILED,
            'error_code' => "DELETED_TERRITORY_FAILED"
        ];
    }
}
