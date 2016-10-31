<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<!doctype html>
<html lang="en">
    <head>
        <title>Perch Locator Example</title>
        <style type="text/css">
            .container {
                margin: 0 auto;
                padding: 50px 0;
                width: 700px;
            }

            .form {
                border-radius: 3px;
                background-color: #F0F0F0;
                margin-bottom: 20px;
                padding: 20px;
                text-align: center;
            }

            #map {
                margin-bottom: 30px;
                width: 100%;
                height: 400px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <form class="form" method="get">
                <label for="address">Address:</label>
                <input type="search" name="address" id="address" value="<?php echo perch_get('address'); ?>" />
                <button type="submit">Search Locations</button>
            </form>

            <?php if(perch_get('address')): ?>
                <div id="map"></div>
                <script>
                    var locations = <?php root_locator_get_custom([
                        'template' => 'address_list_json.html',
                        'address'  => perch_get('address'),
                        'range'    => 50
                    ]); ?>;

                    var map;

                    // Async callback
                    function initMap() {

                        // Set up a boundary object to keep markers in view
                        var bounds = new google.maps.LatLngBounds();

                        map = new google.maps.Map(document.getElementById('map'), {
                            center: new google.maps.LatLng(53.2307, -0.5406),
                            zoom: 15
                        });

                        // Iterate over locations and create markers
                        if(locations.length > 0) {
                            locations.forEach(function(address) {
                                var marker = new google.maps.Marker({
                                    position: new google.maps.LatLng(address.addressLatitude, address.addressLongitude),
                                    map: map,
                                    title: address.locationTitle
                                });

                                bounds.extend(marker.getPosition());
                            });
                        }

                        // Zoom to map to fit the markers on display
                        map.fitBounds(bounds);
                    }
                </script>

                <!-- This is *not* the key you use in the Perch settings area. You will need a Browser key -->
                <script src="https://maps.googleapis.com/maps/api/js?callback=initMap&key=XXXXXXXXXX" async defer></script>

                <?php root_locator_get_custom(array(
                    'address' => perch_get('address'),
                    'range'  => 50
                )); ?>
            <?php endif; ?>
        </div>
    </body>
</html>

<?php PerchUtil::output_debug(); ?>