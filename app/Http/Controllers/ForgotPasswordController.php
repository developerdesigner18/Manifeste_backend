<?php

namespace App\Http\Controllers;

use App\User;
use App\Manifestation;
use App\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use DB;
use Mail;
use Str;


class ForgotPasswordController extends Controller
{
    public function Forgot_LinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_email' => 'required|string|email'
        ]);

        if($validator->fails())
        {
            return response()->json([$validator->errors()]);
        }
        $login_email=$request->login_email;
        
        $check=DB::table('users')->where('login_email','=',$login_email)->get();
        if(count($check)>0)
        {
            $code=str::random(16);
            $forgotlink="http://localhost:8000/api/forgotpassword_verification?email=$login_email&code=$code";
            $data=array('email'=>$login_email,'forgotlink'=>$forgotlink);

            Mail::send('password_mail',$data, function($request) use ($login_email){
            $request->to($login_email)->subject('Forgot Password');
            });
            $forgot_code=DB::table('users')->where(['login_email'=>$login_email])->update(['forgotpass_code'=>$code]);
            if($forgot_code==1)
            {
                return response()->json(['success'=>1,'message'=>'successfully registered..please check your email for reset the password.']);  
            }
            else
            {
                return response()->json(['success'=>0,'message'=>'email not found.']);
            }
        }
        else
        {
            return response()->json(['success'=>0,'message'=>'not matched']); 
        }
    }

    public function forgotpassword_verification(Request $request)
    {
        $login_email=$request->email; 
        $code=$request->code;
        $check=DB::table('users')->where(['login_email'=>$login_email,'forgotpass_code'=>$code])->get();
        if(count($check)>0)
        {
            $code=str::random(16);
            $user=DB::table('users')->where('login_email','=',$login_email)->get();
            $varified=DB::table('users')->where(['login_email'=>$login_email])->update(['forgotpass_code'=>$code]);
            //return response()->json(['success'=>1,'message'=>'email verified']);
            //$token = 'Bearer '.JWTAuth::fromUser($user);
            //return response()->json(['success'=>1,'user'=>$user,'token'=>$token]);
            return response()->json(['success'=>0,'message'=>'verified']);
        }
        else{
            return response()->json(['success'=>0,'message'=>'not varified']);
        }
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_email' => 'required|string|email|max:255',
            'newpassword' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:newpassword',
        ]);

        if($validator->fails())
        {
            return response()->json([$validator->errors()]);
        }
        else
        {
            $login_email=$request->login_email;

            $data=array(
                'password'=>Hash::make($request->newpassword)
            );
            $query=User::where('login_email',$login_email)->update($data);
            if($query)
            {
                return response()->json(['success'=>1,'message'=>'password updated']);
            }
            else
            {
                return response()->json(['success'=>0,'message'=>'not updated']);
            }
        }
    }
}
