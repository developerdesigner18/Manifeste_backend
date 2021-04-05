<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register','UserController@register');
Route::post('login','UserController@login');

Route::post('SendEmail','UserController@SendEmail');

Route::get('email_verification','UserController@email_verification');

Route::post('resend_email_verification','UserController@resend_email_verification');

Route::post('Forgot_LinkEmail','ForgotPasswordController@Forgot_LinkEmail');
Route::get('forgotpassword_verification','ForgotPasswordController@forgotpassword_verification');
Route::post('reset_password','ForgotPasswordController@reset_password');

Route::group(['middleware' => ['jwt.verify']], function() { 

    Route::post('manifestation_insert','MenifestationController@manifestation_insert');
    Route::get('manifestation_details/{id}','MenifestationController@manifestation_details');
    
});

