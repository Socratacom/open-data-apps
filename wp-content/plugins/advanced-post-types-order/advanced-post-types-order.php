<?php
/*
Plugin Name: Advanced Post Types Order
Plugin URI: http://www.nsp-code.com
Description: Order Post Types Objects using a Drag and Drop Sortable javascript capability
Author: Nsp Code
Author URI: http://www.nsp-code.com 
Version: 3.1.6
*/


    define('CPTPATH',   plugin_dir_path(__FILE__));
    define('CPTURL',    plugins_url('', __FILE__));

    define('APTO_VERSION', '3.1.6');
    define('APTO_DB_VERSION', '1.1');
    define('APTO_APP_API_URL',      'http://www.nsp-code.com/index.php'); 
    //define('APTO_APP_API_URL',      'http://127.0.0.1/nsp-code/index.php');
    define('APTO_SLUG',      basename(dirname(__FILE__)));
      
    //load language files
    add_action( 'plugins_loaded', 'apto_load_textdomain'); 
    function apto_load_textdomain() 
        {
            load_plugin_textdomain('apto', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang');
        }

    
    include_once(CPTPATH . '/include/apto_functions-class.php');
    include_once(CPTPATH . '/include/apto_conditionals-class.php');
    include_once(CPTPATH . '/include/apto-class.php');
    
        
    include_once(CPTPATH . '/include/functions.php');
    include_once(CPTPATH . '/include/licence.php'); 
    include_once(CPTPATH . '/include/updater.php'); 

    include_once(CPTPATH . '/include/addons.php');

    register_deactivation_hook(__FILE__, 'CPTO_deactivated');
    register_activation_hook(__FILE__, 'CPTO_activated');

    function CPTO_activated($network_wide) 
        {
            global $wpdb;
            
            include_once(CPTPATH . '/include/apto_admin_functions-class.php');
                             
            // check if it is a network activation
            if ( $network_wide ) 
                {
                    
                    $current_blog = $wpdb->blogid;
                    
                    // Get all blog ids
                    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                    foreach ($blogids as $blog_id) 
                        {
                            switch_to_blog($blog_id);
                            APTO_admin_functions::check_version_update();
                        }
                    
                    switch_to_blog($current_blog);
                    
                    return;
                }
                else
                APTO_admin_functions::check_version_update(); 
        }

    function CPTO_deactivated() 
        {
            
        }
    
    //check on settings when new blog created    
    add_action( 'wpmu_new_blog', 'APTO_new_blog', 10, 6);       
    function APTO_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) 
        {
            global $wpdb;
         
            if (is_plugin_active_for_network('advanced-post-types-order/advanced-post-types-order.php')) 
                {
                    $current_blog = $wpdb->blogid;
                    
                    switch_to_blog($blog_id);
                    
                    include_once(CPTPATH . '/include/apto_admin_functions-class.php');
                    APTO_admin_functions::check_version_update();
                    
                    switch_to_blog($current_blog);
                }
        }
        
    
    //early trigger
    add_action('plugins_loaded', 'APTO_plugins_loaded');
    function APTO_plugins_loaded()
        {
            global $APTO;
            
            $APTO = new APTO();
            
            if(is_admin() && is_user_logged_in() && !defined('DOING_AJAX'))
                {
                    include_once(CPTPATH . '/include/apto_admin_functions-class.php');
                    include_once(CPTPATH . '/include/apto_options_class.php');
                    
                    $APTO_options_interface   =   new APTO_options_interface();
                    if(is_multisite())
                        {
                            if($APTO->licence->licence_key_verify())
                                add_action( 'admin_menu', array($APTO_options_interface, 'create_plugin_options'), 100 );
                        }   
                        else
                        add_action( 'admin_menu', array($APTO_options_interface, 'create_plugin_options'), 100 );
                        
                    if(!is_network_admin())
                        {
                            //check the required settings and database table
                            APTO_admin_functions::check_version_update(); 
                        }
                        else
                        {
                            //run the shceduled actions for all blogs
                            //this is the superadmin interface
                            
                            //ToBe Implemented   
                        }
                }
        }
            
    add_action('init', 'APTO_init' );
    function APTO_init()
        {
            global $APTO;

            //add AJAX actions 
            if(is_admin() && defined('DOING_AJAX'))
                {
                    include_once(CPTPATH . '/include/apto_interface_helper-class.php');
                    include_once(CPTPATH . '/include/apto_admin_functions-class.php');
                    include_once(CPTPATH . '/include/apto_walkers.php'); 
                    
                    $APTO_interface_helper = new APTO_interface_helper();
            
                    add_action( 'wp_ajax_apto_get_rule_box', array($APTO_interface_helper, 'get_rule_box') );
                    add_action( 'wp_ajax_apto_get_conditional_group', array($APTO_interface_helper, 'get_conditional_group') );
                    add_action( 'wp_ajax_apto_get_conditional_rule', array($APTO_interface_helper, 'get_conditional_rule') );
                    add_action( 'wp_ajax_apto_change_taxonomy_item', array($APTO_interface_helper, 'change_taxonomy_item') );
                    add_action( 'wp_ajax_apto_metabox_toggle', array($APTO_interface_helper, 'metabox_toggle') );
                    add_action( 'wp_ajax_update-custom-type-order', array($APTO_interface_helper, 'saveAjaxOrder') );
                    
                    add_action( 'wp_ajax_apto_automatic_add_falback_order', array($APTO_interface_helper, 'automatic_add_falback_order') );
                }
                
            else if (is_admin() && is_user_logged_in()) 
                {
                    include_once(CPTPATH . '/include/apto_admin_functions-class.php');
                    
                    $APTO_admin_functions = new APTO_admin_functions();
                    add_action( 'admin_menu', array($APTO_admin_functions, 'create_menu_items'), 99 );

                }
            else
                {
                    //this is front side load shortcode
                    include_once(CPTPATH . '/shortcodes/apto_shortcodes.php');
                       
                }

        }
     

    
    
    function APTO_posts_groupby($groupby, $query) 
        {
            //check for NOT IN taxonomy operator
            if(isset($query->tax_query->queries) && count($query->tax_query->queries) == 1 )
                {
                    if(isset($query->tax_query->queries[0]['operator']) && $query->tax_query->queries[0]['operator'] == 'NOT IN')
                        $groupby = '';
                }
               
            return($groupby);
        }
        
    function APTO_posts_distinct($distinct, $query) 
        {
            //check for NOT IN taxonomy operator
            if(isset($query->tax_query->queries) && count($query->tax_query->queries) == 1 )
                {
                    if(isset($query->tax_query->queries[0]['operator']) && $query->tax_query->queries[0]['operator'] == 'NOT IN')
                        $distinct = 'DISTINCT';
                }
                   
            return($distinct);
        }    

    add_action('wp_loaded', 'init_APTO', 99 );
    function init_APTO() 
        {
	        global $APTO;
            
            if(!$APTO->licence->licence_key_verify())
                return;
                            
            add_filter('pre_get_posts', array($APTO, 'pre_get_posts'));
            add_filter('posts_orderby', array($APTO, 'posts_orderby'), 99, 2);
                
            add_filter('posts_orderby_request', array($APTO->functions, 'wp_ecommerce_orderby'), 99, 2);
            add_filter('posts_groupby',         'APTO_posts_groupby', 99, 2);
            add_filter('posts_distinct',        'APTO_posts_distinct', 99, 2);
                           
            //make sure the vars are set as default
            $options = $APTO->functions->get_settings();

            //next and prevous post links 
            add_filter('get_next_post_where', array($APTO->functions, 'get_next_previous_post_where'), 10, 3);
            add_filter('get_next_post_sort', array($APTO->functions, 'get_next_post_sort'));

            add_filter('get_previous_post_where', array($APTO->functions, 'get_next_previous_post_where'), 10, 3); 
            add_filter('get_previous_post_sort', array($APTO->functions, 'get_previous_post_sort'));

            //bbpress reverse option check
            if (isset($options['bbpress_replies_reverse_order']) && $options['bbpress_replies_reverse_order'] == "1")
                add_filter('bbp_before_has_replies_parse_args', array($APTO->functions, 'bbp_before_has_replies_parse_args' ));

        }
        
        
        

        
        /*
        add_action( 'contextual_help', 'wptuts_screen_help', 10, 3 );
        function wptuts_screen_help( $contextual_help, $screen_id, $screen ) {
         
            // The add_help_tab function for screen was introduced in WordPress 3.3.
            if ( ! method_exists( $screen, 'add_help_tab' ) )
                return $contextual_help;
         
            global $hook_suffix;
         
            // List screen properties
            $variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
                . sprintf( '<li> Screen id : %s</li>', $screen_id )
                . sprintf( '<li> Screen base : %s</li>', $screen->base )
                . sprintf( '<li>Parent base : %s</li>', $screen->parent_base )
                . sprintf( '<li> Parent file : %s</li>', $screen->parent_file )
                . sprintf( '<li> Hook suffix : %s</li>', $hook_suffix )
                . '</ul>';
         
            // Append global $hook_suffix to the hook stems
            $hooks = array(
                "load-$hook_suffix",
                "admin_print_styles-$hook_suffix",
                "admin_print_scripts-$hook_suffix",
                "admin_head-$hook_suffix",
                "admin_footer-$hook_suffix"
            );
         
            // If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
            if ( did_action( 'add_meta_boxes_' . $screen_id ) )
                $hooks[] = 'add_meta_boxes_' . $screen_id;
         
            if ( did_action( 'add_meta_boxes' ) )
                $hooks[] = 'add_meta_boxes';
         
            // Get List HTML for the hooks
            $hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode( '</li><li>', $hooks ) . '</li></ul>';
         
            // Combine $variables list with $hooks list.
            $help_content = $variables . $hooks;
         
            // Add help panel
            $screen->add_help_tab( array(
                'id'      => 'wptuts-screen-help',
                'title'   => 'Screen Information',
                'content' => $help_content,
            ));
            $screen->add_help_tab( array(
                'id'      => 'wptuts-screen-help2',
                'title'   => 'Screen Information2',
                'content' => $help_content,
            ));
         
            return $contextual_help;
        }
        
        */
        
        
        
        
        
        
        
        
        
        
        
        
        

?>