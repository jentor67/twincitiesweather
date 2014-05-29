@extends('layout.default')

@section('testing')
    <h1>Minneapolis MSP Airport Weather Conditions</h1>

    <?php

        $unique_value = '_'.$_SERVER['REMOTE_ADDR'].'-'.$_SERVER['REMOTE_PORT'];
        $hours_shift = date('Z')/3600;
    ?>

    <?php $first_line = "y"; ?>

    @foreach($observations as $observation)
        @if ($first_line == "y" )
            <?php
                $temp = explode('/',$observation->t_td);
                $temperature = substr($temp[0],1);
                if( substr($temp[0],0,1) == 'M') $temperature = '-'.$temperature;
                $temperature = $temperature*9/5+32;
                $dewpoint = substr($temp[1],1);
                if( substr($temp[1],0,1) == 'M') $dewpoint = '-'.$dewpoint;
                $dewpoint = $dewpoint*9/5+32;
                $gust = '';
                $wind = substr($observation->wind,-4,2)*1.15;
                if( strlen(strstr($observation->wind,'G')) > 0) {
                    $wind = substr($observation->wind,3,2)*1.15;
                    $gust = substr($observation->wind,-4,2)*1.15;
                }
                $wind_direction=substr($observation->wind,0,3);
                $_SESSION['wind_direction'] = $wind_direction;
                $wind_direction_rad=pi()*$wind_direction/180;

            ?>

            <div style="font-size: 40px;">
                {{ $temperature }} F
                <br>
                    Wind {{ $wind }} (mph)  @ {{ substr($observation->wind,0,3) }} degrees
                <br>

            </div>

            <div class="image-box">
                <img src="images/arrow.jpg" alt="" width="30" height="200" />
            </div>


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
    {{ Form::close() }}
    <br>

    @foreach($observations_rev as $observation)
        <?php
            $temp = explode('/',$observation->t_td);
            $temperature = substr($temp[0],1);
            if( substr($temp[0],0,1) == 'M') $temperature = '-'.$temperature;
            $temperature = $temperature*9/5+32;
            $t_f[] = $temperature;

            $gust = '';
            $wind = substr($observation->wind,-4,2);
            if( strlen(strstr($observation->wind,'G')) > 0) {
                $wind = substr($observation->wind,3,2);
                $gust = substr($observation->wind,-4,2);
            }

            $w_mph[] = $wind*1.15;
            $observation_minutes = date("i",strtotime($observation->date_time_reading));
            $adjusted_hours_shift = $hours_shift;
            if( $observation_minutes >= 30) $adjusted_hours_shift=$adjusted_hours_shift+1;
            $date_time_observation[]=date("M d \ng A", strtotime($adjusted_hours_shift.' hours',strtotime($observation->date_time_reading)));
        ?>
    @endforeach


    <?php
        require_once ('jpgraph/Graph1.php');
        $Two_Graphs = new Custom_Graphs();
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
        $Two_Graphs->file_name="images/cache/graph2".$unique_value.".png";
        $Two_Graphs->graph_two( );
    ?>

    <a class="fancybox" rel="group" href=<?php echo $Two_Graphs->file_name; ?> >
        <img src=<?php echo $Two_Graphs->file_name; ?> alt="" id="image2" />
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
        <img src=<?php echo $Two_Graphs->file_name; ?> alt="" id="image2" />
    </a>


    <?php
        require_once ('jpgraph/Graph1.php');
        $Two_Graphs = new Custom_Graphs();
        $Two_Graphs->interval = $interval;
        $Two_Graphs->graph_data1 = $w_mph;
        $Two_Graphs->graph_xdata = $date_time_observation;
        $Two_Graphs->y1_title = "Wind Speed mpg";
        $Two_Graphs->color1 = "blue";
        $Two_Graphs->y2_title = "";
        $Two_Graphs->x_title = "Date Time";
        $Two_Graphs->title = "Wind Speed";
        $Two_Graphs->file_name="images/cache/graph_wind".$unique_value.".png";
        $Two_Graphs->graph_two( );
    ?>
    <a class="fancybox" rel="group" href=<?php echo $Two_Graphs->file_name; ?> >
        <img src=<?php echo $Two_Graphs->file_name; ?> alt="" id="image2" />
    </a>




@endsection

