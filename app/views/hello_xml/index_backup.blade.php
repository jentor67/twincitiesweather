@extends('layout.default')

@section('station_info')
    <?php
  /*      $station_element="";
        $photos = array();
        array_push($photos, array('title'=>"title1",'lat'=>"44.3",'lng'=>"-93.2",'url'=>"http://www.google.com/"));
        array_push($photos, array('title'=>"title2",'lat'=>"43.7",'lng'=>"-94.2",'url'=>"http://www.twincitiesweather.info/"));

        foreach ($photos as $photo) {
            $station_element .= json_encode($photo);
            $station_element .= ",";
        } */
    ?>


@endsection

@section('testing')

    @foreach($distinct_stations as $distinct_station)
        @if ($station_id == $distinct_station->station_id)
            <h1>Twin Cities Weather</h1>
            <?php $location_name = $distinct_station->location; ?>
        @endif
    @endforeach


    {{ Form::open(array('url' => '','method' => 'POST')) }}
    {{ Form::submit('Select Location') }}
    <?php
        $station_element="";
        $photos = array();
    ?>
    <select name="station_id">
        @foreach($distinct_stations as $distinct_station1)
            @if ($distinct_station1->station_id == $station_id)
                <option value="{{ $distinct_station1->station_id }}" selected="selected">{{ $distinct_station1->location }}</option>
            @else
                <option value="{{ $distinct_station1->station_id }}">{{ $distinct_station1->location }}</option>
            @endif
            <?php
                array_push($photos, array('title'=>$distinct_station1->station_id,
                                            'lat'=>$distinct_station1->latitude,
                                            'lng'=>$distinct_station1->longitude,
                                            'img'=>"images/".$distinct_station1->station_id.".png",
                                            'url'=>"?station_id_map=".$distinct_station1->station_id));
            ?>
        @endforeach
    </select>
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

    <?php $first_line = "y"; ?>

    @foreach($observations as $observation)
        @if ($first_line == "y" )
            <?php
                $wind_direction=$observation->wind_degrees;
                $_SESSION['wind_direction'] = $wind_direction;
                $wind_direction_rad=pi()*$wind_direction/180;
                $date_format = date("l jS \of F Y @ h:i A",strtotime($observation->observation_time));
            ?>

            <div style="font-size: 14px;">
                Temperature: {{ $observation->temp_f }} F with Dew Point: {{$observation->dewpoint_f }} F
                <br>
                Weather: {{ $observation->weather }} with {{ $observation->visibility_mi}} miles visibility
                <br>
                Wind Speed: {{ $observation->wind_mph }} (mph) going {{ $observation->wind_dir }}
                <br>
                @if( $observation->pressure_mb > 0 )
                    Pressure: {{ $observation->pressure_mb }} mb
                    <br>
                @endif
                Observation Time: {{ $date_format }}
            </div>

<!--            <div class="image-box">
                <img src="images/arrow.jpg" alt="" width="30" height="200" />
            </div>
            <div style="font-size: 8px;">Observed at: {{ $observation->observation_time }} </div> -->
            <?php $first_line = ""; ?>
        @endif
    @endforeach

    <br>

    <?php
        $hours_back=0;
        if( isset( $passed_hours_back ) ) $hours_back = $passed_hours_back;
        if( $hours_back == 0 ) $hours_back = 48;
        $interval = round($hours_back/12);
    ?>

    {{ Form::open(array('url' => '','method' => 'POST')) }}
    {{ Form::submit('Back Hours') }}
    {{ Form::select('hours_back', array(
    '12' => '12 Hours back',
    '24' => '1 Day',
    '48' => '2 Days',
    '72' => '3 Days',
    '96' => '4 Days'
    ), $hours_back); }}
    {{ Form::hidden('station_id',$station_id) }}
    {{ Form::close() }}
    <br>

    @foreach($observations_rev as $observation)
        <?php
            $t_f[] = $observation->temp_f;
            $w_mph[] = $observation->wind_mph;
            $humidity[] = $observation->relative_humidity;
            $observation_minutes = date("i",strtotime($observation->observation_time));
            $adjusted_hours_shift = 0;
            if( $observation_minutes >= 30) $adjusted_hours_shift=$adjusted_hours_shift+1;
            $date_time_observation[]=date("M d \ng A", strtotime($adjusted_hours_shift.' hours',strtotime($observation->observation_time)));
        ?>
    @endforeach
    <?php $size_of_array= sizeof($t_f) ?>
    @if ( $size_of_array > 1  )

        <?php
            require_once ('jpgraph/Graph1.php');
            $Two_Graphs = new Custom_Graphs();
/*
            $Two_Graphs->interval = $interval;
            $Two_Graphs->graph_data1 = $t_f;
            $Two_Graphs->graph_data2 = $w_mph;
            $Two_Graphs->graph_xdata = $date_time_observation;
            $Two_Graphs->y1_title = "Temperature F";
            $Two_Graphs->color1 = "red";
            $Two_Graphs->y2_title = "Wind Speed MPH";
            $Two_Graphs->color2 = "blue";
            $Two_Graphs->x_title = "Date Time";
            $Two_Graphs->title = "Temperature and Wind Plot";
*/
            $Two_Graphs->interval = $interval;
            $Two_Graphs->graph_data1 = $humidity;
            $Two_Graphs->graph_xdata = $date_time_observation;
            $Two_Graphs->y1_title = "Humidity %";
            $Two_Graphs->color1 = "darkgreen";
            $Two_Graphs->y2_title = "";
            $Two_Graphs->x_title = "Date Time";
            $Two_Graphs->title = "Humidity";

            $Two_Graphs->file_name="images/cache/graph2".$unique_value.".png";
            $Two_Graphs->graph_two( );
        ?>

        <a class="fancybox" rel="group" href=<?php echo $Two_Graphs->file_name; ?> >
            <img style='border:3px solid #FF3300' src=<?php echo $Two_Graphs->file_name; ?> alt="" id="image2" />
        </a>

        <?php
            require_once ('jpgraph/Graph1.php');
            $Two_Graphs = new Custom_Graphs();
            $Two_Graphs->interval = $interval;
            $Two_Graphs->graph_data1 = $t_f;
            $Two_Graphs->graph_xdata = $date_time_observation;
            $Two_Graphs->y1_title = "Temperature F";
            $Two_Graphs->color1 = "red";
            $Two_Graphs->y2_title = "";
            $Two_Graphs->x_title = "Date Time";
            $Two_Graphs->title = "Temperature";
            $Two_Graphs->file_name="images/cache/graph_temp".$unique_value.".png";
            $Two_Graphs->graph_two( );
        ?>

        <a class="fancybox" rel="group" href=<?php echo $Two_Graphs->file_name; ?> >
            <img style='border:3px solid #FF3300' src=<?php echo $Two_Graphs->file_name; ?> alt="" id="image2" />
        </a>


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
            $Two_Graphs->graph_two( );
        ?>
        <a class="fancybox" rel="group" href=<?php echo $Two_Graphs->file_name; ?> >
            <img style='border:3px solid #FF3300' src=<?php echo $Two_Graphs->file_name; ?> alt="" id="image2" />
        </a>
    @endif


    <br>

    <?php
        $map_width=962;
        $map_height=723;
        $factor=.8;
        $map_width=$map_width*$factor;
        $map_height=$map_height*$factor;
    ?>




    <img style='border:5px solid #000000' src="images/msp_map.png" usemap="#msp_map" width="{{ $map_width }}" height="{{ $map_height }}" />

    <map name="msp_map">
        <?php
//            $map_width=962;
//            $map_height=723;
            $mx1=-94.4883;
            $mx2=-91.8472;
            $mx3=-91.8472;
            $mx4=-94.4883;
            $my1=45.6258;
            $my2=45.6258;
            $my3=44.2217;
            $my4=44.2217;
            $wd=$mx2-$mx1;
            $hd=$my1-$my4;
        ?>
        @foreach($observation_maps as $observation_map)
            <?php
                $station_x=$map_width*($mx1-$observation_map->longitude)/$wd;
                if( $mx1 < $observation_map->longitude) $station_x=$map_width*($observation_map->longitude-$mx1)/$wd;
                $station_y=$map_height*($my1-$observation_map->latitude)/$hd;
                if( $my1 < $observation_map->latitude) $station_y=$map_height*($observation_map->latitude-$my1)/$hd;
                $station_x=round($station_x);
                $station_y=round($station_y);
            ?>
            <area shape="circle" coords="{{ $station_x }},{{ $station_y }},25" alt="{{ $observation_map->station_id }}" href="?station_id_map={{$observation_map->station_id }}">
        @endforeach

    </map>



    <div id="map_canvas"></div>

@endsection