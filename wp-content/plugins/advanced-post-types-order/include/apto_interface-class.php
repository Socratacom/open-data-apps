<?php

class APTO_interface 
    {
        var $interface_helper;
        var $functions;
              
        var $sortID;
        var $sort_settings;
        
        var $current_sort_view_ID;
        var $current_sort_view_settings;
        
        var $menu_location  =   '';
        var $menu_tabs      =   array();
        
        var $is_shortcode_interface;
        
        var $interface_hide_archive  =   FALSE;
        
        function __construct() 
            {
                
            }
            
        function init()
            {
                $this->is_shortcode_interface   =   FALSE;
                   
                //load additional resources
                include_once(CPTPATH . '/include/apto_walkers.php');
                
                $this->functions            =   new APTO_functions();
                $this->admin_functions      =   new APTO_admin_functions();
                $this->interface_helper     =   new APTO_interface_helper();
                
                $this->menu_location        =   $this->interface_helper->get_current_menu_location();
                $this->menu_tabs            =   $this->admin_functions->get_tabs($this->menu_location);
                
                $this->new_item_action      =   isset($_GET['new-item']) ?  TRUE : FALSE;
                
                $this->sortID               =   $this->get_current_sort_id();
                                                
                //check for solrt list deletion
                $this->interface_helper->sort_list_delete();
                
                //check for different interface settings changes like order_type
                if($this->sortID != '')
                    $this->interface_helper->general_interface_update($this->sortID);
                
                //check for sort settings update
                $this->interface_helper->settings_update();
                   
                //check for sort list update (automatic order as the manual is sent through ajax)
                $this->interface_helper->automatic_sort_order_update();

            }
            
        /**
        * Get current interface sort_id 
        * 
        */
        function get_current_sort_id()
            {
                $sort_id    =   isset($_GET['sort_id']) ?  $_GET['sort_id'] : '';
                
                if($sort_id == '' && $this->new_item_action ===   TRUE)
                    return '';
                
                if($sort_id == '')
                    {
                             
                        if(count($this->menu_tabs) > 0)
                            {
                                foreach($this->menu_tabs as $menu_tab)
                                    {
                                        //check if use have capability to view this sort
                                        $sort_required_capability   =   get_post_meta($menu_tab->ID, '_capability', TRUE);
                                        if(!current_user_can($sort_required_capability))
                                            continue;
                                            
                                        $sort_id        =   $menu_tab->ID;
                                        break;        
                                    }
                            }
                    }
                    
                if($sort_id == '')
                    $this->new_item_action  =   TRUE;
                
                return $sort_id;
            }
                
        function reorder_interface()
            {
                $this->init();
                
                $this->sort_settings                =   $this->functions->get_sort_settings($this->sortID);
                
                $this->current_sort_view_ID         =   $this->interface_helper->get_last_sort_view_ID($this->sortID);
                
                $view_type  =    $this->interface_helper->get_sort_view_type($this->sortID);
                                
                //if there is no sort view create a default archive
                if($this->new_item_action   === FALSE && $this->current_sort_view_ID == '' && $this->sortID != '')
                    {
                        if($view_type   ==  'multiple')
                            {
                                $sort_view_meta =   array(
                                                            '_order_type'               =>  'manual',
                                                            '_view_selection'           =>  'archive',
                                                            '_view_language'            =>  $this->functions->get_blog_language()
                                                            );
                            }
                        if($view_type   ==  'simple')
                            {
                                $sort_view_meta =   array(
                                                            '_order_type'               =>  'manual',
                                                            '_view_selection'           =>  'simple',
                                                            '_view_language'            =>  $this->functions->get_blog_language()
                                                            );
                            }
                            
                        $this->current_sort_view_ID =   $this->interface_helper->create_view($this->sortID, $sort_view_meta);
                    }
                
                $this->current_sort_view_settings   =   $this->functions->get_sort_view_settings($this->current_sort_view_ID);
                
                $site_settings  = $this->functions->get_settings();
                
                ?>    
                
                    <div class="wrap" id="apto">
                        <div class="icon32" id="icon-edit"><br></div>
                        <h2><?php _e( "Re-order", 'apto' ) ?></h2>
                        
                        <noscript>
                            <div class="error message">
                                <p><?php _e( "This plugin can't work without javascript, because it's use drag and drop and AJAX.", 'apto' ) ?></p>
                            </div>
                        </noscript>

                        <div class="clear"></div>
                    <?php
                              
                        //WPML check the sort rules translation
                        if(defined('ICL_LANGUAGE_CODE') && $view_type   ==  'simple')
                            {
                                $sort_settings_update_languages =   get_post_meta($this->sortID, '_settings_update_languages', TRUE);
                                if(!is_array($sort_settings_update_languages))
                                    $sort_settings_update_languages =   array();
                                    
                                $blog_languages     =   icl_get_languages('skip_missing=N');
                                
                                $default_language   =   $this->functions->get_blog_default_language();
                                $WPML_message       =   '';
                                foreach($blog_languages as $blog_language   =>  $language_data)
                                    {
                                          if(!isset($sort_settings_update_languages[$blog_language]))
                                            $WPML_message   .=  ', ' . $language_data['native_name'];
                                    }
                                
                                $WPML_message   =   ltrim($WPML_message, ", ");
                                if($WPML_message != '')   
                                    {
                                        ?>
                                            <div class="error message">
                                                <p><?php _e( "WPML: The Sort Rules couldn't be translated automatically. You need to set/translate this manually for", 'apto' ) ?> <?php echo $WPML_message .'. Change language, set appropriate rules and click sort settings Update.' ?></p>
                                            </div>
                                        <?php
                                    }                                
                                
                                
                            }

                        if($this->is_shortcode_interface !== TRUE)
                            {
                            ?>
                            
                            <h2 class="nav-tab-wrapper" id="apto-nav-tab-wrapper">
                                <?php
                                                            
                                    foreach( $this->menu_tabs as $apto_sort_data)
                                        {
                                            //check if use have capability to view this sort
                                            $sort_required_capability   =   get_post_meta($apto_sort_data->ID, '_capability', TRUE);
                                            if(!current_user_can($sort_required_capability))
                                                continue;
                                            
                                            //use the first item if $this->sortID is empty
                                            if($this->sortID == '' && $this->new_item_action    === FALSE)
                                                $this->sortID = $apto_sort_data->ID;
                                            
                                            ?>
                                                <a class="nav-tab<?php if($this->sortID == $apto_sort_data->ID) { echo ' nav-tab-active';} ?>" href="<?php 
                                                
                                                $link_argv  =   array(
                                                                        'sort_id' => $apto_sort_data->ID
                                                                        );
                                                $link_argv['page'] =   'apto_' . $this->interface_helper->get_current_menu_location_slug();
                                                
                                                $link_argv['base_url'] =   $this->interface_helper->get_current_menu_location();
                                                                                        
                                                echo $this->interface_helper->get_tab_link($link_argv) ;
                                                
                                                ?>"><?php echo $apto_sort_data->post_title  ?></a>
                                            <?php
                                        }
                                
                                //add also empty selection to allow new sort creation
                                if(current_user_can('switch_themes'))
                                    {
                                        ?>
                                            <a class="nav-tab<?php if($this->sortID == '') { echo ' nav-tab-active';} ?>" href="<?php 
                                                
                                                $link_argv  =   array(
                                                                        'new-item' => 'true'
                                                                        );
                                                $link_argv['page'] =   'apto_' . $this->interface_helper->get_current_menu_location_slug();
                                                
                                                $link_argv['base_url'] =   $this->interface_helper->get_current_menu_location();
                                                echo $this->interface_helper->get_tab_link($link_argv) ;
                                                
                                        ?>">+</a>
                                        <?php
                                    }
                                
                                ?>
                            </h2>
                            
                            
                            <?php 
                            }
                    
                        if(current_user_can('switch_themes') && $this->is_shortcode_interface === FALSE)
                            $this->settings();
                            else
                            $this->sort_description();
                    
                        //output the sort interface only if there is a sort id
                        if($this->sortID != '')
                            $this->sort_area();
                            
                    ?>
                
                    </div>
                
                <?php
    

            }
            
        
        function settings()
            {
                $site_settings  = $this->functions->get_settings();
                
                //this helds information about a query change, i.e. a term has been removed and does not appear anymore on the intface.
                $found_changes  =   FALSE;
                
                ?>
                <form action="" method="post">

                    <input type="hidden"  name="sort_id" value="<?php echo $this->sortID ?>" id="sort_id" />
                    <input type="hidden"  name="apto_sort_settings_form_submit" value="1" />
                    
                    <div id="poststuff" class="meta-box-sortables">
                    <div class="postbox apto_metabox<?php
                        
                            //check the status of metabox
                            $metabox_toggle = get_post_meta($this->sortID, '_metabox_toggle', TRUE);
                            if(!is_array($metabox_toggle))
                                $metabox_toggle = array();
                            
                            if(isset($metabox_toggle['settings']) && $metabox_toggle['settings'] == 'closed')
                                echo ' closed';
                        
                        ?>" id="apto_options">
                        <div title="Click to toggle" class="handlediv"><br></div>
                        <h3 class="handle"><span class="icon settings">&nbsp;</span><span>Sort List Settings</span></h3>
                        <div class="inside"<?php
                                
                            if(isset($metabox_toggle['settings']) && $metabox_toggle['settings'] == 'closed')
                                echo ' style="display: none"';
                        
                        ?>>
                            <table class="apto_input widefat">
                                <tbody>
                                    <tr id="query_rules">
                                        <td class="label">
                                            <label for="">Query Rules</label>
                                            <p class="description">Create a set of criteria rules which match your query. This will determine what to show on the following sort list and the order will apply on front side.</p>
                                            <p class="description">All rules are compared to a query using AND operator. For more details check <a href="http://www.nsp-code.com/advanced-post-types-order-description-and-usage/understanding-sort-list-settings-area/">Query Rules examples</a></p>
                                        </td>
                                        <td>
                                            <div id="rules-post-type">
                                                
                                                <?php
                                                    
                                                    $button_show_advanced   =   '';
                                                    $view_type  =    $this->interface_helper->get_sort_view_type($this->sortID);
                                                      
                                                    if($this->sortID == '' || ($this->sortID != '' && (
                                                        (!isset($this->sort_settings['_rules']['taxonomy']) || (is_array($this->sort_settings['_rules']['taxonomy']) && count($this->sort_settings['_rules']['taxonomy']) < 1)) && 
                                                        (!isset($this->sort_settings['_rules']['author']) || ( is_array($this->sort_settings['_rules']['author']) && count($this->sort_settings['_rules']['author']) < 1))
                                                        )))
                                                        {
                                                            $button_show_advanced = true;
                                                        }
                                                    
                                                    if($button_show_advanced === TRUE)
                                                        {
                                                            ?><a id="button_show_adv" data-status="simple" onClick="APTO.interface_query_advanced_toggle()" href="javascript: void(0)">Show Advanced</a><?php            
                                                        }
                                                ?>
                                                <h4>Post Type</h4>
                                                <table class="apto_input widefat apto_rules apto_table">
                                                    <tbody>
                                                        <?php
                                                        
                                                            if($this->new_item_action   === TRUE)
                                                                $sort_rules =   array();
                                                                else
                                                                $sort_rules =   $this->functions->get_sort_current_language_rules($this->sort_settings);
                                                            if(isset($sort_rules['post_type']) && count($sort_rules['post_type']) > 0)
                                                                {
                                                                    $rule_id = 1;
                                                                    foreach($sort_rules['post_type'] as $rule_post_type)   
                                                                        {
                                                                            //check if post_type still exists
                                                                            $exists =   post_type_exists($rule_post_type);
                                                                            if($exists === FALSE)
                                                                                {
                                                                                    $found_changes[]  =   'Custom Post Type '.  $rule_post_type .' invalid'; 
                                                                                    continue;
                                                                                }
                                                                            
                                                                            $argv = array();
                                                                            
                                                                            if($rule_id < 2)
                                                                                $argv['default']    =   TRUE;
                                                                            
                                                                            $argv['selected_value'] =   $rule_post_type;
    
                                                                            $rule_box = $this->interface_helper->get_rule_post_type_html_box($argv);
                                                                            echo $rule_box;
                                                                            
                                                                            $rule_id++;   
                                                                        }
                                                                }
                                                                else
                                                                    {
                                                                        $interface_post_type    =   isset($_GET['post_type']) ?  $_GET['post_type']   :   '';
                                                                        
                                                                        $argv   =   array(
                                                                                            'default'           =>  TRUE,
                                                                                            'selected_value'    =>  $interface_post_type
                                                                                            );
                                                                        $rule_box = $this->interface_helper->get_rule_post_type_html_box($argv);
                                                                        echo $rule_box;
                                                                        
                                                                        unset($interface_post_type);
                                                                    }
                                                        
                                                        ?>
                                                    </tbody>
                                                </table>
                                                
                                                <table class="apto_input widefat apto_more">
                                                    <tbody>
                                                        <tr><td><a class="button-secondary" href="javascript: void(0)" onClick="APTO.add_rule_post_type()">Add Post Type</a> &nbsp;&nbsp;<img class="ajax_loading" src="<?php echo CPTURL ?>/images/ajax-loader.gif" alt="Loading" /></td></tr>
                                                    </tbody>
                                                 </table>
                                                 
                                             </div>
                                             
                                            <div id="rules-taxonomy"<?php if($button_show_advanced === TRUE) {echo ' style="display: none"';} ?>>
                                                <h4>Taxonomy</h4>

                                                <?php
                                                        
                                                    if(isset($sort_rules['taxonomy']) && count($sort_rules['taxonomy']) > 0)
                                                        {
                                                            $group_id = 1;
                                                            foreach($sort_rules['taxonomy'] as $rule_block)   
                                                                {
                                                                    //check if the taxonomy still exists
                                                                    $exists =   taxonomy_exists($rule_block['taxonomy']);
                                                                    if($exists === FALSE)
                                                                        {
                                                                            $found_changes[]  =   'Taxonomy '.  $rule_block['taxonomy'] .' invalid'; 
                                                                            continue;
                                                                        }
                                                                    
                                                                    foreach($rule_block['terms'] as $rule_term)
                                                                        {
                                                                            $exists =   term_exists( (int)$rule_term, $rule_block['taxonomy']);
                                                                            if($exists === FALSE || $exists == NULL)
                                                                                $found_changes[]  =   'Term '.  $rule_term .' invalid';   
                                                                        }
                                                                    
                                                                    $argv   =   array(
                                                                                        'group_id'      =>  $group_id,
                                                                                        'taxonomy'      =>  $rule_block['taxonomy'],
                                                                                        'operator'      =>  $rule_block['operator'],
                                                                                        'selected'      =>  $rule_block['terms']
                                                                                        ); 
                                                                    
                                                                    $argv['html_alternate'] =   FALSE;
                                                                    if($group_id % 2 == 0)
                                                                        $argv['html_alternate'] =   TRUE;
                                                                    
                                                                    echo $this->interface_helper->get_rule_taxonomy_html_box($argv);
                                                                    
                                                                    $group_id++;   
                                                                }
                                                        }
                                                
                                                ?>

                                                <div class="insert_root"></div>
                                                
                                                <table class="apto_input widefat apto_more">
                                                    <tbody>
                                                        <tr><td><a class="button-secondary" href="javascript: void(0)" onClick="APTO.add_rule_taxonomy()">Add Taxonomy</a> &nbsp;&nbsp;<img class="ajax_loading" src="<?php echo CPTURL ?>/images/ajax-loader.gif" alt="Loading" /></td></tr>
                                                    </tbody>
                                                </table>
                                                
                                                <h4>Taxonomy Relation</h4>
                                                <table class="apto_input widefat taxonomy_relationship">
                                                    <tbody>
                                                        <tr>
                                                            <td class="param">
                                                                <select class="select" name="rules[taxonomy_relation]">
                                                                    <?php
                                            
                                                                        $operator_values = array(
                                                                                                   'AND',
                                                                                                   'OR'
                                                                                                    );
                                                                        foreach($operator_values as $operator_value)
                                                                            {
                                                                                ?><option <?php if(isset($sort_rules['taxonomy_relation']) && $operator_value == $sort_rules['taxonomy_relation']) { echo 'selected="selected"'; }?>    value="<?php echo $operator_value ?>"><?php echo $operator_value ?></option><?php
                                                                            }
                                                                    ?>

                                                                </select>
                                                            </td>
                                                            <td class="value"></td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                                                                                 
                                            </div>     
                                            
                                            <div id="rules-author"<?php if($button_show_advanced === TRUE) {echo ' style="display: none"';} ?>>
                                                <h4>Author</h4>
                                                
                                                <table class="apto_input widefat apto_rules apto_table">
                                                    <tbody>
                                                        <?php
                                                        
                                                            if(isset($sort_rules['author']) && count($sort_rules['author']) > 0)
                                                                {
                                                                    foreach($sort_rules['author'] as $authorID)   
                                                                        {
                                                                            $argv = array();
                                                                            $argv['selected'] =   $authorID;
                                                                            
                                                                            $rule_box = $this->interface_helper->get_rule_author_html_box($argv);
                                                                            echo $rule_box; 
                                                                        }
                                                                }
                                                        
                                                        ?>
                                                    </tbody>
                                                </table>
                                                
                                                <table class="apto_input widefat apto_more">
                                                    <tbody>
                                                        <tr><td><a class="button-secondary" href="javascript: void(0)" onClick="APTO.add_rule_author()">Add Author</a> &nbsp;&nbsp;<img class="ajax_loading" src="<?php echo CPTURL ?>/images/ajax-loader.gif" alt="Loading" /></td></tr>
                                                    </tbody>
                                                </table>

                                           </div>                                                
                                           
                                           
                                           
                                           <?php
                                            
                                                if($found_changes !== FALSE)
                                                    {
                                                        ?>
                                                            <div id="found_changes" class="updated">
                                                                <p>Certain changes has been done to your site and some of Query Rules cannot be displayed anymore. You should review the settings Query Rules area and click Update button.</p>
                                                                <p><?php echo implode("<br />", $found_changes) ?></p>
                                                            </div>
                                                        <?php   
                                                    }
                                            
                                            ?>
                                        </td>
                                    </tr>
                                    
                                    

                                    
                                    <tr id="conditional_rules">
                                        <td class="label">
                                            <label for="">Conditionals</label>
                                            <p class="description">Apply the order only if conditions are true.</p>
                                        </td>
                                        <td>
                                            <h4>Apply if</h4>
                                            
                                            <?php
                                            
                                                $sort_conditionals = get_post_meta($this->sortID, '_conditionals', TRUE);
                                                if(is_array($sort_conditionals)  && count($sort_conditionals) > 0)
                                                    {
                                                        $group_id = 1;
                                                        foreach($sort_conditionals as $key  =>  $group_data)
                                                            {
                                                                $argv   =   array(
                                                                                    'group_id'      =>  $group_id,
                                                                                    'data'          =>  $group_data
                                                                                    );      
                                                                echo $this->interface_helper->get_html_conditional_group($argv);
                                                                
                                                                $group_id++;
                                                            }
                                                    }
                                            
                                            ?>
                                            
                                  
                                            <table class="apto_input widefat apto_more" id="add_conditional_group">
                                                <tbody>
                                                    <tr><td><a class="button-secondary" href="javascript: void(0)" onClick="APTO.add_conditional_group(this)">Add Group </a> &nbsp;&nbsp;<img class="ajax_loading" src="<?php echo CPTURL ?>/images/ajax-loader.gif" alt="Loading" /></td></tr>
                                                </tbody>
                                            </table>

                                    </tr>
                                    

                                    <tr>
                                        <td class="label">
                                            <label for="">Interface</label>
                                            <p class="description">This sort interface settings</p>
                                        </td>
                                        <td class="np">
                                            
                                            <table class="apto_input inner_table widefat">
                                                <tbody>
                                                    <tr><td>
                                                        <h4>Title</h4>
                                                        <p class="description">Sort list tab title</p>
                                                        <input type="text" value="<?php echo $this->interface_helper->get_sort_meta($this->sortID, '_title'); ?>" class="text" name="interface[_title]">
                                                    </td></tr>
                                                    <tr><td>
                                                        <h4>Description</h4>
                                                        <p class="description">Sort list description. This will appear for others (non-admin users) when doing re-sort, it should include a description for what area this sort will apply.</p>
                                                        <textarea class="large-text" cols="50" rows="3" name="interface[_description]"><?php echo htmlspecialchars($this->interface_helper->get_sort_meta($this->sortID, '_description')); ?></textarea>
                                                    </td></tr>
                                                    <tr><td>
                                                        <h4>Menu Location</h4>
                                                        <p class="description">Relocate this Sort Interface to another menu</p>
                                                        <select name="interface[_location]" class="select">
                                                            <?php
                                                            
                                                                foreach ($this->admin_functions->get_available_menu_locations() as $location    =>  $location_data)
                                                                    {
                                                                        //check for hide
                                                                        if(isset($site_settings['show_reorder_interfaces'][$location]) && $site_settings['show_reorder_interfaces'][$location] == 'hide')
                                                                            continue;
                                                                                                                                                
                                                                        ?>
                                                                        <option <?php if(isset($this->sort_settings['_location']) && $location == $this->sort_settings['_location']
                                                                            || (!isset($this->sort_settings['_location']) && $this->interface_helper->get_current_menu_location_slug() == $location_data['slug'])
                                                                            
                                                                        ) { ?>selected="selected" <?php } ?> value="<?php echo $location ?>"><?php echo $location_data['name'] ?></option>
                                                                        <?php
                                                                    }
                                                            
                                                            ?>
                                                        </select>
                                                    </td></tr>
                                                    <tr><td>
                                                        <h4>Auto Sort</h4>
                                                        <p class="description">Automatically apply the sort to theme queries if match.</p>
                                                        <fieldset>
                                                            <label><input type="radio" <?php if($this->interface_helper->get_sort_meta($this->sortID, '_autosort') == 'yes' || $this->interface_helper->get_sort_meta($this->sortID, '_autosort') == '') { ?>checked="checked"<?php } ?> value="yes" name="interface[_autosort]"> <span>Yes</span></label><br>
                                                            <label><input type="radio" <?php if($this->interface_helper->get_sort_meta($this->sortID, '_autosort') == 'no') { ?>checked="checked"<?php } ?> value="no" name="interface[_autosort]"> <span>No</span></label><br>
                                                        </fieldset>
                                                    </td></tr>
                                                    <tr><td>
                                                        <h4>Admin Sort</h4>
                                                        <p class="description">Update admin order if match.</p>
                                                        <fieldset>
                                                            <label><input type="radio" <?php if($this->interface_helper->get_sort_meta($this->sortID, '_adminsort') == 'yes' || $this->interface_helper->get_sort_meta($this->sortID, '_adminsort') == '') { ?>checked="checked"<?php } ?> value="yes" name="interface[_adminsort]"> <span>Yes</span></label><br>
                                                            <label><input type="radio" <?php if($this->interface_helper->get_sort_meta($this->sortID, '_adminsort') == 'no') { ?>checked="checked"<?php } ?> value="no" name="interface[_adminsort]"> <span>No</span></label><br>
                                                        </fieldset>
                                                    </td></tr>
                                                    <tr><td>
                                                        <h4>Send new items to bottom of list</h4>
                                                        <p class="description">All new posts / custom types will append at the end instead top. This will apply when manual ordering.</p>
                                                        <fieldset>
                                                            <label><input type="radio" <?php if($this->interface_helper->get_sort_meta($this->sortID, '_new_items_to_bottom') == 'yes') { ?>checked="checked"<?php } ?> value="yes" name="interface[_new_items_to_bottom]"> <span>Yes</span></label><br>
                                                            <label><input type="radio" <?php if($this->interface_helper->get_sort_meta($this->sortID, '_new_items_to_bottom') == 'no' || $this->interface_helper->get_sort_meta($this->sortID, '_new_items_to_bottom') == '') { ?>checked="checked"<?php } ?> value="no" name="interface[_new_items_to_bottom]"> <span>No</span></label><br>
                                                        </fieldset>
                                                    </td></tr>
                                                    <tr><td>
                                                        <h4>Thumbnails</h4>
                                                        <p class="description">Show thumbnails on sort list</p>
                                                        <fieldset>
                                                            <label><input type="radio" <?php if($this->interface_helper->get_sort_meta($this->sortID, '_show_thumbnails') == 'yes') { ?>checked="checked"<?php } ?> value="yes" name="interface[_show_thumbnails]"> <span>Yes</span></label><br>
                                                            <label><input type="radio" <?php if($this->interface_helper->get_sort_meta($this->sortID, '_show_thumbnails') == 'no' || $this->interface_helper->get_sort_meta($this->sortID, '_show_thumbnails') == '') { ?>checked="checked"<?php } ?> value="no" name="interface[_show_thumbnails]"> <span>No</span></label><br>
                                                        </fieldset>
                                                    </td></tr>

                                                    <tr><td>
                                                        <h4>Capability / Role</h4>
                                                        <p class="description">Minimum Capability to see this Sort</p>
                                                        <select name="interface[_capability]" class="select">
                                                            <?php
                                                            
                                                                $roles_capability = $this->functions->roles_capabilities();
                                                            
                                                                foreach($roles_capability as $role_default_name => $role_info)
                                                                    {
                                                                        ?><option value="<?php echo $role_info['capability'] ?>" <?php 
                                                                            if (
                                                                                    ($this->interface_helper->get_sort_meta($this->sortID, '_capability') == $role_info['capability']) ||
                                                                                    //make default select for Administrator when no capability was previously set
                                                                                    ($this->interface_helper->get_sort_meta($this->sortID, '_capability') == '' & $role_info['capability'] == 'switch_themes')
                                                                                )
                                                                                echo 'selected="selected"';
                                                                            
                                                                        ?>><?php echo $role_info['title'] ?></option><?php
                                                                    }
                                                            
                                                            ?>
                                                        </select>
                                                    </td></tr>
                                                </tbody>
                                            </table>
                                                
                                        </td>
                                    </tr>
                                    
                                    <tr class="submit">
                                        <td class="label">&nbsp;</td>
                                        <td>
                                            <input type="submit" class="save-sort-options button-primary alignright" value="<?php
                                            
                                                if($this->sortID > 0)
                                                    echo 'Update';
                                                    else
                                                    echo 'Create';
                                            
                                            ?>" /> 
                                            <?php if($this->sortID != '') { ?><a href="<?php 
                                                
                                                $link_argv                          =   array();
                                                $link_argv['page']                  =   'apto_' . $this->interface_helper->get_menu_slug_from_menu_id($this->interface_helper->get_current_menu_location());
                                                $link_argv['base_url']              =   $this->interface_helper->get_current_menu_location();
                                                $link_argv['delete_sort']           =   1;
                                                $link_argv['sort_id']               =   $this->sortID;
                                                echo $this->interface_helper->get_tab_link($link_argv);
                                                
                                            ?>" onClick="return APTO.sort_list_delete(this)" class="submitdelete deletion">Delete Sort</a> <?php } ?>
                                        </td>    
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                    
                    </form>
                    
                    <?php
            
            }    
            
        
        function sort_description()
            {
                ?>
                    <div id="sort_description">
                        <?php echo wpautop($this->sort_settings['_description']) ?>
                    </div>
                    
                <?php
            }
        
        function sort_area() 
            {
                global $post; 
                
                //show Archive and Taxonomy (if need)
                $view_type  =    $this->interface_helper->get_sort_view_type($this->sortID);
                $sort_taxonomies = $this->interface_helper->get_sort_taxonomies_by_objects($this->sortID);
                if($view_type == 'multiple' && count($sort_taxonomies) > 0)
                    {
                        //show the hint arrow only if the there are 
                        $this->sort_hint_arrow();
                
                        $this->sort_area_archive_taxonomies();
                    }
                                           
                ?>
                
                <div id="ajax-response"></div> 
                
                <h2 id="apto-nav-tab-wrapper" class="nav-tab-wrapper">
                <?php
                                                                   
                    //output the automatic / manual order tabs menu
                    $tabs = array(
                                    'auto'      =>  'Automatic Order',
                                    'manual'    =>  'Manual Order'
                                    );
                    
                    foreach($tabs as $key => $tab)
                        {
                            ?>
                                <a class="nav-tab<?php if($this->current_sort_view_settings['_order_type'] == $key) { echo ' nav-tab-active';} ?>" href="<?php 
                                
                                $link_argv  =   array(
                                                        'sort_id'       =>  $this->sortID,
                                                        'order_type'    =>  $key,
                                                        'sort_view_id'  =>  $this->current_sort_view_ID
                                                        );
                                
                                if($this->is_shortcode_interface === FALSE)
                                    {
                                        $link_argv['page'] =   'apto_' . $this->interface_helper->get_current_menu_location_slug();
                                        echo $this->interface_helper->get_tab_link($link_argv) ;
                                    }
                                    else
                                    {
                                        $link_argv['base_url']      =   get_permalink($post->ID);
                                        echo $this->interface_helper->get_item_link($link_argv) ;   
                                    }

                                ?>"><?php echo $tab  ?></a>
                            <?php
                        }
               ?>    
               </h2>
               
               <?php
                            
                    //output the re-order interface list
                    if($this->current_sort_view_settings['_order_type'] == 'auto')
                        $this->automatic_interface();
                    
                    if($this->current_sort_view_settings['_order_type'] == 'manual')
                        $this->manual_interface();

            }

        
        function sort_hint_arrow()
            {
                ?>
                    <div id="hint_arrow">
                        <span id="arrow">&nbsp;</span>
                        <p>Select area and customise your order list <br />or switch to automatic</p>
                        <div class="clear"></div>
                    </div>
                <?php
            }
            
    
        /**
        * Output Archive and Taxonomies for current sort id
        * 
        */
        function sort_area_archive_taxonomies()
            {
                global $wpdb, $post;

                //check the taxonomies.
                $sort_taxonomies = $this->interface_helper->get_sort_taxonomies_by_objects($this->sortID);

                if($this->interface_hide_archive !== TRUE)
                    {
                ?>
                <table cellspacing="0" class="wp-list-taxonomy widefat fixed">
                    <thead>
                    <tr>
                        <th style="" class="column-cb check-column" scope="col">&nbsp;</th>
                        <th style="" class="" scope="col"><?php _e( "Archive", 'apto' ) ?></th><th style="" class="manage-column" scope="col"><?php _e( "Total Archive Objects", 'apto' ) ?></th>    
                    </tr>
                    </thead>
                    <tr valign="top" class="alternate">
                            <th class="check-column" scope="row">
                                <input type="radio" onclick="APTO.change_view_selection(this)" value="<?php
                                
                                    $link_argv  =   array(
                                                            'sort_id'           =>  $this->sortID,
                                                            'view_selection'    =>  'archive'
                                                            );
                                    
                                    if($this->is_shortcode_interface === FALSE)
                                        {
                                            $link_argv['page'] =   'apto_' . $this->interface_helper->get_current_menu_location_slug();
                                            echo $this->interface_helper->get_tab_link($link_argv) ;
                                        }
                                        else
                                        {
                                            $link_argv['base_url']      =   get_permalink($post->ID);
                                            echo $this->interface_helper->get_item_link($link_argv) ;   
                                        }
                                
                                
                                ?>" <?php if ($this->current_sort_view_settings['_view_selection'] == 'archive') {echo 'checked="checked"';} ?> name="view_selection">
                            </th>
                            <td class="categories column-categories"><?php _e( "Archive", 'apto' ) ?></td>
                            <td class="categories column-categories"><?php 
                                
                                $count = 0;
                                foreach($this->sort_settings['_rules']['post_type'] as $post_type)
                                    {
                                        $count += array_sum((array)wp_count_posts($post_type));
                                    }
                                    
                                echo $count;
                                
                                ?></td>
                    </tr>
                </tbody>
                </table>
                <?php  } ?>
                    
                <table cellspacing="0" class="wp-list-taxonomy widefat fixed">
                    <thead>
                    <tr>
                        <th style="" class="column-cb check-column" scope="col">&nbsp;</th><th style="" class="" scope="col"><?php _e( "Taxonomy Title", 'apto' ) ?></th><th style="" class="manage-column" scope="col"><?php _e( "Total", 'apto' ) ?> <?php _e( "Posts", 'apto' ) ?></th>    </tr>
                    </thead>

                    <tfoot>
                    <tr>
                        <th style="" class="column-cb check-column" scope="col">&nbsp;</th><th style="" class="" scope="col"><?php _e( "Taxonomy Title", 'apto' ) ?></th><th style="" class="manage-column" scope="col"><?php _e( "Total", 'apto' ) ?> <?php _e( "Posts", 'apto' ) ?></th>    </tr>
                    </tfoot>

                    <tbody id="the-list">
                    <?php
                        
                        $alternate = FALSE;
                        
                        foreach ($sort_taxonomies as $key => $taxonomy)
                            {
                                $alternate = $alternate === TRUE ? FALSE :TRUE;
                                $taxonomy_info = get_taxonomy($taxonomy);
                                
                                $args   =   array(
                                                    'fields'    =>  'ids'
                                                    );
                                $taxonomy_terms_ids = get_terms($taxonomy, $args);

                                if (count($taxonomy_terms_ids) > 0)
                                    {
                                        $term_ids = array_map('intval', $taxonomy_terms_ids );
                                                                                                      
                                        $term_ids = "'" . implode( "', '", $term_ids ) . "'";
                                                                                                                 
                                        $query = "SELECT COUNT(DISTINCT tr.object_id) as count FROM $wpdb->term_relationships AS tr 
                                                        INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                                                        INNER JOIN $wpdb->posts as posts ON tr.object_id = posts.ID
                                                        WHERE tt.taxonomy IN ('$taxonomy') AND tt.term_id IN ($term_ids) AND  posts.post_type IN ('". implode("', '", $this->sort_settings['_rules']['post_type'])   ."')" ;
                                        $count = $wpdb->get_var($query);
                                    }
                                    else
                                        {
                                            $count = 0;   
                                        }
                                
                                ?>
                                    <tr valign="top" class="<?php if ($alternate === TRUE) {echo 'alternate ';} ?>" id="taxonomy-<?php echo $taxonomy  ?>">
                                            <th class="check-column" scope="row"><input type="radio" onclick="APTO.change_view_selection(this)" value="<?php
                                
                                                $link_argv  =   array(
                                                                        'sort_id'           =>  $this->sortID,
                                                                        'view_selection'    =>  'taxonomy',
                                                                        'taxonomy'          =>  $taxonomy
                                                                        );
                                                
                                                if($this->is_shortcode_interface === FALSE)
                                                    {
                                                        $link_argv['page'] =   'apto_' . $this->interface_helper->get_current_menu_location_slug();
                                                        echo $this->interface_helper->get_tab_link($link_argv) ;
                                                    }
                                                    else
                                                    {
                                                        $link_argv['base_url']      =   get_permalink($post->ID);
                                                        echo $this->interface_helper->get_item_link($link_argv) ;   
                                                    }
                                                
                                            
                                            ?>" <?php if ($this->current_sort_view_settings['_view_selection'] == 'taxonomy' && $this->current_sort_view_settings['_taxonomy'] == $taxonomy) {echo 'checked="checked"';} ?> name="view_selection">&nbsp;</th>
                                            <td class="categories column-categories"><p><span><?php echo $taxonomy_info->label ?></span>
                                            
                                                <?php
                                                    if ($this->current_sort_view_settings['_taxonomy'] == $taxonomy)
                                                        {
                                                            //check if there are any terms in that taxonomy before ouptut the dropdown
                                                            $argv = array(
                                                                            'hide_empty'    =>   0
                                                                            );
                                                            $terms = get_terms($this->current_sort_view_settings['_taxonomy'], $argv);
                                                            
                                                            $dropdown_options = array(
                                                                                        'echo'              =>  0,
                                                                                        'hide_empty'        =>  0, 
                                                                                        'hierarchical'      =>  1,
                                                                                        'show_count'        =>  1, 
                                                                                        'orderby'           =>  'name', 
                                                                                        'taxonomy'          =>  $this->current_sort_view_settings['_taxonomy'],
                                                                                        'selected'          =>  $this->current_sort_view_settings['_term_id'],
                                                                                        'class'             =>  'taxonomy_terms',
                                                                                        'walker'            =>  new APTO_Walker_TaxonomiesTermsDropdownCategories(),
                                                                                        'sortID'            =>  $this->sortID,
                                                                                        'apto_interface'    =>  $this
                                                                                        );
                                                            
                                                            if (count($terms) > 0)
                                                                {
                                                                    $select_html = wp_dropdown_categories($dropdown_options);
                                                                    if(!empty($select_html))
                                                                        {
                                                                            $select_html = str_replace("<select ", "<select onchange='APTO.change_view_selection(this)' ", $select_html);
                                                                            echo $select_html;   
                                                                        }
                                                                    
                                                                    $found_action = TRUE;
                                                                }

                                                        } ?></p></td>
                                            <td class="categories column-categories"><?php echo $count ?></td>
                                    </tr>
                                
                                <?php
                            }
                    ?>
                    </tbody>
                </table>
                <div class="spacer">&nbsp;</div>
                <?php

            }
            
        function automatic_interface()
            {
                global $wpdb, $post;
                ?>
                <form action="<?php 
                    
                         $link_argv  =   array(
                                                'sort_id'       =>  $this->sortID,
                                                );
                        
                        if($this->is_shortcode_interface === FALSE)
                            {
                                $link_argv['page'] =   'apto_' . $this->interface_helper->get_current_menu_location_slug();
                                echo $this->interface_helper->get_tab_link($link_argv) ;
                            }
                            else
                            {
                                $link_argv['base_url']      =   get_permalink($post->ID);
                                echo $this->interface_helper->get_item_link($link_argv) ;   
                            }
                        
                        
                        
                    ?>" method="post" id="apto_form_order">
                    
                    <input type="hidden"  name="sort_id" value="<?php echo $this->sortID ?>" id="sort_id" />
                    <input type="hidden" value="<?php echo $this->current_sort_view_ID ?>" name="sort_view_ID" />  
                    <input type="hidden"  name="apto_sort_form_order_update" value="1" />
                    
                    
                    <div id="order-post-type">

                                        
                        <div class="postbox apto_metabox">         
                            <div class="inside">
                                
                                <table class="apto_input widefat apto_table" id="apto_settings">
                                    <tbody>
                                        
                                        <?php 
                                            
                                            $data_set = array(
                                                                'order_by'          =>  (array)$this->current_sort_view_settings['_auto_order_by'],
                                                                'custom_field_name' =>  (array)$this->current_sort_view_settings['_auto_custom_field_name'],
                                                                'custom_field_type' =>  (array)$this->current_sort_view_settings['_auto_custom_field_type'],
                                                                'order'             =>  (array)$this->current_sort_view_settings['_auto_order']
                                                                );
                                                                                        
                                            foreach($data_set['order_by']   as $key =>  $data)
                                                {
                                        
                                                    $options    =   array(
                                                                            'default'           =>  ($key < 1) ? TRUE : FALSE,
                                                                            'group_id'          =>  ($key +  1)
                                                                            );
                                                                                                            
                                                    if(!isset($data_set['order_by'][$key]) || $data_set['order_by'][$key] == '')
                                                        {
                                                            $options['data_set']    =   array(
                                                                                                    'order_by'          =>  '_default_',
                                                                                                    'custom_field_name' =>  '',
                                                                                                    'custom_field_type' =>  '',
                                                                                                    'order'             =>  'DESC'
                                                                                                );
                                                        }
                                                        else
                                                            {
                                                                $options['data_set']    =   array(
                                                                                                            'order_by'          =>  $data_set['order_by'][$key],
                                                                                                            'custom_field_name' =>  $data_set['custom_field_name'][$key],
                                                                                                            'custom_field_type' =>  $data_set['custom_field_type'][$key],
                                                                                                            'order'             =>  $data_set['order'][$key],
                                                                                                            );
                                                            }

                                                    echo $this->interface_helper->html_automatic_add_falback_order($options);
                                                }
                                        ?>
                                    
                                      
                                        
                                        <tr id="automatic_insert_mark">
                                            <td class="label">&nbsp;</td>
                                            <td>
                                                <a onclick="APTO.AddFallBackAutomaticOrder()" href="javascript: void(0)" class="button-secondary">Add Fallback</a> &nbsp;&nbsp;<img class="ajax_loading" src="<?php echo CPTURL ?>/images/ajax-loader.gif" alt="Loading" />
                                            </td>    
                                        </tr>
                                        
                                        <?php 
                            
                                            $view_type  =    $this->interface_helper->get_sort_view_type($this->sortID);
                                            if($view_type == 'multiple' && $this->current_sort_view_settings['_view_selection'] != 'archive')
                                            {
                                        
                                        ?>
                                                                                
                                        <tr>
                                            <td class="label">&nbsp;</td>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <td>
                                                &nbsp;
                                            </td>    
                                        </tr>
                                        
                                        <tr>
                                            <td class="label">
                                                <label for=""><?php _e( "Batch Terms Automatic Update", 'apto' ) ?></label>
                                                <p class="description"><?php _e( "<b>WARNING!</b></i> using this option all existing", 'apto' ) ?> <?php 
                                                    
                                                    $current_taxonomy_info = get_taxonomy($this->current_sort_view_settings['_taxonomy']);
                                                    echo $current_taxonomy_info->label;
                                                    
                                                    ?> <?php _e( "terms order type will update to Automatic Order and change for currrent settings.", 'apto' ) ?> <?php _e( "Existing manual/custom sort lists will be kept, but order type will be switched to Automatic Order.", 'apto' ) ?></p>
                                            </td>
                                            <td>
                                                <input type="radio" checked="checked" value="no" name="batch_order_update" />
                                                <label for="blog-public">No</label><br>

                                                <input type="radio" value="yes" name="batch_order_update" />
                                                <label for="blog-public">Yes</label><br>  

                                            </td> 
                                            <td>
                                                &nbsp;
                                            </td>  
                                        </tr>
                                        <?php } ?>
                                        <tr class="submit">
                                            <td class="label">&nbsp;</td>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <td>
                                                <input type="submit" value="Update" class="button-primary" name="update">    
                                            </td>    
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                            
                        
                    </div>
                    </form>                  
                    <?php
                   
            }
            
        function manual_interface()
            {
                global $wpdb, $wp_locale;
     
                $is_hierarchical = $this->interface_helper->get_is_hierarhical_by_settings($this->sortID);
                
                $filter_date        = isset($_POST['filter_date']) ? $_POST['filter_date'] : 0;
                $search             = isset($_POST['search']) ? $_POST['search'] : '';
                
                
                ?>
                    <form action="" method="post" id="apto_form_order">
                       
                        <div id="order-post-type">
                            
                            <div id="nav-menu-header">
                                <div class="major-publishing-actions">

                                        <div class="alignleft actions"> 
                                        <?php
                                        
                                            $found_action = FALSE;
                                              
                                            if (($is_hierarchical === TRUE || $this->functions->is_woocommerce($this->sortID) === TRUE) && $this->current_sort_view_settings['_view_selection'] == 'archive')
                                                {
                                                }
                                                else
                                                {
                                        
                                                    $arc_query  = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type IN ('". implode("', '", $this->sort_settings['_rules']['post_type'])   ."') ORDER BY post_date DESC";
                                                    $arc_result = $wpdb->get_results( $arc_query );

                                                    $month_count = count($arc_result);

                                                    if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) 
                                                        {
                                                            ?>
                                                                <select name="filter_date">
                                                                    <option<?php selected( $filter_date, 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
                                                                    <option<?php selected( $filter_date, 'today' ); ?> value='today'><?php _e('Today'); ?></option>
                                                                    <option<?php selected( $filter_date, 'yesterday' ); ?> value='yesterday'><?php _e('Yesterday'); ?></option>
                                                                    <option<?php selected( $filter_date, 'last_week' ); ?> value='last_week'><?php _e('Last Week'); ?></option>
                                                                    <?php
                                                                        foreach ($arc_result as $arc_row) 
                                                                            {
                                                                                if ( $arc_row->yyear == 0 )
                                                                                    continue;
                                                                                    
                                                                                $arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

                                                                                if ( $arc_row->yyear . $arc_row->mmonth == $filter_date )
                                                                                    $default = 'selected="selected"';
                                                                                else
                                                                                    $default = '';

                                                                                echo "<option $default value='" . esc_attr("$arc_row->yyear$arc_row->mmonth") . "'>" . $wp_locale->get_month($arc_row->mmonth) . " ". $arc_row->yyear . "</option>\n";
                                                                            }
                                                                    ?>
                                                                </select>
                                                            <?php 
                                                        }
                                                    
                                                    $found_action = TRUE;
                                                }
                                        
                                            if($found_action === TRUE)
                                                {
                                                    ?>
                                                     <input type="submit" class="button-secondary" value="Filter" id="post-query-submit">
                                            <?php } ?>
                                        </div>
                                        
                                        <div class="alignright actions">
                                            <p class="actions">
                                                
                                                <a class="button-secondary alignleft toggle_thumbnails" title="<?php _e( "Toggle Thumbnails", 'apto' ) ?>" href="javascript:;" onclick="APTO.toggle_thumbnails(); return false;"><?php _e( "Toggle Thumbnails", 'apto' ) ?></a>
                                                
                                                <?php if ($is_hierarchical === FALSE && $this->functions->is_woocommerce($this->sortID) === FALSE)
                                                    {
                                                        ?>
                                                        <input type="text" value="<?php echo $search; ?>" name="search" id="post-search-input" class="fl">
                                                        <input type="submit" class="button fl" value="Search">
                                                <?php  } ?>
                                                <span class="img_spacer"><img alt="" src="<?php echo CPTURL ?>/images/wpspin_light.gif" class="waiting pto_ajax_loading" style="display: none;"></span>
                                                <a href="javascript:;" class="save-order button-primary"><?php _e( "Update", 'apto' ) ?></a>
                                            </p>
                                        </div>
                                        
                                        <div class="clear"></div>

                                </div><!-- END .major-publishing-actions -->
                            </div><!-- END #nav-menu-header -->

                                                    
                            <div id="post-body">                    
                                
                                <div id="sort_options">
                                    <a href="javascript: void(0)" onClick="APTO.interface_reverse_order()">Reverse</a> <span>|</span>
                                    <a href="javascript: void(0)" onClick="APTO.interface_title_order('ASC')">Title Asc</a> <span>|</span>
                                    <a href="javascript: void(0)" onClick="APTO.interface_title_order('DESC')">Title Desc</a> <span>|</span>
                                    <a href="javascript: void(0)" onClick="APTO.interface_id_order('ASC')">Id order Asc</a> <span>|</span>
                                    <a href="javascript: void(0)" onClick="APTO.interface_id_order('DESC')">Id order Desc</a>
                                </div>
                                
                                <script type="text/javascript">    
                                
                                    var sort_id         = '<?php echo $this->sortID ?>';
                                    var sort_view_id    = '<?php echo $this->current_sort_view_ID ?>';

                                </script>
                               
                                <ul id="sortable"<?php
                            
                                            if (($is_hierarchical === TRUE || $this->functions->is_woocommerce($this->sortID) === TRUE) && $this->current_sort_view_settings['_view_selection'] == 'archive')
                                                {
                                                    ?> class="nested_sortable"<?php
                                                }
                                                
                                        ?>>
                                    <?php 
                                        
                                        $additional_query_string = 'search='. $search .'&filter_date='.$filter_date;
                                        if (($is_hierarchical === TRUE || $this->functions->is_woocommerce($this->sortID) === TRUE) && $this->current_sort_view_settings['_view_selection'] == 'archive')
                                            {
                                            }
                                            else
                                            $additional_query_string .= '&depth=-1';
                                            
                                        $this->listPostTypeObjects($additional_query_string);
                                    ?>
                                </ul>
                                
                                <div class="clear"></div>
                            </div>
                            
                            <div id="nav-menu-footer">
                                <div class="major-publishing-actions">
                                            
                                        <div class="alignright actions">
                                            <p class="submit">
                                                <img alt="" src="<?php echo CPTURL ?>/images/wpspin_light.gif" class="waiting pto_ajax_loading" style="display: none;">
                                                <a href="javascript:;" class="save-order button-primary"><?php _e( "Update", 'apto' ) ?></a>
                                            </p>
                                        </div>
                                        
                                        <div class="clear"></div>

                                </div><!-- END .major-publishing-actions -->
                            </div><!-- END #nav-menu-header -->  
                            
                        </div> 

                        
                        <br />
                        <a id="order_Reset" class="button-primary" href="javascript: void(0)" onclick="confirmSubmit()"><?php _e( "Reset Order", 'apto' ) ?></a>
                        
                        <script type="text/javascript">
                            
                            function confirmSubmit()
                                {
                                    var agree=confirm("<?php _e( "Are you sure you want to reset the order??", 'apto' ) ?>");
                                    if (agree)
                                        {
                                            jQuery('#apto_form_order_reset').submit();   
                                        }
                                        else
                                        {
                                            return false ;
                                        }
                                }
                            
                            jQuery(document).ready(function() {
                                
                                //jQuery( "#sortable" ).sortable();
                                jQuery('ul#sortable').nestedSortable({
                                        handle:             'div',
                                        tabSize:            30,
                                        listType:           'ul',
                                        items:              'li',
                                        toleranceElement:   '> div',
                                        placeholder:        'ui-sortable-placeholder',
                                        disableNesting:     'no-nesting'
                                        <?php
                            
                                            if (($is_hierarchical === TRUE || $this->functions->is_woocommerce($this->sortID) === TRUE) && $this->current_sort_view_settings['_view_selection'] == 'archive')
                                                {
                                                }
                                                else
                                                {
                                                    ?>,disableNesting      :true<?php
                                                }
                                        ?>
                                    });
                                
                                
                                  
                                jQuery(".save-order").bind( "click", function() {
                                    jQuery(this).parent().find('img').show();
                                    
                                     var queryString = { 
                                                            action:         'update-custom-type-order', 
                                                            order:          jQuery("#sortable").nestedSortable("serialize"), 
                                                            sort_view_id:        sort_view_id, 
                                                            nonce:          '<?php echo wp_create_nonce( 'reorder-interface-' . get_current_user_id()) ?>'
                                                                };
                                    //send the data through ajax
                                    jQuery.ajax({
                                      type: 'POST',
                                      url: ajaxurl,
                                      data: queryString,
                                      cache: false,
                                      dataType: "html",
                                      success: function(response){
                                                        jQuery("#ajax-response").html('<div class="message updated fade"><p>' + response + '</p></div>');
                                                        jQuery("#ajax-response div").delay(3000).hide("slow");
                                                        jQuery('img.pto_ajax_loading').hide();    

                                      },
                                      error: function(html){

                                          }
                                    });
                                });
                            });
                        </script>
                        </form>  
    
                        <form action="" method="post" id="apto_form_order_reset">
                            <input type="hidden" name="order_reset" value="true" />
                            <input type="hidden" value="<?php echo $this->current_sort_view_ID ?>" name="sort_view_ID" /> 
                            
                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'reorder-interface-reset-' . get_current_user_id()) ?>" />
                        </form>

                        <?php
            }
    
        function listPostTypeObjects($args = '') 
            {
                $defaults = array(
                                    'depth'         =>  0,
                                    'post_status'   =>  'any',
                                    'sort_id'       =>  $this->sortID,
                                    'sort_view_id'  =>  $this->current_sort_view_ID
                                );

                $args = wp_parse_args( $args, $defaults );

                if ($this->sort_settings['_view_type'] == 'multiple')
                    {
                        $args['post_type']         =  $this->sort_settings['_rules']['post_type'];
                        $args['posts_per_page']    = -1;
                        $args['orderby']           = 'menu_order';
                        $args['order']             = 'ASC';

                        //set author if need
                        if(isset($this->sort_settings['_rules']['author']) && is_array($this->sort_settings['_rules']['author']) && count($this->sort_settings['_rules']['author']) > 0)
                            $args['author'] =   implode(",",    $this->sort_settings['_rules']['author']);
                        
                        //set taxonomy if need (deppends on current view_selection
                        if($this->current_sort_view_settings['_view_selection'] == 'taxonomy')
                            {
                                $args['tax_query']  =   array(
                                                                    array(
                                                                            'taxonomy'  => $this->current_sort_view_settings['_taxonomy'],
                                                                            'field'     => 'id',
                                                                            'terms'     => $this->current_sort_view_settings['_term_id']
                                                                            )
                                                                    );   
                            }
                             
                    }
                    
                if ($this->sort_settings['_view_type'] == 'simple')
                    {
                        $args['post_type']         =  $this->sort_settings['_rules']['post_type'];
                        $args['posts_per_page']    = -1;
                        $args['orderby']           = 'menu_order';
                        $args['order']             = 'ASC';      

                        $sort_rules = $this->functions->get_sort_current_language_rules($this->sort_settings, FALSE);
                        
                        //set author if need
                        if(isset($sort_rules['author']) && is_array($sort_rules['author']) && count($sort_rules['author']) > 0)
                            $args['author'] =   implode(",",    $sort_rules['author']);
                        
                        //set taxonomy if need (deppends on current view_selection
                        $taxonomy_data              =   $sort_rules['taxonomy'];
                        $taxonomy_data['relation']  =   $sort_rules['taxonomy_relation'];                          
                        $args['tax_query']          =   $taxonomy_data;
                    }
                
                
                //limit the returnds only to IDS to prevent memory exhaust
                if( $this->interface_helper->get_is_hierarhical_by_settings($this->sortID) === TRUE || ($this->functions->is_woocommerce($this->sortID) === TRUE && $this->current_sort_view_settings['_view_selection'] == 'archive'))
                    $args['fields'] = 'ids, post_parent';
                    else
                    $args['fields'] = 'ids';
                    
                if ($this->functions->is_woocommerce($this->sortID) === TRUE && $this->current_sort_view_settings['_view_selection'] == 'archive')
                    {
                        $r['depth'] = 0;
                    }
                    else if($this->functions->is_woocommerce($this->sortID) === TRUE)
                    {
                        $args['meta_query'] = array(
                                                        array(
                                                                'key'       => '_visibility',
                                                                'value'     => array('visible','catalog'),
                                                                'compare'   => 'IN'
                                                            )
                                                    );   
                        
                    }
                    
                //Interface date filter
                if ($args['filter_date'] != '0' )
                    {
                        if ($args['filter_date'] == 'last_week')
                            {
                                $last_week   = strtotime('-8 days');
                                $next_day   = strtotime('+1 day');
      
                                $args['date_query']     = array(
                                                                    array(
                                                                            'after'     => array(
                                                                                                    'year'  => date("Y", $last_week),
                                                                                                    'month' => date("m", $last_week),
                                                                                                    'day'   => date("d", $last_week),
                                                                                                ),
                                                                            'before'    => array(
                                                                                                    'year'  => date("Y", $next_day),
                                                                                                    'month' => date("m", $next_day),
                                                                                                    'day'   => date("d", $next_day),
                                                                                                ),
                                                                            'inclusive' => FALSE,
                                                                        )
                                                                    );
                            }
                            else if ($args['filter_date'] == 'today')
                            {
                                $time = current_time('timestamp');
                                $year               = date("Y", $time);
                                $month              = date("m", $time);
                                $day                = date("d", $time);
                                
                                $args['date_query']     = array(
                                                                    array(
                                                                            'year'  =>  $year,
                                                                            'month' =>  $month,
                                                                            'day'   =>  $day
                                                                        )
                                                                ); 
                            }
                            else if ($args['filter_date'] == 'yesterday')
                            {
                                $time = current_time('timestamp');
                                $time = $time - 86400;
                                $year               = date("Y", $time);
                                $month              = date("m", $time);
                                $day                = date("d", $time);
                                
                                $args['date_query']     = array(
                                                                    array(
                                                                            'year'  =>  $year,
                                                                            'month' =>  $month,
                                                                            'day'   =>  $day
                                                                        )
                                                                );
                            }
                            else
                            {
                                $year   = substr($args['filter_date'], 0, 4);
                                $month  = substr($args['filter_date'], 4, 2);
                                
                                $args['date_query']     = array(
                                                                    array(
                                                                            'year'  => $year,
                                                                            'month' => $month
                                                                        )
                                                                );
                            }
                    }    
                
                //Search filter
                if ($args['search'] != '')
                    {
                        $args['s'] = $args['search'];
                    }
                
                $exclude    =   apply_filters('apto_exclude_posts_from_interface', array(), $args, $this);
                if(is_array($exclude) && count($exclude) > 0)
                    $args['post__not_in']   =   $exclude;
                                                
                $custom_query = new WP_Query($args);
                $found_posts = $custom_query->posts;
                
                if ( !empty($found_posts) ) 
                    {
                        $walker = new Post_Types_Order_Walker;

                        $walker_args = array($found_posts, $args['depth'], $args);
                        echo call_user_func_array(array(&$walker, 'walk'), $walker_args);
                    }

            }

    }





?>
