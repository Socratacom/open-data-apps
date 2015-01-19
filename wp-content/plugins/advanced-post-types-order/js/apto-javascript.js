    
    var APTO = {
            
            add_rule_post_type: function() {
                
                //show the loading
                jQuery('#rules-post-type').find('.apto_more').find('.ajax_loading').show();
                
                var queryString = {};
                queryString.action  =   "apto_get_rule_box";
                queryString.type    =   'post_type';
                this.AJAX_call(queryString, this.process_rule_post_type);
                
            },
            process_rule_post_type: function(response) {
                
                //hide the loading
                jQuery('#rules-post-type').find('.apto_more').find('.ajax_loading').hide();
                jQuery('#rules-post-type').find('.apto_rules tbody').append(response.html);
            },
            
            add_rule_taxonomy: function() {
                
                //show the loading
                jQuery('#rules-taxonomy').find('.apto_more').find('.ajax_loading').show();
                
                var group_id = 1;
                
                if(jQuery('#rules-taxonomy .apto_rules').length > 0) 
                    group_id = parseInt(jQuery('#rules-taxonomy .apto_rules').last().attr('data-id')) + 1;
                
                var queryString         = {};
                queryString.action      =   "apto_get_rule_box";
                queryString.type        =   'taxonomy';
                queryString.group_id    =   group_id;  
                this.AJAX_call(queryString, this.process_rule_taxonomy);

            },
            process_rule_taxonomy: function(response) {
                
                //hide the loading
                jQuery('#rules-taxonomy').find('.apto_more').find('.ajax_loading').hide();
                jQuery('#rules-taxonomy').find('.insert_root').before(response.html);
                
                //add alternate
                if(jQuery('#rules-taxonomy').find('.apto_rules').length % 2 == 0)
                    {
                        jQuery('#rules-taxonomy').find('.apto_rules').last().find('tr').addClass('alternate');
                    }
            },
            
            remove_taxonomy_item : function (element){
                
                jQuery(element).closest('.apto_rules').remove();
                                
                //reprocess all .apto_rules tables and add alternate to even
                jQuery('#rules-taxonomy').find('.apto_rules').each(function() {
                    
                    //remove all alternate
                    jQuery(this).find('tr').removeClass('alternate');
                        
                    var el_index =  jQuery('#rules-taxonomy').find('.apto_rules').index(this);
                    el_index ++;
                     
                    if(el_index % 2 == 0)
                        jQuery(this).find('tr').addClass('alternate');
                        
                })
            },
            
            change_taxonomy_item : function (group_id){
                
                var taxonomy_table = jQuery('#rules-taxonomy').find('table[data-id="'+  group_id +'"]');
                   
                //show the loading
                jQuery('#rules-taxonomy').find('.apto_more').find('.ajax_loading').show();
                
                var queryString         = {};
                queryString.action      =   "apto_change_taxonomy_item";
                queryString.group_id    =   group_id;
                queryString.taxonomy    =   jQuery(taxonomy_table).find('td.param select.taxonomy_item').val();
                this.AJAX_call(queryString, this.process_change_taxonomy_item);   
              
            },
            
            process_change_taxonomy_item : function (response){
                
                //hide the loading
                jQuery('#rules-taxonomy').find('.apto_more').find('.ajax_loading').hide();
                
                var taxonomy_table = jQuery('#rules-taxonomy').find('table[data-id="'+  response.group_id +'"]'); 
                
                jQuery(taxonomy_table).find('td.value').html(response.html);
            },
            
            
            add_rule_author: function() {
                
                //show the loading
                jQuery('#rules-author').find('.apto_more').find('.ajax_loading').show();
                
                var queryString = {};
                queryString.action  =   "apto_get_rule_box";
                queryString.type    =   'author';
                this.AJAX_call(queryString, this.process_rule_author);

            },
            process_rule_author: function(response) {
                
                //hide the loading
                jQuery('#rules-author').find('.apto_more').find('.ajax_loading').hide();
                jQuery('#rules-author').find('.apto_rules tbody').append(response.html);
            },
                        
            AJAX_call : function (queryString, callback) {

                jQuery.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  data: queryString,
                  cache: false,
                  dataType: "json",
                  success: callback,
                  error: function(html){

                      }
                });

            } ,
            
            
            remove_rule_item : function (element, ancestor){
                
                jQuery(element).closest(ancestor).remove();
            },
            
            
            add_conditional_group : function(){

                //show the loading
                jQuery('#add_conditional_group').find('.ajax_loading').show();
                
                var group_id = 1;
                
                if(jQuery('.conditional_rules').length > 0) 
                    group_id = parseInt(jQuery('.conditional_rules').last().attr('data-id')) + 1;
                
                var queryString = {};
                queryString.action      =   "apto_get_conditional_group";
                queryString.group_id    =   group_id;
                this.AJAX_call(queryString, this.process_conditional_group);
                
            },
            process_conditional_group: function(response) {
                
                //hide the loading
                jQuery('#add_conditional_group').find('.ajax_loading').hide();
                if(jQuery('.conditional_rules').length > 0)
                    jQuery('.conditional_rules').last().after(response.html);
                    else
                    {
                        jQuery('#add_conditional_group').before(response.html);
                        jQuery('.conditional_rules > h4').remove();
                    }

            },
            
            add_conditional_rule : function (element) {
                
                //show the loading
                jQuery(element).closest('.apto_more').find('.ajax_loading').show();
                
                var group_id = jQuery(element).closest('.conditional_rules').attr('data-id');
                                
                var queryString = {};
                queryString.action      =   "apto_get_conditional_rule";
                queryString.group_id    =   group_id;
                
                var tr_row = jQuery(element).closest('tr');
                row_id = parseInt(jQuery(element).closest('.conditional_rules').find('.apto_rules tr').last().attr('data-id')) + 1;
                
                queryString.row_id      =   row_id;
                
                this.AJAX_call(queryString, this.process_conditional_rule);    
                
            },
            
            process_conditional_rule : function (response) {
                
                var group_id = response.group_id;
                
                //hide the loading
                jQuery('#conditional_rules_' + group_id).find('.apto_more .ajax_loading').hide();    
                jQuery('#conditional_rules_' + group_id).find('.apto_rules tbody').append(response.html);  
            },
            
            remove_conditional_item : function (element, ancestor){
                
                var holder = jQuery(element).closest('.apto_rules');
                jQuery(element).closest(ancestor).remove();
                
                //remove if there is no tr child
                if(jQuery(holder).find('tr').length < 1)
                    {
                        jQuery(holder).closest('.conditional_rules').remove();
                        
                        //check for remaining conditional_rules items
                        if(jQuery('.conditional_rules').length === 1)
                            jQuery('.conditional_rules > h4').remove();                                
                    }
            },
            
            conditional_item_change : function(element)
                {
                    //show the loading
                    jQuery(element).closest('.conditional_rules').find('.apto_more').find('.ajax_loading').show();
                    
                    var group_id = jQuery(element).closest('.conditional_rules').attr('data-id');
                                    
                    var queryString = {};
                    queryString.action      =   "apto_get_conditional_rule";
                    queryString.group_id    =   group_id;
                    queryString.selected    =   jQuery(element).val();
                    queryString.row_id      =   jQuery(element).closest('tr').attr('data-id');
                    
                    this.AJAX_call(queryString, this.conditional_item_change_process);       
                },
                
            conditional_item_change_process : function(response)
                {
                    var group_id    =   response.group_id;
                    var row_id      =   response.row_id;
                    //hide the loading
                    jQuery('#conditional_rules_' + group_id).find('.apto_more .ajax_loading').hide();    
                    jQuery('#conditional_rules_' + group_id).find('.apto_rules tbody tr[data-id="'+  row_id +'"]').replaceWith(response.html);         
                },
                
                
                
            sort_list_delete :  function()
                {
                    var agree   =   confirm("Are you sure you want to delete this sort list?");
                    if (agree)
                        {
                            jQuery('#apto_form_order_reset').submit();   
                        }
                        else
                        {
                            return false ;
                        }                
                },
                
            change_view_selection   :   function(element)
                {
                    window.location = jQuery(element).val();   
                },
                
            toggle_thumbnails   :   function()
                {
                    jQuery('#sortable .post_type_thumbnail').toggle();   
                },
            interface_query_advanced_toggle :   function()
                {
                    var status = jQuery('#button_show_adv').attr('data-status');
                    
                    if(status == 'simple')
                        {
                            jQuery('#rules-taxonomy').slideDown();
                            jQuery('#rules-author').slideDown();
                            jQuery('#button_show_adv').attr('data-status', 'advanced');
                            jQuery('#button_show_adv').html('Show Simple');
                        }
                        else
                        {
                            jQuery('#rules-taxonomy').slideUp();
                            jQuery('#rules-author').slideUp();
                            
                            //remove any data
                            jQuery('#rules-taxonomy').find('table.apto_rules').remove();
                            jQuery('#rules-author').find('table.apto_rules').find('tr').remove();
                            
                            jQuery('#button_show_adv').attr('data-status', 'simple');
                            jQuery('#button_show_adv').html('Show Advanced');    
                        }
                    
                },
                
                
            interface_reverse_order :   function()
                {
                    //keep the height to prevent browser scroll
                    jQuery("#sortable").css('min-height', 'inherit');
                    jQuery("#sortable").css('min-height', jQuery("#sortable").height() + 'px');
                    
                    jQuery("#sortable").append(jQuery('#sortable > li').hide().get().reverse());
                    jQuery('#sortable > li').slideDown(100);
                },
                
            interface_title_order :   function(order_type)
                {
                    //keep the height to prevent browser scroll
                    jQuery("#sortable").css('min-height', 'inherit');
                    jQuery("#sortable").css('min-height', jQuery("#sortable").height() + 'px');
                            
                    var $sortable_list = jQuery('#sortable'),
                        $sortable_li = jQuery('#sortable > li');

                    $sortable_li.sort(function(a,b){
                        var an = jQuery(a).find('.i_description').html().toLowerCase(),
                            bn = jQuery(b).find('.i_description').html().toLowerCase();

                        if(order_type == 'ASC')
                            {
                                if(an > bn) 
                                    {
                                        return 1;
                                    }
                                if(an < bn) 
                                    {
                                        return -1;
                                    }
                            }
                            
                        if(order_type == 'DESC')
                            {
                                if(an < bn) 
                                    {
                                        return 1;
                                    }
                                if(an > bn) 
                                    {
                                        return -1;
                                    }
                            }
                        
                        return 0;
                    });

                    $sortable_li.detach().hide().appendTo($sortable_list).slideDown(100);
                },
                
            interface_id_order :   function(order_type)
                {
                    //keep the height to prevent browser scroll
                    jQuery("#sortable").css('min-height', 'inherit');
                    jQuery("#sortable").css('min-height', jQuery("#sortable").height() + 'px');
                            
                    var $sortable_list = jQuery('#sortable'),
                        $sortable_li = jQuery('#sortable > li');

                    $sortable_li.sort(function(a,b){
                        var an = jQuery(a).attr('id').toLowerCase().replace("item_", ""),
                            bn = jQuery(b).attr('id').toLowerCase().replace("item_", "");

                        if(order_type == 'ASC')
                            {
                                if(an > bn) 
                                    {
                                        return 1;
                                    }
                                if(an < bn) 
                                    {
                                        return -1;
                                    }
                            }
                            
                        if(order_type == 'DESC')
                            {
                                if(an < bn) 
                                    {
                                        return 1;
                                    }
                                if(an > bn) 
                                    {
                                        return -1;
                                    }
                            }
                        
                        return 0;
                    });

                    $sortable_li.detach().hide().appendTo($sortable_list).slideDown(100);
                },
                
            sticky_toggle: function(element)
                {
                    if(jQuery(element).find('.a_sticky').length > 0)
                        {
                            jQuery(element).find('.a_sticky').remove();
                            jQuery(element).find(' > .item').removeClass('is-sticky');
                            return;   
                        }
                        
                    jQuery(element).prepend('<div class="a_sticky"><input type="text" class="sticky-input" value="" name="p_sticky_val" onblur="APTO.sticky_change(this)" /></div>');
                    jQuery(element).find(' > .item').addClass('is-sticky');
                },
                
            sticky_change:  function(element)
                {
                    var sticky_value = jQuery(element).val();

                    
                },
                
            
            AddFallBackAutomaticOrder:  function (element)
                {
                    //show the loading
                    jQuery('#automatic_insert_mark').find('.ajax_loading').show();
                    
                    var group_id = 1;
                    group_id = parseInt(jQuery('#apto_form_order .automatic_order_by').last().attr('data-id')) + 1;
                                        
                    var queryString = {};
                    queryString.action      =   "apto_automatic_add_falback_order";
                    queryString.group_id    =   group_id;
                    this.AJAX_call(queryString, this.ProcessFallBackAutomaticOrder);
                   
                },
            ProcessFallBackAutomaticOrder:  function (response)
                {
                    //show the loading
                    jQuery('#automatic_insert_mark').find('.ajax_loading').hide();
                    jQuery('#automatic_insert_mark').before(response.html);

                },
            RemoveAutomaticOrderFallback: function (element)
                {
                    var group_id = 1;
                    group_id = jQuery('#apto_form_order').find(element).closest('tr').attr('data-id');
                    
                    jQuery('#apto_form_order .automatic_order_by[data-id="' + group_id  +'"]').remove();
                    jQuery('#apto_form_order .automatic_order[data-id="' + group_id  +'"]').remove();
                    
                },
                
            apto_autosort_orderby_field_change: function (element)
                {
                    var element_value = jQuery(element).val();
                    var group_id = jQuery(element).closest('tr').attr('data-id'); 
                    
                    if(element_value == '_custom_field_')
                        jQuery(element).closest('tr').find('#apto_custom_field_area_' + group_id).show('fast');
                        else
                        jQuery(element).closest('tr').find('#apto_custom_field_area_' + group_id).hide('fast');
                    
                },
                
            ForceNumbersOnly    :   function (element, event)
                {
                    var key;
                    var keychar;

                    if (window.event)
                    {
                        key = window.event.keyCode;
                    }
                    else if (event)
                    {
                        key = event.which;
                    }
                    else
                    {
                        return true;
                    }
                    if(key != 46 && key != 8 && key != 45 && key > 31 && (key < 48 || key > 57))
                    {
                        return false;
                    }
                    else
                    {
                        return true;
                    }
                }
                
        }
        

        
    function apto_change_taxonomy(element, is_archive)
        {
            //select the default category (0)
            if (is_archive === true)
                {
                    jQuery('#apto_form').find('#cat').remove();   
                }
                else
                {
                    jQuery('#apto_form').find('#cat').remove();
                    //jQuery('#apto_form #cat').val(jQuery("#apto_form #cat option:first").val());        
                }
            jQuery('#apto_form').submit();
        }

        
            
        
    function apto_move_element(element, position)
        {
            var sortable_holder = jQuery(element).closest('ul');
            
            switch(position)
                {
                    case    'top'   :
                                        jQuery(element).slideUp('fast', function() {
                                            jQuery(sortable_holder).prepend(jQuery(element));
                                            jQuery(element).slideDown('fast');
                                        });       
                                        break; 
                   
                   case    'bottom'   :
                                        jQuery(element).slideUp('fast', function() {
                                            jQuery(sortable_holder).append(jQuery(element));
                                            jQuery(element).slideDown('fast');
                                        });       
                                        break; 
                    
                }
            
            
            
        }
        
    
    jQuery(document).ready(function() {
        
        jQuery('.postbox .handlediv, .postbox .handle').live('click', function(){
                
                var postbox = jQuery(this).closest('.postbox');
                var queryString = {};
                
                if( postbox.hasClass('closed') )
                    {
                        postbox.find(' > .inside').slideDown('fast', function() { 
                            postbox.removeClass('closed');
                        });
                        
                        queryString.status    =   'opened';
                    }
                    else
                    {
                        postbox.find(' > .inside').slideUp('fast', function() { 
                            postbox.addClass('closed');        
                        });
                        
                        queryString.status    =   'closed';
                    }
                    
                //save the action
                
                queryString.action      =   "apto_metabox_toggle";
                queryString.type        =   'settings';
                queryString.sort_id     =   jQuery('input#sort_id').val();
                
                if(queryString.sort_id !== undefined && queryString.sort_id > 0)
                    APTO.AJAX_call(queryString);
                
            });
            
        jQuery('table.apto_rules tr, table.apto_table tr').live({
                mouseover: function() {
                    jQuery(this).find('.buttons').addClass('visible');
                },
                mouseout: function() {
                    jQuery(this).find('.buttons').removeClass('visible');
                }
                });

        jQuery('body').on('keypress', '.sticky-input', function (event){
            return(APTO.ForceNumbersOnly(this, event));
        });
            
    }) 
