<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Exceptions\Exception;
use App\Http\Resources\EmployeeResource;
use App\Role;
use Hash;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','loginmobile']]);
    }
    /**
    * @SWG\Post(
    *         path="/api/login",
    *         tags={"ADMIN/AUTH"},
    *         description="Login",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               required={"username","password"},
    *               @SWG\Property(property="username", type="string", example="string",),
    *               @SWG\Property(property="password", type="string", example="string")),
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
    public function login(Request $request)
    {
        $input = $request->only('username', 'password');

        $token = null;
        if (!$token = JWTAuth::attempt($input)) {
            return response()->json(
                Exception::LoginFailed()
            ,401);
        }
        $auth = \Auth::user();
        if($auth->role == 'EMPLOYEE') {
            return response()->json(
                Exception::EmployeeLoginFailed()
            ,401);
        }
        // $credentials = $request->only('username', 'password');


        // $user = Employee::where('username', $credentials['username'])->firstOrFail();
        // if (!Hash::check($credentials['password'], $user->password)) {  
        //     return response()->json(['error' => 'invalid_credentials'], 422);
        // }
        // if ($user->active === 0) {  
        //     return response()->json(['error' => 'inactive_user'], 403);
        // }
        // try {   
        //     $customClaims = ['username' => $user->username]; // Here you can pass user data on claims  
        //     $token = JWTAuth::fromUser($user, $customClaims);
        // } catch (JWTException $e) {   
        //     return response()->json(['error' => 'auth_error'], 500);
        // }

        // if($auth->is_active == 0){
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Tài khoản của bạn không tồn tại',
        //     ], 401);
        // }
        // $user = [
        //     'first_name'=>$auth->first_name,
        //     'last_name'=>$auth->last_name,
        //     'username'=> $auth->username,
        //     'phone_number'=>$auth->phone_number,
        //     'email'=>$auth->email,
        //     'avatar'=>$auth->avatar,
        //     'role'=>$auth->role,
        //     'active'=>$auth->is_active,
        // ];
        return response()->json([
            'status' => true,
            'access_type' => 'bearer',
            'token' => $token,
            'sid' => $auth->sid,
            'first_name'=>$auth->first_name,
            'last_name'=>$auth->last_name,
            'username'=> $auth->username,
            'phone_number'=>$auth->phone_number,
            'email'=>$auth->email,
            'avatar'=>$auth->avatar,
            'role'=>$auth->role,
        ]);
    }
    /**
    * @SWG\Post(
    *         path="/api/mobile/login",
    *         tags={"MOBILE/AUTH"},
    *         description="Login",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               @SWG\Property(property="username", type="string", example="string",),
    *               @SWG\Property(property="password", type="string", example="string")),
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
    public function loginmobile(Request $request)
    {
        $input = $request->only('username', 'password');

        $token = null;
        if (!$token = JWTAuth::attempt($input)) {
            return response()->json(
                Exception::LoginFailed()
            ,401);
        }
        $auth = \Auth::user();
        if($auth->role == 'CUSTOMER' || $auth->role == 'ADMIN'){
            return response()->json(
                Exception::CustomerLoginFailed()
            ,401);
        }
        return response()->json([
            'status' => true,
            'access_type' => 'bearer',
            'token' => $token,
            'sid' => $auth->sid,
            'first_name'=>$auth->first_name,
            'last_name'=>$auth->last_name,
            'username'=> $auth->username,
            'phone_number'=>$auth->phone_number,
            'email'=>$auth->email,
            'avatar'=>$auth->avatar,
            'role'=>$auth->role,
        ]);
    }

    // public function logout(Request $request)
    // {
        
    //     $this->validate($request, [
    //         'token' => 'required'
    //     ]);
        
    //     try {
    //         JWTAuth::invalidate($request->token);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'User logged out successfully'
    //         ]);
    //     } catch (JWTException $exception) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Sorry, the user cannot be logged out'
    //         ], 500);
    //     }
    // }
}