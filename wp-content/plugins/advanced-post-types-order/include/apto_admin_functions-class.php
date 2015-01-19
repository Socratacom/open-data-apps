<?php

    class APTO_admin_functions
        {
               
            function __construct()
                {
                    
                }
            
            function __destruct()
                {
                
                }
                
                      
            static function check_version_update()
                {
                    //check if the apto_sort post type exists, oterwise call the function to register it
                    if(!post_type_exists( 'apto_sort' ))
                        APTO_register_post_types();
                    
                    //check on the database version and if need update
                    $settings  = APTO_functions::get_settings();
                    
                    //check the table                    
                    if(!isset($settings['database_version']) || version_compare( $settings['database_version'], APTO_DB_VERSION , '<' ) )
                        self::update_tables();
                    
                    //check for settings
                    if(!isset($settings['plugin_version'])) 
                        self::create_settings();
                        else if (version_compare( $settings['plugin_version'], APTO_VERSION , '<' ))
                        {
                            self::update_from_old_version_settings();   
                        }
                    
                    if(isset($settings['schedule_data_import']) && $settings['schedule_data_import'] === TRUE)
                        {
                            $settings  = APTO_functions::get_settings();
                                    
                            unset($settings['schedule_data_import']);
                            add_action('admin_menu', array(__CLASS__, 'old_version_data_import'), 98);
                            
                            APTO_functions::update_settings($settings);   
                        }
                       
                }
            
            /**
            * Create the required plugin settings
            * 
            */
            static function create_settings()
                {
                    $settings  = APTO_functions::get_settings();      
                    
                    //update and further processing area
                    $settings['plugin_version']   =   APTO_VERSION;
                    
                    $roles_capability = APTO_functions::roles_capabilities();

                    //check if it's a blank install or update from V2
                    $old_version_options = get_option('cpto_options');
                    if (isset($old_version_options['code_version']) && version_compare( strval( '2.5' ), $old_version_options['code_version'] , '<' ) === TRUE)
                        {
                            $settings['schedule_data_import']   =   true;     
                        }
                        else
                        {
                            include_once(CPTPATH . '/include/apto_interface_helper-class.php');
                            
                            //create default sorts instances for post tyes
                            $blog_post_types    =   APTO_functions::get_post_types();   
                            
                            $ignore = array (
                                                'acf'
                                                );
                                                
                            foreach ($blog_post_types as $key => $post_type)
                                {
                                    if (in_array($post_type, $ignore))
                                        continue;
                                        
                                    $post_type_data    =   get_post_type_object($post_type);
                                     
                                    //ignore if not show in menus
                                    if($post_type_data->show_in_menu === FALSE)
                                       continue;
                                        
                                    $post_type_menu_item   =   '';
                                    switch($post_type)
                                        {
                                            case 'post';
                                                            $post_type_menu_item    =   'edit.php';
                                                            break;
                                            
                                            case 'attachment';
                                                            $post_type_menu_item    =   'upload.php';
                                                            break;
                                                            
                                            default:
                                                            $post_type_menu_item    =   'edit.php?post_type=' . $post_type;
                                        }
                                        
                                    if(!is_bool($post_type_data->show_in_menu) && $post_type_data->show_in_menu != '')
                                        $post_type_menu_item    =   $post_type_data->show_in_menu;
                                     
                                     $options    =   array(
                                                                '_title'                =>  'Sort #' . $post_type_data->label,
                                                                '_description'          =>  '',
                                                                '_location'             =>  $post_type_menu_item,
                                                                '_autosort'             =>  'yes',
                                                                '_adminsort'            =>  'yes',
                                                                '_new_items_to_bottom'  =>  'no',
                                                                '_show_thumbnails'      =>  'no',
                                                                '_capability'           =>  $roles_capability['Administrator']['capability']
                                                                );
                                     
                                     $sort_view_meta     =   array(
                                                                    '_order_type'               =>  'manual',
                                                                    '_view_selection'           =>  'archive',
                                                                    '_view_language'            =>  APTO_functions::get_blog_language()
                                                                    );  
                                     $sort_id   =   self::create_post_type_sort($post_type, $options, $sort_view_meta);
                                     
                                }
                                
                                
                            //hide by default certain re-order menus
                            $hide   =   array(
                                                'edit-comments.php',
                                                'edit-tags.php?taxonomy=link_category'
                                                );
                            foreach($hide as $hide_item)
                                {
                                    $settings['show_reorder_interfaces'][$hide_item]    =   'hide';
                                }

                        }
            
                    
                    APTO_functions::update_settings($settings);
                }
            
            
            static function update_from_old_version_settings()
                {
                    $settings  = APTO_functions::get_settings();      
                    
                    //update and further processing area
                    $settings['plugin_version']   =   APTO_VERSION; 
                    
                    APTO_functions::update_settings($settings);  
                }
            
            static function old_version_data_import()
                {
                    $settings  = APTO_functions::get_settings();
                       
                    $roles_capability = APTO_functions::roles_capabilities();
                    
                    $old_version_options = get_option('cpto_options');
                    
                    include_once(CPTPATH . '/include/apto_interface_helper-class.php');
                            
                    $roles_capability = APTO_functions::roles_capabilities();
                    
                    //create default sorts instances for post tyes
                    $blog_post_types    =   APTO_functions::get_post_types();
                    
                    $ignore = array (
                                        'acf'
                                        ); 
                    
                    foreach ($blog_post_types as $key => $post_type)
                        {
                            if (in_array($post_type, $ignore))
                                unset($blog_post_types[$key]);   
                            
                            if(isset($old_version_options['allow_post_types']) && is_array($old_version_options['allow_post_types']) && count($old_version_options['allow_post_types']) > 0)
                                {
                                    if (!in_array($post_type, $old_version_options['allow_post_types']))
                                        unset($blog_post_types[$key]);                                                 
                                }
                        }
                        
                    if(count($blog_post_types) > 0)
                        {
                            $available_menus    =   self::get_available_menu_locations();
                            
                            $autosort   =   isset($old_version_options['autosort']) ?   $old_version_options['autosort']    :   '1';
                            if($autosort    === "1")
                                $autosort   =   'yes';
                                else
                                $autosort   =   'no';
                            $adminsort   =   isset($old_version_options['adminsort']) ?   $old_version_options['adminsort']    :   '1';
                            if($adminsort    === "1")
                                $adminsort   =   'yes';
                                else
                                $adminsort   =   'no';
                            $show_thumbnails   =   isset($old_version_options['always_show_thumbnails']) ?   $old_version_options['always_show_thumbnails']    :   '';
                            if($show_thumbnails    === "1")
                                $show_thumbnails   =   'yes';
                                else
                                $show_thumbnails   =   'no';
                            

                            $capability =   self::match_capability_with_availables($old_version_options['capability'], $roles_capability);
                            
                            global $wpdb, $blog_id;
                            
                            //find out the available languages in the sort
                            $mysql_query            =   "SELECT lang FROM `" . $wpdb->base_prefix ."apto` 
                                                            GROUP BY lang";
                            $sort_languages_raw         =   $wpdb->get_results($mysql_query);
                            foreach($sort_languages_raw as $data)
                                {
                                    $sort_languages[]   =   $data->lang;
                                }
                            unset($sort_languages_raw);
                                
                            foreach ($blog_post_types as $key => $post_type)
                                {
                                    $post_type_data    =   get_post_type_object($post_type);
                                     
                                    //ignore if not show in menus
                                    if($post_type_data->show_in_menu === FALSE)
                                        continue;
                                     
                                    $post_type_menu_item   =   '';
                                    switch($post_type)
                                        {
                                            case 'post';
                                                            $post_type_menu_item    =   'edit.php';
                                                            break;
                                            
                                            case 'attachment';
                                                            $post_type_menu_item    =   'upload.php';
                                                            break;
                                                            
                                            default:
                                                            $post_type_menu_item    =   'edit.php?post_type=' . $post_type;
                                        }
                                        
                                    if(!is_bool($post_type_data->show_in_menu) && $post_type_data->show_in_menu != '')
                                        $post_type_menu_item    =   $post_type_data->show_in_menu;
                                     
                                     $options    =   array(
                                                                '_title'                =>  'Sort #' . $post_type_data->label,
                                                                '_description'          =>  '',
                                                                '_location'             =>  $post_type_menu_item,
                                                                '_autosort'             =>  $autosort,
                                                                '_adminsort'            =>  $adminsort,
                                                                '_new_items_to_bottom'  =>  'no',
                                                                '_show_thumbnails'      =>  $show_thumbnails,
                                                                '_capability'           =>  $capability
                                                                );
                                                                
                                     //create the sort and a default sort view as archive
                                     $sort_view_meta     =   array(
                                                            '_order_type'               =>  'manual',
                                                            '_view_selection'           =>  'archive',
                                                            '_view_language'            =>  APTO_functions::get_blog_language()
                                                            
                                                            );    
                                     $sort_id   =   self::create_post_type_sort($post_type, $options, $sort_view_meta);
                                     
                                     $old_options_post_type_terms   =   array();
                                     
                                     //check sort type auto or manual
                                     if(isset($old_version_options['taxonomy_settings']) && is_array($old_version_options['taxonomy_settings']) && isset($old_version_options['taxonomy_settings'][$post_type])
                                                && is_array($old_version_options['taxonomy_settings'][$post_type]) && count($old_version_options['taxonomy_settings'][$post_type]) > 0)
                                                {
                                                    foreach($old_version_options['taxonomy_settings'][$post_type] as $option_post_type_selection  =>    $data_block)
                                                        {     
                                                            //check if the taxonomy still exists and is assigned to current post type
                                                            if($option_post_type_selection != '_archive_' && (!taxonomy_exists($option_post_type_selection) || !in_array($option_post_type_selection , get_object_taxonomies($post_type))))
                                                                continue;
                                                            
                                                            foreach($data_block as $term_id =>  $data)
                                                                {
                                                                    //check if it's auto
                                                                    if($data['order_type'] != 'auto')
                                                                        continue;
                                                                        
                                                                    if(!isset($data['order_by']) || $data['order_by'] == '')
                                                                        $data['order_by'] = '_default_';
                                                                    if(!isset($data['custom_field_name']) || $data['custom_field_name'] == '')
                                                                        $data['custom_field_name'] = '';
                                                                    if(!isset($data['order'])   ||  $data['order']  ==  '')
                                                                        $data['order'] = 'DESC';
                                                                    
                                                                    $old_options_post_type_terms[]  =   $term_id;
                                                                    
                                                                    if($option_post_type_selection == '_archive_')
                                                                        {
                                                                            foreach($sort_languages as $language)
                                                                                {
                                                                                    //check if already created this sort view
                                                                                    $attr           =   array(
                                                                                                                '_view_selection'           =>  'archive',
                                                                                                                '_view_language'            =>  $language
                                                                                                                );
                                                                                    $sort_view_id   =   APTO_functions::get_sort_view_id_by_attributes($sort_id, $attr);
                                                                                    
                                                                                    //create the view if does not exists
                                                                                    if($sort_view_id    ==  '')
                                                                                        {
                                                                                            $sort_view_meta     =   array(
                                                                                                                                '_view_selection'           =>  'archive',
                                                                                                                                '_view_language'            =>  $language
                                                                                                                            );    
                                                                                            $sort_view_id       =   APTO_interface_helper::create_view($sort_id, $sort_view_meta);   
                                                                                        }
                                                                                    
                                                                                    update_post_meta($sort_view_id, '_order_type', 'auto');
                                                                                    update_post_meta($sort_view_id, '_auto_order_by', $data['order_by']); 
                                                                                    update_post_meta($sort_view_id, '_auto_custom_field_name', $data['custom_field_name']);
                                                                                    update_post_meta($sort_view_id, '_auto_custom_field_type', $data['custom_field_type']); 
                                                                                    update_post_meta($sort_view_id, '_auto_order', $data['order']);
                                                                                }
                                                                            continue;
                                                                        }
                                                                        
                                                                    //check if the term still exists
                                                                    if(!term_exists(intval($term_id), $option_post_type_selection))
                                                                        continue;
                                                                    
                                                                    $sort_view_meta     =   array(
                                                                                                        '_order_type'               =>  'auto',
                                                                                                        '_view_selection'           =>  'taxonomy',
                                                                                                        '_taxonomy'                 =>  $option_post_type_selection,
                                                                                                        '_term_id'                  =>  $term_id
                                                                                                    );    
                                                                    $sort_view_id       =   APTO_interface_helper::create_view($sort_id, $sort_view_meta);
                                                                        
                                                                    update_post_meta($sort_view_id, '_order_type', 'auto');
                                                                    update_post_meta($sort_view_id, '_auto_order_by', $data['order_by']); 
                                                                    update_post_meta($sort_view_id, '_auto_custom_field_name', $data['custom_field_name']);
                                                                    update_post_meta($sort_view_id, '_auto_custom_field_type', $data['custom_field_type']);
                                                                    update_post_meta($sort_view_id, '_auto_order', $data['order']);
                                                                }
                                                        }
                                                    
                                                    
                                                }
                                     
                                     //process data in the table
                                     $mysql_query   =   "SELECT term_id, taxonomy FROM " . $wpdb->base_prefix ."apto
                                                            WHERE blog_id = '". $blog_id ."' AND post_type =   '" . $post_type  . "'
                                                            GROUP BY term_id";
                                     $post_type_terms       =   $wpdb->get_results($mysql_query);
                                     
                                     
                                     foreach ($post_type_terms as $data)
                                        {
                                            //check if is set as autosort
                                            //allow in case user change his mind and switch back to manual sort
                                            /*
                                            if(in_array($data->term_id, $old_options_post_type_terms))
                                                continue;
                                            */
                                                
                                            //check if the term still exists
                                            if($data->term_id > 0 && !term_exists(intval($data->term_id), $data->taxonomy))
                                                continue;
                                                
                                            if($data->term_id < 1)
                                                {
                                                    //process each language as there can be sort for each
                                                    foreach($sort_languages as $language)
                                                        { 
                                                            $mysql_query   =   "SELECT post_id FROM " . $wpdb->base_prefix ."apto
                                                                                    WHERE blog_id = '". $blog_id ."' AND post_type =   '" . $post_type  . "' AND term_id ='".$data->term_id ."' AND taxonomy = '".$data->taxonomy."' AND lang = '".$language."'
                                                                                    ORDER BY id ASC";
                                                            $post_type_term_sort_data      =   $wpdb->get_results($mysql_query);
                                                            
                                                            if(count($post_type_term_sort_data) < 1)
                                                                continue;
                                                            
                                                            $attr           =   array(
                                                                                        '_view_selection'           =>  'archive',
                                                                                        '_view_language'            =>  $language
                                                                                        );
                                                            $sort_view_id   =   APTO_functions::get_sort_view_id_by_attributes($sort_id, $attr);
                                                            
                                                            //create the view if does not exists
                                                            if($sort_view_id    ==  '')
                                                                {
                                                                    $sort_view_meta     =   array(
                                                                                                        '_view_selection'           =>  'archive',
                                                                                                        '_view_language'            =>  $language,
                                                                                                        '_order_type'               =>  'manual'
                                                                                                    );    
                                                                    $sort_view_id       =   APTO_interface_helper::create_view($sort_id, $sort_view_meta);   
                                                                }
                                                            
                                                            //create the entries within the apto_sort_list table
                                                            $mysql_query    =   "INSERT INTO `". $wpdb->prefix ."apto_sort_list`
                                                                                      (id, sort_view_id, object_id)
                                                                                    VALUES ";
                                                            $first = TRUE;
                                                            foreach($post_type_term_sort_data as $sort_post_data)
                                                                {
                                                                    if($first === TRUE)   
                                                                        $first  = FALSE;
                                                                        else
                                                                        $mysql_query    .=  ", \n";
                                                                        
                                                                    $mysql_query  .= "(null, ". $sort_view_id .", ". $sort_post_data->post_id .")";
                                                                }
                                                            $results = $wpdb->get_results($mysql_query);
                                                        }
                                                }
                                                else
                                                {
                                                    //create the sort entries for this
                                                    $mysql_query   =   "SELECT post_id FROM " . $wpdb->base_prefix ."apto
                                                                            WHERE blog_id = '". $blog_id ."' AND post_type =   '" . $post_type  . "' AND term_id ='".$data->term_id ."' AND taxonomy = '".$data->taxonomy."'
                                                                            ORDER BY id ASC";
                                                    $post_type_term_sort_data      =   $wpdb->get_results($mysql_query);
                                                    
                                                    
                                                    $sort_view_meta     =   array(
                                                                            '_order_type'               =>  'manual',
                                                                            '_view_selection'           =>   'taxonomy',
                                                                            '_taxonomy'                 =>   $data->taxonomy,
                                                                            '_term_id'                  =>   $data->term_id
                                                                            );
                                                    
                                                    $sort_view_id       =   APTO_interface_helper::create_view($sort_id, $sort_view_meta);
                                                    
                                                    //create the entries within the apto_sort_list table
                                                    $mysql_query    =   "INSERT INTO `". $wpdb->prefix ."apto_sort_list`
                                                                              (id, sort_view_id, object_id)
                                                                            VALUES ";
                                                    $first = TRUE;
                                                    foreach($post_type_term_sort_data as $sort_post_data)
                                                        {
                                                            if($first === TRUE)   
                                                                $first  = FALSE;
                                                                else
                                                                $mysql_query    .=  ", \n";
                                                                
                                                            $mysql_query  .= "(null, ". $sort_view_id .", ". $sort_post_data->post_id .")";
                                                        }
                                                    $results = $wpdb->get_results($mysql_query);
                                                }

                                            
                                        }
                                        
                                     //mark as show this menu where post type reside
                                     $settings['show_reorder_interfaces'][$post_type_menu_item] =   'show';
                                }   
                            
                        }
                        
                    //migrating the remaining settings
                    $ignore_supress_filters   =   isset($old_version_options['ignore_supress_filters']) ?   $old_version_options['ignore_supress_filters']    :   '';
                    $settings['ignore_supress_filters'] =   $ignore_supress_filters;
                    
                    //mark all remaining menus as hide
                    foreach($available_menus as $available_menu =>  $available_menu_data)
                        {
                            if(!isset($settings['show_reorder_interfaces'][$available_menu]))
                                $settings['show_reorder_interfaces'][$available_menu]   =   'hide';
                        }
                        
                    
                    APTO_functions::update_settings($settings);
                
                }    
            
            /**
            * @desc 
            * 
            * Create plugin required tables
            * 
            */
            static function update_tables()
                {
                    
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    global $wpdb;
                    
                    $query = "CREATE TABLE IF NOT EXISTS `". $wpdb->prefix ."apto_sort_list` (
                                  `id` int(11) NOT NULL auto_increment,
                                  `sort_view_id` int(11) NOT NULL,
                                  `object_id` int(11) NOT NULL,
                                  PRIMARY KEY  (`id`),
                                  KEY `sort_view_id` (`sort_view_id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
                    dbDelta($query);
                    
                    $settings  = APTO_functions::get_settings();
                    
                    //set the database settings
                    $settings['database_version']   =   APTO_DB_VERSION;
                    
                    
                    APTO_functions::update_settings($settings);
                }
            
            
            static function create_post_type_sort($post_type, $options, $sort_view_meta)
                {
                    $post_type_data    =   get_post_type_object($post_type);
                        
                     //create the sort
                    $post_data  =   array(
                                            'post_type'     =>  'apto_sort',
                                            'post_status'   =>  'publish',
                                            'post_title'    =>  'Sort #' . $post_type_data->label
                                            );
                    $sort_id = wp_insert_post( $post_data );
                                        
                    $rules  =   array();
                    $rules['post_type']   =   array($post_type);
                    update_post_meta($sort_id, '_rules', $rules);
                                        
                    //process the conditionals
                    $conditionals = array();
                    update_post_meta($sort_id, '_conditionals', $conditionals);
                    
                    foreach($options as $option_key =>  $value)
                        {
                            //add as meta value
                            update_post_meta($sort_id, $option_key, $value);
                        }
                        
                    update_post_meta($sort_id, '_view_type', 'multiple'); 
                    
                    //create the default view for this sortID
                    $sort_view_id       =   APTO_interface_helper::create_view($sort_id, $sort_view_meta);
                    
                    //set this sort view as default for the main sort
                    update_post_meta($sort_id, '_last_sort_view_ID', $sort_view_id);
                    
                    return $sort_id;
                    
                }
            
                
            /**
            * Create menu items
            * 
            *     
            */
            function create_menu_items()
                {
                    global $userdata, $APTO;
                    
                    //load the general styling
                    add_action('admin_print_styles' , array($this, 'admin_print_styles_general'));
                    
                    if(!$APTO->licence->licence_key_verify())
                        return;
                    
                    $settings  = APTO_functions::get_settings(); 
                    
                    //apply a filter to allow capability overwrite; usefull when in multisite environment
                    $admin_capability = apply_filters('apto_reorder_capability', 'switch_themes');
                    
                    //put a menu for all custom_type
                    $post_types = get_post_types();
                    $ignore = array (
                                        'revision',
                                        'nav_menu_item'
                                        );
                    
                    $location_menus = $this->get_available_menu_locations();
                    
                    //check for removed menu and relocate sorts if belong to those
                    $this->check_removed_menus_and_relocate($location_menus);
                       
                    foreach( $location_menus as $location_menu_slug    =>  $location_menu_data ) 
                        {
                            //check for hide
                            if(isset($settings['show_reorder_interfaces'][$location_menu_slug]) && $settings['show_reorder_interfaces'][$location_menu_slug] == 'hide')
                                continue;
                                
                            //check for capability
                            if(!current_user_can('switch_themes'))
                                {
                                    //get menu items
                                    $menu_sorts =   $this->get_tabs($location_menu_slug);
                                    if($menu_sorts <    1)
                                        continue;
                                    
                                    //check for user capability on at least one sort item
                                    $capability =   '';
                                    foreach($menu_sorts as $menu_sort)
                                        {
                                            $sort_required_capability   =   get_post_meta($menu_sort->ID, '_capability', TRUE);
                                            if(current_user_can($sort_required_capability))
                                                {
                                                    $capability =   $sort_required_capability;
                                                    break;   
                                                }
                                        }
                                    
                                    //continue if no capabioity on any
                                    if($capability == '')
                                        continue;
                                    
                                }
                                else
                                $capability =   $admin_capability;  
                                
                            $hookID   = add_submenu_page($location_menu_slug, 'Re-Order', 'Re-Order', $capability, 'apto_' . $location_menu_data['slug'], array($this, 'load_reorder_interface') );
                                
                            //debug interface
                            //add_action( $hookID, array($this, 'load_required_interface_code'));

                            //load the interface helper
                            add_action('load-' . $hookID , array($this, 'load_dependencies'));
                            add_action('all_admin_notices' , array($this, 'admin_notices'));
                            
                            add_action('admin_print_styles-' . $hookID , array($this, 'admin_print_styles'));
                            add_action('admin_print_scripts-' . $hookID , array($this, 'admin_print_scripts'));
                            
                        }
    
                }
                
            static function get_available_menu_locations()
                {
                    global $menu;
                                                            
                    $location_menus = array();
                    
                    $allow_areas =   array(
                                            'edit.php',
                                            'upload.php'
                                            );
                    
                    //filter the menus
                    foreach($menu as $key   =>  $menu_item)
                        {
                            foreach($allow_areas as $allow_area)
                                {
                                    if(strpos($menu_item[2], $allow_area) === 0)   
                                        $location_menus[]   =   $menu_item;   
                                }
                        }
                    
                    $locations  =   array();    
                    foreach($location_menus as $location_menus_item)
                        {
                            $menu_title =   $location_menus_item[0];
                            $tags = array( 'p', 'span');
                            $menu_title = preg_replace( '#<(' . implode( '|', $tags) . ')[^>]+>.*?</\1>#s', '', $menu_title);
                            $menu_title =   trim(strip_tags($menu_title));
                            $locations[$location_menus_item[2]] =   array(
                                                                                            'slug'  =>  sanitize_title($location_menus_item[2]),  
                                                                                            'name'  =>  $menu_title
                                                                                            );
                        }

                    return $locations;
                }
                
                
            function get_tabs($menu_location)
                {                    
                    global $wpdb;
                    
                    $tabs   =   array();
                    
                    $mysql_query = "SELECT * FROM ". $wpdb->posts ."
                                        INNER JOIN ". $wpdb->postmeta ." AS PM ON (". $wpdb->posts .".ID = PM.post_id)
                                        WHERE ". $wpdb->posts .".post_parent = 0  
                                                AND ". $wpdb->posts .".post_type = 'apto_sort' 
                                                AND ". $wpdb->posts .".post_status = 'publish' 
                                                AND PM.meta_key = '_location' AND PM.meta_value = '".$menu_location."'";
                    $results =   $wpdb->get_results($mysql_query); 
                    
                    foreach($results as $result)
                        {
                            $tabs[]     =   (object)$result;   
                        }
                    
                   
                    return $tabs;
                }
                
                
            function load_reorder_interface()
                {
                    $APTO_interface         = new APTO_interface();
                    $APTO_interface->reorder_interface();
                }
                
            function load_required_interface_code()
                {
                    $screen = get_current_screen();
                    echo '<pre>';
                    print_r($screen);  
                    echo '</pre>';
                }   
                
            function load_dependencies()
                {
                    include_once(CPTPATH . '/include/apto_interface_helper-class.php');
                    include_once(CPTPATH . '/include/apto_interface-class.php');
                }
                
            function admin_notices()
                {
                    $messages = array();
                    if(isset($_GET['settings_saved']) && $_GET['settings_saved'] == 'true')
                        $messages[] =   'Sort settings saved.';    
                        
                    if(isset($_GET['sort_deleted']) && $_GET['sort_deleted'] == 'true')
                        $messages[] =   'Sort deleted.';
                    
                    if(count($messages) > 0)
                        {
                            echo "<div id='notice' class='updated fade'><p>". implode("</p><p>", $messages )  ."</p></div>";
                        }
                }
                  
            function admin_print_styles()
                {
                    wp_register_style('CPTStyleSheets', CPTURL . '/css/apto.css');
                    wp_enqueue_style( 'CPTStyleSheets');   
                }
                
            function admin_print_styles_general()
                {
                    wp_register_style('APTO_GeneralStyleSheet', CPTURL . '/css/general.css');
                    wp_enqueue_style( 'APTO_GeneralStyleSheet');   
                }
                
            function admin_print_scripts()
                {
                    wp_enqueue_script('jquery');                         
                    wp_enqueue_script('jquery-ui-core');
                    wp_enqueue_script('jquery-ui-sortable');
                    wp_enqueue_script('jquery-ui-widget');
                    wp_enqueue_script('jquery-ui-mouse');
                    
                    $myJavascriptFile = CPTURL . '/js/touch-punch.min.js';
                    wp_register_script('touch-punch.min.js', $myJavascriptFile, array(), '', TRUE);
                    wp_enqueue_script( 'touch-punch.min.js');
                       
                    $myJavascriptFile = CPTURL . '/js/nested-sortable.js';
                    wp_register_script('nested-sortable.js', $myJavascriptFile, array(), '', TRUE);
                    wp_enqueue_script( 'nested-sortable.js');
                     
                    $myJavascriptFile = CPTURL . '/js/apto-javascript.js';
                    wp_register_script('apto-javascript.js', $myJavascriptFile);
                    wp_enqueue_script( 'apto-javascript.js');  
                }
                
            
            function check_removed_menus_and_relocate($location_menus)
                {
                    $settings  = APTO_functions::get_settings();
                    
                    //this setting has never been set by user
                    if(count($settings['show_reorder_interfaces']) < 1 || count($location_menus) <  1)
                        return;
                    
                    $first_menu =   $this->get_first_available_menu($location_menus);
                    if($first_menu === FALSE)
                        {
                            $apto_system_messages['relocate'][]   =   'All interfaces are set to hide, at least a visible is required. You can change tht from Settings';   
                        }
                        else
                        {
                            $apto_system_messages['relocate'] =   array();
                            
                            //get all sorts
                            $args = array(
                                            'post_type'             => 'apto_sort',
                                            'post_parent'           =>  0,
                                            'orderby'               => 'ID',
                                            'order'                 => 'ASC',
                                            'posts_per_page'        =>  -1,
                                            'force_no_custom_order' =>  TRUE
                                            );
                            $custom_query = new WP_Query($args);
                            if($custom_query->have_posts())
                                {
                                    global $post;
                                    $_wp_query_post =   $post;
                                    global $apto_system_messages;
                                                                
                                    while($custom_query->have_posts())
                                        {
                                            $custom_query->the_post();
                                            
                                            $sort_location  =   get_post_meta($post->ID, '_location', TRUE);
                                            
                                            //check if the menu still exists
                                            if(!isset($location_menus[$sort_location]) || (isset($settings['show_reorder_interfaces'][$sort_location]) && $settings['show_reorder_interfaces'][$sort_location] == 'hide'))
                                                {
                                                    //relocate the item
                                                    update_post_meta($post->ID, '_location', $first_menu);
                                                    
                                                    //show relocate messages
                                                    $apto_system_messages['relocate'][]   =   'Sort ' . '<b>' . $post->post_title . ' ('. $post->ID .' )</b>'. ' has been relocated to ' .$location_menus[$first_menu]['name'] . ' menu';
                                                }
                                            
                                        }
                                        
                                    //wp_reset_postdata();
                                    //use this instead as using a setup_postdata() without any query will reset to nothing
                                    $post   =   $_wp_query_post;
               
                                }
                        }
                        
                    if(count($apto_system_messages['relocate']) > 0)
                        {
                            array_unshift($apto_system_messages['relocate'], "Advanced Post Types Order - At least one menu has changed:");
                            add_action('admin_notices', array($this, 'relocate_nottices'));
                        }
                  
                }
                
            function get_first_available_menu($location_menus)
                {
                    $settings  = APTO_functions::get_settings();
                    
                    //this setting has never been set by user
                    if(count($settings['show_reorder_interfaces']) < 1 || count($location_menus) <  1)
                        {
                            reset($location_menus);
                            return key($location_menus);
                        }
                        
                    foreach($location_menus as $ocation_key =>  $location_data)
                        {
                            if(isset($settings['show_reorder_interfaces'][$ocation_key]) && $settings['show_reorder_interfaces'][$ocation_key] == 'show')
                                return $ocation_key;
                        }
                        
                    return false;
                }
                
            function relocate_nottices()
                {
                    global $apto_system_messages;
            
                    if(count($apto_system_messages['relocate']) < 1)
                        return;
                    
                    echo "<div id='notice' class='updated fade'><p>". implode("</p><p>", $apto_system_messages['relocate'] )  ."</p></div>";
                }
                
            static function match_capability_with_availables   ($mathch_capability, $available_roles_capability)
                {
                    $match =  $available_roles_capability['Administrator']['capability'];
                    if($mathch_capability == '')
                        return $match;
                        
                    foreach($available_roles_capability as $role    =>  $role_data)
                        {
                            if($mathch_capability   ==  $role_data['capability'])
                                return $mathch_capability;
                        }
                       
                    return $match;
                }
              
            
            
        }



?>