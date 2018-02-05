
angular.module('mapDemo', [])
    .controller('AppCtrl',function($rootScope,$scope,$location,$window) {
        $scope.trackers = {
            1 : {
                name : 'peugeot 308 gt',
                color : '#000000',
                id : 1,
                locations : [
                    {latitude : '46.328007', longitude : '4.841618'},
                    {latitude : '46.335845', longitude : '4.845159'},
                    {latitude : '46.335852', longitude : '4.845459'},
                    {latitude : '46.336130', longitude : '4.845679'},
                    {latitude : '46.336347', longitude : '4.845561'},
                    {latitude : '46.338874', longitude : '4.847342'},
                    {latitude : '46.339200', longitude : '4.848672'},
                    {latitude : '46.338867', longitude : '4.850732'},
                    {latitude : '46.338211', longitude : '4.854592'},
                    {latitude : '46.337611', longitude : '4.858197'},
                    {latitude : '46.335081', longitude : '4.870009'},
                    {latitude : '46.331334', longitude : '4.877562'},
                    {latitude : '46.330727', longitude : '4.878560'},
                    {latitude : '46.330608', longitude : '4.879644'},
                    {latitude : '46.330519', longitude : '4.880642'},
                    {latitude : '46.329726', longitude : '4.881457'},
                    {latitude : '46.328785', longitude : '4.882337'}
                ]
            },
            2 : {
                name : 'Ford Fiesta',
                color : '#ff0000',
                id : 2,
                locations : [
                    {latitude : '46.319773', longitude : '4.838314'},
                    {latitude : '46.3197767', longitude : '4.8361253'},
                    {latitude : '46.341112', longitude : '4.839988'},
                ]
            }
        };
        $scope.map = false;

        $scope.play = function (key) {

            var tracker = $scope.trackers[key];
            var counter = 0;
            var from_location = tracker.marker.getPosition();

            angular.forEach(tracker.locations, function(obj, key) { 
                to_location = $scope.pin(obj.latitude, obj.longitude);
                
                if(to_location == from_location) {
                    counter = key;
                    return;
                }   
            });

            $scope.trackers[key].play = !$scope.trackers[key].play;

            $scope.trackers[key].timer = setInterval(function()
            {
                tracker = $scope.trackers[key];

                var to_location = tracker.locations[counter];
                to_location = $scope.pin(to_location.latitude, to_location.longitude);
                updateHistoryPin(key, tracker.marker.getPosition(), to_location);
                $scope.map.setZoom(15);
                $scope.map.setCenter(tracker.marker.getPosition());
                if (counter > tracker.locations.length - 2)
                {
                    clearInterval($scope.trackers[key].timer);
                    $scope.trackers[key].stop = true;
                    $scope.$digest();
                }
                counter++;
            }, 1000);
        }

        $scope.pause = function (key) {
            $scope.trackers[key].play = false;
            clearInterval($scope.trackers[key].timer);
        }

        $scope.replay = function (key) {
            $scope.trackers[key].stop = false;
            $scope.trackers[key].play = false;
            to_location = $scope.trackers[key].locations[0];
            to_location = $scope.pin(to_location.latitude, to_location.longitude);
            updateHistoryPin(key, false, to_location);
            $scope.play(key);
        }

        var updateHistoryPin = function(key, from_location, to_location) {

            var tracker = $scope.trackers[key];
            var angle = 0;

            if (from_location)
            {
                // Get the rotation angle   locations
                angle = ($scope.angleBends(from_location, to_location)) ;
            }

            var marker = $scope.trackers[key].marker;

            marker.setIcon({
                path: "M20.2,61.1c-3,0-5.9,0-8.9,0c-0.6-0.1-1.2-0.3-1.8-0.4c-4.9-0.4-5.8-0.9-6.2-5.8c-0.6-6.8-0.6-13.6-0.7-20.4c-0.1-3.6,0-7.2,0-11c-0.8,0-1.4,0-2.1,0c2.2-1.6,2.1-1.6,2.2-4.4C2.7,15,2.8,10.8,3,6.7C3.2,3.2,4.4,2,7.9,1.2C13,0,18.2-0.1,23.3,1.1c3.8,0.9,5,1.9,5.2,5.7c0.3,4.3,0.4,8.7,0.4,13c0,1.8,0.4,3,2.3,3.3c0,0.1,0,0.3,0,0.4c-0.7,0-1.4,0.1-2.1,0.1c0,0.4-0.1,0.7-0.1,1.1c-0.1,8.5-0.2,17.1-0.5,25.6c-0.1,2.4-0.4,4.7-0.7,7c-0.3,1.7-1.3,2.7-3.1,3C23.2,60.5,21.7,60.8,20.2,61.1z M16.4,16.1c-3.2,0.5-5.9,0.8-8.6,1.5c-3.1,0.8-3.4,1.5-2.4,4.5c0.4,1.3,0.8,2.7,1.4,3.9c0.2,0.5,1.1,0.9,1.6,0.9c3.9-0.1,7.8-0.3,11.7-0.4c1.4,0,3.3,0.6,4.2,0c1-0.8,1.2-2.7,1.7-4.2c0.2-0.5,0.2-0.9,0.4-1.4c0.4-1.3,0-2.3-1.3-2.6C22.1,17.4,19,16.7,16.4,16.1z M16,54.2c2.3-0.2,4.8-0.4,7.2-0.7c1.6-0.2,2.2-1.3,1.9-2.9c-0.4-1.7-0.8-3.4-1-5.2c-0.2-1.3-0.9-1.6-2.2-1.5c-4,0.1-8,0.1-12,0c-1.3,0-2.1,0.1-2.3,1.6c-0.2,1.7-0.6,3.3-1,4.9c-0.4,1.9,0.4,2.9,2.2,3.1C11.1,53.9,13.5,54,16,54.2z M26.9,35c-2-0.3-2.7,0.4-2.5,2.2c0.2,1.2-0.1,2.6,0.3,3.7c0.3,1,1.1,1.7,1.7,2.6c0.2-0.1,0.3-0.2,0.5-0.3C26.9,40.5,26.9,37.8,26.9,35z M5.1,34.5C5,34.6,4.8,34.8,4.7,35c0,2.7,0,5.5,0,8.5c3.1-1.9,2.4-4.8,2.4-7.3C7.1,35.6,5.8,35.1,5.1,34.5z M5.2,23.9c-0.2,0-0.4,0.1-0.7,0.1c0,2.2,0,4.5,0,6.7c0,2.1,0.3,2.3,3,2.5C6.7,30,6,26.9,5.2,23.9z M24.1,33.2c0.3,0.1,0.5,0.3,0.8,0.4c0.7-0.6,1.9-1.1,2-1.7c0.2-2.6,0.1-5.2,0.1-7.7c-0.3,0-0.5-0.1-0.8-0.1C25.5,27.1,24.8,30.1,24.1,33.2z",
                scale: 0.5,
                fillColor: $scope.trackers[key].color,
                fillOpacity: 1,
                rotation: angle,
                strokeWeight: 0,
                anchor: new google.maps.Point(18, 60),
                //origin : new google.maps.Point(400, 400)
            });
            marker.setPosition(to_location);
        };
    })
    .directive('myMap', function($compile, $templateCache, $timeout, $filter) {
        // directive link function
        var link = function($scope, element, attrs) {
            var infoWindow;         
            $scope.pin = function(latitude, longitude) {
                return new google.maps.LatLng(latitude, longitude);
            };
            $scope.defaultMapCenter = $scope.pin(46.341767, 4.838657);
            // map config
            var mapOptions = {
                center: $scope.defaultMapCenter,
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scrollwheel: false,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_RIGHT
                }
            };
            function initMap() {

                if (!$scope.map) {
                    $scope.map = new google.maps.Map(element[0], mapOptions);
                    $scope.markerCluster = new MarkerClusterer($scope.map);
                }
            }

            $scope.zoom = function($event, id, location) {
                var pinLocation = $scope.pin(location.latitude, location.longitude);
                $scope.map.setCenter(pinLocation);
                $scope.map.setZoom(14);
                $scope.info(id);
                $event.preventDefault();
                $event.stopPropagation();
            };

            $scope.info = function(id) {

                if($scope.trackers[id].locations.length > 0)
                {
                    var location = $scope.trackers[id].locations[$scope.trackers[id].locations.length - 1];
                    var pinLocation = new google.maps.LatLng(location.latitude, location.longitude);
                    $scope.map.setCenter(pinLocation);
                    showInfoWindow($scope.trackers[id], pinLocation);
                    $scope.map.setCenter(pinLocation);
                    $scope.map.setZoom(16);
                }
            };

            var showInfoWindow = function(tracker, pinLocation) {
                $scope.tracker = tracker;

                if (tracker.infowindow) {
                    var infowindow = tracker.infowindow;
                    infowindow.close($scope.map);
                    
                    delete tracker.infowindow;
                }


                var infowindow = new google.maps.InfoWindow({
                    content : '<h2>'+tracker.name+'</h2>'
                });
                
                infowindow.open($scope.map, tracker.marker);
                $scope.trackers[tracker.id].infowindow = infowindow;                
            };

            // Get the angle of rotation
            $scope.angleBends = function (from, to)
            {
                return google.maps.geometry.spherical.computeHeading(from, to);
            }

            $scope.showMarker = function(id){
                
                if($scope.trackers[id].locations.length >0)
                {   
                    if (!$scope.trackers[id].hasOwnProperty('marker')) {
                        $scope.addMarker(id);
                    }
                }
            }

            $scope.showMarkers = function(){
                angular.forEach($scope.trackers, function(tracker, id) {    
                    $scope.showMarker(id)
                });
            }

            $scope.deleteMarkers = function(){
                angular.forEach($scope.trackers, function(tracker, id) {
                    $scope.deleteMarker(id);
                });
            }

            $scope.deleteMarker = function (id) {
                
                if($scope.trackers[id].hasOwnProperty('marker'))
                {
                    marker = $scope.trackers[id].marker;
                    
                    if(marker)
                    {
                        $scope.markerCluster.removeMarker($scope.trackers[id].marker);
                        marker.setMap(null);
                        delete $scope.trackers[id].marker;  
                                        
                    }
                }
            }

            $scope.addMarker = function(id)
            {
                var tracker = $scope.trackers[id];
                
                if (!tracker.hasOwnProperty('marker')) {
                    
                    var angle = 0;

                    if (tracker.locations.length == 0 )
                    {
                        return false;
                    }

                    var location = tracker.locations[0];
                    
                    var pinLocation = $scope.pin(location.latitude, location.longitude);
                    

                    var location_before = tracker.locations[1];
                    var pinLocationBefore = $scope.pin(location_before.latitude, location_before.longitude);
                    // Get the rotation angle
                    angle = $scope.angleBends(pinLocationBefore , pinLocation);
                        

                    // Define Marker properties
                    var marker = new google.maps.Marker({
                        id: id,
                        position: pinLocationBefore || pinLocation,
                        icon: {
                        path: "M20.2,61.1c-3,0-5.9,0-8.9,0c-0.6-0.1-1.2-0.3-1.8-0.4c-4.9-0.4-5.8-0.9-6.2-5.8c-0.6-6.8-0.6-13.6-0.7-20.4c-0.1-3.6,0-7.2,0-11c-0.8,0-1.4,0-2.1,0c2.2-1.6,2.1-1.6,2.2-4.4C2.7,15,2.8,10.8,3,6.7C3.2,3.2,4.4,2,7.9,1.2C13,0,18.2-0.1,23.3,1.1c3.8,0.9,5,1.9,5.2,5.7c0.3,4.3,0.4,8.7,0.4,13c0,1.8,0.4,3,2.3,3.3c0,0.1,0,0.3,0,0.4c-0.7,0-1.4,0.1-2.1,0.1c0,0.4-0.1,0.7-0.1,1.1c-0.1,8.5-0.2,17.1-0.5,25.6c-0.1,2.4-0.4,4.7-0.7,7c-0.3,1.7-1.3,2.7-3.1,3C23.2,60.5,21.7,60.8,20.2,61.1z M16.4,16.1c-3.2,0.5-5.9,0.8-8.6,1.5c-3.1,0.8-3.4,1.5-2.4,4.5c0.4,1.3,0.8,2.7,1.4,3.9c0.2,0.5,1.1,0.9,1.6,0.9c3.9-0.1,7.8-0.3,11.7-0.4c1.4,0,3.3,0.6,4.2,0c1-0.8,1.2-2.7,1.7-4.2c0.2-0.5,0.2-0.9,0.4-1.4c0.4-1.3,0-2.3-1.3-2.6C22.1,17.4,19,16.7,16.4,16.1z M16,54.2c2.3-0.2,4.8-0.4,7.2-0.7c1.6-0.2,2.2-1.3,1.9-2.9c-0.4-1.7-0.8-3.4-1-5.2c-0.2-1.3-0.9-1.6-2.2-1.5c-4,0.1-8,0.1-12,0c-1.3,0-2.1,0.1-2.3,1.6c-0.2,1.7-0.6,3.3-1,4.9c-0.4,1.9,0.4,2.9,2.2,3.1C11.1,53.9,13.5,54,16,54.2z M26.9,35c-2-0.3-2.7,0.4-2.5,2.2c0.2,1.2-0.1,2.6,0.3,3.7c0.3,1,1.1,1.7,1.7,2.6c0.2-0.1,0.3-0.2,0.5-0.3C26.9,40.5,26.9,37.8,26.9,35z M5.1,34.5C5,34.6,4.8,34.8,4.7,35c0,2.7,0,5.5,0,8.5c3.1-1.9,2.4-4.8,2.4-7.3C7.1,35.6,5.8,35.1,5.1,34.5z M5.2,23.9c-0.2,0-0.4,0.1-0.7,0.1c0,2.2,0,4.5,0,6.7c0,2.1,0.3,2.3,3,2.5C6.7,30,6,26.9,5.2,23.9z M24.1,33.2c0.3,0.1,0.5,0.3,0.8,0.4c0.7-0.6,1.9-1.1,2-1.7c0.2-2.6,0.1-5.2,0.1-7.7c-0.3,0-0.5-0.1-0.8-0.1C25.5,27.1,24.8,30.1,24.1,33.2z",
                            scale: 0.5,
                            fillColor: tracker.color,
                            duration: 10000,
                            fillOpacity: 1,
                            rotation: angle,
                            strokeWeight: 0,
                            anchor: new google.maps.Point(18, 60),
                            //origin : new google.maps.Point(400, 400)
                        },
                        map: $scope.map
                    });
                    
                    marker.setPosition(pinLocation);

                    $scope.trackers[id].marker = marker;
                    $scope.markerCluster.setGridSize(30);
                    $scope.markerCluster.addMarker(marker);

                    google.maps.event.addListener(marker, 'click', function() {
                        showInfoWindow(tracker, pinLocation);
                        $scope.map.setCenter(pinLocation);
                        $scope.map.setZoom(17);
                        //$scope.open(tracker.id);
                    });
                }

                return true;
            };
            // show the map 
            initMap();      
            $scope.showMarkers();
        };
        
        return {
            restrict: 'A',
            template: '<div id="gmaps" style = "height : 100%"></div>',
            replace: true,
            link: link
        }
    });

