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
class datatable {
    private $obj;
    public function __construct(){
        $this->obj =& get_instance();
        $this->obj->jquery->script(base_url('assets/datatables/jquery.dataTables.min.js')); 
        $this->obj->jquery->script('https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js');
        $this->obj->jquery->script('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js');
        $this->obj->jquery->script('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js');
        $this->obj->jquery->script('https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js');
        $this->obj->jquery->script('https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js');
        $this->obj->jquery->script('https://cdn.datatables.net/buttons/1.3.1/js/buttons.colVis.min.js');
         $this->obj->jquery->script('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js');
        $this->obj->jquery->script('https://cdn.datatables.net/plug-ins/1.10.15/sorting/datetime-moment.js');
        $this->obj->javascript->compile();
    }
    
    public function load_Script($table_id,$buttons=NULL,$columnDef=NULL,$others=NULL){
        
        $js = "$.fn.dataTable.moment( 'dd-mm-yyyy');";
        $js .= '$("#'.$table_id.'").DataTable({
                    "ordering": true,
                    "scrollY":        "300px",
//                    "scrollX":        "1500px",
                    "scrollCollapse": true,
                    "iDisplayLength": -1,
                
                    "dom": \'lfrtipB\',
                    buttons: [';
                      
                    $js .= '\'excelHtml5\',
                            \'csvHtml5\',
                            \'pdfHtml5\'
                            ';
                    $js .= $buttons;  
                    $js .= '
                    ],
                    "language": {
                            "lengthMenu": \'Show <select>\'+
                            \'<option value="10">10</option>\'+
                            \'<option value="20">20</option>\'+
                            \'<option value="30">30</option>\'+
                            \'<option value="40">40</option>\'+
                            \'<option value="50">50</option>\'+
                            \'<option value="-1">All</option>\'+
                            \'</select> entries\',
                            "search": "Filter &nbsp;",
//                            "info": "Showing page _PAGE_ of _PAGES_"
                        },
                    "columnDefs": [
                        ';
                    $js .= is_null($columnDef) ? '{ "orderable": false, "width": \'1%\', "targets": 0 }' : $columnDef;
                    $js .= '],
//                    "aoColumnDefs": [ { "bSortable": false, "aTargets":  0  }]
                    ';
                    $js .= $others;
                    $js .= '
            });';
        $this->obj->javascript->output($js);
        $this->obj->javascript->compile();
        
    }
}
