@extends('layout.defaultcomments')

<?php
    Log::info('comments.index.blade.php');
?>
@section('past_comments')

    <div id="comments">
        <br>
        @foreach($comments_list as $past_comment)
            <div id="comments_subsection">
                <?php
                    $date_input = date_create($past_comment->datetime);
                    $formal_date = date_format($date_input,'l jS \of F Y h:i:s A'); ?>
                <b>Name:</b> {{ $past_comment->name }} -- {{ $formal_date }}
                <br><br>
                <b>Comment</b> {{ $past_comment->comments }}
            </div>
            <br>
        @endforeach
    </div>

@endsection


@section('make_comment')
    <div id="making_comments">
        {{ Form::open(array('url' => 'comments/submit','method' => 'POST')) }}

        {{ Form::token() }}

        @if(Session::has('name_message'))
            <div style="font-size: 16px; color: #ff0000;">{{ Session::get('name_message') }}</div><br >
        @endif

        {{ Form::label('Name','Name:') }}

        @if(Session::has('name_in'))
            {{ Form::text('name_in',Session::get('name_in')) }}
        @else
            {{ Form::text('name_in') }}
        @endif
        <br >

        @if(Session::has('comment_message'))
            <br ><div style="font-size: 16px; color: #ff0000;">{{ Session::get('comment_message') }}</div><br >
        @endif

        {{ Form::label('Comment','Comment:') }}
        <br>
        @if(Session::has('comment_in'))
            {{ Form::textarea('comment_in',Session::get('comment_in')) }}<br >
        @else
            {{ Form::textarea('comment_in') }}<br >
        @endif

        <p>{{ Form::submit('Submit Comment') }}</p>

        {{ Form::close() }}



    </div>

@endsection



@section('copywrite')
    <div id="copy_write" style="font-size: 8px;">
        &copy 2015 twincitiesweather.info All Rights Reserved
    </div>
@endsection


@section('return')
    <div id="return">
        {{ Form::open(array('url' => '')) }}
        {{ Form::submit('Return to Twin Cities Weather') }}
        {{ Form::close() }}

    </div>
@endsection




