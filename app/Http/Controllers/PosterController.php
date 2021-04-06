<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Poster;
use JWTAuth;
use DB;
use Str;

class PosterController extends Controller
{
    public function poster_insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posterimage' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([$validator->errors()]);
        }
        else
        { 
            if(!empty($request->posterimage))
            {
                $path = public_path().'/images/Posters/';
                $image = $request->posterimage;
                $filename = str_replace(' ', '_',strtolower(time().$image->getClientOriginalName()));
                $image->move($path, $filename);
                $file = $filename;
            }
            $posterimage=$file;
            $shapeid=$request->shapeid;
            $description=$request->description;
            $color=$request->color;
            // $data=array(
            //     'posterimage'=>$posterimage,
            //     'shapeid'=>$shapeid,
            //     'description'=>$description,
            //     'color'=>$color
            // );
            $Poster = new Poster;
            $Poster->posterimage = $posterimage;
            $Poster->shapeid = $shapeid;
            $Poster->description = $description;
            $Poster->color = $color;

            if($Poster->save())
            {
                    return response()->json(['success'=>1,'Poster'=>$Poster]);
            }
            else
            {
                    return response()->json(['success'=>0,'message'=>'data not inserted.']); 
            }
        }
    }
    
    public function poster_details(Request $request)
    {
        $id=$request->id;
        $data = Poster::where('id',$id)->first();
        if($data)
        {
                return response()->json(['success'=>1,'Poster'=>$data]);
        }
        else
        {
                return response()->json(['success'=>0,'message'=>'data not found.']); 
        }
    }

    public function poster_delete(Request $request)
    {
        $id=$request->id; 
        $data = Poster::where('id',$id)->delete();
        if($data)
        {
            return response()->json(['success'=>1,'message'=>'data deleted successfully.']);
        }
        else
        {
            return response()->json(['success'=>0,'message'=>'data not deleted.']); 
        }
    }

    public function poster_update(Request $request)
    { 
        $id = $request->id;
        $shapeid=$request->shapeid;
        $description=$request->description;
        $color=$request->color;
        $posterimage = $request->file('posterimage');
        if( $posterimage != "" )
        {
            $path = public_path().'/images/Posters/';
            $image = $request->posterimage;
            $filename = str_replace(' ', '_',strtolower(time().$image->getClientOriginalName()));
            $image->move($path, $filename);
            $file = $filename;
            
            $data=array(
                'posterimage'=>$file,
                'shapeid'=>$shapeid,
                'description'=>$description,
                'color'=>$color
            );
        }
        else
        {
            $data=array(
                'shapeid'=>$shapeid,
                'description'=>$description,
                'color'=>$color
            );
        }
        $poster = Poster::where('id',$id)->update($data);
        if($poster)
        {
            return response()->json(['success'=>1,'message'=>'data updated.']);
        }
        else
        {
            return response()->json(['success'=>0,'message'=>'data not updated.']); 
        }
    }

    public function show_all_posters()
    {
        $data = Poster::get();
        if( count($data) >0 )
        {
            return response()->json(['success'=>1,'Poster'=> $data]);
        }
        else
        {
            return response()->json(['success'=>0,'message'=>'data not found.']); 
        }
    }
}
