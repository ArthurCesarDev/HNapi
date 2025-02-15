<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


use App\Models\Warning;
use App\Models\Unit;

class WarningController extends Controller
{
     // criar uma occorencia
    public function getMyWarnings(Request $request) {
        $array = ['error' => '' ];

        $property = $request->input('property');  

        if($property) {

            $user = auth()->user();
            $unit = Unit::where('id', $property)
            ->where('id_owner', $user['id'])
            ->count();


            if($unit > 0  ) { 
                     
                $warnings = Warning::where('id_unit', $property)
                ->orderBy('datecreat', 'DESC')
                ->orderBy('id', 'DESC')
                ->get();

                foreach($warnings as $warnKey => $warnValue) {
                    $warnings[$warnKey]['datecreat'] = date('d/m/Y', strtotime($warnValue['datecreat']));
                    $photoList = [];
                    $photos = explode(',', $warnValue['photos']);
                    foreach($photos as $photo) {
                        if(!empty($photos)) {
                             $photoList[] = asset('storage/'.$photo);
                        }
                    }


                    $warnings[$warnKey]['photos'] = $photoList;
                }

                     
                $array['list'] = $warnings;

            }else {
                $array['error'] = 'Esta Unidade não e a sua!';
            }

        } else {
            $array['error'] = 'A propriedade é Necessária';
        }


        return $array;

    }

    // inserir fotos na ocorrencia
    public  function addWarningFile(Request $request){
        $array = ['error' => '' ];

        $validator = Validator::make($request->all(),[
            'photo' => 'required|file|mimes:jpg,png'
        ]);

        if(!$validator->fails()){
         
            $file = $request->file('photo')->store('public');

            $array ['photo'] = asset(Storage::url($file));
            

        }else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }


        return $array;

    }

    // fazer livro de ocorrencia

    public function setWarning(Request $request){
        $array = ['error' => '' ];

        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'property' => 'required'
        ]);

        if(!$validator->fails()){

            $title = $request->input('title');
            $property =  $request->input('property');
            $list = $request->input('list');


            $newWarn = new Warning();
            $newWarn->id_unit = $property;
            $newWarn->title = $title;
            $newWarn->status = 'IN_REVIEW';
            $newWarn->datecreat = date('Y-m-d');
            
            if($list && is_array($list)){
                $photos = [];
                foreach($list as $listItem) {
                   $url = explode('/', $listItem);
                   $photos[] = end($url); 
                }
                $newWarn->photos = implode(',', $photos);
            } else {
                $newWarn->photos = '';
            }

            $newWarn->save();



        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }


}
