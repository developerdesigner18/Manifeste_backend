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

class MenifestationController extends Controller
{
    public function manifestation_insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'manifestation_hold' => 'required|date_format:Y-m-d',
            'start_time' => 'required',
            'end_time' => 'required',
            'type' => 'required|string',
            'logo' => 'required',
            'description' => 'required|string',
        ]);

        if($validator->fails())
        {
            return response()->json([$validator->errors()]);
        }
        else
        {
            $file = ''; 
            if(!empty($request->logo))
            {
                $path = public_path().'/images/ManifestationLogo/';
                $image = $request->logo;
                $filename = str_replace(' ', '_',strtolower(time().$image->getClientOriginalName()));
                $image->move($path, $filename);
                $file = $filename;
            }
            
            $title=$request->title;
            $address=$request->address;
            $country=$request->country;
            $province=$request->province;
            $manifestation_hold=$request->manifestation_hold;
            $start_time=$request->start_time;
            $end_time=$request->end_time;
            $type=$request->type;
            $description=$request->description;

            // $data=array(
            //     'title'=>$title,
            //     'address'=>$address,
            //     'country'=>$country,
            //     'province'=>$province,
            //     'manifestation_hold'=>$manifestation_hold,
            //     'start_time'=>$start_time,
            //     'end_time'=>$end_time,
            //     'type'=>$type,
            //     'logo'=>$filename,
            //     'description'=>$description
            // );
            $manifestation=new Manifestation;
            $manifestation->title=$title;
            $manifestation->address=$address;
            $manifestation->country=$country;
            $manifestation->province=$province;
            $manifestation->manifestation_hold=$manifestation_hold;
            $manifestation->start_time=$start_time;
            $manifestation->end_time=$end_time;
            $manifestation->type=$type;
            $manifestation->logo=$file;
            $manifestation->description=$description;
            if($manifestation->save())
            {
                    return response()->json(['success'=>1,'manifestation'=>$manifestation]);
            }
            else
            {
                    return response()->json(['success'=>0,'message'=>'data not inserted.']); 
            }
        }
    }
    public function manifestation_details()
    {
        $data = Manifestation::get();
        if( count($data) >0 )
        {
                return response()->json(['success'=>1,'manifestation'=>$data]);
        }
        else
        {
                return response()->json(['success'=>0,'message'=>'data not found.']); 
        }
    }
}
