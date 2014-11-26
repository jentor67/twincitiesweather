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


    $shttp = HelloModel::determineHtml();

?>

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


    <div id="title_space" style="font-size: 36px;">Twin Cities Weather</div>


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
                                                    'url'=>$shttp."?station_id_map=".$distinct_station1->stations_id));
                    ?>
                @endforeach
            </select>
            <?php
                $station_element = HelloModel::stationElement($photos);
            ?>
        {{ Form::close() }}


    </div>

    <?php
        $unique_value = '_'.$_SERVER['REMOTE_ADDR'].'-'.$_SERVER['REMOTE_PORT'];
    ?>

    @foreach($observations as $observation)
        <div id="station_info" style="font-size: 10px; color: black;">
            <div style="font-size: 12px;">Weather at {{ HelloModel::locationName($stations_id, $distinct_stations) }}</div>observed {{ HelloModel::date_format(($observation->observation_time)) }}
            <ul>
                <li>Temperature: {{ $observation->temp_f }} F feels like: {{$observation->feelslike_f }} F</li>
                <li>Weather: {{ $observation->weather }} with {{ $observation->visibility_mi}} miles visibility</li>
                <li>Wind Speed: {{ $observation->wind_mph }} mph going {{ $observation->wind_dir }}</li>
                <li>Pressure: {{ $observation->pressure_in }} in</li>
                <li>Rain today: {{ $observation->precip_today_in }} in</li>
            </ul>
            <p><img hspace=30 src= {{ $observation->icon_url }} height=40 width=40 >
        </div>
    @endforeach


    <?php $avg_stat = HelloModel::statisticsAverageArray($average_stats); ?>



    <div id="average_stats" style="font-size: 12px; color: black;">
        <div style="font-size: 12px;">Twin Cities Past Statistics for Today</div>

        <table>
            <?php $avg_stat_column = 0; ?>
            @while( $avg_stat_column < 6 )
                <?php $avg_stat_row = 0; ?>
                <tr>
                    @while($avg_stat_row < 5)
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
                        @while($avg_stat_row < 5)
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

        <div id="analysis">
            {{ Form::open(array('url' => 'analysis/gather')) }}
            {{ Form::submit('Go to Historic Analysis') }}
            {{ Form::close() }}
        </div>
    </div>


    <?php
        $hours_back = HelloModel::hoursBack($passed_hours_back);
        $interval = round(($hours_back+36)/12);
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


    <div id="graph_group">

        <div id="graph_div">
            <a class="fancybox" rel="group" href=<?php echo HelloModel::temp_f($observations_rev, $result_36_hour_forecasts, $passed_hours_back); ?> >
                <img style='border:3px solid #FF3300' src=<?php echo HelloModel::temp_f($observations_rev, $result_36_hour_forecasts, $passed_hours_back); ?> alt="" id="image2" />
            </a>
        </div>

        <br>

        <button class="hidegraphs">More Graphs Toggle</button>

        <div class="graphs_show" style="display:none">
            <div id="graph_div">
                <a class="fancybox" rel="group" href=<?php echo HelloModel::rain($observations_rev, $result_36_hour_forecasts, $passed_hours_back); ?> >
                    <img style='border:3px solid #FF3300' src=<?php echo HelloModel::rain($observations_rev, $result_36_hour_forecasts, $passed_hours_back); ?> alt="" id="image2" />
                </a>
            </div>

            <div id="graph_div">
                <a class="fancybox" rel="group" href=<?php echo HelloModel::humidity($observations_rev, $result_36_hour_forecasts, $passed_hours_back); ?> >
                    <img style='border:3px solid #FF3300' src=<?php echo HelloModel::humidity($observations_rev, $result_36_hour_forecasts, $passed_hours_back); ?> alt="" id="image2" />
                </a>
            </div>

            <div id="graph_div">
                <a class="fancybox" rel="group" href=<?php echo HelloModel::wind_mph($observations_rev, $result_36_hour_forecasts, $passed_hours_back); ?> >
                    <img style='border:3px solid #FF3300' src=<?php echo HelloModel::wind_mph($observations_rev, $result_36_hour_forecasts, $passed_hours_back); ?> alt="" id="image2" />
                </a>
            </div>
        </div>

    </div>


    <div id="map_canvas"></div>

@endsection


@section('station_forcast')

    <?php
        list($forecast_when, $forecast_detail, $forecast_temperature) = HelloModel::stationForecastError($historic_daily_errors, $result_10_day_forecasts);
    ?>

    <div id="forecast_box">

        <div id="forecast_title_row">
            <div id="forecast_title">DAY<P>DATE</div>
            <?php
               $max=0;
               if( isset($forecast_when) ) $max= sizeof($forecast_when);
            ?>
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
            <?php
                $max = 0;
                if( isset( $forecast_temperature ) ) $max= sizeof($forecast_temperature);
            ?>
            @for ($i = 0; $i < $max; $i++)
                <div id="forecast_temperature"><?php echo $forecast_temperature[$i] ?></div>
            @endfor
        </div>

        <div id="forecast_detail_row">
            <div id="forecast_detail"><P></div>
            <?php
                $max = 0;
                if( isset($forecast_detail)  ) $max= sizeof($forecast_detail);
            ?>
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
            <img src="images/msptour_logo.png" width="179" height="52">
        </a>
    </div>

    <div id="advertising2">
        <a href="https://www.metrotransit.org/">
            <img src="images/MetroTransitLogo.png" width="179" height="52">
        </a>
    </div>

    <div id="advertising3">
        <a href="http://www.wunderground.com/">
            <img src="images/wunderground.jpg" width="170" height="128">
        </a>
    </div>


@endsection

@section('submit_comments')
    <div id="submit_comments">
        {{ Form::open(array('url' => 'comments', 'method' => 'GET')) }}
        {{ Form::submit('Write A Comment') }}
        {{ Form::close() }}
    </div>

@endsection

@section('dailytopten')

    <?php
        $dailyTopTenValue = HelloModel::dailyTopTen($dailytopten);
        $count=0;
        $count1=0;
    ?>

    <div id="dailytopten">
        <div style="font-size: 12px; color: black">Twin Cities Top 10 records for today</div>
        <table border="1" style="font-size: 9px;">
            <tr>
                <td width="100" align="center">High</td>
                <td width="100" align="center">Low</td>
                <td width="100" align="center">Rain</td>
                <td width="100" align="center">Snow</td>
                <td width="100" align="center">Snow Cov.</td>
            </tr>
        </table>
        <table border="1" style="font-size: 9px;">
            <tr>
                <td width="50" align="center">Temp</td>

                <td width="50">Year</td>

                <td width="50" align="center">Temp</td>

                <td width="50">Year</td>

                <td width="50" align="center">inches</td>

                <td width="50">Year</td>

                <td width="50" align="center">inches</td>

                <td width="50">Year</td>

                <td width="50" align="center">inches</td>

                <td width="50">Year</td>
            </tr>
            @while( $count < 10 )
                <tr>
                    @while( $count1 < 10 )
                        <td width="50" align="center">{{ $dailyTopTenValue[$count][$count1] }}</td>
                        <?php $count1++; ?>
                    @endwhile
                </tr>
                <?php
                   $count++;
                   $count1=0;
                ?>
            @endwhile
        </table>
    </div>

@endsection
