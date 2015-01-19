<?php

    class APTO
        {
            var $functions  =   '';
            var $conditional_rules  = '';
            
            var $licence;
            
            function __construct()
                {
                    $this->licence              =   new APTO_licence();
                    $this->functions            =   new APTO_functions();
                    $this->conditional_rules    =   new APTO_conditionals();
                    
                    //set the start session log
                    $this->create_start_log();
                }
                
                
            function pre_get_posts($query)
                {
                    //check for the force_no_custom_order param, usefull when Autosort is ON
                    if (isset($query->query_vars['force_no_custom_order']) && $query->query_vars['force_no_custom_order'] === TRUE)
                        return $query;
                        
                    $settings = $this->functions->get_settings();
                    if (is_admin() && !defined('DOING_AJAX'))
                        {
                            //no need if it's admin interface
                            return $query;   
                        }
                    
                    
                    //force a suppress filters false, used mainly for get_posts function
                    if (isset($settings['ignore_supress_filters']) && $settings['ignore_supress_filters'] == "1")
                        $query->query_vars['suppress_filters'] = FALSE;
                     
                    //ignore the sticky if setting set for true
                    if (isset($settings['ignore_sticky_posts']) && $settings['ignore_sticky_posts'] == "1")
                        {
                            $query->query_vars['ignore_sticky_posts'] = TRUE;
                        }
                         
                    if($this->functions->exists_sorts_with_autosort_on() === FALSE)
                        return $query;
                    
                    //remove the supresed filters;
                    if (isset($query->query['suppress_filters']))
                        $query->query['suppress_filters'] = FALSE;    
                    
                    

                    return $query;
                }

            function posts_orderby($orderBy, $query) 
                {
                    //check for single object query on which no sort apply is necesarelly
                    if($query->is_single() || $query->is_page() || $query->is_singular() || $query->is_preview() || $query->is_attachment())
                        return $orderBy; 
                    
                    //check for orderby GET paramether in which case return default data
                    if (isset($_GET['orderby']) && $_GET['orderby'] !=  'menu_order')
                        return $orderBy;
                    
                    //check for the force_no_custom_order param
                    if (isset($query->query_vars['force_no_custom_order']) && $query->query_vars['force_no_custom_order'] === TRUE)
                        return $orderBy;
                          
                    if (apto_is_plugin_active('bbpress/bbpress.php') && isset($query->query_vars['post_type']) && ((is_array($query->query_vars['post_type']) && in_array("reply", $query->query_vars['post_type'])) || ($query->query_vars['post_type'] == "reply")))
                        return $orderBy;
                    
                    //WP E-Commerce check if drag-and-drop for archive 
                    if (apto_is_plugin_active('wp-e-commerce/wp-shopping-cart.php') && $query->is_archive('wpsc-product') && !$query->is_tax('wpsc_product_category'))
                        {
                            if($this->functions->wp_ecommerce_is_draganddrop() === FALSE)
                                return $orderBy;
                        }  
                        
                    
                    global $wpdb;
                                                    
                    //check if menu_order provided through the query params
                    if (isset($query->query['orderby']) && $query->query['orderby'] == 'menu_order')
                        {
                            $orderBy    =   $this->functions->query_get_orderby($orderBy, $query);
                                
                            return($orderBy);   
                        }
                    
                    $default_orderBy = $orderBy;
                    
                    if (is_admin() && !defined('DOING_AJAX'))
                            {

                                //force to use the custom order
                                //$orderBy = $wpdb->posts.".menu_order, " . $wpdb->posts.".post_date DESC"; 
                                $args   =   array(
                                                    '_adminsort' =>  array('yes')  
                                                    );
                                $orderBy    =   $this->functions->query_get_orderby($orderBy, $query, $args);
                                
                                if($orderBy == '')
                                    $orderBy = $default_orderBy;

                            }
                        else
                            {
                             
                                //check against any Autosort On sort list                    
                                $args   =   array(
                                                            '_autosort' =>  array('yes')  
                                                            );
                                $orderBy    =   $this->functions->query_get_orderby($orderBy, $query , $args);

                                return($orderBy);

                            }

                    return($orderBy);
                }  
                
            function create_start_log()
                {
                    $this->functions->save_log('log_start', array());
                }
            
            
        }


?>