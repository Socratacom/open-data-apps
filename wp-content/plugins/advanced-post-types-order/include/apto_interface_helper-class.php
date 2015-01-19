<?php
 
    class APTO_interface_helper
        {
            
            var $response           =   array();
            var $functions;
            var $admin_functions;
            var $conditional_rules;
                        
            function __construct()
                {
                    $this->functions            =   new APTO_functions();
                    $this->admin_functions      =   new APTO_admin_functions(); 
                    
                    $this->conditional_rules    =   new APTO_conditionals();
                                                        
                }

            
            function get_current_menu_location()
                {
                    $location_menus = $this->admin_functions->get_available_menu_locations();
                    
                    $current_menu_page  = $_GET['page'];
                    $current_menu_page  =   str_replace('apto_' ,   '', $current_menu_page);
                    
                    foreach($location_menus as $menu_id =>  $location_menu_data)
                        {
                            if($location_menu_data['slug']  ==  $current_menu_page)
                                return $menu_id;
                        }
                    
                    return FALSE;   
                }
                
            function get_current_menu_location_slug()
                {
                    $location_menus = $this->admin_functions->get_available_menu_locations();
                    
                    $current_menu_page  = $_GET['page'];
                    $current_menu_page  =   str_replace('apto_' ,   '', $current_menu_page);
                    
                    foreach($location_menus as $menu_id =>  $location_menu_data)
                        {
                            if($location_menu_data['slug']  ==  $current_menu_page)
                                return $location_menu_data['slug'];
                        }
                    
                    return FALSE;   
                }
                
            function get_menu_id_from_menu_slug($menu_slug)
                {
                    $location_menus = $this->admin_functions->get_available_menu_locations();
                     
                    foreach($location_menus as $menu_id =>  $location_menu_data)
                        {
                            if($location_menu_data['slug']  ==  $menu_slug)
                                return $menu_id;
                        }
                    
                    return FALSE;   
                }
                
            function get_menu_slug_from_menu_id($menu_id)
                {
                    $location_menus = $this->admin_functions->get_available_menu_locations();
                     
                    foreach($location_menus as $location_menu_id =>  $location_menu_data)
                        {
                            if($location_menu_id  ==  $menu_id)
                                return $location_menu_data['slug'];
                        }
                    
                    return FALSE;   
                }
                
            
            
                
            
            function get_sort_meta($sort_id, $meta_name)
                {
                    if($sort_id == '')   
                        return '';
                        
                    return get_post_meta($sort_id, $meta_name, TRUE);
                }
                
            
            /**
            * Check for sort list deletion
            * 
            */
            function sort_list_delete()
                {
                    if(!isset($_GET['delete_sort']))
                        return FALSE;
                    
                    global $post;
                    $_wp_query_post =   $post;
                    
                    $sort_id    =   $_GET['sort_id'];
                    
                    //delete all views sorts (childs)
                    $argv   =   array(
                                        'post_type'         =>  'apto_sort',
                                        'posts_per_page'    =>  '-1',
                                        'post_parent'       =>  $sort_id,
                                        'force_no_custom_order' =>  TRUE  
                                        );
                    $custom_query       =   new WP_Query($argv);
                    while($custom_query->have_posts())
                        {
                            $custom_query->the_post();
                            wp_delete_post( $post->ID, TRUE );    
                        }
                    
                    //wp_reset_postdata();
                    //use this instead as using a setup_postdata() without any query will reset to nothing
                    $post   =   $_wp_query_post;
                    
                    //delete sort holder
                    wp_delete_post( $sort_id, TRUE );
                    
                    //redirect to the list                        
                    $redirect_argv                          =   array();
                    $redirect_argv['page']                  =   'apto_' . $this->get_menu_slug_from_menu_id($this->get_current_menu_location());
                    $redirect_argv['base_url']              =   $this->get_current_menu_location();
                    $redirect_argv['sort_deleted']     =   'true';
                    
                    wp_redirect($this->get_tab_link($redirect_argv));
                    //echo 'REDIRECT TO '  . $this->get_tab_link($redirect_argv);
                    die();
                }
            
            
            function general_interface_update($sortID)
                {
                    $sort_view_ID   =   isset($_GET['sort_view_id']) ? $_GET['sort_view_id'] : '';
                    $taxonomy       =   isset($_GET['taxonomy']) ? $_GET['taxonomy'] : '';
                    $term_id        =   isset($_GET['term_id']) ? $_GET['term_id'] : '';
                    $doRedirect     =   FALSE;
                    
                    //check for order_type update auto /manual
                    $order_type = isset($_GET['order_type']) ? $_GET['order_type'] : '';
                    if($order_type != '')
                        {
                            update_post_meta($sort_view_ID, '_order_type', $order_type); 
                        }
                        
                    //check for archive
                    $view_selection =   isset($_GET['view_selection']) ? $_GET['view_selection'] : '';
                    if($view_selection != '')
                        {
                            //get a $term_id if empty and $taxonomny is set
                            if($taxonomy != '' && $term_id == '')
                                {
                                    $argv = array(
                                                    'hide_empty'    =>   0,
                                                    'fields'        =>  'ids'
                                                    );
                                    $terms = get_terms($taxonomy, $argv);
                                    if(count($terms) > 0)
                                        {
                                            reset ($terms);
                                            $term_id    =   current($terms);
                                        }
                                }
                            
                            //get archive view id 
                            $attr   =   array(
                                                '_view_selection'   =>  $view_selection
                                                );
                            
                            if($taxonomy != '' && $term_id != '')
                                {
                                    $attr['_taxonomy']  =   $taxonomy;
                                    $attr['_term_id']   =   $term_id;
                                }
                            
                            if($view_selection  ==  'archive')
                                $attr['_view_language']   =   $this->functions->get_blog_language();
                            
                            $view_ID   =   $this->functions->get_sort_view_id_by_attributes($sortID, $attr);
                            
                            //create view if not exists
                            if($view_ID == '')
                                {
                                    //create the sort view. 
                                    $sort_view_meta     =   array(
                                                                    '_order_type'               =>  'manual',
                                                                    '_view_selection'           =>  $view_selection
                                                                    );
                                    if($taxonomy != '' && $term_id != '')
                                        {
                                            $sort_view_meta['_taxonomy']  =   $taxonomy;
                                            $sort_view_meta['_term_id']   =   $term_id;
                                        }
                                        
                                    if($view_selection  ==  'archive')
                                        $attr['_view_language']   =   $this->functions->get_blog_language();
                                        
                                    $view_ID       =   $this->create_view($sortID, $sort_view_meta);   
                                    
                                }
                            
                            update_post_meta($sortID, '_last_sort_view_ID', $view_ID);
                                
                        }
                        
                    //check for taxonomy / term change
                    if($taxonomy != '' && $term_id != '')
                        {
                            $attr   =   array(
                                                '_view_selection'   =>  'taxonomy',
                                                '_taxonomy'         =>  $taxonomy,
                                                '_term_id'          =>  $term_id
                                                );
                            $view_ID   =   $this->functions->get_sort_view_id_by_attributes($sortID, $attr); 
                            if($view_ID == '')
                                {
                                    //create the sort view. 
                                    $sort_view_meta     =   array(
                                                                    '_order_type'               =>  'manual',
                                                                    '_view_selection'           =>  'taxonomy'
                                                                    );
                                    if($taxonomy != '' && $term_id != '')
                                        {
                                            $sort_view_meta['_taxonomy']  =   $taxonomy;
                                            $sort_view_meta['_term_id']   =   $term_id;
                                        }
                                    $view_ID       =   $this->create_view($sortID, $sort_view_meta);
                                    
                                    //$doRedirect = TRUE;
                                }
                                
                            update_post_meta($sortID, '_last_sort_view_ID', $view_ID);
                        }
                    
                    //check for order reset
                    if (isset($_POST['order_reset']) && $_POST['order_reset'] == 'true')
                        {
                            if(wp_verify_nonce($_POST['nonce'],  'reorder-interface-reset-' . get_current_user_id()))
                                { 
                                    $reset_sort_view_ID =   $_POST['sort_view_ID'];
                                    
                                     global $wpdb;
                                                        
                                    $query = "DELETE FROM `". $wpdb->prefix ."apto_sort_list`
                                                    WHERE `sort_view_id`    =   ". $reset_sort_view_ID;
                                    $results = $wpdb->get_results($query);
                                    
                                    //check if archive, then reset the posts table too
                                    $sort_view__view_selection  =   get_post_meta( $reset_sort_view_ID,  '_view_selection', TRUE);
                                    if($sort_view__view_selection   ==  'archive')
                                        {
                                            $sort_view_post =   get_post($reset_sort_view_ID);
                                            $sort_settings  =   $this->functions->get_sort_settings($sort_view_post->post_parent);
                                            
                                            if(isset($sort_settings['_rules']['post_type']) && count($sort_settings['_rules']['post_type']) == 1)
                                                {
                                                    $sort_post_type =   $sort_settings['_rules']['post_type'][0];
                                                    
                                                    //reset the menu_order
                                                    $query = "UPDATE `". $wpdb->posts ."`
                                                                    SET menu_order = 0
                                                                    WHERE `post_type`    =   '". $sort_post_type ."'";
                                                    $results = $wpdb->get_results($query);
                                                }
                                                
                                        }
                                    
                                    echo '<div id="message" class="updated"><p>' . __('Sort order reset successfully', 'apto') . '</p></div>';
                                }
                                else
                                {
                                    echo '<div id="message" class="updated"><p>' . __( 'Invalid Nonce', 'apto' )  . '</p></div>';
                                } 
                        } 
                    
                    if($doRedirect === TRUE)
                        {
                            $sort_view_data     =   get_post($view_ID);
                            if($sort_view_data->post_parent > 0)
                                $sortID             =   $sort_view_data->post_parent;
                                else
                                $sortID             =   $argv['sort_view_id'];
                            
                            //redirect to new sort view
                            $redirect_argv  =   array(
                                                'sort_id'    =>  $sortID
                                                );
                            wp_redirect($this->get_tab_link($redirect_argv));
                            die();
                        }
                    
                }
                           
            
            /**
            * Check for settings form update
            * 
            */
            function settings_update()
                {
                    if(!isset($_POST['apto_sort_settings_form_submit']))
                        return FALSE;
                        
                    $sort_id =  $_POST['sort_id'];
                    
                    //check for new sort tab
                    if($sort_id == '')
                        {
                            //create new sort
                            $post_data  =   array(
                                                    'post_type'     =>  'apto_sort',
                                                    'post_status'   =>  'publish'
                                                    );
                            $sort_id = wp_insert_post( $post_data );
                        }
                    
                    $current_language   =   $this->functions->get_blog_language();
                    
                    //process the query rules
                    $rules  =   array();
                    if(isset($_POST['rules']))
                        $rules = $this->query_rules_filter( $_POST['rules'] );
                    
                    //WPML
                    if(defined('ICL_LANGUAGE_CODE'))
                        {
                            $default_language   =   $this->functions->get_blog_default_language(); 

                            if($default_language    ==  $current_language)
                                update_post_meta($sort_id, '_rules', $rules);
                                
                            //if there is no sort for default create one
                            $sort_rules =   get_post_meta($sort_id, '_rules', TRUE);
                            if($sort_rules  ==  '')
                                update_post_meta($sort_id, '_rules', $rules);
                            
                            update_post_meta($sort_id, '_rules_' . $current_language, $rules);
                        }
                        else
                        update_post_meta($sort_id, '_rules', $rules);
                    
                    //mark the current language sort update
                    $sort_settings_update_languages =   get_post_meta($sort_id, '_settings_update_languages', TRUE);
                    $sort_settings_update_languages[$current_language]  =   true;
                    update_post_meta($sort_id, '_settings_update_languages', $sort_settings_update_languages);
                    
                                        
                    //process the conditionals
                    $conditionals = array();
                    if(isset($_POST['conditional_rules']))
                        $conditionals = $this->conditional_rules_filter( $_POST['conditional_rules'] );
                    update_post_meta($sort_id, '_conditionals', $conditionals);
                    
                    
                    //process the interface
                    $options    =   array(
                                            '_title',
                                            '_description',
                                            '_location',
                                            '_autosort',
                                            '_adminsort',
                                            '_new_items_to_bottom',
                                            '_show_thumbnails',
                                            '_capability'
                                            );
                    
                    $post_main_fields   =   array(
                                                    '_title'         =>  'post_title',
                                                    '_description'   =>  'post_content'
                                                    );                        
                    
                    foreach($options as $option)
                        {
                            $value = trim(stripslashes($_POST['interface'][$option]));
                            
                            //check for empty titles
                            if($option == '_title' && $value == '')
                                $value = 'Sort #' . $sort_id;
                            
                            
                            //add as meta value
                            update_post_meta($sort_id, $option, $value);
                            
                            //check if it's title or description to update the main apto_sort data
                            if(isset($post_main_fields[$option]))
                                {
                                    $post_data['ID']    =   $sort_id;
                                    $post_data[$post_main_fields[$option]]    =   $value;

                                    wp_update_post( $post_data );
                                }
                        }
                    
                    $redirect_argv  =   array(
                                                'sort_id'    =>  $sort_id
                                                );
                                                
                    $redirect_argv['page']      = 'apto_' . $this->get_menu_slug_from_menu_id($_POST['interface']['_location']);
                    $redirect_argv['base_url']  = $_POST['interface']['_location'];
                                        
                    //set the infromation regarding view type (if use single list or multiple;
                    $sort_view_type     =   $this->get_sort_view_type_by_settings($sort_id);
                    update_post_meta($sort_id, '_view_type', $sort_view_type); 
                    
                    if($sort_view_type == 'simple')
                        {
                            //check if default sort view already exists
                            $attr = array(
                                            '_view_selection'       =>  'simple',
                                            '_view_language'        =>  $this->functions->get_blog_language()
                                            );
                            $sort_view_id   =   $this->functions->get_sort_view_id_by_attributes($sort_id, $attr);
                            
                            if($sort_view_id == '')
                                {
                                    //create the default view for this sortID
                                    $sort_view_meta     =   array(
                                                                    '_view_selection'       =>  'simple',
                                                                    '_order_type'           =>  'manual',
                                                                    '_view_language'        =>  $this->functions->get_blog_language()
                                                                    );
                                    $sort_view_id       =   $this->create_view($sort_id, $sort_view_meta);
                                }
                                
                            //set this sort view as default for the main sort
                            update_post_meta($sort_id, '_last_sort_view_ID', $sort_view_id);
                        }
                    
                    else if($sort_view_type == 'multiple')
                        {
                            //check if default sort view already exists
                            $attr = array(
                                            '_view_selection'       =>  'archive',
                                            '_view_language'        =>  $this->functions->get_blog_language()
                                            );
                            
                            $sort_view_id   =   $this->functions->get_sort_view_id_by_attributes($sort_id, $attr);
                            
                            if($sort_view_id == '')
                                {
                                    //create the default view for this sortID
                                    $sort_view_meta     =   array(
                                                                    '_order_type'               =>  'manual',
                                                                    '_view_selection'           =>  'archive',
                                                                    '_view_language'            =>  $this->functions->get_blog_language()
                                                                    );
                                    $sort_view_id       =   $this->create_view($sort_id, $sort_view_meta);
                                    
                                    //set this sort view as default for the main sort
                                    update_post_meta($sort_id, '_last_sort_view_ID', $sort_view_id);
                                }
                        }

                    $redirect_argv['settings_saved']    =   'true';
                    
                    wp_redirect($this->get_tab_link($redirect_argv));
                    //echo 'REDIRECT TO '  . $this->get_tab_link($redirect_argv);
                    die();
                                        
                }
                
            
            /**
            * Check for sort list update (automatic order, manual is sent through ajax) 
            * 
            */
            function automatic_sort_order_update()
                {
                    if(!isset($_POST['apto_sort_form_order_update']))
                        return FALSE;        
                        
                    $sort_id        =   $_POST['sort_id'];
                    $sort_view_id   =   $_POST['sort_view_ID'];
                    
                    $order_by           =   array_values($_POST['auto_order_by']);
                    $custom_field_name  =   array_values($_POST['auto_custom_field_name']);
                    $custom_field_type  =   array_values($_POST['auto_custom_field_type']);
                    $order              =   array_values($_POST['auto_order']);
                    
                    update_post_meta($sort_view_id, '_auto_order_by', $order_by); 
                    update_post_meta($sort_view_id, '_auto_custom_field_name', $custom_field_name);
                    update_post_meta($sort_view_id, '_auto_custom_field_type', $custom_field_type); 
                    update_post_meta($sort_view_id, '_auto_order', $order);
                    
                    $is_batch_update    = FALSE;
                    if(isset($_POST['batch_order_update']) && $_POST['batch_order_update'] == 'yes')
                        $is_batch_update = TRUE;
                    
                    if($is_batch_update === TRUE)
                        {
                            $sort_view_settings =   $this->functions->get_sort_view_settings($sort_view_id);
                            
                            //get all terms of current taxonomy
                            $args = array(
                                            'hide_empty'    => false,
                                            'fields'        =>  'ids'
                                            );
                            $batch_work_terms = get_terms( $sort_view_settings['_taxonomy'], $args );
                            
                            //update the order type for all terms
                            foreach($batch_work_terms as $batch_term_id)   
                                {
                                    //check if the sort view already exists
                                    $attr = array(
                                                    '_view_selection'   =>  'taxonomy',
                                                    '_taxonomy'         =>  $sort_view_settings['_taxonomy'],
                                                    '_term_id'          =>  $batch_term_id
                                                    );
                                    $sort_view_id   =   $this->functions->get_sort_view_id_by_attributes($sort_id, $attr);
                                    
                                    if($sort_view_id > 0)
                                        continue;
                                        
                                    //the sort view does not exists, create that
                                    $sort_view_meta     =   array(
                                                                    '_order_type'       =>  'auto',
                                                                    '_view_selection'   =>  'taxonomy',
                                                                    '_taxonomy'         =>  $sort_view_settings['_taxonomy'],
                                                                    '_term_id'          =>  $batch_term_id,
                                                                    '_auto_order_by'    =>  $order_by,
                                                                    '_auto_custom_field_name'   =>  $custom_field_name,
                                                                    '_auto_custom_field_type'   =>  $custom_field_type,
                                                                    '_auto_order'       =>  $order
                                                                    );
                                    $batch_sort_view_id =   $this->create_view($sort_id, $sort_view_meta);                                    
                                    
                                }
                        } 

                }
            
            
            static function create_view($sortID, $sort_view_meta = array())
                {
                    
                    $post_data  =   array(
                                            'post_type'     =>  'apto_sort',
                                            'post_status'   =>  'publish',
                                            'post_parent'   =>  $sortID
                                            );
                    $sort_view_id = wp_insert_post( $post_data );
                          
                    //add the meta
                    foreach($sort_view_meta as $key =>  $value)
                        {
                            update_post_meta($sort_view_id, $key, $value); 
                        }
                        
                    return $sort_view_id;
                    
                }
                
            
            /**
            *   Filter the conditionals before save
            *   remove empty data
            * 
            *   @param array $conditionals
            */
            function conditional_rules_filter($conditionals)
                {
                    if(count($conditionals) > 0)
                        {
                            foreach($conditionals as $key   =>  $conditional_block)   
                                {
                                    $conditionals[$key]   =   array_filter($conditional_block, array($this, 'conditional_rules_empty_callback'));
                                }
                        }
                    
                    //filter again for empty blocks
                    $conditionals   =   array_filter($conditionals);
                                        
                    return $conditionals;   
                }
                
            function conditional_rules_empty_callback($element)
                {
                    return !empty($element['conditional_id']);   
                }
                
                
            /**
            *   Filter the query rules before save
            *   remove empty data
            * 
            *   @param array $conditionals
            */
            function query_rules_filter($rules)
                {
                    if(isset($rules['post_type']))
                        {
                            $rules['post_type'] = array_unique($rules['post_type']);
                        }
                        
                    if(isset($rules['author']))
                        {
                            $rules['author'] = array_unique($rules['author']);
                        }
                        
                    if(isset($rules['taxonomy']))
                        {
                            $rules['taxonomy']   =   array_filter($rules['taxonomy'], array($this, 'rules_taxonomy_filter_callback'));
                        }
                                        
                    return $rules;   
                }
                
            function rules_taxonomy_filter_callback($element)
                {
                    //check for terms, this need to include at least one element
                    if(!isset($element['terms']) || count($element['terms']) < 1)
                        return FALSE;
                    
                    return TRUE;    
                }
                
                
                
            function get_tab_link($attr)
                {
                    $defaults   = array (
                                            'page'      =>   $this->get_menu_slug_from_menu_id($this->get_current_menu_location()),
                                            'post'      =>   isset($_GET['post'])    ?   $_GET['post']   :   '',
                                            'base_url'  =>   $this->get_current_menu_location()
                                        );
                                        
                    // Parse incoming $args into an array and merge it with $defaults
                    $attr   =   wp_parse_args( $attr, $defaults );
                    $attr   =   array_filter($attr);
                    
                    $base_url =     'edit.php';
                    if(isset($attr['base_url']))
                        {
                            $base_url   =   $attr['base_url'];
                            unset($attr['base_url']);
                        }
                    
                    $link = admin_url($base_url);
                    if(strpos($base_url, "?") === FALSE)
                        $link .= '?';
                    
                    $link .=    '&' . http_build_query($attr);
                    
                    return $link;                        
                }
             
            /**
            * Return link for items within front side
            *     
            * @param array $attr
            */
            function get_item_link($attr)
                {
                    $defaults   = array (

                                        );
                                        
                    // Parse incoming $args into an array and merge it with $defaults
                    $attr   =   wp_parse_args( $attr, $defaults );
                    $attr   =   array_filter($attr);
                    
                    global $wp_rewrite;
                    
                    $link   =   $attr['base_url'];
                    unset($attr['base_url']);

                    if(strpos($link, "?") === FALSE)
                        $link .= '?';
                    
                    $link .=    '&' . http_build_query($attr);
                    
                    return $link;                        
                }
               
            
            function get_rule_box()
                {
                    $rule_type   =   $_POST['type'];
                    
                    switch($rule_type)
                        {
                            case 'post_type'    :
                                                    $html_data  =   $this->get_rule_post_type_html_box();
                                                    
                                                    $this->response['html']             =   $html_data;
                                                    $this->response['message']          =   '';
                                                    $this->response['response_code']    =   '0';
                                                    
                                                    break;   
                            
                            case 'taxonomy'     :
                                                    $options    = array(
                                                                            'group_id'  =>  $_POST['group_id']
                                                                        );
                                                    $html_data  =   $this->get_rule_taxonomy_html_box($options);
                                                    
                                                    $this->response['html']             =   $html_data;
                                                    $this->response['message']          =   '';
                                                    $this->response['response_code']    =   '0';
                                                    
                                                    break;
                                                    
                            case 'author'     :
                                                    $html_data  =   $this->get_rule_author_html_box();
                                                    
                                                    $this->response['html']             =   $html_data;
                                                    $this->response['message']          =   '';
                                                    $this->response['response_code']    =   '0';
                                                    
                                                    break;  
                        }
                    
                                        
                    $this->output_response();
                    die();                    
                }
                
            
            function get_rule_post_type_html_box($options = array())
                {
                    $defaults = array (
                                             'default'          =>  FALSE,
                                             'selected_value'   =>  ''
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $options = wp_parse_args( $options, $defaults );
                    
                    
                    ob_start();
                    
                    ?>
                        <tr>
                                                                                                      
                            <td class="param">
                                <select class="select" name="rules[post_type][]">
                                    <?php
                                    
                                        $post_types =   $this->functions->get_post_types();
                                        foreach($post_types as $post_type)
                                            {
                                                $post_type_data = get_post_type_object ( $post_type );
                                                
                                                ?><option <?php
                                                
                                                    if($options['selected_value'] == $post_type)
                                                        echo 'selected="selected" ';
                                                
                                                ?>value="<?php echo $post_type ?>"><?php echo $post_type_data->label ?> <small>(<?php echo $post_type; ?>)</small></option><?php            
                                            }
                                    
                                    ?>
                                </select>
                            </td>
                            <td class="buttons">
                                <?php if($options['default'] !== TRUE) { ?><a href="javascript: void(0);" onClick="APTO.remove_rule_item(this, 'tr')" class="remove item"></a><?php } ?>
                            </td>
                        </tr>
                    <?php
                    
                    $html_data  =   ob_get_contents();
                    ob_end_clean();
                    
                    return $html_data;   
                }
                
                
            function get_rule_taxonomy_html_box($options = array())
                {
                    $defaults   = array (
                                            'group_id'      =>  1,
                                            'taxonomy'      =>  '',
                                            'operator'      =>  'IN',
                                            'selected'      =>  array(),
                                            'html_alternate'   =>  FALSE
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $options = wp_parse_args( $options, $defaults );
                    
                    
                    ob_start();
                    
                    ?>
                        <table class="apto_input widefat apto_rules apto_table" data-id="<?php echo $options['group_id'] ?>">
                            <tbody>
                                <tr<?php
                        
                                    if($options['html_alternate'] === TRUE)
                                        echo ' class="alternate"';
                                    
                                ?>>
                                    <td class="param">
                                        <select onChange="APTO.change_taxonomy_item(<?php echo $options['group_id'] ?>)" name="rules[taxonomy][<?php echo $options['group_id'] ?>][taxonomy]" class="select taxonomy_item">
                                            <?php
                                            
                                                $taxonomies =   get_taxonomies(array(), 'objects'); 
                                                foreach ($taxonomies as $taxonomy ) 
                                                    {
                                                        ?><option <?php if($taxonomy->name == $options['taxonomy']) { echo 'selected="selected"'; }?> value="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?> <small>(<?php echo $taxonomy->name; ?>)</small></option><?php
                                                    }
                                            
                                            ?>
                                        </select>
                                        <h5>Taxonomy Operator</h5>
                                        <select name="rules[taxonomy][<?php echo $options['group_id'] ?>][operator]" class="select">
                                            <?php
                                            
                                                $operator_values = array(
                                                                            'IN',
                                                                            'NOT IN',
                                                                            'AND'
                                                                            );
                                                foreach($operator_values as $operator_value)
                                                    {
                                                        ?><option <?php if($operator_value == $options['operator']) { echo 'selected="selected"'; }?>    value="<?php echo $operator_value ?>"><?php echo $operator_value ?></option><?php
                                                    }
                                            ?>
                                        </select>
                                    </td>
                                                            
                                    <td class="value">
                                        <?php
                                        
                                            $taxonomy_name  =   '';
                                            if($options['taxonomy'] == '')
                                                {
                                                    //get first taxonomy
                                                    reset($taxonomies);
                                                    $first_taxonomy = current($taxonomies);
                                                    
                                                    $taxonomy_name  =   $first_taxonomy->name;
                                                }
                                                else
                                                {
                                                    $taxonomy_name  =   $options['taxonomy'];  
                                                }
                                        
                                            $ti_options = array(
                                                                    'group_id'      =>  $options['group_id'],
                                                                    'taxonomy'      =>  $taxonomy_name,
                                                                    'selected'      =>  $options['selected']
                                                                    );
                                            echo $this->change_taxonomy_item_html($ti_options);                                            
                                        
                                        ?>
                                        
                                    </td>
                                    <td class="buttons">
                                        <a href="javascript: void(0);" onClick="APTO.remove_taxonomy_item(this, 'table')" class="remove item"></a>                                                                 
                                    </td>
                                </tr>
                                </tbody>
                         </table>
                    <?php
                    
                    $html_data  =   ob_get_contents();
                    ob_end_clean();
                    
                    return $html_data;   
                }
            
                
            function change_taxonomy_item()
                {
                    $options    = array(
                                            'group_id'  =>  $_POST['group_id'],
                                            'taxonomy'  =>  $_POST['taxonomy']
                                        );
                       
                    $html_data  =   $this->change_taxonomy_item_html($options);
                                                    
                    $this->response['html']             =   $html_data;
                    $this->response['message']          =   '';
                    $this->response['group_id']         =   $options['group_id'];
                    $this->response['response_code']    =   '0';
  
                    $this->output_response();
                    die(); 
                } 
                
            function change_taxonomy_item_html($options)
                {
                    $defaults = array (
                                            'group_id'      =>  1,
                                            'taxonomy'      =>  '',
                                            'selected'      =>  array()
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $options = wp_parse_args( $options, $defaults );
                    
                    ob_start();
                    
                    $args   =   array(
                                        'orderby'               =>  'name', 
                                        'hide_empty'            =>  FALSE,
                                        'hierarchical'          =>  TRUE,
                                        'walker'                =>  new APTO_Walker_TermsDropdownCategories(),
                                        'taxonomy'              =>  $options['taxonomy'],
                                        'echo'                  =>  FALSE,
                                        'name'                  =>  'rules[taxonomy]['. $options['group_id'] .'][terms][]',
                                        'class'                 =>  'select multiple',
                                        'selected'              =>  $options['selected']
                                        );
                    $select_html        =   wp_dropdown_categories($args);
                    $select_html        = str_replace("<select ", '<select multiple="multiple"', $select_html);
                    echo ($select_html);
                      
                    $html_data  =   ob_get_contents();
                    ob_end_clean();
                    
                    return $html_data; 
                }
            
            
            function get_rule_author_html_box($options = array())
                {
                    $defaults = array (
                                            'selected'    =>  ''
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $options = wp_parse_args( $options, $defaults );
                    
                    
                    ob_start();
                    
                    ?>
                        <tr>
                            <td class="param">
                                <select class="select" name="rules[author][]">
                                    <?php
                                        
                                        $blogusers = get_users();
                                        foreach ($blogusers as $user) 
                                            {
                                                ?><option <?php if($options['selected'] == $user->ID) { echo 'selected="selected"'; }?> value="<?php echo $user->ID ?>"><?php echo $user->data->display_name ?></option><?php
                                            }
                                    ?>
                                </select>
                            </td>
                            <td class="buttons">
                                <a href="javascript: void(0);" onClick="APTO.remove_rule_item(this, 'tr')" class="remove item"></a>
                            </td>
                        </tr>
                    <?php
                    
                    $html_data  =   ob_get_contents();
                    ob_end_clean();
                    
                    return $html_data;   
                }
            
            
            function get_conditional_group()
                {
                    $options    = array(
                                            'group_id'  =>  $_POST['group_id']
                                        );
                       
                    $html_data  =   $this->get_html_conditional_group($options);
                                                    
                    $this->response['html']             =   $html_data;
                    $this->response['message']          =   '';
                    $this->response['response_code']    =   '0';
  
                    $this->output_response();
                    die(); 
                    
                }
            
            function get_html_conditional_group($options = array())
                {
                    $defaults   = array (
                                            'group_id'  =>  1,
                                            'data'      =>  array()
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $options = wp_parse_args( $options, $defaults );
                    
                    
                    ob_start();
                    
                    ?>
                        <div data-id="<?php echo $options['group_id'] ?>" class="conditional_rules" id="conditional_rules_<?php echo $options['group_id'] ?>">
                            <h4>Or</h4>
                            <table class="apto_input widefat apto_rules apto_table">
                                <tbody>
                                    
                                    <?php 
                                    
                                        if(count($options['data']) > 0)
                                            {
                                                $row_id = 1;
                                                foreach($options['data'] as $key => $data)
                                                    {
                                                        $rule_options    = array(
                                                                                'group_id'          =>  $options['group_id'],
                                                                                'row_id'            =>  $row_id,
                                                                                'selected'          =>  $data['conditional_id'],
                                                                                'comparison'        =>  $this->conditional_rules->get_rule_comparison($data['conditional_id']),
                                                                                'comparison_value'  =>  $data['conditional_comparison'],
                                                                                'selected_value'    =>  isset($data['conditional_value']) ? $data['conditional_value'] : ''
                                                                            );
                                                        echo $this->get_html_conditional_rule($rule_options);   
                                                        
                                                        $row_id++;
                                                    }
                                            }
                                            else
                                            echo $this->get_html_conditional_rule($options);
                                    
                                    ?>
                                                                      
                                </tbody>
                             </table>
                             
                             
                             <table class="apto_input widefat apto_more">
                                <tbody>
                                    <tr><td><a class="button-secondary" href="javascript: void(0)" onClick="APTO.add_conditional_rule(this)">Add </a> &nbsp;&nbsp;<img class="ajax_loading" src="<?php echo CPTURL ?>/images/ajax-loader.gif" alt="Loading" /></td></tr>
                                </tbody>
                            </table>
                        </div>
                    <?php
                    
                    $html_data  =   ob_get_contents();
                    ob_end_clean();
                    
                    return $html_data;   
                }
                
                
            function get_conditional_rule()
                {
                    $options    = array(
                                            'group_id'  =>  $_POST['group_id'],
                                            'selected'  =>  isset($_POST['selected']) ? $_POST['selected'] : '',
                                            'row_id'    =>  isset($_POST['row_id']) ? $_POST['row_id'] : 1
                                        );
                                        
                    if($options['selected'] !=  '')
                        {
                            $options['comparison']  =   $this->conditional_rules->get_rule_comparison($options['selected']);
                        }
                       
                    $html_data  =   $this->get_html_conditional_rule($options);
                                                    
                    $this->response['html']             =   $html_data;
                    $this->response['group_id']         =   $options['group_id'];
                    $this->response['row_id']           =   $options['row_id'];
                    
                    $this->response['message']          =   '';
                    $this->response['response_code']    =   '0';
  
                    $this->output_response();
                    die(); 
                    
                }
            
            function get_html_conditional_rule($options = array())
                {
                    $defaults   = array (
                                            'selected'          =>  '',
                                            'comparison'        =>  array(),
                                            'comparison_value'  =>  '', 
                                            'selected_value'    =>  '',
                                            'group_id'          =>  1,
                                            'row_id'            =>  1 
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $options = wp_parse_args( $options, $defaults );
                    
                    
                    ob_start();
                    
                    ?>
                        <tr data-id="<?php echo $options['row_id'] ?>">
                            <td class="param">
                                <select name="conditional_rules[<?php echo $options['group_id'] ?>][<?php echo $options['row_id'] ?>][conditional_id]" class="select" onchange="APTO.conditional_item_change(this)">
                                    <option <?php if($options['selected'] == '') { echo 'selected="selected"'; }?> value="">&nbsp;</option>
                                    <?php
                                    
                                        foreach($this->conditional_rules->rules as $rule_id => $rule_data)
                                            {
                                                ?><option <?php if($options['selected'] == $rule_id) { echo 'selected="selected"'; }?> value="<?php echo $rule_id ?>"><?php echo $rule_data['title'] ?></option><?php                  
                                            }
                                    
                                    ?>

                                </select>
                            </td>
                            
                            <?php if(count($options['comparison']) > 0) { ?>
                            <td class="comparison">
                                <select name="conditional_rules[<?php echo $options['group_id'] ?>][<?php echo $options['row_id'] ?>][conditional_comparison]" class="select">
                                    <?php
                                    
                                        foreach($options['comparison'] as $key  =>  $comparison)
                                            {
                                                ?>
                                                    <option <?php if($options['comparison_value'] == $comparison || ($options['comparison_value'] == '' && $key == 0)) { echo 'selected="selected"'; }?> value="<?php echo $comparison ?>"><?php echo $comparison ?></option>            
                                                <?php   
                                            }
                                        
                                    
                                    ?>
                                </select>
                            </td>
                            <?php } ?>
                            
                            <?php
                            
                                if ($options['selected'] != '')
                                    {
                                        $rule_html_output = call_user_func_array($this->conditional_rules->rules[$options['selected']]['admin_html'], array($options)) ;
                                    }
                                    
                                if($options['selected'] == '' || $rule_html_output == '') 
                                    {
                                        ?>
                                            <td colspan="2" class="buttons">
                                                <a href="javascript: void(0);" onClick="APTO.remove_conditional_item(this, 'tr')" class="remove item"></a>
                                            </td>
                                        <?php   
                                    }
                                    else
                                    {
                                        ?>
                                            <td class="value">
                                                <?php echo $rule_html_output ?>
                                            </td>
                                            <td class="buttons">
                                                <a href="javascript: void(0);" onClick="APTO.remove_conditional_item(this, 'tr')" class="remove item"></a>
                                            </td>
                                        <?php
                                    }
                            
                            ?>
                        </tr>
                    <?php
                    
                    $html_data  =   ob_get_contents();
                    ob_end_clean();
                    
                    return $html_data;   
                }

                
            function output_response()
                {
                    echo json_encode($this->response);
                }
                
                
            function automatic_add_falback_order($options = array())
                {
                    $defaults = array (
                                             'default'              =>  FALSE,
                                             'group_id'             =>  isset($_POST['group_id']) ? $_POST['group_id'] : 1,
                                             'data_set'             =>  array()
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $options = wp_parse_args( $options, $defaults );   
                    
                    $html_data  =    $this->html_automatic_add_falback_order($options);
                    
                    $this->response['html']             =   $html_data;
                    $this->response['group_id']         =   $options['group_id'];
     
                    $this->output_response();
                    die();   
                }
                
                
            function html_automatic_add_falback_order($options = array())
                {
                    $defaults = array (
                                             'default'              =>  FALSE,
                                             'group_id'             =>  1,
                                             'data_set'             =>  array()
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $options = wp_parse_args( $options, $defaults );     
                    
                    if(count($options['data_set']) < 1)
                        {
                            $options['data_set']    =   array(
                                                                    'order_by'          =>  '_default_',
                                                                    'custom_field_name' =>  '',
                                                                    'custom_field_type' =>  '',
                                                                    'order'             =>  'DESC'
                                                                );
                        }
                    
                    ob_start();  
                    
                    ?>
                        <tr class="automatic_order_by" data-id="<?php echo $options['group_id'] ?>">
                            <td class="label">
                                <label for=""><?php _e( "Order By", 'apto' ) ?></label>
                                <p class="description">More details about Order By paramether can be found at <a target="_blank" href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">Order & Orderby Parameters</a></p>
                            </td>
                            <td>
                                <input type="radio" id="auto_order_by_default_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == '_default_') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="_default_" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_default_<?php echo $options['group_id'] ?>">Default / None</label><br>
                                
                                <input type="radio" id="auto_order_by_id_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == 'ID') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="ID" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_id_<?php echo $options['group_id'] ?>">Post ID</label><br>
                                
                                <input type="radio" id="auto_order_by_post_author_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == 'post_author') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="post_author" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_post_author_<?php echo $options['group_id'] ?>">Author</label><br>
                                
                                <input type="radio" id="auto_order_by_post_title_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == 'post_title') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="post_title" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_post_title_<?php echo $options['group_id'] ?>">Title</label><br>
                                  
                                <input type="radio" id="auto_order_by_post_name_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == 'post_name') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="post_name" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_post_name_<?php echo $options['group_id'] ?>">Slug</label><br>
                                
                                <input type="radio" id="auto_order_by_post_date_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == 'post_date') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="post_date" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_post_date_<?php echo $options['group_id'] ?>">Date</label><br>
                                
                                <input type="radio" id="auto_order_by_post_modified_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == 'post_modified') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="post_modified" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_post_modified_<?php echo $options['group_id'] ?>">Modified</label><br>
                                
                                <input type="radio" id="auto_order_by_comment_count_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == 'comment_count') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="comment_count" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_comment_count_<?php echo $options['group_id'] ?>">Comments Count</label><br>
                                
                                <input type="radio" id="auto_order_by_random_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == '_random_') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="_random_" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_random_<?php echo $options['group_id'] ?>">Random</label><br><br>
                                
                                <input type="radio" id="auto_order_by_custom_field_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order_by'] == '_custom_field_') {echo 'checked="checked"'; } ?> onchange="APTO.apto_autosort_orderby_field_change(this)" value="_custom_field_" name="auto_order_by[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_by_custom_field_<?php echo $options['group_id'] ?>">Custom Field</label><br>
                                <div id="apto_custom_field_area_<?php echo $options['group_id'] ?>" <?php
                                    if ($options['data_set']['order_by'] != '_custom_field_')
                                        echo 'style="display: none"';
                                ?>>
                                    <table class="apto_input inner_table widefat">
                                        <tbody>
                                            <tr class="alt"><td>
                                                <h4>Field Name</h4>
                                                <p class="description">The name of custom field</p>
                                                <input id="auto_custom_field_name_<?php echo $options['group_id'] ?>" type="text" class="regular-text custom-field-text" value="<?php echo $options['data_set']['custom_field_name'] ?>" name="auto_custom_field_name[<?php echo $options['group_id'] ?>]">
                                            </td></tr>
                                            
                                            <tr class="alt"><td>
                                                <h4>Field Type</h4>
                                                <p class="description">MySql Type of field, more details at <a href="http://dev.mysql.com/doc/refman/5.0/en/cast-functions.html" target="_blank">Cast Functions and Operators</a></p>
                                                
                                                <input type="radio" id="custom_field_type_none_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['custom_field_type'] == 'none' || $options['data_set']['custom_field_type'] == '') {echo 'checked="checked"'; } ?> value="none" name="auto_custom_field_type[<?php echo $options['group_id'] ?>]" />
                                                <label for="custom_field_type_none_<?php echo $options['group_id'] ?>">None / Default</label><br>
                                                
                                                <input type="radio" id="custom_field_type_signed_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['custom_field_type'] == 'SIGNED') {echo 'checked="checked"'; } ?> value="SIGNED" name="auto_custom_field_type[<?php echo $options['group_id'] ?>]" />
                                                <label for="custom_field_type_signed_<?php echo $options['group_id'] ?>">Signed (Integer)</label><br>
                                                
                                                <input type="radio" id="custom_field_type_signed_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['custom_field_type'] == 'UNSIGNED') {echo 'checked="checked"'; } ?> value="UNSIGNED" name="auto_custom_field_type[<?php echo $options['group_id'] ?>]" />
                                                <label for="custom_field_type_signed_<?php echo $options['group_id'] ?>">Unsigned (Integer)</label><br>
                                                
                                                <input type="radio" id="custom_field_type_date_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['custom_field_type'] == 'DATE') {echo 'checked="checked"'; } ?> value="DATE" name="auto_custom_field_type[<?php echo $options['group_id'] ?>]" />
                                                <label for="custom_field_type_date_<?php echo $options['group_id'] ?>">Date</label><br>
                                                
                                                <input type="radio" id="custom_field_type_datetime_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['custom_field_type'] == 'DATETIME') {echo 'checked="checked"'; } ?> value="DATETIME" name="auto_custom_field_type[<?php echo $options['group_id'] ?>]" />
                                                <label for="custom_field_type_datetime_<?php echo $options['group_id'] ?>">Datetime</label><br>
                                                
                                                <input type="radio" id="custom_field_type_time_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['custom_field_type'] == 'TIME') {echo 'checked="checked"'; } ?> value="TIME" name="auto_custom_field_type[<?php echo $options['group_id'] ?>]" />
                                                <label for="custom_field_type_time_<?php echo $options['group_id'] ?>">Time</label><br>
                                                
                                            </td></tr>
                                    
                                        </tbody>
                                    </table>
                                </div>    

                            </td>
                            
                            <td class="buttons">
                                <?php if($options['default'] !== TRUE) { ?><a href="javascript: void(0);" onClick="APTO.RemoveAutomaticOrderFallback(this)" class="remove item"></a><?php } ?>
                            </td>
                        </tr>
                    
                        <tr class="automatic_order" data-id="<?php echo $options['group_id'] ?>">
                            <td class="label">
                                <label for=""><?php _e( "Order", 'apto' ) ?></label>
                                <p class="description">More details about Order paramether can be found at <a target="_blank" href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">Order & Orderby Parameters</a></p>
                            </td>
                            <td>
                                <input type="radio" id="auto_order_desc_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order'] == 'DESC') {echo 'checked="checked"'; } ?> value="DESC" name="auto_order[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_desc_<?php echo $options['group_id'] ?>">Descending</label><br>

                                <input type="radio" id="auto_order_asc_<?php echo $options['group_id'] ?>" <?php if ($options['data_set']['order'] == 'ASC') {echo 'checked="checked"'; } ?> value="ASC" name="auto_order[<?php echo $options['group_id'] ?>]" />
                                <label for="auto_order_asc_<?php echo $options['group_id'] ?>">Ascending</label><br>  

                            </td>
                            
                            <td class="buttons">
                                <?php if($options['default'] !== TRUE) { ?><a href="javascript: void(0);" onClick="APTO.RemoveAutomaticOrderFallback(this)" class="remove item"></a><?php } ?>
                            </td>
                        </tr>

                    <?php
                    
                    $html_data  =   ob_get_contents();
                    ob_end_clean();
                    
                    return $html_data; 
                }    
                
                
            function metabox_toggle()
                {
                    $sort_id    =   $_POST['sort_id'];
                    $status     =   $_POST['status'];
                    $type       =   $_POST['type'];
                       
                    $metabox_toggle = get_post_meta($sort_id, '_metabox_toggle', TRUE);
                    if(!is_array($metabox_toggle))
                        $metabox_toggle = array();
                        
                    $metabox_toggle[$type]  =  $status;
                    update_post_meta($sort_id, '_metabox_toggle', $metabox_toggle);  
                                                                        
                    $this->response['html']             =   '';
                    $this->response['message']          =   '';
                    $this->response['response_code']    =   '0';
  
                    $this->output_response();
                    die();    
                    
                }
                
            function get_sort_view_type_by_settings($sortID)
                {
                    $view_type = 'simple';
                    
                    $sort_rules     =   get_post_meta($sortID, '_rules', TRUE);
                    
                    if(!isset($sort_rules['taxonomy']) || (is_array($sort_rules['taxonomy']) && count($sort_rules['taxonomy']) < 1))
                        $view_type = 'multiple';
                        
                    return $view_type;    
                }
            
            function get_sort_view_type($sortID)
                {
                    if($sortID == '' )
                        return '';
                    
                    $view_type = get_post_meta($sortID, '_view_type', TRUE);
                           
                    return $view_type;
                }
                
            function get_last_sort_view_ID($sortID)
                {
                    if($sortID == '' )
                        return '';
                    
                    $current_sort_view_ID = '';
                    
                    if(absint($sortID) < 1)
                        return $current_sort_view_ID;
                        
                    $current_sort_view_ID = get_post_meta($sortID, '_last_sort_view_ID', TRUE);
                    if($this->get_sort_view_type($sortID)   ==  "simple")
                        $current_sort_view__view_selection  =   'simple';
                        else
                        $current_sort_view__view_selection  =   'archive';
                    $current_sort_view__view_language   =   $this->functions->get_blog_language();
                    
                    //check if for another language WPML
                    if($current_sort_view_ID != '' && $this->functions->get_sort_view_language($current_sort_view_ID) != $this->functions->get_blog_language())
                        {
                            $current_sort_view__view_selection  =   get_post_meta($current_sort_view_ID, '_view_selection', TRUE);      
                            $current_sort_view_ID = '';
                        }
                    
                    if(absint($current_sort_view_ID) > 0)
                        return $current_sort_view_ID;
                    
                    global $post;
                    $_wp_query_post =   $post;
                    
                    //fetch the archive one in case there is no current view
                    $args = array(
                                    'posts_per_page'    => 1,
                                    'post_type'         =>  'apto_sort',
                                    'orderby'           =>  'ID',
                                    'order'             =>  'ASC',
                                    'post_parent'       =>  $sortID,
                                    'ignore_supress_filters'    =>  TRUE,
                                    'meta_query'        => array(
                                                                    'relation' => 'AND',
                                                                    array(
                                                                            'key'       => '_view_selection',
                                                                            'value'     => 'archive',
                                                                            'compare'   => '='
                                                                        ),
                                                                    array(
                                                                            'key'       => '_view_language',
                                                                            'value'     => $current_sort_view__view_language,
                                                                            'compare'   => '='
                                                                        )
                                                                )
                                );
                    $list = new WP_Query( $args );
                    if($list->have_posts())
                        {
                            $list->the_post();
                               
                            $current_sort_view_ID = $post->ID;
                        }
                    
                    //wp_reset_postdata();
                    //use this instead as using a setup_postdata() without any query will reset to nothing
                    $post   =   $_wp_query_post;
                    
                    return $current_sort_view_ID;
                }
             
            
                
            function get_sort_taxonomies_by_objects($sortID)
                {

                    $sort_rules     =   get_post_meta($sortID, '_rules', TRUE);
                    
                    $post_types =   $sort_rules['post_type'];
                    $taxonomies = array();
                    foreach($post_types as $post_type)  
                        {
                            $post_types_taxonomies  =   get_object_taxonomies( $post_type );
                            if(count($taxonomies)   < 1)
                                $taxonomies = $post_types_taxonomies;
                            $taxonomies =    array_intersect($taxonomies, $post_types_taxonomies);
                            
                            if(count($taxonomies) < 1 )
                                break;
                        }
                    
                    //filter the taxonomies and remove the ones whithout any term
                    foreach ($taxonomies as $key    =>  $taxonomy)
                        {
                            $count  =   wp_count_terms( $taxonomy, array('hide_empty' => FALSE) );
                            if($count   <   1)
                                unset($taxonomies[$key]);
                        }
                    
                    //re-index the array    
                    $taxonomies =   array_values($taxonomies);    
                                        
                    return $taxonomies;
                    
                }
                
            function get_is_hierarhical_by_settings($sortID)
                {
                    $is_hierarhical     = FALSE;
                    $sort_rules         = get_post_meta($sortID, '_rules', TRUE);
                       
                    if(count($sort_rules['post_type']) > 1)
                        return FALSE;
                        
                    if(isset($sort_rules['taxonomy']) && is_array($sort_rules['taxonomy']) && count($sort_rules['taxonomy']) > 0)
                        return FALSE;
                    
                    reset($sort_rules['post_type']);
                    $post_type          =   current($sort_rules['post_type']);
                    
                    if(!post_type_exists($post_type))
                        return 'INVALID POST TYPE';
                    
                    $post_type_data     =   get_post_type_object($post_type);
                        
                    return $post_type_data->hierarchical;    
                }
            
            /**
            * 
            * Show the sticky info when in re-order interface
            * 
            */
            function apto_showsticky_info($additiona_details, $post_data)
                {
                    $sticky_list = get_option('sticky_posts');
                    
                    if(!is_array($sticky_list) || count($sticky_list) < 0)
                        return $additiona_details;
                        
                    if(in_array($post_data->ID, $sticky_list))
                        $additiona_details .= ' <span class="item-status">Sticky</span>';
                    
                    return $additiona_details;   
                }
            
            function saveAjaxOrder() 
                {
                    global $wpdb, $blog_id;
                    
                    set_time_limit(600);
                    
                    //check for nonce
                    if(! wp_verify_nonce($_POST['nonce'],  'reorder-interface-' . get_current_user_id()))
                        {
                            _e( 'Invalid Nonce', 'apto' );
                            die();   
                        }
                    
                    //avoid using parse_Str due to the max_input_vars for large amount of data
                    $_data = explode("&", $_POST['order']);
                    $_data_parsed           = array();
                    $_data_sticky_parsed    = array();
                    
                    foreach ($_data as $_data_item)
                        {
                            list($key, $value) = explode("=", $_data_item);
                            
                            if(strpos($key, 'item[') === 0)
                                {
                                    $key = str_replace("item[", "", $key);
                                    $key = str_replace("]", "", $key);
                                    $_data_parsed[$key] = trim($value);
                                }
                                
                            if(strpos($key, 'sticky_item[') === 0)
                                {
                                    $key = str_replace("sticky_item[", "", $key);
                                    $key = str_replace("]", "", $key);
                                    $_data_sticky_parsed[$key] = trim($value);
                                }
                        }

                    $_data_sticky_parsed    =   array_flip($_data_sticky_parsed);
                        
                    $data = '';
                    if(count($_data_parsed) > 0)
                        $data['item'] = $_data_parsed;
                    
                    $sort_view_id       =   $_POST['sort_view_id'];
                    $sort_view_settings =   $this->functions->get_sort_view_settings($sort_view_id);
                    
                    $sort_view_data     =   get_post($sort_view_id);
                    if($sort_view_data->post_parent > 0)
                        $sortID             =   $sort_view_data->post_parent;
                        else
                        $sortID             =   $sort_view_id;
                        
                    $sort_settings      =   $this->functions->get_sort_settings($sortID);
                    
                    $is_hierarhical     = $this->get_is_hierarhical_by_settings($sortID);
  
                    if (is_array($data))
                        {
                            //remove the old order
                            $query = "DELETE FROM `". $wpdb->prefix ."apto_sort_list`
                                        WHERE `sort_view_id` = ". $sort_view_id;
                            $results = $wpdb->get_results($query);
                                
                            //prepare the var which will hold the item childs current order
                            $childs_current_order = array();
                            
                            $current_item_menu_order = 0;
                            
                            foreach($data['item'] as $post_id => $parent_id ) 
                                {
                                    if($is_hierarhical === TRUE || ($this->functions->is_woocommerce($sortID) === TRUE && $sort_view_settings['_view_selection'] == 'archive'))
                                        {
                                            $current_item_menu_order = '';
                                            if($parent_id != 'null')
                                                {
                                                    if(!isset($childs_current_order[$parent_id]))
                                                        $childs_current_order[$parent_id] = 1;
                                                        else
                                                        $childs_current_order[$parent_id] = $childs_current_order[$parent_id] + 1;
                                                        
                                                    $current_item_menu_order    = $childs_current_order[$parent_id];
                                                    $post_parent                = $parent_id;
                                                }
                                                else
                                                    {
                                                        if(!isset($childs_current_order['root']))
                                                            $childs_current_order['root'] = 1;
                                                            else
                                                            $childs_current_order['root'] = $childs_current_order['root'] + 1;
                                                            
                                                        $current_item_menu_order    = $childs_current_order['root'];
                                                        $post_parent                = 0;
                                                    }
                                                
                                            //update the menu_order and parent
                                            $wpdb->update( $wpdb->posts, array('menu_order' => $current_item_menu_order, 'post_parent' => $post_parent), array('ID' => $post_id) );
                                            
                                            $query = "INSERT INTO `". $wpdb->prefix ."apto_sort_list` 
                                                        (`sort_view_id`, `object_id`) 
                                                        VALUES ('".$sort_view_id."', '".$post_id."');";
                                            $results = $wpdb->get_results($query);
                                            
                                            //deprecated since 2.6  Do not rely on this anymore
                                            do_action('apto_order_update_hierarchical', array('post_id' =>  $post_id, 'position' =>  $current_item_menu_order, 'page_parent'    =>  $post_parent));
                                            
                                            do_action('apto_object_order_update', array('post_id' =>  $post_id, 'position' =>  $current_item_menu_order, 'page_parent'    =>  $post_parent, 'sort_view_id'  =>  $sort_view_id));

                                            continue;
                                        }
                                        
                                                                        
                                    //maintain the simple order if is archive
                                    if($sort_settings['_view_type']    ==  'multiple' && $sort_view_settings['_view_selection'] == 'archive')
                                        $wpdb->update( $wpdb->posts, array('menu_order' => $current_item_menu_order), array('ID' => $post_id) ); 
                                         
                                    $query = "INSERT INTO `". $wpdb->prefix ."apto_sort_list` 
                                                (`sort_view_id`, `object_id`) 
                                                VALUES ('".$sort_view_id."', '".$post_id."');";
                                    $results = $wpdb->get_results($query);
                                    
                                    //deprecated since 2.6  Do not rely on this anymore
                                    do_action('apto_order_update', array('post_id' => $post_id, 'position' => $current_item_menu_order));
                                    
                                    do_action('apto_object_order_update', array('post_id' =>  $post_id, 'position' =>  $current_item_menu_order, 'sort_view_id'  =>  $sort_view_id));
                                    
                                    $current_item_menu_order++;
                
                                }
                                
                                
                            //save the sticky data if any
                            update_post_meta($sort_view_id, '_sticky_data', $_data_sticky_parsed);
                        }
                        
                        
                    do_action('apto_order_update_complete', $sort_view_id); 
                    
                    _e( "Items Order Updated", 'apto' );
                    die();                    
                }
             
        }

?>