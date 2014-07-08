@extends('layout.default')


@section('testing')

<div style="font-size: 12px; color: black" id="map_legend" xmlns="http://www.w3.org/1999/html">
    Map Legend
    <div style="font-size: 8px;">
        <ul>
            <li><div style="color: blue;">
                Wind below 10 mph
            </div></li>
            <li><div style="color: purple;">
                Wind below 20 mph
            </div></li>
            <li><div style="color: red;">
                Wind at or greater than 20 mph
            </div></li>
        </ul>
        Size of arrow changes with wind speed up to 20mph
    </div>
</div>

<?php $location_name = ""; ?>
@foreach($distinct_stations as $distinct_station)
    @if ($stations_id == $distinct_station->stations_id)
        <div id="title_space" style="font-size: 36px;">
            Twin Cities Weather
            <?php $location_name = $distinct_station->location; ?>
        </div>
    @endif
@endforeach


<div id="station_select">
    {{ Form::open(array('url' => '','method' => 'POST')) }}
    {{ Form::submit('Select Location') }}
    <?php
        $station_element="";
        $photos = array();
        $lat_selected=44.96;
        $lng_selected=-93.207;
    ?>
    <select name="stations_id">
        @foreach($distinct_stations as $distinct_station1)
            @if ($distinct_station1->stations_id == $stations_id)
                <option value="{{ $distinct_station1->stations_id }}" selected="selected">{{ $distinct_station1->location }}</option>
                <?php
                    $lat_selected=$distinct_station1->latitude;
                    $lng_selected=$distinct_station1->longitude;
                ?>
            @else
                <option value="{{ $distinct_station1->stations_id }}">{{ $distinct_station1->location }}</option>
            @endif
            <?php
                array_push($photos, array('title'=>$distinct_station1->location,
                                            'lat'=>$distinct_station1->latitude,
                                            'lng'=>$distinct_station1->longitude,
                                            'img'=>"images/".$distinct_station1->station_id.".png",
                                            'url'=>"?station_id_map=".$distinct_station1->stations_id));
            ?>
        @endforeach
    </select>
</div>

<?php
    foreach ($photos as $photo) {
        $station_element .= json_encode($photo);
        $station_element .= ",";
    }
?>
{{ Form::close() }}

<?php
    $unique_value = '_'.$_SERVER['REMOTE_ADDR'].'-'.$_SERVER['REMOTE_PORT'];
?>

@foreach($observations as $observation)
    <?php
        $wind_direction=$observation->wind_degrees;
        $_SESSION['wind_direction'] = $wind_direction;
        $wind_direction_rad=pi()*$wind_direction/180;
        $date_format = date("l jS \of F Y @ h:i A",strtotime($observation->observation_time));
    ?>

    <div id="station_info" style="font-size: 10px; color: black;">
        <div style="font-size: 12px;">Weather at {{ $location_name }}</div>observed {{ $date_format }}
        <ul>
            <li>Temperature: {{ $observation->temp_f }} F feels like: {{$observation->feelslike_f }} F</li>
            <li>Weather: {{ $observation->weather }} with {{ $observation->visibility_mi}} miles visibility</li>
            <li>Wind Speed: {{ $observation->wind_mph }} mph going {{ $observation->wind_dir }}</li>
            @if( $observation->pressure_in > 0 )
                <li>Pressure: {{ $observation->pressure_in }} in</li>
            @endif
            @if( $observation->precip_today_in > 0 )
                <li>Rain today: {{ $observation->precip_today_in }} in</li>
            @endif
        </ul>
        <p><img hspace=30 src= {{ $observation->icon_url }} height=40 width=40 >
    </div>
@endforeach

<?php
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
?>

@foreach($average_stats as $average_stat)
    <?php
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
        $avg_stat_limit = $avg_stat_row;
    ?>
@endforeach
<div id="average_stats" style="font-size: 12px; color: black;">
    <div style="font-size: 12px;">Twin Cities Past Statistics for Today</div>
        <table>
            <?php $avg_stat_column = 0; ?>
            @while( $avg_stat_column < 6 )
                <?php $avg_stat_row = 0; ?>
                <tr>
                    @while($avg_stat_row < $avg_stat_limit)
                        @if( $avg_stat_row == 0 )
                            <td width="100">{{ $avg_stat[$avg_stat_row][$avg_stat_column] }}</td>
                        @else
                            <td width="50" align="center">{{ $avg_stat[$avg_stat_row][$avg_stat_column] }}</td>
                        @endif
                        <?php $avg_stat_row++; ?>
                    @endwhile
                </tr>
                <?php $avg_stat_column++; ?>
            @endwhile
         </table>
        <button class="hidestats">More Stats</button>
        <div class="stats_show" style="display:none">
            <table>
                @while( $avg_stat_column < 13 )
                    <?php $avg_stat_row = 0; ?>
                    <tr>
                        @while($avg_stat_row < $avg_stat_limit)
                            @if( $avg_stat_row == 0 )
                            <td width="100">{{ $avg_stat[$avg_stat_row][$avg_stat_column] }}</td>
                            @else
                            <td width="50" align="center">{{ $avg_stat[$avg_stat_row][$avg_stat_column] }}</td>
                            @endif
                            <?php $avg_stat_row++; ?>
                        @endwhile
                    </tr>
                    <?php $avg_stat_column++; ?>
                @endwhile
            </table>
        </div>
</div>




<?php
    $hours_back=0;
    if( isset( $passed_hours_back ) ) $hours_back = $passed_hours_back;
    if( $hours_back == 0 ) $hours_back = 48;
    $interval = round($hours_back/12);
?>

<div id="back_history">
    {{ Form::open(array('url' => '','method' => 'POST')) }}
    {{ Form::submit('Graph Back History') }}
    {{ Form::select('hours_back', array(
    '12' => '12 Hours back',
    '24' => '1 Day',
    '48' => '2 Days',
    '72' => '3 Days',
    '96' => '4 Days'
    ), $hours_back); }}
    {{ Form::hidden('stations_id',$stations_id) }}
    {{ Form::close() }}
</div>

@foreach($observations_rev as $observation)
    <?php
        $t_f[] = $observation->temp_f;
//        $w_mph[] = $observation->wind_mph;
        $int_val = intval($observation->wind_mph);
        if( $int_val < 0) $w_mph[] =0;
        if( $int_val > 100) $w_mph[] = 0;
        if( $int_val <= 100 && $int_val >= 0) $w_mph[]=$int_val;
        $observation->relative_humidity = str_replace("%","",$observation->relative_humidity);
        $int_val = intval($observation->relative_humidity);
        //$humidity[] = $int_val;
        if( $int_val < 0) $humidity[] =0;
        if( $int_val > 100) $humidity[] = 0;
        if( $int_val <= 100 && $int_val >= 0) $humidity[]=$int_val;
        $observation_minutes = date("i",strtotime($observation->observation_time));
        $adjusted_hours_shift = 0;
        if( $observation_minutes >= 30) $adjusted_hours_shift=$adjusted_hours_shift+1;
        $date_time_observation[]=date("M d \ng A", strtotime($adjusted_hours_shift.' hours',strtotime($observation->observation_time)));
    ?>
@endforeach

@foreach($result_36_hour_forecasts as $result_36_hour_forecast)
    <?php
        $t_f[] =  $result_36_hour_forecast->temp_f;
        $w_mph[] = $result_36_hour_forecast->wspd;
        $humidity[]= $result_36_hour_forecast->humidity;
        $date_time_observation[]=date("M d \ng A", strtotime($result_36_hour_forecast->datetime_predict));
    ?>
@endforeach

<?php $size_of_array= sizeof($t_f) ?>
@if ( $size_of_array > 1  )

        <div id="graph_group">

            <?php
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
            ?>
            <div id="graph_div">
                <a class="fancybox" rel="group" href=<?php echo $Two_Graphs_Temp->file_name; ?> >
                    <img style='border:3px solid #FF3300' src=<?php echo $Two_Graphs_Temp->file_name; ?> alt="" id="image2" />
                </a>
            </div>

            <br>
            <button class="hidegraphs">More Graphs Toggle</button>
            <div class="graphs_show" style="display:none">
                <?php
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
                ?>
                <div id="graph_div">
                    <a class="fancybox" rel="group" href=<?php echo $Two_Graphs_Humidity->file_name; ?> >
                        <img style='border:3px solid #FF3300' src=<?php echo $Two_Graphs_Humidity->file_name; ?> alt="" id="image2" />
                    </a>
                </div>


                <?php
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
                ?>
                <div id="graph_div">
                    <a class="fancybox" rel="group" href=<?php echo $Two_Graphs->file_name; ?> >
                        <img style='border:3px solid #FF3300' src=<?php echo $Two_Graphs->file_name; ?> alt="" id="image2" />
                    </a>
                </div>
            </div>

        </div>
    @endif

    <div id="map_canvas"></div>

@endsection

@section('station_forcast')

    <?php $x=1;
        $forecast_error[0]="<p>Accuracy";
        $forecast_error[0].="<p>High";
        $forecast_error[0].="<p>Low";
    ?>
    @foreach($historic_daily_errors as $historic_daily_error)
        <?php
            $forecast_error[$x]="<p> </p>";
            $forecast_error_hi[$x]=number_format($historic_daily_error->hi_temp_error)."+-".number_format($historic_daily_error->hi_std);
            $forecast_error_lo[$x]=number_format($historic_daily_error->lo_temp_error)."+-".number_format($historic_daily_error->lo_std);
            $forecast_error_hi_std[$x]=number_format($historic_daily_error->hi_std);
            $forecast_error_lo_std[$x]=number_format($historic_daily_error->lo_std);
            $x++;
        ?>
    @endforeach


    <?php $x=0; ?>
    @foreach($result_10_day_forecasts as $result_10_day_forecast)
        <?php
            $forecast_when[$x]= date('l',strtotime($result_10_day_forecast->date_predict));
            $forecast_when[$x].="<p>".date('M j',strtotime($result_10_day_forecast->date_predict));
            $forecast_detail[$x]="";
            $forecast_detail[$x].="<p>".$result_10_day_forecast->condition_w;
            $forecast_detail[$x].="<p><img src=".$result_10_day_forecast->icon_url." height=\"42\" width=\"42\">";
            $forecast_temperature[$x]=$result_10_day_forecast->high_f;
            if( $x == 0 ) $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><font><br /></font></div>";
            if( $x  > 0 ) $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><font color=#ffff00>".($result_10_day_forecast->high_f+$forecast_error_hi[$x])."&plusmn".$forecast_error_hi_std[$x]."</font></div>";
            $forecast_temperature[$x].="<br>".$result_10_day_forecast->low_f;
            if( $x  > 0 ) $forecast_temperature[$x].=" <div class=\"predictedtemperature_show\" style=\"display:none\"><font color=#ffff00>".($result_10_day_forecast->low_f+$forecast_error_lo[$x])."&plusmn".$forecast_error_lo_std[$x]."</font></div>";
            $x++;
        ?>
    @endforeach


    <div id="forecast_box">
        <div id="forecast_title_row">
            <div id="forecast_title">DAY<P>DATE</div>
            <?php $max= sizeof($forecast_when); ?>
            @for ($i = 0; $i < $max; $i++)
               <div id="forecast_title"><?php echo $forecast_when[$i] ?></div>
            @endfor

        </div>
        <div id="forecast_error_row">
            <button class="hidepredictedtemperature" >Temperature accounting for resent errors</button>
            <div class="predictedtemperature_show" style="display:none">The value in yellow is accounting for resent predicted errors.</div>
        </div>

        <div id="forecast_temperature_row">
            <div id="forecast_temperature">HIGH<br><div class="predictedtemperature_show" style="display:none"><br /><br /></div>LOW</div>
            <?php $max= sizeof($forecast_temperature); ?>
            @for ($i = 0; $i < $max; $i++)
                <div id="forecast_temperature"><?php echo $forecast_temperature[$i] ?></div>
            @endfor
        </div>

        <div id="forecast_detail_row">
            <div id="forecast_detail"><P></div>
            <?php $max= sizeof($forecast_detail); ?>
            @for ($i = 0; $i < $max; $i++)
                <div id="forecast_detail"><?php echo $forecast_detail[$i] ?></div>
            @endfor
        </div>
    </div>


    <div id="copy_write" style="font-size: 8px;">
        &copy 2014 twincitiesweather.info All Rights Reserved
    </div>

    <div id="advertising1">
        <a href="http://www.visit-twincities.com/">
            <img src="images/msptour_logo.png" width="199" height="58">
        </a>
    </div>

    <div id="advertising2">
        <a href="https://www.metrotransit.org/">
            <img src="images/MetroTransitLogo.png" width="199" height="58">
        </a>
    </div>

    <div id="advertising3">
        <a href="http://www.wunderground.com/">
            <img src="images/wunderground.jpg" width="170" height="128">
        </a>
    </div>


@endsection

@section('carousel')


@endsection