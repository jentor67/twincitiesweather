<!DOCTYPE html>

<?php
    session_start();

    require_once("css/main.php");
?>

<html>

    <head>
        <title>Analysis</title>

        <?php
            $shttp = 'http';
            if( isset($_SERVER['HTTPS']) ){
                if( $_SERVER['HTTPS'] == 'on' ) $shttp = "https";
            }
            $shttp = $shttp.'://'.$_SERVER['SERVER_NAME'];

            setcookie("weather","123info",time()+(3600*24*7));
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/analysis.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/background_with_gradient.css">
    </head>

    <body>

        @yield('main')

        @yield('controller')

        @yield('instructions')

        @yield('return')

        @yield('title_space')

    </body>

</html>