<?php

    class APTO_functions
        {
            var $conditional_rules  = '';
               
            function __construct()
                {
                    $this->conditional_rules    = new APTO_conditionals(); 
                }
                
            function __destruct()
                {
                
                }
            
            /**
            * put your comment there...
            * 
            */
            static function  get_post_types()
                {
                    $all_post_types =   get_post_types();
                    $ignore = array (
                                        'revision',
                                        'nav_menu_item'
                                        );
                    
                    
                    if (apto_is_plugin_active('bbpress/bbpress.php'))
                        {
                            $ignore = array_merge($ignore, array( 'reply', 'forum'));
                        }                    
                    
                    foreach ($all_post_types as $key => $post_type)
                        {
                             if (in_array($post_type, $ignore))
                                unset($all_post_types[$key]);
                        }
                        
                     
                     return $all_post_types;    
                    
                }
            
            static function get_sort_settings($item_ID)
                {
                    if($item_ID == '')
                        return array();
                    
                    $data = get_post_meta($item_ID);
                    
                    $settings = array();
                    
                    //process the data and return as simple array
                    foreach($data as $key => $key_data)
                        {
                            reset($key_data);
                            $value =    maybe_unserialize(current($key_data));
                            
                            $settings[$key] =   $value;
                        }
                    
                    $defaults   = array (
                                            '_rules'                    =>  array(),
                                            '_conditionals'             =>  array(),
                                            '_last_sort_view_ID'        =>  '',
                                            '_view_type'                =>  '',
                                            '_title'                    =>  '',
                                            '_description'              =>  '',
                                            '_location'                 =>  '',
                                            '_autosort'                 =>  'yes',
                                            '_adminsort'                =>  'yes',
                                            '_new_items_to_bottom'      =>  'no',
                                            '_show_thumbnails'          =>  'no',
                                            '_capability'               =>  'switch_themes'
                                        );
                    $settings          = wp_parse_args( $settings, $defaults );
                    
                    return $settings;
                }
                
            static function get_sort_view_settings($item_ID)
                {
                    if($item_ID == '')
                        return array();
                    
                    $data = get_post_meta($item_ID);
                    
                    $settings = array();
                    
                    //process the data and return as simple array
                    foreach($data as $key => $key_data)
                        {
                            reset($key_data);
                            $value =    maybe_unserialize(current($key_data));
                            
                            $settings[$key] =   $value;
                        }
                        
                    $defaults   = array (
                                            '_order_type'               =>  'manual',
                                            '_view_selection'           =>  'archive',
                                            '_taxonomy'                 =>  '',
                                            '_term_id'                  =>  '',
                                            '_auto_order_by'            =>  '_default_',
                                            '_auto_custom_field_name'   =>  '',
                                            '_auto_custom_field_type'   =>  '',
                                            '_auto_order'               =>  'DESC'
                                        );
                    $settings          = wp_parse_args( $settings, $defaults );
                    
                    return $settings;
                }
                
            static function get_settings()
                {
                    $settings = get_option('apto_settings');    
                    
                    $defaults   = array (
                                            'plugin_version'                =>  1,
                                            'database_version'              =>  1,
                                            'show_reorder_interfaces'       =>  array()
                                        );
                    $settings          = wp_parse_args( $settings, $defaults );
                    
                    return $settings;
                }
                
            static function update_settings($settings)
                {
                    update_option('apto_settings', $settings);
                }
                
            
            /**
            * Check against the settings rule if it's a single woocommerce sort type
            * 
            */
            static function is_woocommerce($sortID)
                {
                    $is_woocommerce = FALSE;
                    if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
                        return FALSE;
                    
                    $sort_settings  =   self::get_sort_settings($sortID);    
                    $sort_rules     =   $sort_settings['_rules'];
                    if(count($sort_rules['post_type']) > 1)
                        return FALSE;
                        
                    reset($sort_rules['post_type']);
                    $post_type          =   current($sort_rules['post_type']);
                    if($post_type != "product")
                        return FALSE;
                        
                    return TRUE;                    
                }
                
            
            function query_get_orderby($orderBy, $query, $sorts_match_filter = array())
                {
                    //filter the query for unnecesarelly data;  i.e. empty taxonomy rules
                    $query          =   $this->query_filter_valid_data($query);

                    //identify the appropiate sort id and sort_view_id which match this query
                    $sort_view_id   =   $this->query_match_sort_id($query, $sorts_match_filter);
                    
                    //return default $orderBy if nothing found
                    if ($sort_view_id == '')
                        return  $orderBy;
                        
                    if(!is_admin())
                        $this->save_log('query_match', array('sort_view_id'  =>  $sort_view_id, 'query'  =>  $query));
                    
                    global $wpdb;
                    
                    $new_orderBy = $orderBy;
                        
                    $sort_view_settings =   $this->get_sort_view_settings($sort_view_id);
                    
                    $sort_view_data     =   get_post($sort_view_id);
                    if($sort_view_data->post_parent > 0)
                        $sortID             =   $sort_view_data->post_parent;
                        else
                        $sortID             =   $sort_view_id;
                    $sort_settings      =   $this->get_sort_settings($sortID); 
                    
                    if($sort_view_settings['_order_type'] == 'auto')
                        {
                            //Add falback for multiple 
                            $data_set = array(
                                                'order_by'          =>  (array)$sort_view_settings['_auto_order_by'],
                                                'custom_field_name' =>  (array)$sort_view_settings['_auto_custom_field_name'],
                                                'custom_field_type' =>  (array)$sort_view_settings['_auto_custom_field_type'],
                                                'order'             =>  (array)$sort_view_settings['_auto_order']
                                                );
                            
                            $new_orderBy = '';
                            
                            $counter = 0;
                            foreach($data_set['order_by']   as $key =>  $data)
                                {
                                    if($new_orderBy != '')
                                        $new_orderBy .= ', ';
                                    
                                    switch ($sort_view_settings['_auto_order_by'][$key])
                                        {
                                            case '_default_'        :
                                                                        break;
                                            
                                            case '_random_'         :
                                                                        $new_orderBy .= "RAND()";
                                                                        
                                                                        break;
                                            
                                            case '_custom_field_'   :
                                                                        
                                                                        $new_orderBy .=  $this->query_get_orderby_custom_field($key, $sort_view_id, $orderBy, $query);
                                   
                                                                        break;
                                                                    
                                            default: 
                                                                        $new_orderBy .= $wpdb->posts .".". $sort_view_settings['_auto_order_by'][$key] . " " . $sort_view_settings['_auto_order'][$key];
                                                                        
                                                                        break;
                                            
                                        }
                                        
                                   $counter++; 
                                }
                                
                            if($counter <   2)
                                $new_orderBy .= ", ". $wpdb->posts .".post_date ". $data_set['order'][0];
                              
                            return  apply_filters('apto_get_orderby', $new_orderBy, $orderBy, $query);    
                        }
                    
                    
                    //check for sticky posts then use another filter instead.
                    if(isset($sort_view_settings['_sticky_data']) && is_array($sort_view_settings['_sticky_data']) && count($sort_view_settings['_sticky_data']) > 0)
                        {
                            //hold the $sorts_match_filter piece of information for posts_clauses_request filter
                            //TTTTTTTTTTTTCCCCCCCCCCCCC
                            //to find another way to replace superglobal
                            global $sorts_match_filter__posts_clauses_request;
                            $sorts_match_filter__posts_clauses_request  =   $sorts_match_filter;
                            add_filter('posts_clauses_request', array($this, 'sticky_posts_clauses_request'), 999, 2);   
                            
                            return $orderBy;
                        }
                        

                    //custom order apply
                    $order_list  = $this->get_order_list($sort_view_id);
                    
                    $new_orderBy    =   $this->query_get_new_orderBy($orderBy, $query, $sort_view_id, $order_list);
                          
                    return  apply_filters('apto_get_orderby', $new_orderBy, $orderBy, $query); 
                    
                }
            
            
            function query_get_new_orderBy($orderBy, $query, $sort_view_id, $order_list)
                {
                    
                    global $wpdb;
                    
                    $new_orderBy = $orderBy;
                        
                    $sort_view_settings =   $this->get_sort_view_settings($sort_view_id);
                    
                    $sort_view_data     =   get_post($sort_view_id);
                    if($sort_view_data->post_parent > 0)
                        $sortID             =   $sort_view_data->post_parent;
                        else
                        $sortID             =   $sort_view_id;
                    $sort_settings      =   $this->get_sort_settings($sortID);
                    
                    
                    if (count($order_list) > 0 )
                        {
                            $query_order = isset($query->query['order']) ? strtoupper($query->query['order']) : 'ASC';
                            
                            //check if the orderby is not menu_order and autosort is turned on to make the order as ASC;  This will fix when use the get_posts() as it send DESC by default
                            if((!isset($query->query['orderby']) || (isset($query->query['orderby']) && $query->query['orderby'] != 'menu_order'))
                                && $sort_settings['_autosort'] == "yes")
                                {
                                    $query_order   =   'ASC';   
                                }

                            //check for bottom append new posts
                            $new_items_to_bottom    =   $sort_settings['_new_items_to_bottom'];
                            $new_items_to_bottom    =   apply_filters('new_items_to_bottom', $new_items_to_bottom, $sort_view_id, $query);

                            if($new_items_to_bottom == "yes")
                                {
                                    $_order_list = array_reverse($order_list);
                                    if($query_order == 'DESC')   
                                        $_order_list = array_reverse($_order_list);
                                    
                                    $new_orderBy = "FIELD(".$wpdb->posts.".ID, ". implode(",", $_order_list) .") DESC, ".$wpdb->posts.".post_date DESC";
                                }
                                else
                                {
                                    $_order_list = $order_list;
                                    if($query_order == 'DESC')   
                                        $_order_list = array_reverse($_order_list);
                                        
                                    $new_orderBy = "FIELD(".$wpdb->posts.".ID, ". implode(",", $_order_list) ."), ".$wpdb->posts.".post_date DESC";
                                }
                        }
                        else if($new_orderBy != '')
                            {
                                //if use just menu_order, append post_date in case a menu_order haven't been set
                                $temp_orderBy = $new_orderBy;
                                $temp_orderBy = str_ireplace("asc", "", $temp_orderBy);
                                $temp_orderBy = str_ireplace("desc", "", $temp_orderBy);
                                $temp_orderBy = trim($temp_orderBy);
                                if($temp_orderBy != $wpdb->posts . '.menu_order')
                                    {
                                        unset($temp_orderBy);
                                        return  apply_filters('apto_get_orderby', $new_orderBy, 'DESC', $query);
                                    }
                                    else
                                    {
                                       
                                        //apply order only when in _archive_
                                        if ($sort_settings['_view_type'] == 'multiple' && $sort_view_settings['_view_selection'] == 'archive')
                                            {
                                                $new_orderBy = $wpdb->posts.".menu_order, " . $wpdb->posts.".post_date ";
                                                //$new_orderBy .= $query->query_vars['order'];
                                                $new_orderBy .= "DESC";
                                            }
                                            else
                                            {
                                                $new_orderBy = $wpdb->posts. ".post_date DESC";   
                                            }
                                        
                                          
                                        return  apply_filters('apto_get_orderby', $new_orderBy, $orderBy, $query);
                                    }
                                                        
                            }
                        else
                        {
                            $new_orderBy = $wpdb->posts.".menu_order, " . $wpdb->posts.".post_date " . $query->query_vars['order'];
                        }
                       
                    return $new_orderBy;   
                }
            
                
            /**
            * Identify the appropiate sort id and sort_view_id which match this query    
            * 
            * @param mixed $query
            * @param mixed $sorts_match_filter  Contain set of filters (cutsto fileld with values) to allos sorts filtering
            */
            function query_match_sort_id($query, $sorts_match_filter)
                {
                    //check if there is already a query paramether as sort_view_id
                    if(isset($query->query['sort_view_id']))
                        return $query->query['sort_view_id'];
                    
                    $sort_items =   array();
                        
                    //check if there is already a query paramether as sort_id
                    if(isset($query->query['sort_id']) && $query->query['sort_id'] > 0)
                        {
                            $object         = new stdClass();
                            $object->ID     =   $query->query['sort_id'];
                            $sort_items[]   =   $object;
                            unset($object);     
                        }
                        
                    if(count($sort_items) < 1)
                        {
                            $sort_items =   $this->get_sorts_by_filters($sorts_match_filter);
                        }
                    
                    foreach($sort_items as $sort_item)
                        {
                            if($this->sort_id_exists($sort_item->ID) === FALSE)
                                continue;
                            
                            $sort_view_settings =   $this->get_sort_view_settings($sort_item->ID);
                            
                            switch($sort_view_settings['_view_type'])
                                {
                                    case 'simple'   :
                                                        $match  =   $this->sort_simple_match_check_on_query($sort_item->ID, $query);
                                                        if($match   !== FALSE)
                                                            return $match;
                                                            
                                                        break;      
                                    
                                    case 'multiple'   :
                                                        $match  =   $this->sort_multiple_match_check_on_query($sort_item->ID, $query);
                                                        if($match   !== FALSE)
                                                            return $match;
                                                            
                                                        break;
                                }
                        }
                    
                    return '';
                }
                
                
            function get_sorts_by_filters($sort_filters, $post_column_filters = array())
                {
                    $defaults   = array (
                                            'post_parent'               =>  '0',
                                            'post_type'                 =>  'apto_sort',
                                            'post_status'               =>  'publish'
                                        );
                    $post_column_filters          = wp_parse_args( $post_column_filters, $defaults );
                            
                    //try to identify other sorts which match this
                            
                    //get all sort items
                    //First try the specific / simple sorts then use the multiple / general
                    global $wpdb;
                    $mysql_query = "SELECT ". $wpdb->posts .".ID FROM ". $wpdb->posts ;
                    
                    if(count($sort_filters) > 0)
                        {
                            $q_inner_count  = 1;
                            foreach($sort_filters as $cf_name =>  $cf_values)
                                {
                                    $mysql_query .= " INNER JOIN ". $wpdb->postmeta ." AS PMF". $q_inner_count ." ON (". $wpdb->posts .".ID = PMF" . $q_inner_count ." .post_id) ";
                                    $q_inner_count++;
                                }
                        }
                        
                    $mysql_query .= " INNER JOIN ". $wpdb->postmeta ." AS PM2 ON (". $wpdb->posts .".ID = PM2.post_id) 
                                        
                                        WHERE 1 = 1 ";
                    
                    foreach($post_column_filters as $post_column    =>  $volumn_value)
                        {
                            $mysql_query .= " AND " . $wpdb->posts . "." . $post_column ." = '". $volumn_value  ."'" ;
                        }
                     
                    if(count($sort_filters) > 0)
                        {
                            $q_inner_count  = 1;
                            foreach($sort_filters as $cf_name =>  $cf_values)
                                {
                                    $mysql_query .= " AND (PMF" . $q_inner_count ." .meta_key = '" . $cf_name . "' AND CAST(PMF". $q_inner_count ." .meta_value AS CHAR) IN ('". implode("', '", $cf_values)  ."'))";
                                    $q_inner_count++;
                                }
                        }
                                                
                    $mysql_query .= " AND PM2.meta_key = '_view_type'
                                                
                                        GROUP BY ". $wpdb->posts .".ID 
                                        
                                        ORDER BY FIELD(PM2.meta_value, 'simple', 'multiple'),  ". $wpdb->posts .".ID ASC  ";
                    $sort_items =   $wpdb->get_results($mysql_query);   
                    
                    
                    return $sort_items;
                    
                }
            
            
            /**
            * Simple view match check
            *     
            * @param mixed $sortID
            * @param mixed $query
            */
            function sort_simple_match_check_on_query($sortID, $query)
                {
                    $sort_settings  =   $this->get_sort_settings($sortID);
                    $sort_rules     =   $this->get_sort_current_language_rules($sort_settings, FALSE);
                    if($sort_rules  === FALSE)
                        return FALSE;
                    
                    //check for query rules match
                    
                    /**
                    * 
                    * Check for post type
                    * 
                    */
                    $query_post_type = $this->query_get_post_types($query);
                    $differences = array_diff($query_post_type, $sort_rules['post_type']);
                    if(count($query_post_type) != count($sort_rules['post_type']) || count($differences) > 0)
                        return FALSE;
                        
                    
                    /**
                    * 
                    * Check for taxonomies match
                    * 
                    */
                    if(count($query->tax_query->queries) != count($sort_rules['taxonomy']))
                        return FALSE;
                    
                    //check for relation
                    if($query->tax_query->relation != $sort_rules['taxonomy_relation'])
                        return FALSE;
                    
                    //check for exact taxonomy match
                    if(count($query->tax_query->queries) > 0)
                        {
                            foreach($query->tax_query->queries as $query_tax)
                                {
                                    $found_match = FALSE;
                                    
                                    switch ($query_tax['field'])
                                        {
                                            case 'term_id':
                                            case 'ID':
                                            case 'id':
                                                        $query_tax_terms    = $query_tax['terms'];
                                                        if(!is_array($query_tax_terms))
                                                            $query_tax_terms    =   array($query_tax_terms);
                                                        break;
                                                        
                                            case 'slug':
                                                        
                                                        $query_tax_terms    = $query_tax['terms'];
                                                        if(!is_array($query_tax_terms))
                                                            $query_tax_terms    =   array($query_tax_terms);
                                                        
                                                        //switch terms to id 
                                                        foreach($query_tax_terms as $key => $query_tax_term_slug)
                                                            {
                                                                  $term_data                =   get_term_by('slug', $query_tax_term_slug, $query_tax['taxonomy']);
                                                                  $query_tax_terms[$key]    =   $term_data->term_id;
                                                            }

                                                        break;
                                        }
                                    
                                    foreach($sort_rules['taxonomy'] as $tax_rule)
                                        {
                                            //check for taxonomy name match
                                            if($tax_rule['taxonomy'] != $query_tax['taxonomy'])
                                                continue;
                                            
                                            //check for operator match
                                            if($tax_rule['operator'] != $query_tax['operator'])
                                                continue;
                                            
                                            //check for terms
                                            $differences = array_diff($query_tax_terms, $tax_rule['terms']);
                                            if(count($query_tax_terms) != count($tax_rule['terms']) || count($differences) > 0)
                                                continue;
                                                
                                            $found_match    =   TRUE;
                                        }
                                    
                                    if($found_match === FALSE)
                                        return FALSE;
                                }
                            
                        }
                    
                    
                    
                    /**
                    * 
                    * Check for conditionals match
                    * 
                    */
                    if(count($sort_settings['_conditionals']) > 0)
                        {
                            foreach($sort_settings['_conditionals'] as $conditional_group)
                                {
                                    $group_match    =   TRUE;
                                    foreach($conditional_group as $conditional)
                                        {
                                            $value      =   isset($conditional['conditional_value']) ?  $conditional['conditional_value'] :   '';
                                            $comparison =   isset($conditional['conditional_comparison']) ?  $conditional['conditional_comparison'] :   '';
                                            $match  =   call_user_func_array($this->conditional_rules->rules[$conditional['conditional_id']]['query_check_callback'], array($comparison, $value, $query));
                                            if($match   ===  FALSE)
                                                {
                                                    $group_match    =   FALSE;
                                                    break;
                                                }
                                        }
                                        
                                    if($group_match === TRUE)
                                        break;
                                }
                                
                            if($group_match === FALSE)
                                return FALSE;

                        }
                    
                    //identify the sort view
                    $attr = array(
                                    '_view_selection'   =>  'simple',
                                    '_view_language'    =>  $this->get_blog_language()
                                    );
                    $sort_view_id   =   $this->get_sort_view_id_by_attributes($sortID, $attr);
                    
                    if($sort_view_id > 0)
                        return $sort_view_id;     
                        else
                        return FALSE;   
                }
                
            
            /**
            * Multiple view match check
            * 
            * @param mixed $sortID
            * @param mixed $query
            */
            function sort_multiple_match_check_on_query($sortID, $query)
                {
                    $sort_settings =   $this->get_sort_settings($sortID);
                    //check for query rules match
                    
                    /**
                    * 
                    * Check for post type
                    * 
                    */
                    $query_post_type = $this->query_get_post_types($query);
                    /*
                    $differences = array_diff($query_post_type, $sort_settings['_rules']['post_type']);
                    if(count($query_post_type) != count($sort_settings['_rules']['post_type']) || count($differences) > 0)
                        return FALSE;
                    */
                    //v3.0 try a partial match, for general queries like category term without a post type specification (presuming the category is assigned to multiple post types)
                    if(count($query_post_type) === 1 && strtolower($query_post_type[0]) == 'any')
                        $query_post_type[0] =   'post';
                    if(count(array_intersect($query_post_type, $sort_settings['_rules']['post_type'])) < 1)
                        return FALSE;                    
                    
                    //check the taxonomy
                    $_view_selection    =   '';
                    //need a single taxonomy to match otherwise a simple sort need to be manually created
                    //fallback on archive;  This maybe changed later and return FALSE !! 
                    if(count($query->tax_query->queries) < 1 || count($query->tax_query->queries) > 1)
                        $_view_selection    =   'archive';
                        else
                            {
                                reset($query->tax_query->queries);
                                $query_tax      =   current($query->tax_query->queries);
                                $taxonomy       =   $query_tax['taxonomy'];
                                
                                //identify the term
                                switch ($query_tax['field'])
                                    {
                                        case 'term_id':
                                        case 'ID':
                                        case 'id':
                                                    $query_tax_terms    = $query_tax['terms'];
                                                    if(!is_array($query_tax_terms))
                                                        $query_tax_terms    =   array($query_tax_terms);
                                                    break;
                                                    
                                        case 'slug':
                                                    
                                                    $query_tax_terms    = $query_tax['terms'];
                                                    if(!is_array($query_tax_terms))
                                                        $query_tax_terms    =   array($query_tax_terms);
                                                    
                                                    //switch terms to id 
                                                    foreach($query_tax_terms as $key => $query_tax_term_slug)
                                                        {
                                                              $term_data                =   get_term_by('slug', $query_tax_term_slug, $query_tax['taxonomy']);
                                                              $query_tax_terms[$key]    =   $term_data->term_id;
                                                        }

                                                    break;
                                    }
                                     
                                //fallback on archive;  
                                //This maybe changed later and return FALSE !!    
                                if(count($query_tax_terms) < 1 || count($query_tax_terms) > 1)
                                    {
                                        //check agains the include_children paramether 
                                        if(count($query_tax_terms) > 1 && $query_tax['include_children'] == FALSE)
                                            {
                                                $_view_selection    =   'taxonomy'; 
                                                 
                                                reset($query_tax_terms);
                                                $term_id    =      current($query_tax_terms);
                                            }
                                            else
                                            $_view_selection    =   'archive';
                                    }
                                    else
                                    {
                                        //check the operator
                                        //fallback on archive;  This maybe changed later and return FALSE !! 
                                        if(!in_array($query_tax['operator'], array('IN', 'AND', 'NOT IN')))
                                            $_view_selection    =   'archive';
                                            else
                                            {
                                                $_view_selection    =   'taxonomy';
                                                
                                                reset($query_tax_terms);
                                                $term_id    =      current($query_tax_terms);
                                            }
                                    }
                            }
                    
                    /**
                    * 
                    * Check for conditionals match
                    * 
                    */
                    if(count($sort_settings['_conditionals']) > 0)
                        {
                            foreach($sort_settings['_conditionals'] as $conditional_group)
                                {
                                    $group_match    =   TRUE;
                                    foreach($conditional_group as $conditional)
                                        {
                                            $value      =   isset($conditional['conditional_value']) ?  $conditional['conditional_value'] :   '';
                                            $comparison =   isset($conditional['conditional_comparison']) ?  $conditional['conditional_comparison'] :   '';
                                            $match  =   call_user_func_array($this->conditional_rules->rules[$conditional['conditional_id']]['query_check_callback'], array($comparison, $value, $query));
                                            if($match   ===  FALSE)
                                                {
                                                    $group_match    =   FALSE;
                                                    break;
                                                }
                                        }
                                        
                                    if($group_match === TRUE)
                                        break;
                                }
                                
                            if($group_match === FALSE)
                                return FALSE;

                        }
                    
                            
                    //identify the sort view
                    $attr = array(
                                    '_view_selection'    =>  $_view_selection
                                    );
                    if($_view_selection == 'taxonomy')
                        {
                            $attr['_taxonomy']  =   $taxonomy;
                            $attr['_term_id']   =   $term_id;
                        }
                    if($_view_selection  ==  'archive')
                                $attr['_view_language']   =   $this->get_blog_language();                    
                    $sort_view_id   =   $this->get_sort_view_id_by_attributes($sortID, $attr);
                    
                    if($sort_view_id > 0)
                        return $sort_view_id;     
                        else
                        return FALSE;
                }
                
                
            function query_get_post_types($query)
                {
                    $query_post_types = isset($query->query_vars['post_type']) ? $query->query_vars['post_type'] :   array();
                    if(!empty($query_post_types) && !is_array($query_post_types))
                        $query_post_types    =   (array)$query_post_types;
                    if(empty($query_post_types) && !is_array($query_post_types))
                        $query_post_types    =   array();
                        
                    //TTTTTTTTTTTTTTTTTTTCCCCCCCCCCCCCCCC
                    /*
                    if ( 'any' == $post_type ) {
                        $in_search_post_types = get_post_types( array('exclude_from_search' => false) );
                        if ( empty( $in_search_post_types ) )
                            $where .= ' AND 1=0 ';
                        else
                            $where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $in_search_post_types ) . "')";
                    } elseif ( !empty( $post_type ) && is_array( $post_type ) ) {
                        $where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $post_type) . "')";
                    } elseif ( ! empty( $post_type ) ) {
                        $where .= " AND $wpdb->posts.post_type = '$post_type'";
                        $post_type_object = get_post_type_object ( $post_type );
                    } elseif ( $this->is_attachment ) {
                        $where .= " AND $wpdb->posts.post_type = 'attachment'";
                        $post_type_object = get_post_type_object ( 'attachment' );
                    } elseif ( $this->is_page ) {
                        $where .= " AND $wpdb->posts.post_type = 'page'";
                        $post_type_object = get_post_type_object ( 'page' );
                    } else {
                        $where .= " AND $wpdb->posts.post_type = 'post'";
                        $post_type_object = get_post_type_object ( 'post' );
                    }
                    */
                        
                    if ( empty($query_post_types) ) 
                        {
                            $taxonomies =   array();
                            if(isset($query->tax_query) && isset($query->tax_query->queries))
                                $taxonomies = wp_list_pluck( $query->tax_query->queries, 'taxonomy' );
                            foreach ( $this->get_post_types() as $pt ) 
                                {
                                    $object_taxonomies = $pt === 'attachment' ? get_taxonomies_for_attachments() : get_object_taxonomies( $pt );
                                    if ( array_intersect( $taxonomies, $object_taxonomies ) )
                                        $query_post_types[] = $pt;
                                }
                               
                            //v3.0  ??????chose the first
                            /*
                            if(count($query_post_types) > 1)
                                $query_post_types  =   array_slice($query_post_types, 0, 1);
                            */
                        }
                        
                    if(count($query_post_types) < 1)
                        $query_post_types[]  =   'post';
                        
                    return  $query_post_types;    
                }
            
            
            function sticky_posts_clauses_request($query_pieces, $query)
                {
                    //remove this filter for being triggered again
                    remove_filter('posts_request', array($this, 'sticky_posts_clauses_request'), 999, 2);
                    
                    //filter the query for unnecesarelly data;  i.e. empty taxonomy rules
                    $query          =   $this->query_filter_valid_data($query);

                    global $sorts_match_filter__posts_clauses_request;
                    
                    //identify the appropiate sort id and sort_view_id which match this query
                    $sort_view_id   =   $this->query_match_sort_id($query, $sorts_match_filter__posts_clauses_request);
                    
                    global $wpdb;
                    
                    $sort_view_settings =   $this->get_sort_view_settings($sort_view_id);
                    
                    $sort_view_data     =   get_post($sort_view_id);
                    if($sort_view_data->post_parent > 0)
                        $sortID             =   $sort_view_data->post_parent;
                        else
                        $sortID             =   $sort_view_id;
                    $sort_settings      =   $this->get_sort_settings($sortID);
                    
                    
                    $new_orderBy    =   $orderBy    =   $query_pieces['orderby'];
                    
                    $order_list     =   $this->get_order_list($sort_view_id);
                    
                    if (count($order_list) > 0 )
                        {
                            $query_order = isset($query->query['order']) ? strtoupper($query->query['order']) : 'ASC';
                            
                            //check if the orderby is not menu_order and autosort is turned on to make the order as ASC;  This will fix when use the get_posts() as it send DESC by default
                            if((!isset($query->query['orderby']) || (isset($query->query['orderby']) && $query->query['orderby'] != 'menu_order'))
                                && $sort_settings['_autosort'] == "yes")
                                {
                                    $query_order   =   'ASC';   
                                }

                            //check for bottom append new posts
                            $new_items_to_bottom    =   $sort_settings['_new_items_to_bottom'];
                            $new_items_to_bottom    =   apply_filters('new_items_to_bottom', $new_items_to_bottom, $sort_view_id, $query);

                            if($new_items_to_bottom == "yes")
                                {
                                    $_order_list = array_reverse($order_list);
                                    if($query_order == 'DESC')   
                                        $_order_list = array_reverse($_order_list);
                                    
                                    $new_orderBy = "FIELD(".$wpdb->posts.".ID, ". implode(",", $_order_list) .") DESC, ".$wpdb->posts.".post_date DESC";
                                }
                                else
                                {
                                    $_order_list = $order_list;
                                    if($query_order == 'DESC')   
                                        $_order_list = array_reverse($_order_list);
                                        
                                    $new_orderBy = "FIELD(".$wpdb->posts.".ID, ". implode(",", $_order_list) ."), ".$wpdb->posts.".post_date DESC";
                                }
                        }
                        else if($new_orderBy != '')
                            {
                                //if use just menu_order, append post_date in case a menu_order haven't been set
                                $temp_orderBy = $new_orderBy;
                                $temp_orderBy = str_ireplace("asc", "", $temp_orderBy);
                                $temp_orderBy = str_ireplace("desc", "", $temp_orderBy);
                                $temp_orderBy = trim($temp_orderBy);
                                if($temp_orderBy != $wpdb->posts . '.menu_order')
                                    {
                                        unset($temp_orderBy);
                                    }
                                    else
                                    {
                                        //apply order only when in _archive_
                                        if ($sort_settings['_view_type'] == 'multiple' && $sort_view_settings['_view_selection'] == 'archive')
                                            {
                                                $new_orderBy = $wpdb->posts.".menu_order, " . $wpdb->posts.".post_date ";
                                                //$new_orderBy .= $query->query_vars['order'];
                                                $new_orderBy .= "DESC";
                                            }
                                            else
                                            {
                                                $new_orderBy = $wpdb->posts. ".post_date DESC";   
                                            }
                                    }
                                                        
                            }
                        else
                        {
                            $new_orderBy = $wpdb->posts.".menu_order, " . $wpdb->posts.".post_date " . $query->query_vars['order'];
                        }
                    
                    
                    $query_groupby    =   "";
                    if($query_pieces['groupby'] !=  '')
                        $query_groupby    =   'GROUP BY ' . $query_pieces['groupby'];
                        
                    $query_orderby    =   "";
                    if($new_orderBy !=  '')
                        $query_orderby    =   'ORDER BY ' . $new_orderBy;
                    
                    //create the sort list
                    $query_request  = "SELECT ". $query_pieces['distinct'] ." " . $wpdb->posts .".ID FROM " . $wpdb->posts ." " . $query_pieces['join'] ." WHERE 1=1 " . $query_pieces['where'] ." " . $query_groupby . "  " . $query_orderby;
                    $results = $wpdb->get_results($query_request);
                    
                    $order_list =   array();
                    foreach ($results as $result)
                        $order_list[] = $result->ID;
                    
                    //apply sicky
                    $order_list     =   $this->order_list_apply_sticky_data($order_list, $sort_view_settings['_sticky_data']);

                    $new_orderBy    =   $this->query_get_new_orderBy($orderBy, $query, $sort_view_id, $order_list);
                    
                    //update the orderby piece
                    $query_pieces['orderby']    =   $new_orderBy;
                       
                    return  $query_pieces;   
                }
                
            function get_order_list($sort_view_id)
                {
                    global $wpdb;
                    
                    $order_list = array();
                    
                    $query = "SELECT object_id FROM `". $wpdb->prefix ."apto_sort_list`
                                    WHERE `sort_view_id`    =   ". $sort_view_id;
                    $query .= " ORDER BY id ASC";
                    
                    $results = $wpdb->get_results($query);
                    
                    foreach ($results as $result)
                        $order_list[] = $result->object_id;
                        
                    $sort_view_settings =   $this->get_sort_view_settings($sort_view_id);
                    
                    $order_list = apply_filters('apto_get_order_list', $order_list, $sort_view_id);
                    
                    return $order_list;    
                }
                
            function order_list_apply_sticky_data($order_list, $sticky_data)
                {
                    $updated_order_list     =   array();
                    
                    foreach($sticky_data as $key =>  $object_id)
                        {
                             if(array_search($object_id, $order_list)   !== FALSE)
                                $updated_order_list[$key - 1]  =   $object_id;   
                        }
                    
                    
                    $current_index = 0;
                    foreach($order_list as $key =>  $object_id)
                        {
                            if(array_search($object_id, $updated_order_list)   !== FALSE)
                                continue;
                            
                            while(isset($updated_order_list[$current_index]))
                                {
                                    $current_index++;
                                }
                                
                             $updated_order_list[$current_index]  =   $object_id;   
                        }
                        
                    ksort($updated_order_list);
                    
                    return $updated_order_list;
                }   
            
            /**
            * Return the orderby argv for query on a custom field sort
            * 
            * @param mixed $sort_view_id
            * @param mixed $query
            */
            function query_get_orderby_custom_field($data_set_key, $sort_view_id, $orderBy, $query)
                {
                    global $wpdb;
                        
                    $sort_view_settings =   $this->get_sort_view_settings($sort_view_id);
                    
                    $sort_view_data     =   get_post($sort_view_id);
                    $sortID             =   $sort_view_data->post_parent;
                    
                    $sort_settings      =   $this->get_sort_settings($sortID);
                    
                    $data_set = array(
                                                'order_by'          =>  (array)$sort_view_settings['_auto_order_by'],
                                                'custom_field_name' =>  (array)$sort_view_settings['_auto_custom_field_name'],
                                                'custom_field_type' =>  (array)$sort_view_settings['_auto_custom_field_type'],
                                                'order'             =>  (array)$sort_view_settings['_auto_order']
                                                );
                    
                    $custom_field_name    = $data_set['custom_field_name'][$data_set_key];
                    //if empty no need to continue
                    if(empty($custom_field_name))
                        return $orderBy;
                        
                    $custom_field_type    = $data_set['custom_field_type'][$data_set_key];
                    
                    //fallback compatibility
                    if($custom_field_type   ==  '')
                        $custom_field_type  =   'none';
                    
                    $order_list = array();
                    
                    //retrieve the list of posts which contain the custom field
                    if(isset($sort_settings['_view_type']) && $sort_settings['_view_type']    == 'simple')
                        {
                            //this is the simple view
                        
                            $mysql_query = "SELECT DISTINCT ". $wpdb->posts .".* FROM ". $wpdb->posts ."  
                                            JOIN ". $wpdb->postmeta ." as pm1 ON (". $wpdb->posts .".ID = pm1.post_id)";
                            
                            //taxonomy
                            if(isset($sort_settings['_rules']['taxonomy']) && count($sort_settings['_rules']['taxonomy']) > 0)
                                {
                                    $q_inner_count  =   1;
                                    foreach($sort_settings['_rules']['taxonomy'] as $rule_tax)
                                        {
                                            $mysql_query .= " INNER JOIN ". $wpdb->term_relationships ." AS tr" . $q_inner_count ." ON (". $wpdb->posts .".ID = tr" . $q_inner_count .".object_id)";        
                                            
                                            $q_inner_count++;
                                        }
                                }

                            $mysql_query .= " WHERE 1=1";
                            
                            //taxonomy
                            if(isset($sort_settings['_rules']['taxonomy']) && count($sort_settings['_rules']['taxonomy']) > 0)
                                {
                                    $mysql_query .= " AND ( ";
                                    
                                    $first_tax      =   TRUE;
                                    $q_inner_count  =   1;
                                    foreach($sort_settings['_rules']['taxonomy'] as $rule_tax)
                                        {
                                            if($first_tax   === TRUE)
                                                {
                                                    $first_tax  =   FALSE;
                                                    $mysql_query .= " ( ";
                                                }
                                                else
                                                $mysql_query .= " " . $sort_settings['_rules']['taxonomy_relation'] . " ( ";
                                            
                                            $query_terms = array();
                                            foreach($rule_tax['terms'] as $term_id)
                                                {
                                                    $term_data      =    get_term($term_id, $rule_tax['taxonomy']);
                                                    $query_terms[]  =   $term_data->term_taxonomy_id;
                                                }
                                            
                                            if($rule_tax['operator'] == 'IN')
                                                {
                                                    $mysql_query .=   "tr" . $q_inner_count .".term_taxonomy_id IN (". implode(",", $query_terms) .")";
                                                }
                                                else if($rule_tax['operator'] == 'NOT IN')
                                                    {
                                                        $mysql_query .=   $wpdb->posts . ".ID NOT IN (
                                                                                    SELECT object_id
                                                                                    FROM tr" . $q_inner_count ."
                                                                                    WHERE term_taxonomy_id IN (". implode(",", $query_terms) ."))";
                                                    }
                                                else if($rule_tax['operator'] == 'AND')
                                                    {
                                                        $mysql_query .=   " (
                                                                            SELECT COUNT(1)
                                                                            FROM ". $wpdb->term_relationships ."
                                                                            WHERE term_taxonomy_id IN (". implode(",", $query_terms) .")
                                                                            AND object_id = wp_posts.ID
                                                                        ) = ". count($query_terms) ." ";
                                                    }
                                            
                                            $mysql_query .= " ) ";
                                            
                                            $q_inner_count++;
                                        }
                                        
                                    $mysql_query .= " ) ";
                                }
                                  
                            //add author if set
                            if(isset($sort_settings['_rules']['author']))
                                {
                                    $mysql_query .= " AND ". $wpdb->posts .".post_author IN ('"  .   implode("', '", $sort_settings['_rules']['author']) .   "')";        
                                }
                                
                            $mysql_query .= " AND pm1.meta_key = '". esc_sql($custom_field_name) ."'
                                        AND ". $wpdb->posts .".post_type IN ('"  .   implode("', '", $sort_settings['_rules']['post_type']) .   "') ";
                            
                            
                            switch($custom_field_type)
                                {
                                    case "SIGNED"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS SIGNED) ". $data_set['order'][$data_set_key];
                                                        break;
                                    
                                    case "UNSIGNED"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS UNSIGNED) ". $data_set['order'][$data_set_key];
                                                        break;
                                                        
                                    case "DATE"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS DATE) ". $data_set['order'][$data_set_key];
                                                        break;
                                                        
                                    case "DATETIME"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS DATETIME) ". $data_set['order'][$data_set_key];
                                                        break;
                                                        
                                    case "TIME"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS TIME) ". $data_set['order'][$data_set_key]; 
                                                        break;
                                                        
                                    default:
                                                        $mysql_query .= " ORDER BY pm1.meta_value ". $data_set['order'][$data_set_key];                            
                                                        break;
                                }
                                        
                            
                            $results = $wpdb->get_results($mysql_query);    

                        }
                        else
                        {
                            /**
                            * To deep Check !!
                            * Possible just to run query?
                            */
                            
                            //this is the multiple view  
                            $mysql_query = "SELECT DISTINCT ". $wpdb->posts .".ID, pm1.meta_value FROM ". $wpdb->posts ."  
                                            JOIN ". $wpdb->postmeta ." as pm1 ON (". $wpdb->posts .".ID = pm1.post_id)";
                            
                            //taxonomy
                            if(isset($query->tax_query->queries) && count($query->tax_query->queries) > 0)
                                {
                                    $q_inner_count  =   1;
                                    foreach($query->tax_query->queries as $rule_tax)
                                        {
                                            $mysql_query .= " INNER JOIN ". $wpdb->term_relationships ." AS tr" . $q_inner_count ." ON (". $wpdb->posts .".ID = tr" . $q_inner_count .".object_id)";        
                                            
                                            $q_inner_count++;
                                        }
                                }

                            $mysql_query .= " WHERE 1=1";
                            
                            //taxonomy
                            if(isset($query->tax_query->queries) && count($query->tax_query->queries) > 0)
                                {
                                    $mysql_query .= " AND ( ";
                                    
                                    $first_tax      =   TRUE;
                                    $q_inner_count  =   1;
                                    foreach($query->tax_query->queries as $rule_tax)
                                        {
                                            if($first_tax   === TRUE)
                                                {
                                                    $first_tax  =   FALSE;
                                                    $mysql_query .= " ( ";
                                                }
                                                else
                                                $mysql_query .= " " . $query->tax_query->relation . " ( ";
                                            
                                            $query_terms = array();
                                            foreach($rule_tax['terms'] as $term_id)
                                                {
                                                    $term_data      =    get_term_by($rule_tax['field'], $term_id, $rule_tax['taxonomy']);
                                                    $query_terms[]  =   $term_data->term_taxonomy_id;
                                                }
                                            
                                            if($rule_tax['operator'] == 'IN')
                                                {
                                                    $mysql_query .=   "tr" . $q_inner_count .".term_taxonomy_id IN (". implode(",", $query_terms) .")";
                                                }
                                                else if($rule_tax['operator'] == 'NOT IN')
                                                    {
                                                        $mysql_query .=   $wpdb->posts . ".ID NOT IN (
                                                                                    SELECT object_id
                                                                                    FROM tr" . $q_inner_count ."
                                                                                    WHERE term_taxonomy_id IN (". implode(",", $query_terms) ."))";
                                                    }
                                                else if($rule_tax['operator'] == 'AND')
                                                    {
                                                        $mysql_query .=   " (
                                                                            SELECT COUNT(1)
                                                                            FROM ". $wpdb->term_relationships ."
                                                                            WHERE term_taxonomy_id IN (". implode(",", $query_terms) .")
                                                                            AND object_id = wp_posts.ID
                                                                        ) = ". count($query_terms) ." ";
                                                    }
                                            
                                            $mysql_query .= " ) ";
                                            
                                            $q_inner_count++;
                                        }
                                        
                                    $mysql_query .= " ) ";
                                }
                                  
                            //add author if set
                            if(isset($query->query['author']) && $query->query['author'] != '')
                                {
                                    $authors    =   (array)$query->query['author'];
                                    $mysql_query .= " AND ". $wpdb->posts .".post_author IN ('"  .   implode("', '", $query->query['author']) .   "')";        
                                }
                                
                            $post_types =   $this->query_get_post_types($query);
                            $mysql_query .= " AND pm1.meta_key = '". esc_sql($custom_field_name) ."'
                                        AND ". $wpdb->posts .".post_type IN ('"  .   implode("', '", $post_types) .   "')" ;
                            
                            switch($custom_field_type)
                                {
                                    case "SIGNED"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS SIGNED) ". $data_set['order'][$data_set_key];
                                                        break;
                                    
                                    case "UNSIGNED"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS UNSIGNED) ". $data_set['order'][$data_set_key];
                                                        break;
                                                        
                                    case "DATE"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS DATE) ". $data_set['order'][$data_set_key];
                                                        break;
                                                        
                                    case "DATETIME"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS DATETIME) ". $data_set['order'][$data_set_key];
                                                        break;
                                                        
                                    case "TIME"     :
                                                        $mysql_query .= " ORDER BY CAST(pm1.meta_value AS TIME) ". $data_set['order'][$data_set_key]; 
                                                        break;
                                                        
                                    default:
                                                        $mysql_query .= " ORDER BY pm1.meta_value ". $data_set['order'][$data_set_key];                            
                                                        break;
                                }            
                            
                            $results = $wpdb->get_results($mysql_query);    
                        }
                        
                    $orderBy    =   '';    
                    if (count($results) > 0 )
                        {
                                
                            $counter = 1;
                            $previous_meta_value    =   NULL;  
                            
                            $orderBy = "CASE ";
                            foreach ($results as $result)
                                {
                                    if($previous_meta_value !== NULL && $previous_meta_value != $result->meta_value)
                                        $counter++;
                                    
                                    $previous_meta_value    =   $result->meta_value;
                                    
                                    $orderBy .= " WHEN ". $wpdb->posts .".ID = ".$result->ID."  THEN  ". $counter;   
                                }
                            
                            $counter++;
                            $orderBy .= " ELSE ". $counter ." END";
                        }
                    
                    
                    return $orderBy;
                }
            
            
            /**
            * Retrieve the sort view ID
            *     
            * @param mixed $sortID      This is the main sort ID holder
            * @param mixed $attr
            */
            static function get_sort_view_id_by_attributes($sortID, $attr)
                {
                    $defaults   = array (
                                            '_view_selection'          =>  'archive'
                                        );
                    
                    // Parse incoming $args into an array and merge it with $defaults
                    $attr = wp_parse_args( $attr, $defaults );
                    
                    $sort_view_ID = '';
                    
                    global $wpdb;
                    
                    $mysql_query    =   "SELECT ID FROM ". $wpdb->posts;
                    
                    $inner_no   =   1;
                    foreach($attr   as $key =>  $value)
                        {
                            $mysql_query    .=  "   INNER JOIN ". $wpdb->postmeta ." AS pm". $inner_no ."  ON (". $wpdb->posts .".ID = pm". $inner_no .".post_id) ";
                            
                            $inner_no++;
                        }
                    
                    $mysql_query    .=  " WHERE 1=1 AND ". $wpdb->posts .".post_parent = ". $sortID ."  AND ". $wpdb->posts .".post_type = 'apto_sort' AND (". $wpdb->posts .".post_status = 'publish')";
                    
                    $inner_no   =   1;
                    foreach($attr   as $key =>  $value)
                        {
                            $mysql_query    .=  "   AND  (pm". $inner_no .".meta_key = '".  $key ."' AND CAST(pm". $inner_no .".meta_value AS CHAR) = '".   $value  ."') ";
                            
                            $inner_no++;
                        }
                        
                    $mysql_query    .=  "  LIMIT 1 ";
                    
                    $sort_view_ID = $wpdb->get_var($mysql_query);
                             
                    return $sort_view_ID;   
                    
                }
                
            
            /**
            * Check if a given sort id exists
            * 
            * @param mixed $sortID
            */
            function sort_id_exists($sortID)
                {
                    if($sortID == '')
                        return FALSE;
                    
                    global $wpdb;
                    
                    $query              =    "SELECT count(ID) AS founds FROM " .  $wpdb->posts ."
                                                    WHERE ID = '". $sortID ."'";
                    $founds             =   $wpdb->get_var($query);
                    if($founds > 0)
                        return TRUE;
                    
                    return FALSE;   
                }
            
            function exists_sorts_with_autosort_on()
                {
                    global $wpdb;
                    
                    $mysql_query = "SELECT ". $wpdb->posts .".ID FROM ". $wpdb->posts ."
                                        INNER JOIN ". $wpdb->postmeta ." AS PM ON (". $wpdb->posts .".ID = PM.post_id)
                                        WHERE ". $wpdb->posts .".post_parent = 0  
                                                AND ". $wpdb->posts .".post_type = 'apto_sort' 
                                                AND ". $wpdb->posts .".post_status = 'publish' 
                                                AND PM.meta_key = '_autosort' AND PM.meta_value = 'yes'";
                    $sort_items =   $wpdb->get_results($mysql_query); 
                    if(count($sort_items) > 0)   
                        return TRUE;
                        else
                        return FALSE;
                }
            
            
            static function roles_capabilities()
                {
                    $roles_capability = array(
                                                'Subscriber'                =>    array(
                                                                                            'title'         =>  __('Subscriber', 'apto'),
                                                                                            'capability'    =>  'read'
                                                                                            ),
                                                'Contributor'               =>    array(
                                                                                            'title'         =>  __('Contributor', 'apto'),
                                                                                            'capability'    =>  'edit_posts'
                                                                                            ),
                                                'Author'                    =>    array(
                                                                                            'title'         =>  __('Author', 'apto'),
                                                                                            'capability'    =>  'publish_posts'
                                                                                            ),
                                                'Editor'                    =>    array(
                                                                                            'title'         =>  __('Editor', 'apto'),
                                                                                            'capability'    =>  'publish_pages'
                                                                                            ),
                                                'Administrator'             =>    array(
                                                                                            'title'         =>  __('Administrator', 'apto'),
                                                                                            'capability'    =>  'switch_themes'
                                                                                            )                                                                                                                                                             
                                                );
                   $roles_capability = apply_filters('apto_get_roles_capability', $roles_capability);
                   
                   return $roles_capability;    
                    
                }
                
            function query_filter_valid_data($query)
                {
                    
                    //filter the taxonomies
                    if(count($query->tax_query->queries) > 0)
                        {
                            $query_tax  =   array();
                                
                            foreach($query->tax_query->queries as $key  =>  $data)
                                {
                                    if($key ===  'relation')
                                        {
                                            $query_tax['relation']  =   $data;
                                        }
                                        else
                                        {
                                            if((isset($data['terms']) && is_array($data['terms']) && count(array_filter($data['terms'])) > 0) 
                                                    || (isset($data['terms']) && !is_array($data['terms']) && $data['terms'] != ''))
                                                    {
                                                        $query_tax[$key]            =   $data;
                                                        $query_tax[$key]['terms']   =   array_filter($query_tax[$key]['terms']);
                                                    }
                                        }                                    
                                }
                            
                            $query->tax_query->queries  =   $query_tax;
                            unset($query_tax);
                                
                            //check for duplicate queries with the very same data
                            //for WP E-Commerce bugs ...
                            $found_duplicate = TRUE;
                            if(count($query->tax_query->queries) < 2)
                                $found_duplicate    =   FALSE;
                            while($found_duplicate)
                                {
                                    if(count($query->tax_query->queries) < 2)
                                        break;
                                        
                                    $found_duplicate = FALSE;
                                    
                                    foreach($query->tax_query->queries as $a_key  =>  $a_data)
                                        {
                                            foreach($query->tax_query->queries as $b_key  =>  $b_data)
                                                {
                                                    if($a_key   ==  $b_key)                                     
                                                        continue;
                                                        
                                                    if($a_data  === $b_data)
                                                        {
                                                            unset($query->tax_query->queries[$b_key]);
                                                            $found_duplicate    =   TRUE;
                                                        }
                                                        
                                                    if($found_duplicate === TRUE)
                                                        break;
                                                }
                                                
                                            if($found_duplicate === TRUE)
                                                break;
                                        }       
                                }                            
                        }
                    
                    //reindex the query
                    $query->tax_query->queries  =   array_values($query->tax_query->queries);
                    
                    return $query;
                }    
            
                
            static function get_blog_language()
                {
                    $language   =   '';
                    
                    //check if WPML is active
                    if (defined('ICL_LANGUAGE_CODE') && defined('ICL_SITEPRESS_VERSION'))
                        {
                            //do not rely on ICL_LANGUAGE_CODE as main language can switch durring the code execution
                            //$language = ICL_LANGUAGE_CODE;
                            global $sitepress;
                            if(is_object($sitepress))
                                $language   =   $sitepress->get_current_language();
                            
                            //polylang
                            global $polylang;
                            if(is_object($polylang))
                                $language   =   $polylang->curlang->slug;
                        }
                        
                    //check Polylang
                    if(function_exists('pll_current_language'))
                        $lang   =   pll_current_language();
                    
                    $wp_locale  =   get_locale();
                    if($language == '' && $wp_locale   != '')
                        {
                            $locale_data    =   explode("_", $wp_locale);
                            $language       =   $locale_data[0];
                        }
                    
                    if ($language == '')
                        $language = 'en';
                    
                    return $language;   
                }
                
            public function get_blog_default_language()
                {
                    $language   =   '';
                    
                    //check if WPML is active
                    if (defined('ICL_LANGUAGE_CODE'))
                        {
                            global $sitepress;
                            if(is_object($sitepress))
                                $language   =   $sitepress->get_current_language();
                            
                            //polylang
                            global $polylang;
                            if(is_object($polylang))
                                $language   =   $polylang->curlang->slug;
                        }
                    
                    $wp_locale  =   get_locale();
                    if($wp_locale   != '')
                        {
                            $locale_data    =   explode("_", $wp_locale);
                            $language       =   $locale_data[0];
                        }
                    
                    if ($language == '')
                        $language = 'en';
                    
                    return $language;   
                }
                
            function get_sort_current_language_rules($sort_settings, $ReturnDefaultIfEmpty = TRUE)
                {

                    if (!defined('ICL_LANGUAGE_CODE'))
                        return $sort_settings['_rules'];
                                        
                    $default_language   =   $this->get_blog_default_language(); 
                    $current_language   =   $this->get_blog_language();
                    if(isset($sort_settings['_rules_' . $current_language]))
                        return $sort_settings['_rules_' . $current_language];
                    
                    if($ReturnDefaultIfEmpty    === TRUE)
                        return $sort_settings['_rules'];   
                        else
                        return false;
                }
                
            function get_sort_view_language($sort_view_ID)
                {
                    $language   =   '';
                    
                    $sort_view_selection    =   get_post_meta($sort_view_ID, '_view_selection', TRUE);
                    
                    switch($sort_view_selection)
                        {
                            case 'archive'  :
                                                $language    =   get_post_meta($sort_view_ID, '_view_language', TRUE);
                                                                    
                                                break;
                                                
                            case 'taxonomy'  :
                                                //only specific for WPML
                                                if (defined('ICL_LANGUAGE_CODE'))
                                                    {
                                                        $_taxonomy      =   get_post_meta($sort_view_ID, '_taxonomy', TRUE);
                                                        $_term_id       =   get_post_meta($sort_view_ID, '_term_id', TRUE);
                                                        
                                                        
                                                        //TTTTTTTTTTTCCCCCCCCCCCCCCCC
                                                        $language_term_is =     icl_object_id($_term_id, $_taxonomy, FALSE, $this->get_blog_language());
                                                        if($language_term_is == $_term_id)
                                                            $language   =   $this->get_blog_language();
                                                    }
                                                                    
                                                break;
                                                
                            case 'simple'     :
                                                $language    =   get_post_meta($sort_view_ID, '_view_language', TRUE); 
                                                break;
                        }
                    
                    if($language    ==   '')
                        $language   =   $this->get_blog_default_language();
                           
                    return $language;   
                }
            
            
            function save_log($event, $argv)
                {
                    //check for disabled logs
                    $settings   =   $this->get_settings();
                    if (!isset($settings['create_logs']) || $settings['create_logs'] != "1")
                        return FALSE;
                    
                    $apto_logs  =   get_option('apto_logs');
                    if(!is_array($apto_logs))
                        $apto_logs  =   array();
                        
                    $apto_logs  =   array_slice($apto_logs, 0, 19);
                    
                    switch($event)
                        {
                            case 'query_match':
                                                    $sort_view_data     =   get_post($argv['sort_view_id']);
                                                    if($sort_view_data->post_parent > 0)
                                                        $sortID             =   $sort_view_data->post_parent;
                                                        else
                                                        $sortID             =   $argv['sort_view_id'];
                                                        
                                                    $sort_data          =   get_post($sortID);
                                                    
                                                    array_unshift($apto_logs, date("Y-m-d H:i:s", time()) . ' Found Sort ID '. $sortID .' (<b>'. $sort_data->post_title .'</b>), Sort View ID '. $argv['sort_view_id'] .',  for query hash ' . $argv['query']->query_vars_hash);
                                                    break;
                                                    
                            case 'raw':             
                                                    array_unshift($apto_logs, $argv['raw']);
                                                    break;
                                                    
                            case 'log_start':
                                                    if(count($apto_logs) > 0)
                                                        {
                                                            reset($apto_logs);
                                                            if(current($apto_logs) != '-----')
                                                                array_unshift($apto_logs, '-----');
                                                        }
                                                    break;
                            
                        }
                        
                    update_option('apto_logs', $apto_logs);
                    
                }
            

            function next_previous_get_posts_list($post_type)
                {
                    global $wpdb;
                    
                    //check if WPML is active
                    if (defined('ICL_LANGUAGE_CODE') && defined('ICL_SITEPRESS_VERSION'))
                        {
                            //do not rely on ICL_LANGUAGE_CODE as main language can switch durring the code execution
                            //$language = ICL_LANGUAGE_CODE;
                            global $sitepress;
                            if(is_object($sitepress))
                                $language   =   $sitepress->get_current_language();
                            
                            $query  =   "SELECT ID FROM ". $wpdb->posts ."
                                            JOIN ". $wpdb->prefix ."icl_translations as wpml_it  ON wpml_it.element_id = ". $wpdb->posts .".ID    
                                            WHERE post_type =   '". $post_type ."' AND post_status = 'publish'
                                                    AND wpml_it.language_code = '". $language ."'
                                            GROUP BY  ". $wpdb->posts .".ID
                                            ORDER BY menu_order ASC, post_date DESC";
                            
                        }
                        else
                        {
                            $query  =   "SELECT ID FROM ". $wpdb->posts ."
                                            WHERE post_type =   '". $post_type ."' AND post_status = 'publish'
                                            ORDER BY menu_order ASC, post_date DESC";   
                            
                        }

                    $results         =   $wpdb->get_results($query);
                    
                    $order_list =   array();
                    foreach($results as $item)
                        {
                            $order_list[]   =   $item->ID;
                        }
                        
                    return $order_list;
                }
            
            
            /**
            * 
            * 
            * @param mixed $where
            * @param mixed $in_same_cat
            * @param mixed $excluded_categories
            */
            function get_next_previous_post_where($where, $in_same_cat, $excluded_categories)
                {
                    global $post;
                      
                    //check if there are any items saved for this sort view
                    $order_list  = $this->next_previous_get_posts_list($post->post_type);
                    
                    if(count($order_list)   <    1)
                        return $where;
                        
                    
                    return '';
                }

            /**
            * put your comment there...
            * 
            * @param mixed $sort
            */
            function get_next_post_sort($sort)
                {
                    $sort = $this->get_adjacent_post_sort(FALSE, $sort);
                    
                    return $sort;    
                }   

            
            /**
            * put your comment there...
            *     
            * @param mixed $sort
            */
            function get_previous_post_sort($sort)
                {
                    $sort = $this->get_adjacent_post_sort(TRUE, $sort);
                    
                    return $sort;
                }

            
            /**
            * put your comment there...
            *     
            * @param mixed $previous
            * @param mixed $sort
            */
            function get_adjacent_post_sort($previous = TRUE, $sort)
                {
                    global $post, $wpdb;
                    
                    $order_list  = $this->next_previous_get_posts_list($post->post_type);
                    
                    if(count($order_list)   <    1)
                        return $sort;
                        
                     //get the current element key
                    $current_position_key = array_search($post->ID, $order_list);
                    
                    if ($previous === TRUE)
                        $required_index = $current_position_key + 1;
                        else
                        $required_index = $current_position_key - 1;
                    
                    //check if there is another position after the current in the list
                    if (isset($order_list[ ($required_index) ]))
                        {
                            //found
                            $sort = 'ORDER BY FIELD(p.ID, "'. $order_list[ ($required_index) ] .'") DESC LIMIT 1 ';   
                        }
                        else
                        {
                            //not found 
                            $sort = 'ORDER BY p.post_date DESC LIMIT 0';  
                        }
           
                    return $sort;   
                
                }
            

                
                
           /**
           * put your comment there...
           *      
           * @param mixed $sorts_list
           * @param mixed $post_type
           * @return array
           */
           function filter_sorts_list_by_post_type($sorts_list, $post_type)
                {
                    foreach($sorts_list as $key =>  $sort_item)   
                        {
                            $sort_data  =   $this->get_sort_settings($sort_item->ID);
                            
                            if(!isset($sort_data['_rules']['post_type']) || count($sort_data['_rules']['post_type']) !== 1)
                                {
                                    unset($sorts_list[$key]);
                                    continue;
                                }
                                
                            $sort_post_type =   $sort_data['_rules']['post_type'][0];
                            if($sort_post_type  !=  $post_type)
                                unset($sorts_list[$key]);
                        }
                        
                    return array_values($sorts_list);
                } 
                
            /**
            * put your comment there...
            * 
            * @param mixed $format
            * @param mixed $link
            * @param mixed $args
            * @param mixed $previous
            */
            function adjacent_post_type_link($format, $link, $args,  $previous = TRUE) 
                {
                                        
                    if ( $previous && is_attachment() )
                        $post = & get_post($GLOBALS['post']->post_parent);
                        else
                        $post = $this->apto_get_adjacent_post($args, $previous);

                    if ( !$post )
                        return;

                    $title = $post->post_title;

                    if ( empty($post->post_title) )
                        $title = $previous ? __('Previous Post') : __('Next Post');

                    $title = apply_filters('the_title', $title, $post->ID);
                    $date = mysql2date(get_option('date_format'), $post->post_date);
                    $rel = $previous ? 'prev' : 'next';

                    $string = '<a href="'.get_permalink($post).'" rel="'.$rel.'">';
                    $link = str_replace('%title', $title, $link);
                    $link = str_replace('%date', $date, $link);
                    $link = $string . $link . '</a>';

                    $format = str_replace('%link', $link, $format);

                    $adjacent = $previous ? 'previous' : 'next';
                    echo apply_filters( "{$adjacent}_post_link", $format, $link );
                }
                
            
            function get_first_match_sort_id_for_post_type($post_type)
                {
                    global $post, $wpdb;
                    
                    $args   =   array(
                                        '_autosort' =>  array('yes'),
                                        '_view_type' =>  array('multiple')
                                        );
                    $available_sorts    =   $this->get_sorts_by_filters($args);
                    $available_sorts    =   $this->filter_sorts_list_by_post_type($available_sorts, $post->post_type);
                    
                    if(count($available_sorts)  <   1)
                        return '';
                        
                    //use the first
                    reset($available_sorts);
                    $use_sort   =   current($available_sorts);
                    $sortID     =   $use_sort->ID;
                    
                    return $sortID;   
                }
                
            function apto_get_adjacent_post( $args, $previous = TRUE ) 
                {
                    global $post, $wpdb;
                    
                    if ( empty( $post ) )
                        return null;

                    $defaults   = array (
                                            'sort_id'                   =>  '',
                                            'sort_view_id'              =>  '',
                                            'taxonomy'                  =>  '',
                                            'term_id'                   =>  '',
                                            'use_default_order'         =>  FALSE
                                        );
                    $function_args    = wp_parse_args( $args, $defaults );
                    
                    //try to get a sort id to match this
                    if($function_args['sort_id'] == '')
                        {
                            $function_args['sort_id']   =   $this->get_first_match_sort_id_for_post_type($post->post_type);
                        }
                        
                    if($function_args['sort_id']    ==  ''  ||  $function_args['sort_id'] < 1   ||  $function_args['use_default_order']  ===  TRUE)
                        return $this->get_default_adjacent_post($post, $previous);
                    
                    $sort_view_id   =   '';    
                    if($function_args['sort_id'] != ''  && ($function_args['taxonomy']  ==  ''  ||  $function_args['term_id']  ==  ''))
                        {
                            //try archive
                            //get sort archive view id
                            $attr = array(
                                            '_view_selection'   =>  'archive',
                                            '_view_language'    =>  $this->get_blog_language()
                                            );
                            $function_args['sort_view_id']   =   $this->get_sort_view_id_by_attributes($function_args['sort_id'], $attr);
                            
                        }
                        else if($function_args['sort_id'] != '')
                            {
                                //get taxonomy sort view id
                                $attr = array(
                                                '_view_selection'   =>  'taxonomy',
                                                '_taxonomy'         =>  $function_args['taxonomy'],
                                                '_term_id'          =>  $function_args['term_id'],
                                                );
                                $function_args['sort_view_id']   =   $this->get_sort_view_id_by_attributes($function_args['sort_id'], $attr);
                                
                            }
                    
                    if($function_args['sort_view_id']    ==  ''  ||  $function_args['sort_view_id'] < 1)
                        return $this->get_default_adjacent_post($post, $previous);
                    
                    
                    $sort_settings          =   $this->get_sort_settings($function_args['sort_id']);
                    $sort_view_settings     =   $this->get_sort_view_settings($function_args['sort_view_id']);
                    
                    //prepare the query to get the full list for this
                    $args = array(
                                        'depth'         =>  0,
                                        'post_status'   =>  'publish',
                                        'sort_id'       =>  $function_args['sort_id'],
                                        'sort_view_id'  =>  $function_args['sort_view_id'],
                                        'fields'        =>  'ids'
                                    );
   
                    if ($sort_settings['_view_type'] == 'multiple')
                        {
                            $args['post_type']         =  $sort_settings['_rules']['post_type'];
                            $args['posts_per_page']    = -1;
                            $args['orderby']           = 'menu_order';
                            $args['order']             = 'ASC';

                            //set author if need
                            if(isset($sort_settings['_rules']['author']) && is_array($sort_settings['_rules']['author']) && count($sort_settings['_rules']['author']) > 0)
                                $args['author'] =   implode(",",    $sort_settings['_rules']['author']);
                            
                            //set taxonomy if need (deppends on current view_selection
                            if($sort_view_settings['_view_selection'] == 'taxonomy')
                                {
                                    $args['tax_query']  =   array(
                                                                        array(
                                                                                'taxonomy'  => $sort_view_settings['_taxonomy'],
                                                                                'field'     => 'id',
                                                                                'terms'     => $sort_view_settings['_term_id']
                                                                                )
                                                                        );   
                                }
                                 
                        }
                        
                    if ($sort_settings['_view_type'] == 'simple')
                        {
                            $args['post_type']         =  $sort_settings['_rules']['post_type'];
                            $args['posts_per_page']    = -1;
                            $args['orderby']           = 'menu_order';
                            $args['order']             = 'ASC';      

                            $sort_rules = $this->get_sort_current_language_rules($sort_settings, FALSE);
                            
                            //set author if need
                            if(isset($sort_rules['author']) && is_array($sort_rules['author']) && count($sort_rules['author']) > 0)
                                $args['author'] =   implode(",",    $sort_rules['author']);
                            
                            //set taxonomy if need (deppends on current view_selection
                            $taxonomy_data              =   $sort_rules['taxonomy'];
                            $taxonomy_data['relation']  =   $sort_rules['taxonomy_relation'];                          
                            $args['tax_query']          =   $taxonomy_data;
                        } 
                    
                    
                    $custom_query = new WP_Query($args);
                    $order_list = $custom_query->posts;
                               
                    //get the current element key
                    $current_position_key = array_search($post->ID, $order_list);
                    
                    if ($previous === TRUE)
                        $required_index = $current_position_key + 1;
                        else
                        $required_index = $current_position_key - 1;
                    
                    //check if there is another position after the current in the list
                     if (isset($order_list[ ($required_index) ]))
                            {
                                //found
                                $post_data  =   get_post($order_list[ ($required_index) ]);   
                            }
                        else
                            {
                                //not found 
                                $post_data  =   null;  
                            }
                            
                     return $post_data;

                }
                
            
            function get_default_adjacent_post($post, $previous)
                {
                    $order_list  = $this->next_previous_get_posts_list($post->post_type);
                    
                    if(count($order_list)   <    1)
                        return null;
                        
                     //get the current element key
                    $current_position_key = array_search($post->ID, $order_list);
                    
                    if ($previous === TRUE)
                        $required_index = $current_position_key + 1;
                        else
                        $required_index = $current_position_key - 1;
                    
                    //check if there is another position after the current in the list
                    if (isset($order_list[ ($required_index) ]))
                        {
                            //found
                            $post_data  =   get_post($order_list[ ($required_index) ]);   
                        }
                        else
                        {
                            //not found 
                            $post_data  =   null;  
                        }
                        
                      
                    return $post_data;
                }
                
                
            
            /**
            * 
            * bbPress filter function 
            * 
            */
            function bbp_before_has_replies_parse_args($args)
                {
                    $args['order'] = 'DESC';  
            
                    return $args;
                }
                
                
            /**
            * 
            * 
            */
            function wp_ecommerce_is_draganddrop()
                {
                    $wpec_orderby = get_option( 'wpsc_sort_by' );
                    if ($wpec_orderby != "dragndrop")
                        return FALSE;
                        
                    return TRUE;
                }
                
                
            /**
            * WP E-Commerce Order Update 
            * 
            * @param mixed $orderBy
            * @param mixed $query
            */
            function wp_ecommerce_orderby($orderBy, $query)
                {
                    //only for non-admin
                    if (is_admin())
                        return $orderBy;
                    
                    if (!apto_is_plugin_active('wp-e-commerce/wp-shopping-cart.php') || ($query->is_archive('wpsc-product') === FALSE && $query->is_tax('wpsc_product_category') === FALSE))
                        return $orderBy;
                      
                    if($this->wp_ecommerce_is_draganddrop() === FALSE)
                        return $orderBy;

                    //always use ascending
                    $query->query['order']  =   'ASC';
                    $orderBy = $this->query_get_orderby('menu_order', $query);

                    return $orderBy;
                }

            
        }
        

?>