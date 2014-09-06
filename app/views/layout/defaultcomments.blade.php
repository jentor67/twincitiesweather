<!DOCTYPE html>

<?php
    session_start();

    require_once("css/main.php");
?>

<html>

    <head>
        <title>Comments</title>

        <?php
            $shttp = 'http';
            if( isset($_SERVER['HTTPS']) ){
                if( $_SERVER['HTTPS'] == 'on' ) $shttp = "https";
            }
            $shttp = $shttp.'://'.$_SERVER['SERVER_NAME'];

            setcookie("weather","123info",time()+(3600*24*7));
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/comments.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/background_with_gradient.css">
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $shttp?>/images/favicon.ico" />
    </head>

    <body>

        @yield('copywrite')

        @yield('make_comment')

        @yield('past_comments')

        @yield('return')


    </body>

</html>
