<?php

namespace App\Http\Controllers;

use App\Mail\ForgetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use ErrorException;
use Illuminate\Support\Facades\Hash;
use Exception;
use HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Login method
     */
    public function login(Request $r){

        $r->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        try{

            if (Auth::attempt($r->only("email", "password"))) {

                $currentUser = Auth::user();
                $token = $currentUser->createToken('Access Token')->accessToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login Successfully",
                    "data" => [
                        "user" => $currentUser,
                        "token" => $token
                    ]
                ], 200);

            }
            
        } catch (Exception $e) {

            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 401);

        }

        return response()->json([
                "status" => false,
                "message" => "Email or Password is invalid",
                "data" => null
        ], 401);

    } // End Login Method

    /**
     * Register Method
     */
    public function register(Request $r){

        $r->validate([
            "name" => "required|max:255",
            "email" => "required|email|max:255",
            "password" => "required|confirmed|max:255",
        ]);

        try{

            $hashed_password = Hash::make($r->password);
            // $encoded_password = base64_encode($hashed_password);

            $user = User::create([
                "name" => $r->name,
                "email" => $r->email,
                "password" => $hashed_password,
            ]);

            $token = $user->createToken("Access Token")->accessToken;

            return response()->json([
                "status" => true,
                "message" => "New Account Successfully Created",
                "data" => [
                    "user" => $user,
                    "token" => $token,
                ]
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 401);

        }

    } // End Register Method

    /**
     * Request Reset Password Method
     */
    public function request_forget_password(Request $r){

       try {

        $r->validate([
            "email" => "required|email"
        ]);

        $email_exist = User::where('email', $r->email);

        if($email_exist->doesntExist()){
            return response()->json([
                "status" => false,
                "message" => $r->email." is invalid or not exist",
                "data" => null
            ], 404);
        }

        $token = base64_encode(random_bytes(152));

        while (DB::table('password_resets')->where('token', $token)->get()->first()) {
            $token = base64_encode(random_bytes(189));
        }

        $token = str_replace("/", "", $token);
        $token = str_replace("+", "", $token);
        
        // Send mail to user
        Mail::to($r->email)->send(new ForgetPasswordMail($token));

        DB::table("password_resets")->insert([
            "email" => $email_exist->get()->first()->email,
            "token" => $token,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Sending Forgot/Reset Password Successfully, please check your email",
            "data" => [
                "email" => $email_exist->get()->first()->email,
                "token" => $token,
            ],
        ], 201);

       } catch (Exception $e) {

            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 401);

       }

    } // Request Reset Password Method End

    /**
     * Reset/Change Password Method
     */
    public function reset_password(Request $r){
        try {

            $r->validate([
                "email" => "required|email",
                "password" => "required|confirmed",
                "token" => "required",
            ]);

            $email = DB::table('password_resets')->where('email', $r->emai)->get()->first();
            $token = DB::table('password_resets')->where('token', $r->token)->get()->first();
            
            if($email == null){
                
                return response()->json([
                    "status" => false,
                    "message" => "Email not found",
                    "data" => null    
                ], 404);
                
            }
            
            if($token == null){
                
                return response()->json([
                    "status" => false,
                    "message" => "Token not found",
                    "data" => null    
                ], 404);
                
            }
            
            $user = DB::table('users')->where('email', $r->email)->update([
                "password" => Hash::make($r->password),
            ]);

            return response()->json([
                "status" => true,
                "message" => "Change Password Successfully",
                "data" => [
                    "user" => $user
                ],
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 401);

       }

    } // Reset/Change Password Method End

}
