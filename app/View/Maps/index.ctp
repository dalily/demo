<?php 
    echo $this->Html->script(array("https://maps.googleapis.com/maps/api/js?key=AIzaSyCBKp7mpjH5LksyOaXg45x5ZHfLqeynFF0&libraries=drawing,geometry"
    ), array('inline' => false));
    echo $this->Html->script(array(
        '../plugins/jquery/jquery.easing.1.3.js',
        '../plugins/angular/angular.min.js',
        '../plugins/marker/markerclusterer.js',
        '../plugins/marker/markerAnimate.js',
        '../plugins/marker/SlidingMarker.min.js',
        'controllers',
    ), array('inline' => false));
?>
<script>
<?php  $this->Html->scriptStart(array('inline' => false)); ?>
angular.element(document).ready(function() {

    jQuery('#content').attr('ng-controller', "AppCtrl");
    SlidingMarker.initializeGlobally();
    angular.bootstrap(document, ['mapDemo']);
    
});
<?php $this->Html->scriptEnd(); ?>
</script>

<div id = 'mapContainer'>
    <table class="table grid">
        <thead class="panel-heading">
        <tr>
            <th>
                Véhicule
            </th>
            <th class = "actions">
                <span class = "glyphicon glyphicon-cog">
                </span>
                Actions
            </th>
        </tr>
        </thead>
        <tbody ng-repeat="tracker in trackers">
        <tr>
            <td>{{tracker.name}}</td>
            <td>
                <span ng-if = '!tracker.stop'>
                    <a href ng-if = '!tracker.play' ng-click= "play(tracker.id)">
                        <span class = "glyphicon glyphicon-play" data-toggle="tooltip" title = "Play">
                        </span>
                    </a>
                    <a href ng-if = 'tracker.play' ng-click= "pause(tracker.id)">
                        <span class = "glyphicon glyphicon-pause" data-toggle="tooltip" title = "Pause">
                        </span>
                    </a>
                </span>
                <a href ng-if = 'tracker.stop' ng-click= "replay(tracker.id)">
                    <span class = "glyphicon glyphicon-repeat" data-toggle="tooltip" title = "Replay">
                    </span>
                </a>
                <a href ng-click= "info(tracker.id)">
                    <span class = "glyphicon glyphicon-eye-open" data-toggle="tooltip" title = "Afficher">
                    </span>
                </a>     
            </td>   
        </tr>
        </tbody>
    </table>
    <div my-map="" ></div>
</div>