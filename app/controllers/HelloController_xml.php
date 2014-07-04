<?php

class Hello_xml extends BaseController {

    public $restful = true;

    public function hello(){

        if( Input::has('stations_id') ) {
            $stations_id = Input::get('stations_id');
        }
        else{
            $stations_id=1;
        }

        if( Input::has('station_id_map') ) {
            if( Input::get('station_id_map') <> "" ) $stations_id = Input::get('station_id_map');
        }

        //**Gathers the last records that are less than 2 hours old
        $kmsp_temperature = DB::select("call list_conditions(0,'desc',$stations_id);");

        if( Input::has('hours_back') ) {
            $hours_back = Input::get('hours_back');
        }
        else{
            $hours_back=48;
        }

        if( $hours_back == 0 ) $hours_back=48;

         //**Gather the last x hours back **
        $distinct_stations = DB::select("call active_stations();");

        $kmsp_temperature_rev = DB::select("call list_conditions($hours_back,'',$stations_id);");

        $get_10_day_forecast = DB::select("call get_10_day_forecast($stations_id);");

        $get_36_hour_forecast = DB::select("call get_36_hour_forecast($stations_id);");

        $get_historic_daily_error = DB::select("call get_historic_error($stations_id,10,10);");

        $get_average_stats = DB::select("call past_stats();");

        return View::make('hello_xml.index')
            ->with('observations',$kmsp_temperature)
            ->with('observations_rev',$kmsp_temperature_rev)
            ->with('distinct_stations',$distinct_stations)
            ->with('observation_maps',$distinct_stations)
            ->with('result_10_day_forecasts',$get_10_day_forecast)
            ->with('result_36_hour_forecasts',$get_36_hour_forecast)
            ->with('historic_daily_errors',$get_historic_daily_error)
            ->with('passed_hours_back',$hours_back)
            ->with('stations_id',$stations_id)
            ->with('average_stats',$get_average_stats)
            ->with('title','Welcome to Twin Cities Weather')
            ->with('wind_direction','0');// works
    }


}