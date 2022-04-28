<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of table_to_excel
 *
 * @author User
 */
class table_to_excel {
    private $obj;
    public function __construct(){
         $this->obj =& get_instance();
         $this->obj->jquery->script(base_url('assets/js/jquery.table2excel.js'));
         $this->obj->javascript->compile();
    }
    
    public function load_Script($button_id,$table_id,$exclud_class=NULL,$name,$col=NULL){
        $js = '$("#'.$button_id.'").click(function(){
                $("#'.$table_id.'").table2excel({
                        exclude: ".'.$exclud_class.'",
                        name: "'.$name.'",
                        filename: "'.$name.'",
                        fileext: ".xls",
                        exclude_img: true,
                        exclude_links: true,
                        exclude_inputs: true,
                        columns : '.$col.'
                    });
                });';
        $this->obj->javascript->output($js);
        $this->obj->javascript->compile();
        
    }
}
