<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of table_edit
 *
 * @author User
 */
class table_edit {
    private $obj;
    public function __construct(){
         $this->obj =& get_instance();
         $this->obj->jquery->script(base_url('assets/js/jquery.tabledit.js'));
         $this->obj->javascript->compile();
    }
    public function load_Script($tableID,$editable,$identifier,$url){
         $js = '$("#'.$tableID.'").Tabledit({
             
                toolbarClass: \'btn-toolbar\',
                
                url: "'.base_url($url).'",
                // activate edit button instead of spreadsheet style
                editButton: true,

                // activate delete button
                deleteButton: false,

                // activate save button when click on edit button
                saveButton: true,

                // activate restore button to undo delete action
                restoreButton: false,

                // change the name of attribute in td element for the row identifier
                rowIdentifier: \'ID\',

                autoFocus: false,
                
                // hide the column that has the identifier
                hideIdentifier: false,
                
                // custom action buttons
                buttons: {
                    edit: {
                      class: \'btn btn-sm btn-default\',
                      html: \''.img(base_url('assets/images/glyphicons-10-pencil.png')).'\',
                      action: \'edit\'
                    },
                    delete: {
                      class: \'btn btn-sm btn-default\',
                      html: \'<span class="glyphicon glyphicon-trash"></span>\',
                      action: \'delete\'
                    },
                    save: {
                      class: \'btn btn-sm btn-success\',
                      html: \''.img(base_url('assets/images/glyphicons-10-floppy-save.png')).'\'
                    },
                    restore: {
                        class: \'btn btn-sm btn-warning\',
                        html: \'Restore\',
                        action: \'restore\'
                      },
                    confirm: {
                      class: \'btn btn-sm btn-danger\',
                      html: \'Confirm\'
                    }
                },

                columns: {
//                  identifier: [0, "Date"], 
                  identifier: '.$identifier.',                  
                  editable: ['.$editable.']
                }
                
//        onAjax: function() { 
//               
//                headers: {\''.$this->obj->security->get_csrf_token_name().'\':\''.$this->obj->security->get_csrf_hash().'\'}
//        }

                });';//[1, "First Name"], [2, "Last Name"]
        $this->obj->javascript->output($js);
        $this->obj->javascript->compile();
    }
}
