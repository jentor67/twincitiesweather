<?php

class Analysis extends BaseController {

    public $restful = true;

    public function analysis(){

        $start_year='1873';
        if( Input::has('start_year') ) $start_year = Input::get('start_year');
        if( $start_year < 1873 ) $start_year = '1873';

        $end_year = '2012';
        if( Input::has('end_year') ) $end_year = Input::get('end_year');
        if( $end_year < 1873 ) $end_year = '1873';

        $start_segment = date('m-d');
        if( Input::has('start_segment') ) $start_segment = Input::get('start_segment');

        $end_segment = date('m-d');
        if( Input::has('end_segment') ) $end_segment = Input::get('end_segment');


        $segments = 14;
        if( Input::has('segments') ) $segments = Input::get('segments');

        $historic_segments = DB::select("call historic_segment('$start_segment', '$end_segment' ,$start_year,$end_year,$segments);");

        return View::make('analysis.index')
            ->with('start_year',$start_year)
            ->with('end_year',$end_year)
            ->with('start_segment',$start_segment)
            ->with('end_segment',$end_segment)
            ->with('historic_segments',$historic_segments);

    }

}