<?php

class HelloModel extends Eloquent {

    static public function dailyTopTen( $dailytopten){
        $dailytopcount = 0;
        $count=0;
        $count1=0;

        foreach($dailytopten as $dailytop) {
            if( $dailytop->value1 == 0 && $dailytopcount > 40){
                $year[$dailytopcount] = "";
                $value1[$dailytopcount] = "";
            }
            else{
                $year[$dailytopcount] = $dailytop->year;
                $value1[$dailytopcount] = $dailytop->value1;
            }
            $dailytopcount++;
        }

        $row=0;
        while( $count < 10 ) {
            while( $count1 < 8 ) {
                if( $count1 == 0 || $count1 == 3 || $count1 == 4 || $count1 == 5 || $count1 == 6 ) {
                    $toptenValue[$row][] = $value1[$count + $count1*10];
                    $toptenValue[$row][] = $year[$count + $count1*10];
                }
                $count1++;
            }
            $row++;
            $count++;
            $count1=0;
        }
        return $toptenValue;
    }

    static public function date_format( $observation_time ) {
        $date_format = date("l jS",strtotime($observation_time));
        $date_format .= " of ".date("F Y",strtotime($observation_time));
        $date_format .= " at ".date("h:i A",strtotime($observation_time));
        return $date_format;
    }

    static public function date_time_observation( $observations_rev, $result_36_hour_forecasts){

        foreach($observations_rev as $observation){
            $observation_minutes = date("i",strtotime($observation->observation_time));
            $adjusted_hours_shift = 0;
            if( $observation_minutes >= 30) $adjusted_hours_shift=$adjusted_hours_shift+1;
            $date_time_observation[]=date("M d \ng A", strtotime($adjusted_hours_shift.' hours',strtotime($observation->observation_time)));
        }

        foreach($result_36_hour_forecasts as $result_36_hour_forecast) {
            $date_time_observation[]=date("M d \ng A", strtotime($result_36_hour_forecast->datetime_predict));
        }

        return $date_time_observation;
    }

    static public function determineHtml( ){
        $shttp = 'http';
        if( isset($_SERVER['HTTPS']) ){
            if( $_SERVER['HTTPS'] == 'on' ) $shttp = "https";
        }
        $shttp = $shttp.'://'.$_SERVER['SERVER_NAME'];
        return $shttp;
    }

    static public function get10DayForecast( $stations_id){
        return DB::select("call get_10_day_forecast($stations_id);");
    }

    static public function get36HourForecast( $stations_id){
        return DB::select("call get_36_hour_forecast($stations_id);");
    }

    static public function getActiveStations(){
        return DB::select("call active_stations();");
    }

    static public function getData(){
        return 'Hello this is a test';
    }

    static public function getDailyTopTen(){
        return  DB::select("call calldailytopten();");
    }

    static public function getHoursBack(){
        if( Input::has('hours_back') ) {
            $hours_back = Input::get('hours_back');
        }
        else{
            $hours_back=48;
        }

        if( $hours_back == 0 ) $hours_back=48;

        return $hours_back;
    }

    static public function getObservation( $stations_id, $order ){
        $hours_back = 0;
        if( $order == "asc") $hours_back = HelloModel::getHoursBack();
        return DB::select("call list_conditions($hours_back,'$order',$stations_id);");
    }

    static public function getStation(){
        if( Input::has('stations_id') ) {
            $stations_id = Input::get('stations_id');
        }
        else{
            $stations_id=1;
        }

        if( Input::has('station_id_map') ) {
            if( Input::get('station_id_map') <> "" ) $stations_id = Input::get('station_id_map');
        }

        return $stations_id;
    }

    static public function getStationHistoricError($stations_id){
        return DB::select("call get_station_historic_error($stations_id);");
    }


    static public function getPastStats(){
        return DB::select("call past_stats();");
    }

    static public function hoursBack( $passed_hours_back){
        $hours_back=0;
        if( isset( $passed_hours_back ) ) $hours_back = $passed_hours_back;
        if( $hours_back == 0 ) $hours_back = 48;
        return $hours_back;
    }

    static public function humidity( $observations_rev, $result_36_hour_forecasts, $passed_hours_back){

        $unique_value = '_'.$_SERVER['REMOTE_ADDR'].'-'.$_SERVER['REMOTE_PORT'];

        $hours_back = HelloModel::hoursBack($passed_hours_back);
        $interval = round(($hours_back+36)/12);


        foreach($observations_rev as $observation){
            $observation->relative_humidity = str_replace("%","",$observation->relative_humidity);
            $int_val = intval($observation->relative_humidity);
            if( $int_val < 0) $humidity[] =0;
            if( $int_val > 100) $humidity[] = 0;
            if( $int_val <= 100 && $int_val >= 0) $humidity[]=$int_val;
        }

        foreach($result_36_hour_forecasts as $result_36_hour_forecast) {
            $humidity[]= $result_36_hour_forecast->humidity;
        }

        $date_time_observation = HelloModel::date_time_observation($observations_rev, $result_36_hour_forecasts);

        require_once ('jpgraph/Graph1.php');
        $Two_Graphs_Humidity = new Custom_Graphs();
        $Two_Graphs_Humidity->interval = $interval;
        $Two_Graphs_Humidity->graph_data1 = $humidity;
        $Two_Graphs_Humidity->graph_xdata = $date_time_observation;
        $Two_Graphs_Humidity->y1_title = "Humidity %";
        $Two_Graphs_Humidity->color1 = "darkgreen";
        $Two_Graphs_Humidity->y2_title = "";
        $Two_Graphs_Humidity->x_title = "Date Time";
        $Two_Graphs_Humidity->title = "Humidity";
        $Two_Graphs_Humidity->back_ticks = $passed_hours_back;

        $Two_Graphs_Humidity->file_name="images/cache/graph2".$unique_value.".png";
        $Two_Graphs_Humidity->graph_two( );

        return $Two_Graphs_Humidity->file_name;
    }


    static public function locationName ( $stations_id,  $distinct_stations){
        $location_name = "";
        foreach($distinct_stations as $distinct_station){
            if( $stations_id == $distinct_station->stations_id ) $location_name = $distinct_station->location;
        }
        return $location_name;
    }

    static public function rain( $observations_rev, $result_36_hour_forecasts, $passed_hours_back){
        $unique_value = '_'.$_SERVER['REMOTE_ADDR'].'-'.$_SERVER['REMOTE_PORT'];

        $hours_back = HelloModel::hoursBack($passed_hours_back);
        $interval = round(($hours_back+36)/12);

        foreach($observations_rev as $observation){
            $int_val = $observation->precip_1hr_in;
            if( $int_val < 0) $rain[] =0;
            if( $int_val > 100) $rain[] = 0;
            if( $int_val <= 100 && $int_val >= 0) $rain[]=$int_val;
        }

        foreach($result_36_hour_forecasts as $result_36_hour_forecast) {
            $rain[] = $result_36_hour_forecast->qpf;
        }

        $date_time_observation = HelloModel::date_time_observation($observations_rev, $result_36_hour_forecasts);

        require_once ('jpgraph/Graph1.php');
        $Two_Graphs_Rain = new Custom_Graphs();
        $Two_Graphs_Rain->interval = $interval;
        $Two_Graphs_Rain->graph_data1 = $rain;
        $Two_Graphs_Rain->graph_xdata = $date_time_observation;
        $Two_Graphs_Rain->y1_title = "Rain in";
        $Two_Graphs_Rain->color1 = "darkgreen";
        $Two_Graphs_Rain->y2_title = "";
        $Two_Graphs_Rain->x_title = "Date Time";
        $Two_Graphs_Rain->title = "Rain";
        $Two_Graphs_Rain->back_ticks = $passed_hours_back;
        $Two_Graphs_Rain->file_name="images/cache/graph_rain".$unique_value.".png";
        $Two_Graphs_Rain->graph_two( );

        return $Two_Graphs_Rain->file_name;
    }

    static public function stationElement ( $photos){
        $station_element = "";
        foreach ($photos as $photo) {
            $station_element .= json_encode($photo);
            $station_element .= ",";
        }
        return $station_element;
    }

    static public function stationForecastError($historic_daily_errors, $result_10_day_forecasts){
        $x=1;
        $forecast_error[0]="<p>Accuracy";
        $forecast_error[0].="<p>High";
        $forecast_error[0].="<p>Low";

        foreach($historic_daily_errors as $historic_daily_error){

            while($x < $historic_daily_error->days_back ) {
                $forecast_error[$x]="<p> </p>";
                $forecast_error_hi[$x]="NA";
                $forecast_error_lo[$x]="NA";
                $forecast_error_hi_std[$x]="NA";
                $forecast_error_lo_std[$x]="NA";
                $x++;
            }

            $forecast_error[$x]="<p> </p>";
            $forecast_error_hi[$x]=number_format($historic_daily_error->hi_temp_error)."+-".number_format($historic_daily_error->hi_std);
            $forecast_error_lo[$x]=number_format($historic_daily_error->lo_temp_error)."+-".number_format($historic_daily_error->lo_std);
            $forecast_error_hi_std[$x]=number_format($historic_daily_error->hi_std);
            $forecast_error_lo_std[$x]=number_format($historic_daily_error->lo_std);
            $x++;
        }

        //***Just in in case the last set of days are missing***
        while($x <= 10){
            $forecast_error[$x]="<p> </p>";
            $forecast_error_hi[$x]="NA";
            $forecast_error_lo[$x]="NA";
            $forecast_error_hi_std[$x]="NA";
            $forecast_error_lo_std[$x]="NA";
            $x++;
        }


        $x=0;
        foreach($result_10_day_forecasts as $result_10_day_forecast){
            $forecast_when[$x]= date('l',strtotime($result_10_day_forecast->date_predict));
            $forecast_when[$x].="<p>".date('M j',strtotime($result_10_day_forecast->date_predict));

            $forecast_detail[$x]="";
            $forecast_detail[$x].="<p>".$result_10_day_forecast->condition_w;
            $forecast_detail[$x].="<p><img src=".$result_10_day_forecast->icon_url." height=\"42\" width=\"42\">";

            $forecast_temperature[$x]=$result_10_day_forecast->high_f;
            if( $x == 0 ) $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><br /></div>";
            if( $x  > 0 ) {
                if( $forecast_error_hi[$x] == "NA"){
                    $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><font color=#ffff00>NA</font></div>";
                }
                else{
                    $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><font color=#ffff00>".($result_10_day_forecast->high_f+$forecast_error_hi[$x])."</font></div>";
                }
            }
            $forecast_temperature[$x].="<br>".$result_10_day_forecast->low_f;
            if( $x  > 0 ) {
                if( $forecast_error_hi[$x] == "NA"){
                    $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><font color=#ffff00>NA</font></div>";
                }
                else{
                    $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><font color=#ffff00>".($result_10_day_forecast->low_f+$forecast_error_lo[$x])."</font></div>";
                }
            }
            if( $x  > 0 ) $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><font color=#ffff00></font></div>";
            $x++;
        }
        return array(  $forecast_when, $forecast_detail, $forecast_temperature);
    }

    static public function statisticsAverageArray ( $average_stats){
        $avg_stat_row = 0;
        $avg_stat[$avg_stat_row][] = 'Title';
        $avg_stat[$avg_stat_row][] = 'Avg. High Temp F';
        $avg_stat[$avg_stat_row][] = 'Avg. Low Temp F';
        $avg_stat[$avg_stat_row][] = 'Avg. Rain in.';
        $avg_stat[$avg_stat_row][] = 'Avg. Snow in.';
        $avg_stat[$avg_stat_row][] = 'Avg. Snow Depth in.';
        $avg_stat[$avg_stat_row][] = 'Max High Temp';
        $avg_stat[$avg_stat_row][] = 'Min High Temp';
        $avg_stat[$avg_stat_row][] = 'Max Low Temp';
        $avg_stat[$avg_stat_row][] = 'Min Low Temp';
        $avg_stat[$avg_stat_row][] = 'Max Rain';
        $avg_stat[$avg_stat_row][] = 'Max Snow';
        $avg_stat[$avg_stat_row][] = 'Max Snow Depth';
        $avg_stat_row++;

        foreach($average_stats as $average_stat){
            $avg_stat[$avg_stat_row][] = $average_stat->Start_Year.'-'.($average_stat->End_Year-1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->average_high1,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->average_low,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->average_precipitation,2);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->average_snow_fall,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->average_snow_depth,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->maximum_high,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->minimum_high,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->maximum_low,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->minimum_low,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->maximum_precipitation,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->maximum_snow_fall,1);
            $avg_stat[$avg_stat_row][] = number_format($average_stat->maximum_snow_depth,1);
            $avg_stat_row++;
        }
        return $avg_stat;
    }


    static public function temp_f( $observations_rev, $result_36_hour_forecasts, $passed_hours_back){

        $unique_value = '_'.$_SERVER['REMOTE_ADDR'].'-'.$_SERVER['REMOTE_PORT'];

        $hours_back = HelloModel::hoursBack($passed_hours_back);
        $interval = round(($hours_back+36)/12);

        foreach($observations_rev as $observation){
            $t_f[] = $observation->temp_f;
        }

        foreach($result_36_hour_forecasts as $result_36_hour_forecast) {
            $t_f[] = $result_36_hour_forecast->temp_f;
        }

        $date_time_observation = HelloModel::date_time_observation($observations_rev, $result_36_hour_forecasts);

        require_once ('jpgraph/Graph1.php');
        $Two_Graphs_Temp = new Custom_Graphs();
        $Two_Graphs_Temp->interval = $interval;
        $Two_Graphs_Temp->graph_data1 = $t_f;
        $Two_Graphs_Temp->graph_xdata = $date_time_observation;
        $Two_Graphs_Temp->y1_title = "Temperature F";
        $Two_Graphs_Temp->color1 = "red";
        $Two_Graphs_Temp->y2_title = "";
        $Two_Graphs_Temp->x_title = "Date Time";
        $Two_Graphs_Temp->title = "Temperature";
        $Two_Graphs_Temp->file_name="images/cache/graph_temp".$unique_value.".png";
        $Two_Graphs_Temp->back_ticks = $passed_hours_back;
        $Two_Graphs_Temp->graph_two( );

        return $Two_Graphs_Temp->file_name;
    }

    static public function wind_mph( $observations_rev, $result_36_hour_forecasts, $passed_hours_back){

        $unique_value = '_'.$_SERVER['REMOTE_ADDR'].'-'.$_SERVER['REMOTE_PORT'];

        $hours_back = HelloModel::hoursBack($passed_hours_back);
        $interval = round(($hours_back+36)/12);

        foreach($observations_rev as $observation){
            $int_val = intval($observation->wind_mph);
            if( $int_val < 0) $w_mph[] =0;
            if( $int_val > 100) $w_mph[] = 0;
            if( $int_val <= 100 && $int_val >= 0) $w_mph[]=$int_val;
        }

        foreach($result_36_hour_forecasts as $result_36_hour_forecast) {
            $w_mph[] = $result_36_hour_forecast->wspd;
        }

        $date_time_observation = HelloModel::date_time_observation($observations_rev, $result_36_hour_forecasts);


        require_once ('jpgraph/Graph1.php');
        $Two_Graphs = new Custom_Graphs();
        $Two_Graphs->interval = $interval;
        $Two_Graphs->graph_data1 = $w_mph;
        $Two_Graphs->graph_xdata = $date_time_observation;
        $Two_Graphs->y1_title = "Wind Speed mph";
        $Two_Graphs->color1 = "blue";
        $Two_Graphs->y2_title = "";
        $Two_Graphs->x_title = "Date Time";
        $Two_Graphs->title = "Wind Speed";
        $Two_Graphs->file_name="images/cache/graph_wind".$unique_value.".png";
        $Two_Graphs->back_ticks = $passed_hours_back;
        $Two_Graphs->graph_two( );

        return $Two_Graphs->file_name;
    }

}

