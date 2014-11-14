@extends('layout.default')

<?php
   //***gather all sessions
    $observations = Session::get('observations');
    $observations_rev = Session::get('observations_rev');
    $distinct_stations = Session::get('distinct_stations');
    $observation_maps = Session::get('observation_maps');
    $result_10_day_forecasts = Session::get('result_10_day_forecasts');
    $result_36_hour_forecasts = Session::get('result_36_hour_forecasts');
    $historic_daily_errors = Session::get('historic_daily_errors');
    $passed_hours_back = Session::get('passed_hours_back');
    $stations_id = Session::get('stations_id');
    $average_stats = Session::get('average_stats');
    $title = Session::get('title');
    $wind_direction = Session::get('wind_direction');
    $dailytopten = Session::get('dailytopten');







    $shttp = 'http';
    if( isset($_SERVER['HTTPS']) ){
        if( $_SERVER['HTTPS'] == 'on' ) $shttp = "https";
    }
    $shttp = $shttp.'://'.$_SERVER['SERVER_NAME'];


?>





