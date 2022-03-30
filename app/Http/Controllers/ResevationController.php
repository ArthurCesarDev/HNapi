<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Area;
use App\Models\AreaDisableDay;
use App\Models\Reservation;
use App\Models\Unit;

class ResevationController extends Controller
{
    public function getReservations(){
        $array = ['error' => '', 'list' =>[]];

        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $areas = Area::where('allowed', 1)->get();

        foreach($areas as $area) {
           
            $dayList = explode(',', $area['days']);

            $dayGroups = [];


            //Adicionando o primeiro dia
            $lastDay = intVal(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            // adicionando dias releventes

            foreach($dayList as $day) {
           
                if(intVal($day) != $lastDay+1) {
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];

                }

                $lastDay = intVal($day);
            }


            //adicionando o ultimo dia

            // JUTANDO AS DATAS


            $dayGroups[] = $daysHelper[end($dayList)];
            $dates = '';
            $close = 0;
            foreach($dayGroups as $group) {
                if($close === 0) {
                  
                    $dates .=$group;
                }else {
                    $dates .= '-'.$group.',';
                }

                $close  = 1 - $close;
            }

            $dates = explode(',', $dates);
            array_pop($dates);

            //adicionando o time

            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));


            foreach($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' '.$start.' ás '.$end; 
            }

            $array['list'][] =  [

                'id' => $area['id'],
                'cover' => asset('storage/'.$area['cover']),
                'title' => $area['title'],
                'dates' => $dates 

            ];

        }

        

        return $array;
    }
    public function getDisableDates($id){
        $array = ['error' => '','list' =>[]];

        //Dias disabled padrão

        $area = Area::find($id);
        if($area){
        $disableDays = AreaDisableDay::where("id_area", $id)->get();
            foreach($disableDays as $disabledDay){
            $array['list'][] = $disabledDay['day'];
        }

        // Dias disabled atráves do allowed
        $allowedDays = explode(',',$area['days']);
        $offDays = [];
        for($q=0; $q<7; $q++){
            if(!in_array($q , $allowedDays)){
                $offDays[] = $q;
            }
        }

         //Listar od Dias proibidos + 3 meses pra frente
         $start = time();
         $end   = strtotime('+3 months');
         $current = $start;
         

for($current = $start;$current < $end; $current = strtotime('+1 day',$current)){
        $wd = date('w',$current);
    if(in_array($wd,$offDays)){
        $array['list'][] = date('Y-m-d',$current);
        }
    }

    } else {
            $array['error'] = "Area não existe";
            return $array;   
        }
        return $array;
    }


    public function getTimes($id, Request $request)
    {
        $array = ['error' => '', 'list' => []];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);

        if(!$validator->fails()) {

            $date = $request->input('date');

            $area = Area::find($id);
            if($area) {

                $can = true;

                // Verificar se o dia está desabilitado
                $existingDisabledDay = AreaDisableDay::where('id_area', $id)
                    ->where('day', $date)
                    ->count();
                if($existingDisabledDay > 0) {
                    $can = false;
                }

                // Verificar se o dia esta permitido
                $allowedDays = explode(',', $area['days']);
                $weekDay = date('w', strtotime($date));
                if(!in_array($weekDay, $allowedDays)) {
                    $can = false;
                }

                if($can) {
                    $start = strtotime($area['start_time']);
                    $end = strtotime($area['end_time']);

                    $times = [];

                    for(
                        $lastTime = $start;
                        $lastTime < $end;
                        $lastTime = strtotime('+1 hour', $lastTime)
                    ) {
                        $times[] = $lastTime;
                    }

                    $timeList = [];
                    foreach($times as $time) {
                        $timeList[] = [
                            'id' => date('H:i:s', $time),
                            'title' => date('H:i', $time).' - '.date('H:i', strtotime('+1 hour', $time))
                        ];
                    }

                    // Removendo reservas
                    $reservations = Reservation::where('id_area', $id)
                        ->whereBetween('reservation_date', [
                            $date.' 00:00:00',
                            $date.' 23:59:59'
                        ])
                        ->get();
                    
                    $toRemove = [];
                    foreach($reservations as $reservation) {
                        $time = date('H:i:s', strtotime($reservation['reservation_date']));
                        $toRemove[] = $time;
                    }

                    foreach($timeList as $timeItem) {
                        if(!in_array($timeItem['id'], $toRemove)) {
                            $array['list'][] = $timeItem;
                        }
                    }
                    
                }

            } else {
                $array['error'] = 'Area não existe!';
                return $array;
            }

        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;

}

public function setReservations($id, Request $request){
    $array = ['error' => ''];

    $validator = Validator::make($request->all(),[
      'date' => 'required|date_format:Y-m-d',
      'time' => 'required|date_format:H:i:s',
      'property' => 'required'
    ]);
     if(!$validator->fails()) {
    
        $date = $request->input('date');
        $time = $request->input('time');
        $property = $request->input('property');


        $unit = Unit::find($property);
        $area = Area::find($id);

        if($unit && $area) {
            
            $can = true.

            $weekDay = date('w', strtotime($date));

            // verificar se está dentro da disponibilidade padrão

            $allowedDays = explode(',', $area['days']);
            if(!in_array($weekDay, $allowedDays )) {
                $can =  false;
            } else {
                $start = strtotime($area['start_time']);
                $end = strtotime('-1 hour', strtotime($area['end_time']));
                $revtime = strtotime($time);
                if($revtime < $start || $revtime > $end ) {
                    $can =  false;
                }
            }

            // verificar se está fora dos dias marcados

            $existingDisabledDay = AreaDisableDay::where('id_area', $id)
            ->Where('day', $date)
            ->count();
            if($existingDisabledDay > 0 ) {
                $can = false;
            }

            // verificar se não existe outra reserva no mesmo dia e hora

            $existingReservations = Reservation::where('id_area', $id)
            ->where('reservation_date', $date.' '.$time)
            ->count();
            if($existingReservations > 0) {
                $can = false;
            }

            if($can) {

                $newRersevation =  new Reservation();
                $newRersevation->id_unit = $property;
                $newRersevation->id_area = $id;
                $newRersevation->reservation_date = $date.' '.$time;
                $newRersevation->save();


            } else {
                $array['error'] =  'Reserva não permitida neste dia/Horário';
                return $array;
            }


        } else {
            $array['error'] = 'Dados Incorretos';
            return $array;
        }

     } else {
         $array['error'] = $validator->errors()->first();
         return $array;
     }

    return $array;
  }
  // MINHAS RESERVAS

  public function getMyReservations(Request $request) {
    $array = ['error' => '', 'list' => []];

    $property = $request->input('property');
    if($property) {
        $unit = Unit::find($property);
        if($unit) {

            $reservations = Reservation::where('id_unit', $property)
            ->orderBy('reservation_date', 'DESC')
            ->get();

            foreach($reservations as $reservation) {
                $area = Area::find($reservation['id_area']);

                $daterev = date('d/m/Y H:i', strtotime($reservation['reservation_date']));
                $aftertime = date('H:i', strtotime('1+ hour',strtotime($reservation['reservation_date'])));
                $daterev .= ' â '.$aftertime;

                $array['list'][] = [

                 'id' => $reservation['id'],
                 'id_area' => $reservation['id_area'],
                 'title' => $area['title'],
                 'cover' =>asset('storage/'.$area['cover']),
                 'datereserved' => $daterev
                ];

            }

        } else {
            $array['error'] = 'Propiedade necessaria';
            return $array;

        }

    } else {
        $array['error'] = 'Propiedade necessaria';
        return $array;
    }

    return $array;
  }

  public function delReservations($id){
      $array = ['error' => ''];

      $user =  auth()->user();
      $reservation = Reservation::find($id);

      if($reservation) {

        $unit = Unit::where('id', $reservation['id_unit'])
        ->where('id_owner', $user['id'])
        ->count();

        if($unit > 0) {
            Reservation::find($id)->delete();
        }else {
            $array['error'] = 'Esta Reserva não e sua';
            return $array;
        }

      } else {
          $array['error'] = 'Reserva inexistente';
          return $array;
      }


      return $array;
  }

}
