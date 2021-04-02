<?php

namespace App\Http\Controllers;

use App\ChangePassword;
use App\Employee;
use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequests;
use App\Http\Requests\UpdatePasswordRequest;
use App\Mail\ResetPassword;
use App\Mail\ResetPasswordAdmin;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Uuid;

class ChangePasswordController extends Controller
{
    /**
    * @SWG\Post(
    *         path="/api/resetPassword",
    *         tags={"ADMIN/AUTH"},
    *         description="request reset password",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               required={"email"},
    *               @SWG\Property(property="email", type="string", example="string"),
    *               @SWG\Property(property="username", type="string", example="string"),
    *           ),
    *               
    *      ),
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Response(
    *           response=401,
    *           description="Unauthenticated"
    *         ),
    *         @SWG\Response(
    *           response=400,
    *           description="Bad Request"
    *         ),
    *       security={
    *           {"ApiKeyAuth": {}}
    *       },
    * )
    */
    public function AdminResetPassword(Request $request){
        // If email does not exist
        $employee = Employee::whereEmail($request->email)->whereUsername($request->username)->whereRole('ADMIN')->whereIsActive(1)->first();
        if(!empty($employee)){
            $this->sendMailAdmin($request->email,$request->username);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::SENDMAIL_ADMIN_SUCCESS
            ], Response::HTTP_OK);
        }else {
            return response()->json(
                Exception::emailAminIncorrect(),
                Response::HTTP_NOT_FOUND);
        }
    }
    /**
    * @SWG\Post(
    *         path="/api/mobile/resetPassword",
    *         tags={"MOBILE/AUTH"},
    *         description="request reset password",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               @SWG\Property(property="email", type="string", example="string"),
    *           ),
    *               
    *      ),
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Response(
    *           response=401,
    *           description="Unauthenticated"
    *         ),
    *         @SWG\Response(
    *           response=400,
    *           description="Bad Request"
    *         ),
    *       security={
    *           {"ApiKeyAuth": {}}
    *       },
    * )
    */
    public function sendPasswordResetEmail(Request $request){
        // If email does not exist
        if(!$this->validEmail($request->email)) {
            return response()->json(
                Exception::emailIncorrect(),
                Response::HTTP_NOT_FOUND);
        } else {
            // If email exists
            $this->sendMailMobile($request->email);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::SENDMAIL_SUCCESS
            ], Response::HTTP_OK);            
        }
    }
    public function random_strings_with_num($length_of_string) { 
		$str_result = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
		$str_generate = substr(str_shuffle($str_result), 0, $length_of_string);
		
		//pr($str_generate);
		if(preg_match('(0|1|2|3|4|5|6|7|8|9)', $str_generate) === 0) {
			$str_generate = substr_replace($str_generate, strval(rand(0, 9)), rand(0, $length_of_string - 1), 1);
		}
		return $str_generate;
    }
    public function random_password_with_num($length_of_string) { 
		$str_result = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
		$str_generate = substr(str_shuffle($str_result), 0, $length_of_string);
		
		//pr($str_generate);
		if(preg_match('(0|1|2|3|4|5|6|7|8|9)', $str_generate) === 0) {
			$str_generate = substr_replace($str_generate, strval(rand(0, 9)), rand(0, $length_of_string - 1), 1);
		}
		return $str_generate;
	}
    public function sendMailMobile($email){
        $code = $this->generateCode($email);
        Mail::to($email)->send(new ResetPassword($code));
    }

    public function sendMailAdmin($email,$username){
        $password_random = $this->random_password_with_num(8);
        $employee = Employee::whereEmail($email)->whereUsername($username)->first();
        $data = array();
        $data['password'] = Hash::make($password_random);
        $employee->update($data);
        Mail::to($email)->send(new ResetPasswordAdmin($password_random));
    }

    public function validEmail($email) {
       return !!Employee::where('email', $email)->first();
    }

    public function generateCode($email){
      $isOtherCode = DB::table('change_passwords')->where('email', $email)->first();

      if($isOtherCode) {
        return $isOtherCode->code;
      }
      //su dung code=uuid
      $code = $this->random_strings_with_num(8);
      $this->storeToken($code, $email);
      return $code;
    }

    public function storeToken($code, $email){
        $data['email'] = $email;
        $data['code'] = $code;
        ChangePassword::create($data);
    }
     /**
    * @SWG\Post(
    *         path="/api/mobile/restorePassword",
    *         tags={"MOBILE/AUTH"},
    *         description="send restore password",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               @SWG\Property(property="code", type="string", example="string"),
    *               @SWG\Property(property="password", type="string", example="string"),
    *               @SWG\Property(property="confirm_password", type="string", example="string"),
    *           ),
    *               
    *      ),
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Response(
    *           response=401,
    *           description="Unauthenticated"
    *         ),
    *         @SWG\Response(
    *           response=400,
    *           description="Bad Request"
    *         ),
    *       security={
    *           {"ApiKeyAuth": {}}
    *       },
    * )
    */
    public function resetPassword(ChangePasswordRequests $request) {

        $passwordReset = ChangePassword::where('code', $request->input('code'))->first();

        if(!empty($passwordReset)) {
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(5)->isPast()) {
                $passwordReset->delete();
                return response()->json(
                    Exception::codeExpired(), 
                    Response::HTTP_NOT_FOUND);
            }
            $employee = Employee::where('email', $passwordReset->email)->firstOrFail();
            $employee->update(['password' =>Hash::make($request->input('password'))]);
            $passwordReset->delete();

            $activiti = array();    
            $activiti['description'] = 'Reset password';
            $activiti['subject_type'] = 'App\Employee';
            $activiti['causer_id'] = $employee->id;
            $activiti['causer_sid'] = $employee->sid;
            $activiti['log_name'] = $employee->username;
            Activity::create($activiti);

            return response()->json([
                'status' => 'Success',
                'message' => Exception::RESET_PASSWORD_SUCCESS,
            ]);
        }else {
            return response()->json(
                Exception::codeIncorrect(),400);
        }
        
    } 

    



    // public function passwordResetProcess(UpdatePasswordRequest $request){
    //     return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();
    //   }
  
    //   // Verify if token is valid
    //   private function updatePasswordRow($request){
    //      return DB::table('recover_password')->where([
    //          'email' => $request->email,
    //          'token' => $request->passwordToken,
             
    //      ]);

    //     $change = ChangePassword::orderBy('id','desc')->join('employees', 'recover_password.email', '=', 'employees.email')->get(['recover_password.id','recover_password.email','recover_password.token', 'employees.username AS username']);
    //     return $change;
    //   }
  
    //   // Token not found response
    //   private function tokenNotFoundError() {
    //       return response()->json([
    //         'error' => 'Either your email or token is wrong.'
    //       ],Response::HTTP_UNPROCESSABLE_ENTITY);
    //   }
  
    //   // Reset password
    //   private function resetPassword(Request $request) {
    //       // find email
    //       $userData = Employee::whereEmail($request->email)->first();
    //       // update password
    //       $userData->update([
    //         'password'=>bcrypt($request->password)
    //       ]);
    //       // remove verification data from db
    //       $this->updatePasswordRow($request)->delete();
  
    //       // reset password response
    //       return response()->json([
    //         'data'=>'Password has been updated.'
    //       ],Response::HTTP_CREATED);
    //   } 
}
