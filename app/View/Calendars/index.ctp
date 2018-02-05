 <?php 
$this->viewVars['title_for_layout'] = __('Calendrier');
    echo $this->Form->month('mois', array('class' => 'form-control', 'value' => date('mm'), 'empty' => false));
?>
<br>
<script>

<?php  $this->Html->scriptStart(array('inline' => false)); ?>
var fullCalendar = {
        init : function(){
            $('#calendar').fullCalendar({
                lang: 'fr',
                header:  false,
                selectable: true,
                select: function (start, end, jsEvent, view) {
                    $('td[data-date]').removeClass("fc-highlight");
                    if(end.diff(start, 'days') == 1) {
                        fullCalendar.selectAllWeekDay(start);
                    }
                }
            });         
        },
        selectAllWeekDay : function(start){
            
            var dayOfWeek = start.day();
            var weekday = moment(start)
                .startOf('month')
                .day(dayOfWeek); 
            if (weekday.date() > 7) {
                weekday.add(7,'d');
            }
            var month = weekday.month();           
            while(month === weekday.month()){
                var dayTarget = weekday.format("YYYY-MM-DD");
                $('td[data-date = '+dayTarget+']').addClass("fc-highlight");
                weekday.add(7,'d');
            }
        }
    }

    jQuery(document).ready(function() {
        fullCalendar.init();
        
        $('#moisMonth').change(function(e){
            var toDate = moment();
            toDate.date(01);
            toDate.month(parseInt($(this).val())-1);
            $('#calendar').fullCalendar('gotoDate', toDate);
            e.preventDefault();
        });
    });

<?php $this->Html->scriptEnd(); ?></script>

<div class="calendars index">
    <div id='calendar'></div>
</div>