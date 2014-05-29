<?php

class Hello extends BaseController {

    public $restful = true;

    public function hello(){

        //**Gathers the last records that are less than 2 hours old
        $kmsp_temperature = DB::select("call list_observation(2,'desc');");


        if( Input::has('hours_back') ) {
            $hours_back = Input::get('hours_back');
        }
        else{
            $hours_back=48;
        }

        if( $hours_back == 0 ) $hours_back=48;
        //$hours_back=$hours_back-6;
        //**Gather the last x hours back **
        $hours_shift = date('Z')/3600;
        $kmsp_temperature_rev = DB::select("call list_observation($hours_back+$hours_shift,'');");

        return View::make('hello.index')
            ->with('observations',$kmsp_temperature)
            ->with('observations_rev',$kmsp_temperature_rev)
            ->with('passed_hours_back',$hours_back)
            ->with('title','Welcome to Twin Cities Weather')
            ->with('wind_direction','0');// works
    }


}