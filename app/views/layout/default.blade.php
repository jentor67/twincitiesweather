<!DOCTYPE html>
    <?php
        session_start();

        if (isset($_SESSION['views'])) {
            $_SESSION['views'] = $_SESSION['views'] + 1;
        }
        else {
            $_SESSION['views'] = 1;
        }

        $day=date('z');
        //$day=$day+260;

        //**red
        $red_day = $day-202;
        $factor=.5;
        if($red_day < 0) $red_day = $red_day+365;
        $red_day=$red_day/365*2*pi(); // puts it in radians
        $rgb[0] = $factor*(127*cos($red_day)+127);
        //**green
        $green_day = $day-112; //112
        if($green_day < 0) $green_day = $green_day+365;
        $green_day=$green_day/365*2*pi(); // puts it in radians
        $rgb[1] = $factor*(127*cos($green_day)+127);
        //**Blue
        $blue_day = $day-22;
        if($blue_day < 0) $blue_day = $blue_day+365;
        $blue_day=$blue_day/365*2*pi(); // puts it in radians
        $rgb[2] = $factor*(127*cos($blue_day)+127);

        $hex = "";
        $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        $_SESSION['background_color'] = '0044AA'; //$hex;

        $_SESSION['wind_direction']=$wind_direction;
        require_once("css/main.php");
    ?>


<html>




<head>
    <title>{{ $title }}</title>
    <?php
        $shttp = 'http';
        if( isset($_SERVER['HTTPS']) ){
            if( $_SERVER['HTTPS'] == 'on' ) $shttp = "https";
        }
        $shttp = $shttp.'://'.$_SERVER['SERVER_NAME'];

        setcookie("weather","123info",time()+(3600*24*7));

        $color_background = "FFFFFF";
    ?>

    <!-- Add jQuery library -->
    <script type="text/javascript" src="/jquery/fancybox/jquery-1.10.1.min.js"></script>

    <!-- Add mousewheel plugin (this is optional) -->
    <script type="text/javascript" src="/jquery/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

    <!-- Add fancyBox -->
    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/jquery/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
    <script type="text/javascript" src="/jquery/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

    <!-- Optionally add helpers - button, thumbnail and/or media -->
    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/jquery/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" media="screen" />
    <script type="text/javascript" src="/jquery/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
    <script type="text/javascript" src="/jquery/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

    <link rel="stylesheet" type="text/css"  href="<?php echo $shttp?>/jquery/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" media="screen" />
    <script type="text/javascript" src="/jquery/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

    <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>


    <script>

        
        function initialize() {
            var json = [
                {{ $station_element }}
            ]

              var latlng = new google.maps.LatLng( {{ $lat_selected }}, {{ $lng_selected }});
            var mapOptions = {
                zoom: 9,
                center: latlng,
                mapTypeId:google.maps.MapTypeId.ROADMAP
            };

            var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

            i=0;

            for (var i = 0, length = json.length; i < length; i++) {
                var data = json[i], latLng = new google.maps.LatLng(data.lat, data.lng);

                var markerImage = new google.maps.MarkerImage(data.img,
                    new google.maps.Size(100, 100),
                    new google.maps.Point(0, 0),
                    new google.maps.Point(50, 50));

                var marker = new google.maps.Marker({
                    position:latLng,
                    map:map,
                    title:data.title,
                    url:data.url,
                    icon:markerImage
                });


                var infoWindow = new google.maps.InfoWindow();

                google.maps.event.addListener(marker, "mouseover", function (e) {
                    infoWindow.setContent(data.description);
                    infoWindow.open(firstmap, marker);
                });
                google.maps.event.addListener(marker, "mouseout", function () {
                    infoWindow.setContent("");
                    infoWindow.close();
                });
                google.maps.event.addListener(marker, "click", function () {
                    window.location.href = this.url;
                });
            }



        }
        google.maps.event.addDomListener(window, 'load', initialize);

    </script>


    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/main_20140625.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/background_with_gradient.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/jquery.datetimepicker.css">

    <script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>

    <style type="text/css">
        <?php echo $css_image_box; ?>
    </style>




</head>

<body>
    <?php
        $test= isset($_COOKIE['weather']) ? $_COOKIE['weather'] : '';
     //   echo $test;
     //   echo $_SESSION['views'];
    ?>



    @yield('testing')

    @yield('station_forcast')


</body>
<script src="<?php echo $shttp?>/js/jquery.js"></script>
<script src="<?php echo $shttp?>/js/jquery.datetimepicker.js"></script>
<script src="<?php echo $shttp?>/js/jquery.min.js"></script>
<script>

    $(document).ready(function() {
        $(".hidestats").click(function () {
            $(".stats_show").toggle("slow");
        });
    });

    $(document).ready(function() {
        $(".hidegraphs").click(function () {
            $(".graphs_show").toggle("slow");
        });
    });

    $(document).ready(function() {
        $(".hidepredictedtemperature").click(function () {
            $(".predictedtemperature_show").toggle("slow");
        });
    });

</script>
<script>
    var d = new Date();
    var hour = d.getHours();
    if(hour < 10) { hour = '0'+hour}
    var minutes = d.getMinutes();
    if(minutes < 10) { minutes = '0'+ minutes}
    var toDay = d.getFullYear() + '/' + (d.getMonth()+1) + '/' + d.getDate() + ' ' + hour + ':' + minutes;
    var toDay_Day = d.getFullYear() + '/' + (d.getMonth()+1) + '/' + d.getDate();
    $('#datetimepickerstart').datetimepicker();
    $('#datetimepickerstart').datetimepicker({value: toDay, step:1});
    $('#datetimepickerstop').datetimepicker();
    $('#datetimepickerstop').datetimepicker({value: toDay, step:1});
    $('#datetimepickerstop_edit').datetimepicker();
    $('#datetimepickerstop_edit').datetimepicker({ step:1});

    $('#dateworking').datetimepicker({
        format:'Y/m/d',
        value: toDay_Day,
        timepicker:false
    });



</script>

</html>