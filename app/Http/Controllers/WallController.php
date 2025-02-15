<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Wall;
use App\Models\WallLike;

class WallController extends Controller
{
    public function getAll(){
        $array = ['error' => '', 'list' => []];

          
        $user = auth()->user();


        $walls =  Wall::all();

        foreach($walls as $wallkey => $wallValue) {
            $walls[$wallkey]['likes'] = 0;
            $walls[$wallkey]['liked'] = false;

            $likes = WallLike::where('id_wall', $wallValue['id'])->count();
            $walls[$wallkey]['likes'] = $likes;


            $meLikes = WallLike::where('id_wall', $wallValue['id'])
            ->where('id_user', $user['id'])
            ->count();

            if($meLikes > 0) {

                $walls[$wallkey]['liked'] = true;
            }

        }

        $array ['list'] = $walls;

        return $array;
    }

      // REQUESIÇÃO LIKE

    public function like($id) {
        $array = ['error' =>''];

        
        $user = auth()->user();

        $meLikes = WallLike::where('id_wall', $id)
        ->where('id_user', $user['id'])
        ->count();

        if($meLikes > 0) {
            //REMOVER O LIKE
            WallLike::where('id_wall', $id)
            ->where('id_user', $user['id'])
            ->delete();

            $array['liked'] = false;

        }else{
           // ADICIONAR O LIKE

           $newLike = new WallLike();
           $newLike->id_wall = $id;
           $newLike->id_user = $user['id'];
           $newLike->save();
           $array['liked'] = true;
        }

        $array['likes'] = WallLike::where('id_wall', $id)->count(); 
        return $array;

    }
}
