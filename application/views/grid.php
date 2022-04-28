<?php echo doctype('html5');?>
<html>
    <head>
        <meta charset="UTF-8">
        <!--[if lte IE 8]>
               <script src="../assets/js/html5.js" type="text/javascript"></script>
       <![endif]-->
        <?php if(isset($library_src))echo $library_src;
        foreach($link as $links)echo link_tag($links);?>
        <title><?php echo $title.nbs().'-'.nbs().$this->lang->line('title');?></title>
        <script src="../assets/js/notification.js" type="text/javascript"></script>
        
                <!--<script src="../assets/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>-->
<?php if(isset($elements)):?>
        <script type="text/javascript">
            $( function() {
                $.widget( "custom.combobox", {
                    _create: function() {
                        this.wrapper = $( "<span>" )
                                .addClass( "custom-combobox" )
                                .insertAfter( this.element );
 
                        this.element.hide();
                        this._createAutocomplete();
                        this._createShowAllButton();
                    },
 
                    _createAutocomplete: function() {
                        var selected = this.element.children( ":selected" ),
                        value = selected.val() ? selected.text() : "";
 
                        this.input = $( "<input>" )
                                .appendTo( this.wrapper )
                                .val( value )
                                .attr( "title", "" )
                                .attr( "id", "employee1" )
                                .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default " )
                                .autocomplete({
                                    delay: 0,
                            minLength: 0,
                            source: $.proxy( this, "_source" )
                        })
                                .tooltip({
                                    classes: {
                                        "ui-tooltip": "ui-state-highlight"
                            }
                        });
 
                        this._on( this.input, {
                            autocompleteselect: function( event, ui ) {
                                ui.item.option.selected = true;
                                this._trigger( "select", event, {
                                    item: ui.item.option
                                });
                            },
 
                            autocompletechange: "_removeIfInvalid"
                        });
                    },
 
                    _createShowAllButton: function() {
                        var input = this.input,
                        wasOpen = false;
 
                        $( "<a>" )
                                .attr( "tabIndex", -1 )
                                .attr( "title", "Show All Items" )
                                .tooltip()
                                .appendTo( this.wrapper )
                                .button({
                                    icons: {
                                        primary: "ui-icon-triangle-1-s"
                            },
                            text: false
                        })
                                .removeClass( "ui-corner-all" )
                                .addClass( "custom-combobox-toggle ui-corner-right" )
                                .on( "mousedown", function() {
                                    wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                        })
                                .on( "click", function() {
                                    input.trigger( "focus" );
 
                            // Close if already visible
                            if ( wasOpen ) {
                                return;
                            }
 
                            // Pass empty string as value to search for, displaying all results
                            input.autocomplete( "search", "" );
                        });
                    },
 
                    _source: function( request, response ) {
                        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                        response( this.element.children( "option" ).map(function() {
                            var text = $( this ).text();
                            if ( this.value && ( !request.term || matcher.test(text) ) )
                                return {
                                    label: text,
                                    value: text,
                                    option: this
                                };
                        }) );
                    },
 
                    _removeIfInvalid: function( event, ui ) {
          
                        // Selected an item, nothing to do
                        if ( ui.item ) {
                            return;
                        }
 
                        // Search for a match (case-insensitive)
                        var value = this.input.val(),
                        valueLowerCase = value.toLowerCase(),
                        valid = false;
                        this.element.children( "option" ).each(function() {
                            if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                                this.selected = valid = true;
                                return false;
                            }
                        });
 
                        // Found a match, nothing to do
                        if ( valid ) {
                            return;
                        }
 
                        // Remove invalid value
                        this.input
                                .val( "" )
                                .attr( "title", value + " didn\'t match any item" )
                                .tooltip( "open" );
                        this.element.val( "" );
                        this._delay(function() {
                            this.input.tooltip( "close" ).attr( "title", "" );
                        }, 2500 );
                        this.input.autocomplete( "instance" ).term = "";
                    },
 
                    _destroy: function() {
                        this.wrapper.remove();
                        this.element.show();
                    }
                });
            <?php foreach($elements as $element):?>
                    $( "#<?php echo $element?>" ).combobox();
                    $( "#toggle" ).on( "click", function(){
                        $( "#<?php echo $element?>" ).toggle();
                    });
            <?php endforeach;?>
                });
        </script>
    <?php  endif;?>
    </head>
    <body>
        <?php $this->load->view('common/header');?>
        <?php if(isset($menu)) echo $menu;?>
        <div id="window" style="width:<?php echo $width?>px;margin:0 auto;">
            <section class="titlebar" style="float:left;width:<?php echo $width?>px;"><?php echo $title;?></section>
            <section class="window" style="float:left;height:<?php echo $height;?>px;width:<?php echo $width;?>px;">
        <?php echo form_open_multipart($form);?>
            <?php if(isset($other_fields)){?>
                <table id="nav" width="100%">
                    <tr><td>
        <?php foreach($other_fields as $caption => $key):?>
                    <?php foreach($key as $type => $value):?>
                    <?php if($caption!=''){?><label><?php echo $caption;?></label><?php }?>
                    <?php echo nbs(2);
                    $form='form_'.$type;
                    echo ($type=='dropdown')?form_dropdown($value[0],$value[1],$value[2],isset($value[3])?$value[3]:NULL).nbs(2): $form($value).nbs(2);?>
                    <?php endforeach;?>
                <?php 
                endforeach;?>
                        <td><?php echo validation_errors();?></td>
                    </tr>
                    </td>
                </table>
            <?php }?>
                <div class="grid">
        <?php
                        $column_names = array(); // to store the column names from the data keys  

                        if ( !is_array($field) )  
                        {  
                          echo "Error: You must specify at least one column";  
                          return;  
                        }  
                        //$width_td_th=90/count($field); 
                        foreach ($field as $column_name => $array)  
                        {  
                          $column_names[] = $column_name;  
                        }?>  
                    <table width="100%" class="<?php echo( isset($class) ? $class : "" )?>" id="grid">
                        <thead>
                            <tr>  
                        <?php $i=0;
                        foreach ($column_names as $column_name){?>    
                            <th ><?php echo $column_name;?></th>  <!--width="<?php //echo $width_th[$i]?>%"-->
                            <!--width="<?php $i++;// echo $width_td_th?>%"-->
                        <?php }?>  
                            </tr> 
                        </thead>
                        <tbody id="fbody">
                        <?php  
                        // find the longest column  
                        $longest_column_count = 0;  
                        foreach ($field as $column_data)  
                        { 
                          if (!is_null($column_data) && count($column_data) > $longest_column_count)  
                          {  
                                $longest_column_count = count($column_data);  
                          }  
                        }
                        // display the data  
                        // loop thru each row of data  
                        reset($field);  
                        for ($i = 0; $i < $longest_column_count; $i++ ){?>  
                            <tr >  <!-- width="100%"-->
                        <?php  
                          // loop thru each column of each row  
                          reset($column_names);  
                          for ($g = 0; $g < count($column_names); $g++ )  
                          {  //width='".$width_th[key($column_names)]."%'
                                echo "<td   class='col-". key($column_names) ."'>". current($field[ current($column_names) ]) ."</td>";  
        //                      width='".$width_td_th1."' 
                                // advance to the next row  
                                next ($field[ current($column_names) ]);  
                                // advance to the next column  
                                next ($column_names);  
                          }?>  
                            </tr>  
                        <?php }?>  
                        </tbody> 
                        <tfoot>
                            <tr class="noExl"><td></td><td>&nbsp;Displaying&nbsp;<span id="row_count"><?php echo $i;?></span> row
                                     <?php if(isset($footer)):
                                    foreach($footer as $data)
                                        echo nbs(2).$data.nbs(2);
                                endif;?>
                                </td></tr>
                        </tfoot>
                    </table>  
                </div>
        <?php echo form_close();?>
            </section>
        </div>
        <input type ='hidden' name='csrf_hash' id='csrf_hash' value='<?php echo $this->security->get_csrf_hash(); ?>'>
    </body>
        <?php if(isset($html))echo $html?>    
    <?php if(isset($script_foot))echo $script_foot;?>
    <!--script>
      $( function() {
        $("#grid").DataTable({
            "dom": 'lfrtipB',
           buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                 columns: ':contains("Office")'
                }
            },
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
            "language": {
                                "lengthMenu": 'Show <select>'+
                                '<option value="10">10</option>'+
                                '<option value="20">20</option>'+
                                '<option value="30">30</option>'+
                                '<option value="40">40</option>'+
                                '<option value="50">50</option>'+
                                '<option value="-1">All</option>'+
                                '</select> entries',
                                "search": "Filter &nbsp;",
//                                "info": "Showing page _PAGE_ of _PAGES_"
                            },
        "columnDefs": [
            { "width": '1%', "targets": 0 }
        ],
        "aoColumnDefs": [ 
         { "bSortable": false, "aTargets":  0  }
       ],
 
 
                "ordering": true,
                "scrollY":        "300px",
//                "scrollX":        "1500px",
                "scrollCollapse": true,
                "iDisplayLength": -1
                
        });
//        $( "#window" ).draggable();
      } );
    </script-->
</html>

