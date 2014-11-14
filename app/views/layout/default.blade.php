<!DOCTYPE html>
    <?php
        session_start();

        if (isset($_SESSION['views'])) {
            $_SESSION['views'] = $_SESSION['views'] + 1;
        }
        else {
            $_SESSION['views'] = 1;
        }

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

    ?>

    <!-- Add jQuery library -->

    <script type="text/javascript" src="<?php echo $shttp?>/jquery/fancybox/jquery-1.10.1.min.js"></script>

    <!-- Add mousewheel plugin (this is optional) -->
    <script type="text/javascript" src="<?php echo $shttp?>/jquery/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

    <!-- Add fancyBox -->
    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/jquery/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
    <script type="text/javascript" src="/jquery/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

    <!-- Optionally add helpers - button, thumbnail and/or media -->
    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/jquery/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" media="screen" />
    <script type="text/javascript" src="<?php echo $shttp?>/jquery/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
    <script type="text/javascript" src="<?php echo $shttp?>/jquery/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

    <link rel="stylesheet" type="text/css"  href="<?php echo $shttp?>/jquery/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" media="screen" />
    <script type="text/javascript" src="<?php echo $shttp?>/jquery/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

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


    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/main_20141110.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $shttp?>/css/background_with_gradient.css">


    <script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>


</head>

<body>

    @yield('testing')

    @yield('station_forcast')

    @yield('submit_comments')

    @yield('dailytopten')
</body>


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


</html>