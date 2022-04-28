<?php echo doctype('html5');?>
<html>
    <head>
        <!--[if lte IE 8]>
            <script src="assets/js/html5.js" type="text/javascript"></script>
        <![endif]-->
        <?php if(isset($library_src))echo $library_src;
        foreach($link as $links)echo link_tag($links);?>
        <title>Office Attendance Software</title>
        <script src="assets/js/notification.js" type="text/javascript"></script>
        <script src="assets/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
        <?php if($session['type'] == "Administrator")?>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    </head>
    <body>
        <?php $this->load->view('common/header');?>

 <?php if($this->session->userdata('first_time_logged') && $session['type']!= 'Administrator'):?>
<div id="dialog-message" title="Attendence Time <?php echo$in_time;?>">
  <p><?php echo $session['fullname'].nbs().$this->lang->line('msg_office_start').nbs().(isset($in_time) ? $in_time : NULL)?></p>
  <?php if(isset($html)) echo $html?>
</div>
<?php endif;?>


        <?php if(isset($menu)) echo $menu;?>
        <div id="window" style="width:<?php echo $width?>px;margin:0 auto;">
        <section class="titlebar" style="float:left;width:<?php echo $width?>px;"><?php echo $title;?></section>
        <section class="window" style="float:left;height:<?php echo $height;?>px;width:<?php echo $width;?>px;">
         <?php 
         if($session['type'] != "Administrator"):?>
        <label style="float:left"><?php echo $date.br().'Log In Time:'.nbs().$in_time.br();
    if(isset($out_time))echo 'Log Out Time:'.nbs().$out_time.br();
    echo 'Office Hours:'.nbs().$office_hours.br().'Supervisor:'.nbs().$supervisor.br();?>
        </label>
            <div class="picture_frame"><?php echo $profile_pic?></div>
    
            <?php echo form_fieldset('Activity Report of'.nbs(). mdate('25th %F %Y', strtotime("-1 month",now())).nbs().'to'.nbs().mdate('25th %F %Y', now()));?>
        <div class="grid">
        <table align="center">
            <thead>
                <tr><th width="20%">Monthly Total Working Days</th><th width="22%">Current Counted Working Days</th><th width="8%">Present Count</th><th width="8%">Absent Count</th><th width="12%">On Time In Count</th><th width="8%">Late Count</th><th width="12%">Off Day Count</th><th>Holiday Counted </th><th>Leave Counted</th></tr>
            </thead>
            <tbody>
                <tr><td width="20%"><?php echo $total_working_days;?></td><td width="22%"><?php echo $current_working_days;?></td><td width="8%"><?php echo $present;?></td><td width="9%"><?php $absent= /*$current_working_days-($present+$leave);*/ sizeof($absent_report);echo $absent<0 ? 0 : $absent;?></td><td width="12%"><?php echo $on_time;?></td><td width="8%"><?php echo $late;?></td><td width="12%"><?php echo $offdays;?></td><td width="10%"><?php echo $holiday;?></td><td width="8%"><?php echo $leave;?></td></tr>
            </tbody>
        </table>
        </div>
        <?php echo form_fieldset_close().br();?>
        <?php // if(sizeof($absent_report)>0):?>
        <?php echo form_fieldset('Absent Report of'.nbs().mdate('25th %F %Y', strtotime("-1 month",now())).nbs().'to'.nbs().mdate('%dth %F %Y', now()));?>
        <div class="grid">
        <table id="grid" align="center" width="90%">
            <thead>
                <tr><th width="30%">Date</th></tr>
            </thead>
            <tbody style="max-height: 300px;">
              
                <?php for($i=0; $i<sizeof($absent_report);$i++):?>
                <tr><td width="30%"><?php echo mdate('%d-%m-%Y',  strtotime($absent_report[$i]));?></td></tr>
                <?php endfor;?>
                
            </tbody>
        </table>
        </div>
        <?php echo form_fieldset_close().br();?>
        <?php // endif;?>
        <?php echo form_fieldset('Late Report of'.nbs().mdate('25th %F %Y', strtotime("-1 month",now())).nbs().'to'.nbs().mdate('%dth %F %Y', now()));?>
        <div class="grid">
        <table id="grid1" align="center" width="90%">
            <thead>
                <tr><th width="30%">Date</th><th width="30%">In Time</th><th width="30%">Out Time</th></tr>
            </thead>
            <tbody style="max-height: 300px;">
                <?php foreach($late_report as $late_row){?>
                <tr><td width="30%"><?php echo mdate('%d-%m-%Y',  strtotime($late_row->Date));?></td><td width="30%"><?php echo $late_row->In_Time;?></td><td width="30%"><?php echo $late_row->Out_Time;?></td></tr>
                <?php }?>
            </tbody>
        </table>
        </div>
            <?php echo form_fieldset_close();?>
        <?php else:?>
            <div id="piechart_3d" ></div>
        <?php endif;?>
        </section>
        </div>
        <input type ='hidden' name='csrf_hash' id='csrf_hash' value='<?php echo $this->security->get_csrf_hash(); ?>'>
    </body>
    <?php if(isset($script_foot))echo $script_foot;?>
     <?php if($this->session->userdata('first_time_logged')):?>
    <script type="text/javascript">
          var hash = $('#csrf_hash').val();
          $( "#dialog-message" ).dialog({
            modal: true,
            buttons: {
              <?php if(isset($html)):?>
              Submit: function() {
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url('home/insert_LateReason')?>',
                    data: {'csrf_test_name':hash,'reason':$('#reason').val()},
                    success: function(data) 
                        {  
                             $("#dialog-message").dialog( "close" );
                        }
                   
                });
              }
              <?php else:?>
              Ok: function() {
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url('home/destroy_FirstTimeLogged')?>',
                    data: {},
                    success: function(data) 
                        {  
                             $("#dialog-message").dialog( "close" );
                        }
                   
                });
                $( this ).dialog( "close" );
              }
              <?php endif;?>   
            }
          });
    </script>
    <?php endif;?>
    <?php if($session['type'] == "Administrator"):?>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Company', 'Total Employee'],
          <?php echo $pie;?>
        ]);

        var options = {
          title: 'Company Wise Total Human Resource',
          is3D: true,

        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));

        chart.draw(data, options);
      }
    </script>
    <?php endif;?>
    
    <script>
      $( function() {
        $("#grid").DataTable({
            "bLengthChange": false,
            "bPaginate": false,
            "ordering": false,
            "bFilter": false,
            "bInfo": false,
            "scrollY": "100px",
            "scrollCollapse": true
                
	});
        $("#grid1").DataTable({
            "bLengthChange": false,
            "bPaginate": false,
            "ordering": false,
            "bFilter": false,
            "bInfo": false,
            "scrollY": "100px",
            "scrollCollapse": true
                
	});
//        $( "#window" ).draggable();
      } );
    </script>
</html>