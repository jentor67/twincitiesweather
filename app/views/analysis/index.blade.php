@extends('layout.defaultanalysis')


@section('main')
    <?php
        $unique_value = '_'.$_SERVER['REMOTE_ADDR'].'-'.$_SERVER['REMOTE_PORT'];

        $row_counts = 0;
        $hist_max_hi = -200;
        $hist_min_low = 200;
        $rain_max =0;
        $historic_segments = Session::get('historic_segments');
        $start_year = Session::get('start_year');
        $end_year = Session::get('end_year');
        $start_segment = Session::get('start_segment');
        $end_segment = Session::get('end_segment');
    /*
                 ->with('start_year',$start_year)
            ->with('end_year',$end_year)
            ->with('start_segment',$start_segment)
            ->with('end_segment',$end_segment)
            ->with('historic_segments',$historic_segments);
     */
    ?>


    @foreach($historic_segments as $historic_segment)
        <?php
            $high[] = round($historic_segment->ahigh);
            if( round($historic_segment->ahigh) > $hist_max_hi ) $hist_max_hi = round($historic_segment->ahigh);
            $low[] = round($historic_segment->alow);
            if( round($historic_segment->alow) < $hist_min_low ) $hist_min_low = round($historic_segment->alow);
            $rain[] = $historic_segment->arain;
            if( $historic_segment->arain > $rain_max ) $rain_max = $historic_segment->arain;
            $high_error[] = $historic_segment->ahigh+$historic_segment->shigh;
            $high_error[] = $historic_segment->ahigh-$historic_segment->shigh;
            $xdata[] = $historic_segment->vstart_segment.'-'.$historic_segment->vend_segment;
            $segments = $historic_segment->segments;
            $row_counts++;
        ?>

    @endforeach



    <div id="main">
        <?php

            require_once ('jpgraph/Graph1.php');
            $Plot_Temp = new Custom_Graphs();
            $Plot_Temp->interval = ceil($segments/10);
            if( $Plot_Temp->interval < 1 ) $Plot_Temp->interval = 1;
            $Plot_Temp->graph_data1 = $high;
            $Plot_Temp->graph_xdata = $xdata;
            $Plot_Temp->Max_High = $hist_max_hi+10;
            $Plot_Temp->Min_Low = $hist_min_low-10;
            $Plot_Temp->Scale = "Y";
            $Plot_Temp->y1_title = "High and Low Temp F";
            $Plot_Temp->color1 = "red";

            $Plot_Temp->add_to_yaxis = "y";
            $Plot_Temp->y2_title = "Low Temp F";
            $Plot_Temp->color2 = "blue";
            $Plot_Temp->graph_data2 = $low;

            $Plot_Temp->y3_title = "Rain in";
            $Plot_Temp->color3 = "chartreuse4";
            $Plot_Temp->graph_data3 = $rain;
            $Plot_Temp->max3 = $rain_max+1;
            $Plot_Temp->min3 = 0;

            $Plot_Temp->x_title = "Year";
            $Plot_Temp->back_ticks = "";
            $Plot_Temp->title = "High Low and Precip from ".$start_year." to ".$end_year."";
            if( $start_segment == $end_segment) {
                $date = new DateTime('2000-'.$start_segment);
                $Plot_Temp->title .= " date of ".$date->format('M j');
            }
            else{
                $date = new DateTime('2000-'.$start_segment);
                $date1 = new DateTime('2000-'.$end_segment);
                $Plot_Temp->title .= " for Segment of ".$date->format('M j')." - ".$date1->format('M j');
            }
            //$date->format('m-d');
            $Plot_Temp->file_name="images/cache/graph_hist_hi_low".$unique_value.".png";
            $Plot_Temp->graph_two( );


       ?>
       <div id="graph_div">
           <a class="fancybox" rel="group" href=<?php echo $Plot_Temp->file_name; ?> >
               <img style='border:3px solid #FF3300' src=<?php echo $Plot_Temp->file_name; ?> alt="" id="image2" />
           </a>
       </div>

   </div>
@endsection


@section('controller')
    <div id="controller">
        <p>Put a beginning year and end year between 1873 - 2012</p>


        {{ Form::open(array('url' => 'analysis/gather','method' => 'POST')) }}

        {{ Form::token() }}

        <?php
            //***Create Year Array****
            $historic_start_year = Session::get('start_low_year');
            $historic_end_year = Session::get('end_high_year');

            $i=$historic_start_year;
            while( $i <= $historic_end_year){
                $year_array[$i] = $i;
                $i++;
            }
            if( $start_year == "" ) $start_year = $historic_start_year;
            if( $end_year == "") $end_year = $historic_end_year;

            //***Create date array***
            $date = new DateTime('2000-01-01');
            $historic_start_date = $date->format('m-d') . "\n";
            $test_date = $date->format('m-d');
            $test_date_formal = $date->format('M j');
            $date_array[$test_date] = $test_date_formal;

            $date->modify('+1 day');
            $test_date = $date->format('m-d');
            $test_date_formal = $date->format('M j');

            $i=0;
            while( $test_date <> '01-01' && $i < 370){
                $date_array[$test_date] = $test_date_formal;

                $date->modify('+1 day');
                $test_date = $date->format('m-d');
                $test_date_formal = $date->format('M j');
                $i++;
            }

            //if( $start_segment == "" ) $start_segment = '01-01';
            //if( $end_segment == "")  $end_segment = '12-31';


        ?>

        {{ Form::label('start_year','Start Year:') }}<br />
        {{ Form::select('start_year',$year_array, $start_year) }}<br />

        {{ Form::label('end_year','End Year:') }}<br />
        {{ Form::select('end_year',$year_array, $end_year) }}<br />

        <br />

        {{ Form::label('start_segment','Start Date:') }}<br />
        {{ Form::select('start_segment',$date_array, $start_segment) }}<br />

        {{ Form::label('end_segment','End Date:') }}<br />
        {{ Form::select('end_segment',$date_array, $end_segment) }}<br />



        {{ Form::label('max_segments','Max Segments:') }}<br />
        {{ Form::text('segments',$segments) }}<br >

        <p>{{ Form::submit('Analyse') }}</p>

        {{ Form::close() }}


    </div>

@endsection


@section('copywrite')
    <div id="copy_write" style="font-size: 8px;">
        &copy 2014 twincitiesweather.info All Rights Reserved
    </div>
@endsection


@section('instructions')
    <div id="instructions">
        This section lets the user create graphs from historical data going back from 1873 to 2012.

        The user specifies which year to start and end, and what data to start and end, and then the number of segments.  An example would
         be to determine the average temperature from <b>July 7</b> - <b>July 14</b> starting in <b>1950</b> and ending in <b>1990</b> with no more than <b>20</b> segments. The user would set<br />
        <br />
        Start Year: <b>1950</b><br />
        End Year: <b>1990</b><br />
        Start Date: <b>Jul 7</b><br />
        End  Date: <b>Jul 14</b><br />
        Segments: <b>20</b><br />
        <br />
        <b>Explanation</b>: What this did was to determine the best fit to meet the max segments and to include the year 1950.  In this case it chose to start on 1949 and
        have 3 years per segment.  Therefore the first segment 1949-1951 is the average temperature of all 3 years for the dates July 7 - July 14<br />
        <br />
        If the user wants a data point for each year then set the Max Segments = 41 or above.

    </div>

@endsection

@section('return')
    <div id="return">
        {{ Form::open(array('url' => '')) }}
        {{ Form::submit('Return to Twin Cities Weather') }}
        {{ Form::close() }}

    </div>
@endsection

@section('title_space')
    <div id="title_space">
        Twin Cities Weather - Historic Analysis
    </div>
@endsection

@section('advertising1')
    <div id="advertising1">
        <a href="http://www.umn.edu/">
            <img src="images/UofM.jpg" width="194" height="117">
        </a>
    </div>
@endsection


