<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function is_image($path)
{
    $a = getimagesize($path);
    $image_type = $a[2];
     
    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
    {
        return true;
    }
    return false;
}

function numeric($array_id){
    $js = NULL;
    foreach($array_id as $id)    
    $js .=  '$("#'.$id.'").keypress(function (e) {
     //if the letter is not digit then display error and dont type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });';
    return $js;
}

function elapsed_leave_calculation($join_date){
    if(strtotime(mdate('%Y',strtotime($join_date))==strtotime(mdate('%Y',now())))){
        $d1 = strtotime(mdate('%Y-01-01',now()));
        $d2 = strtotime(mdate('%Y-%m-%d',strtotime($join_date)));
        $min_date = min($d1, $d2);
        $max_date = max($d1, $d2);
        $i = 0;

        while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
            $i++;
        }
        return $i; // 8
    }else
        return 0;
}

function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' ){
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
        
    $interval = date_diff($datetime1, $datetime2);
        
    return $interval->format($differenceFormat);
        
}

//function dateDifference($from , $to){
//    $from = strtotime($from);
//    $to= strtotime($to);
//    
//    $datediff = $to-$from;
//        
//    return floor($datediff / (60 * 60 * 24));
//        
//}
