<?php

class Comments extends BaseController  {

    public $restful = true;

    public function comments(){

/*
        $comment_in = "";

        if( Input::has('comment_in') ) $comment_in = Input::get('comment_in');

        $name_in = "";
        if( Input::has('name_in') ) $name_in = Input::get('name_in');
        */

        Log::info('Comments@comments');

        $comments_list = DB::select("call comments();");
/*
        return View::make('comments.index')
            ->with('name_in',$name_in)
            ->with('comment_in',$comment_in)
            ->with('comments_list',$comments_list);
*/
        return View::make('comments.index')
            ->with('comments_list',$comments_list);
    }




    public function comments_submit(){

        Log::info('Comments@comments_submit');

        $comment_in = "";
        if( Input::has('comment_in') ) $comment_in = Input::get('comment_in');

        $name_in = "";
        if( Input::has('name_in') ) $name_in = Input::get('name_in');




        if( $name_in <> "" && $comment_in <> "") {
            $ip_address = Request::getClientIp();
            DB::insert("call comment_insert( '$ip_address', '$name_in', '$comment_in');");
            return Redirect::to('comments');
        }
        else{
            $name_message="";
            $comment_message="";
            if( $name_in == "" ) $name_message="Need a name";
            if( $comment_in == "" ) $comment_message="Need a comment";
            return Redirect::to('comments')->
                with('comment_message',$comment_message)->
                with('name_message',$name_message)->
                with('message','You have to have a Name and Comment')->
                with('name_in',$name_in)->
                with('comment_in',$comment_in);
        }

    }
}