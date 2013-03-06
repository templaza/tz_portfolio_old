<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

$doc    = JFactory::getDocument();
$params = $this -> item -> params;
if($params -> get('tz_show_gmap',1) == 1):
    
?>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3.x&language=en&libraries=places&sensor=false"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        jQuery(window).load(function(){
            var map;
            var geocoder;
            var InitializeMap = function () {

                var latlng = new google.maps.LatLng(<?php echo $params -> get('tz_gmap_latitude',21.0333333);?>,
                                <?php echo $params -> get('tz_gmap_longitude',105.8500000);?>);
                var myOptions =
                {
                    zoom: <?php echo $params -> get('tz_gmap_zoomlevel',10);?>,
                    tooltip:true,
                    center: latlng,
                    scrollwheel: <?php if($params -> get('tz_gmap_mousewheel_zoom',1) == 1) echo 'true'; else echo 'false';?>,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            }
            var FindLocaiton = function () {
                geocoder = new google.maps.Geocoder();
                InitializeMap();

                <?php if(!$params -> get('tz_gmap_address')):?>
                    var latlng = new google.maps.LatLng(<?php echo $params -> get('tz_gmap_latitude',20.9815260);?>,
                                    <?php echo $params -> get('tz_gmap_longitude',105.7890379);?>);
                    geocoder.geocode({ 'location': latlng }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            map.setCenter(results[0].geometry.location);
                            var marker = new google.maps.Marker({
                                map: map,
                                position: results[0].geometry.location
                            });
                            if (results[0].formatted_address) {
                                region = results[0].formatted_address + '<br/>';
                            }
                            var infowindow = new google.maps.InfoWindow({
                                content: <?php if($params -> get('tz_gmap_custom_tooltip')):?>
                                            <?php echo '\''.$params -> get('tz_gmap_custom_tooltip').'\'';?>
                                        <?php else:?>
                                            'Location info:<br/>Country Name:' + region +
                                            '<br/>LatLng:' + results[0].geometry.location + ''
                                        <?php endif;?>
                            });
                            infowindow.open(map, marker);
                            google.maps.event.addListener(marker, 'click', function () {
                                // Calling the open method of the infoWindow
                                infowindow.open(map, marker);
                            });

                        }
                        else {
                            alert("Geocode was not successful for the following reason: " + status);
                        }
                    });
                <?php endif;?>

                <?php if($params -> get('tz_gmap_address')):?>
                    var address = "<?php echo $params -> get('tz_gmap_address');?>";
                    geocoder.geocode({ 'address': address }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            map.setCenter(results[0].geometry.location);
                            var marker = new google.maps.Marker({
                                map: map,
                                position: results[0].geometry.location
                            });
                            if (results[0].formatted_address) {
                                region = results[0].formatted_address + '<br/>';
                            }
                            var infowindow = new google.maps.InfoWindow({
                                content: <?php if($params -> get('tz_gmap_custom_tooltip')):?>
                                            <?php echo '\''.$params -> get('tz_gmap_custom_tooltip').'\'';?>
                                        <?php else:?>
                                            'Location info:<br/>Country Name:' + region +
                                            '<br/>LatLng:' + results[0].geometry.location + ''
                                        <?php endif;?>
                            });
                            infowindow.open(map, marker);
                            google.maps.event.addListener(marker, 'click', function () {
                                // Calling the open method of the infoWindow
                                infowindow.open(map, marker);
                            });

                        }
                        else {
                            alert("Geocode was not successful for the following reason: " + status);
                        }

                    });
                <?php endif;?>
            }
            FindLocaiton();

        });
    </script>
    <div class="TzGoogleMap">
        <h3 class="TzGoogleMapTitle"><?php echo JText::_('COM_TZ_PORTFOLIO_GOOGLE_MAP_TITLE');?></h3>
        <?php
            $width  = $params -> get('tz_gmap_width','100%');
            $height = $params -> get('tz_gmap_height','500px');
            if(!preg_match('/^[0-9]+%$/',$width) AND !preg_match('/^[0-9]+px$/',$width)):
                $width  = $params -> get('tz_gmap_width').'px';
            endif;
            if(!preg_match('/^[0-9]+%$/',$height) AND !preg_match('/^[0-9]+px$/',$height)):
                $height  = $params -> get('tz_gmap_height').'px';
            endif;
        ?>
        <div id="map_canvas" style="width:<?php echo $width;?>; height:<?php echo $height?>"></div>
    </div>
<?php endif;?>
