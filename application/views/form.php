<?php echo doctype('html5');?>
<html>
    <head>
        <!--[if lte IE 8]>
            <script src="<?php echo base_url('assets/js/html5.js')?>" type="text/javascript"></script>
        <![endif]-->
        <?php if(isset($library_src))echo $library_src;
        foreach($link as $links)echo link_tag($links);?>
        <title><?php echo $title.nbs().'-'.nbs().$this->lang->line('title');?></title>
        <script src="<?php echo base_url('assets/js/notification.js')?>" type="text/javascript"></script>
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
    </head>
    <body>
        <?php $this->load->view('common/header');?>
        <?php if(isset($menu)) echo $menu;?>
        <div id="window" style="<?php if($height)echo 'height:'.$height.'px;';?>width:<?php echo $width?>px;margin:0 auto;">
        <section class="titlebar" style="float:left;width:<?php echo $width?>px;"><?php echo $title;?></section>
        <section class="window" style="float:left;<?php if($height)echo 'height:'.$height.'px;';?>width:<?php echo $width;?>px;">
        <?php if(isset($hidden))echo form_open_multipart($form,'',$hidden);else echo form_open_multipart($form);?>
        <table>
                <?php foreach($field as $caption => $key){
                   
                    if(substr($caption, 0, 8)=='fieldset'){?>
                    <tr>
                        <td colspan="3"><?php echo form_fieldset($key);?>
                            <table>
                    <?php }else if(substr($caption, 0, 14)=='close_fieldset'){?>
                                
                    </table><?php form_fieldset_close();?></td></tr>
                    <?php }else{?>
                    <tr>
                        <td><label><?php echo $caption;?></label></td>
                    <?php foreach($key as $type => $value):?>
                        <td><?php $form='form_'.$type; echo ($type=='dropdown')?form_dropdown($value[0],$value[1],$value[2],isset($value[3])? $value[3] : '') : $form($value);?></td><td><?php echo form_error(($type=='dropdown')? $value[0] : $value['name']); ?></td>
                    <?php endforeach;?>
                    </tr>

                <?php 
                    }
                }
        if(isset($select)){
                $i=0;
                foreach($select as $label => $data){?>
                    <tr><td><label><?php echo $label;?></label></td><td><?php echo form_dropdown($label,$data,$selected[$i]);?></td><td><?php echo form_error($label); ?></td></tr>
                <?php $i++;
                }
        }
        if(isset($preview)){
                foreach($preview as $label => $images){
                ?>
                    <tr><td><label><?php echo $label;?></label></td><td colspan="2"><?php echo $images;?></td></tr>
                <?php }
        }
        if(isset($check)){?>
                    <tr><td><label><?php echo $check_caption;?></label></td><td colspan="2"><?php foreach($check as $caption => $value){ 
                    if($value!=1){$bool=FALSE;$value=1;}else{$bool=TRUE;}
                    $name = str_replace(' ','',strtolower($caption)); echo form_checkbox($name,$value,$bool,'id="'.$name.'"').'<label>'.$caption.'</label>'.nbs(2);}?></td></tr>
        <?php }
        if(isset($others)){?>
                    <tr><td colspan="3"><?php echo $others;?></td></tr>
                    <?php }?>
        <?php if(isset($submit)):?><tr><td></td><td colspan="2"><?php echo form_submit($submit);?></td></tr><?php endif;?>
        </table>
            <?php echo form_close();?>
        </section>
        </div>
        <input type ='hidden' name='csrf_hash' id='csrf_hash' value='<?php echo $this->security->get_csrf_hash(); ?>'>
    </body>
    <?php if(isset($html))echo $html?>
    <?php if(isset($script_foot))echo $script_foot;?>
<!--    <script>
     $( function() {
       $( "#window" ).draggable();
     } );
    </script>-->
</html>
