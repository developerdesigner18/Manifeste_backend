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

use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if($validator->fails())
        {
            return response()->json([$validator->errors()]);
        }
        else
        {
            $credentials = $request->only('login_email', 'password');
            // echo $credentials['login_email'];
            // die();
            $query=DB::table('users')->where(['login_email'=>$credentials['login_email'],'is_varified'=>'1'])->get();
                if(count($query)>0){
                    try {
                            if (! $token =JWTAuth::attempt($credentials)) {
                                return response()->json(['error' => 'invalid_credentials'], 400);
                            }else{
                                $user=DB::table('users')->where('login_email','=',$credentials['login_email'])->first();
                            }
                        }
                    catch (JWTException $e) {
                        return response()->json(['error' => 'could_not_create_token'], 500);
                    }
                    return response()->json(['success'=>'true','user'=>$user,'token'=>'Bearer '.$token]);
                }else{
                    return response()->json(['success'=>'false','messege'=>'login failed..first you have to verified your email']);
                }
            
        }
    }

    public function register(Request $request)
    {   
        //common fields in both table
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|max:255',
            'country' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'login_email' => 'required|string|email|max:255|unique:users',
            'confirm_login_email' => 'required|string|email|max:255|same:login_email',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
        ]);

        if($validator->fails())
        {
            return response()->json([$validator->errors()]);
        }
        else
        {
            //common fields in both table
            $user_type=$request->user_type;
            if($user_type==1 || $user_type==2){
                $user_type=$request->user_type;
            }else{
                return response()->json(['success'=>0,'message'=>'user type must be either 1 or either 2']); 
            }
            $country=$request->country;
            $province=$request->province;
            $first_name=$request->first_name;
            $last_name=$request->last_name;
            $login_email=$request->login_email;
            $confirm_login_email=$request->confirm_login_email;
            $password=$request->password;
            $confirm_password=$request->confirm_password;
            //common fields end

            //foundation fields
            $foundation_name=$request->foundation_name;
            $address=$request->address;
            $foundation_phone=$request->foundation_phone;
            $foundation_website=$request->foundation_website;
            $foundation_email=$request->foundation_email;
            $manifestation_phone=$request->manifestation_phone;
            $manifestation_email=$request->manifestation_email;
            
            if($user_type==1)
            {
                $user=new User;
                    $user->user_type = $user_type;
                    $user->foundation_name =$foundation_name;
                    $user->address=$address;
                    $user->country=$country;
                    $user->province=$province;
                    $user->foundation_phone=$foundation_phone;
                    $user->foundation_website=$foundation_website;
                    $user->foundation_email=$foundation_email;
                    $user->first_name=$first_name;
                    $user->last_name=$last_name;
                    $user->manifestation_phone=$manifestation_phone;
                    $user->manifestation_email=$manifestation_email;
                    $user->login_email=$login_email;
                    $user->password=Hash::make($password);
                    $token = JWTAuth::fromUser($user);
            }else{
                $user=new User;
                    $user->user_type=$user_type;
                    $user->country=$country;
                    $user->province=$province;
                    $user->first_name=$first_name;
                    $user->last_name=$last_name;
                    $user->login_email=$login_email;
                    $user->password=Hash::make($password);   
            }
            if($user->save())
            {
                $code=str::random(16);
                $link="http://localhost:8000/api/email_verification?email=$login_email&code=$code";
                $data=array('email'=>$login_email,'link'=>$link);

                Mail::send('otp_mail',$data, function($request) use ($login_email){
                $request->to($login_email)->subject('Email Verification');
                });

                $user_code=DB::table('users')->where(['login_email'=>$login_email])->update(['verificarion_code'=>$code]);
                if($user_code==1)
                {
                    return response()->json(['success'=>1,'message'=>'successfully registered..please check your email for activate your account.']);  
                }
                else
                {
                    return response()->json(['success'=>0,'message'=>'registration failed.']);  
                }
                //$token = 'Bearer '.JWTAuth::fromUser($user);
                //return response()->json(['success'=>1,'user'=>$user,'token'=>$token]);
            }
            else
            {
                return response()->json(['success'=>0,'message'=>'registration failed.']); 
            }   
        }
         //$user_type=$request->user_type;
        // if($user_type==""){
        //     return response()->json(['success'=>0,'message'=>'user type is required..']);
        // }else{
        //     $user_type=$request->user_type;
        // }
        // $country=$request->country;
        // if($country==""){
        //     return response()->json(['success'=>0,'message'=>'country is required..']);
        // }else{
        //     $country=$request->country;
        // }
        // $province=$request->province;
        // if($province==""){
        //     return response()->json(['success'=>0,'message'=>'province is required..']);
        // }else{
        //     $province=$request->province;
        // }
        // $first_name=$request->first_name;
        // if($first_name==""){
        //     return response()->json(['success'=>0,'message'=>'first name is required..']);
        // }else{
        //     $first_name=$request->first_name;
        // }
        // $last_name=$request->last_name;
        // if($last_name==""){
        //     return response()->json(['success'=>0,'message'=>'last name is required..']);
        // }else{
        //     $last_name=$request->last_name;
        // }
        // $login_email=$request->login_email;
        // if($login_email==""){
        //     return response()->json(['success'=>0,'message'=>'email id is required..']);
        // }else{
        //     $login_email=$request->login_email;
        // }
        // $confirm_login_email=$request->confirm_login_email;
        // if($confirm_login_email==""){
        //     return response()->json(['success'=>0,'message'=>'confirm email id is required..']);
        // }elseif($confirm_login_email != $login_email)
        // {
        //     return response()->json(['success'=>0,'message'=>'emails are not matched..']);
        // }else{
        //     $confirm_login_email=$request->confirm_login_email;
        // }
        // $password=$request->password;
        // if($password==""){
        //     return response()->json(['success'=>0,'message'=>'password is required..']);
        // }
        // else{
        //     $password=$request->password;
        // }
        // $confirm_password=$request->confirm_password;
        // if($confirm_password==""){
        //     return response()->json(['success'=>0,'message'=>'confirm password is required..']);
        // }elseif($confirm_password != $password)
        // {
        //     return response()->json(['success'=>0,'message'=>'passwords are not matched..']);
        // }else{
        //     $confirm_password=$request->confirm_password;
        // }
    }
    public function email_verification(Request $request)
    {
        //dd($request->all());
        $login_email=$request->email; 
        $code=$request->code;
        $check=DB::table('users')->where(['login_email'=>$login_email,'verificarion_code'=>$code])->get();
        if(count($check)>0)
        {
            $code=str::random(16);
            $user=DB::table('users')->where('login_email','=',$login_email)->first();
            $varified=DB::table('users')->where(['login_email'=>$login_email])->update(['verificarion_code'=>$code,'is_varified'=>'1']);
           
            if($user->user_type==1)
            {
                return redirect("http://localhost:4200/user-landing");

            }
            else{
                return redirect("http://localhost:4200/user-landing2");

            }
        }
   
        else{
            return response()->json(['success'=>0,'message'=>'email not varified']);
        }
    }

    // public function SendEmail(Request $request)
    // {
    //     $login_email=$request->login_email;
    //     $code=str::random(16);
    //     $link="http://localhost:8000/api/email_verification?email=$login_email&code=$code";
    //     $data=array('email'=>$login_email,'link'=>$link);

    //     Mail::send('otp_mail',$data, function($request) use ($login_email){
    //     $request->to($login_email)->subject('Email Verification');
    //     });
    //     $code_store=new OtpVerification;
    //     $code_store->login_email=$login_email;
    //     $code_store->code=$code;
    //     if($code_store->save())
    //     {
    //         return response()->json(['success'=>1,'message'=>'email sent']);
    //     }
    //     else
    //     {
    //         return response()->json(['success'=>0,'message'=>'something wrong']);
    //     }  
    // }
    
        public function resend_email_verification(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'login_email' => 'required|string|email|max:255'
            ]);
    
            if($validator->fails())
            {
                return response()->json([$validator->errors()]);
            }
            else
            {
                $login_email=$request->login_email;
                $code=str::random(16);
                $link="http://localhost:8000/api/email_verification?email=$login_email&code=$code";
                $data=array('email'=>$login_email,'link'=>$link);

                Mail::send('otp_mail',$data, function($request) use ($login_email){
                $request->to($login_email)->subject('Email Verification');
                });

                $user_code=DB::table('users')->where(['login_email'=>$login_email])->update(['verificarion_code'=>$code]);
                if($user_code==1)
                {
                    return response()->json(['success'=>1,'message'=>'successfully mail send..please check your email for activate your account.']);  
                }
                else
                {
                    return response()->json(['success'=>0,'message'=>'registration failed.']);  
                }
            }
        }
}
