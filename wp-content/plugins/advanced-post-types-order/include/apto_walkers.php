<?php


    /**
    * 
    * Post Types Order Walker Class
    * 
    */
    class Post_Types_Order_Walker extends Walker 
        {

            var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

            /**
            * Starts the list before the elements are added.
            *
            * @see Walker::start_lvl()
            *
            * @since 3.0.0
            *
            * @param string $output Passed by reference. Used to append additional content.
            * @param int    $depth  Depth of menu item. Used for padding.
            * @param array  $args   An array of arguments. @see wp_nav_menu()
            */
            function start_lvl(&$output, $depth = 0, $args = array()) 
                {
                    extract($args, EXTR_SKIP);
                      
                    $indent = str_repeat("\t", $depth);
                    $output .= "\n$indent<ul class='children'>\n";
                }

            /**
            * Ends the list of after the elements are added.
            *
            * @see Walker::end_lvl()
            *
            * @since 3.0.0
            *
            * @param string $output Passed by reference. Used to append additional content.
            * @param int    $depth  Depth of menu item. Used for padding.
            * @param array  $args   An array of arguments. @see wp_nav_menu()
            */
            function end_lvl(&$output, $depth = 0, $args = array()) 
                {
                    extract($args, EXTR_SKIP);
                           
                    $indent = str_repeat("\t", $depth);
                    $output .= "$indent</ul>\n";
                }

            /**
            * Start the element output.
            *
            * @see Walker::start_el()
            *
            * @since 3.0.0
            *
            * @param string $output Passed by reference. Used to append additional content.
            * @param object $post_info   Menu item data object.
            * @param int    $depth  Depth of menu item. Used for padding.
            * @param array  $args   An array of arguments. @see wp_nav_menu()
            * @param int    $id     Current item ID.
            */ 
            function start_el(&$output, $post_info, $depth = 0, $args = array(), $id = 0) 
                {
                    if ( $depth )
                        $indent = str_repeat("\t", $depth);
                    else
                        $indent = '';

                    $post_data = get_post($post_info);
                    
                    if ($post_data->post_type == 'attachment')
                        $post_data->post_parent = null;
                        
                    extract($args, EXTR_SKIP);
                    
                    $is_woocommerce = FALSE;                
                    if ($post_data->post_type == "product" && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
                        $is_woocommerce = TRUE;
                    
                    global $APTO;
                    $sort_settings      =   $APTO->functions->get_sort_settings($args['sort_id']);
                    $sort_view_settings =   $APTO->functions->get_sort_view_settings($sort_view_id); 
                    
                    //check post thumbnail
                    if (function_exists('get_post_thumbnail_id'))
                            {
                                if($post_data->post_type == 'attachment')
                                    $image_id = $post_data->ID;
                                    else
                                    $image_id = get_post_thumbnail_id( $post_data->ID , 'post-thumbnail' );
                            }
                        else
                            {
                                $image_id = NULL;    
                            }
                    if ($image_id > 0)
                        {
                            $image = wp_get_attachment_image_src( $image_id , array(64,64)); 
                            if($image !== FALSE)
                                $image_html =  '<img src="'. $image[0] .'" width="64" alt="" />';
                                else
                                $image_html =  '<img src="'. CPTURL .'/images/nt.gif" width="64" alt="" />'; 
                        }
                        else
                            {
                                $image_html =  '<img src="'. CPTURL .'/images/nt.gif" width="64" alt="" />';    
                            }
                    
                    
                    //allow the thumbnail image to be changed through a filter
                    $image_html = apply_filters( 'apto_reorder_item_thumbnail', $image_html, $post_data->ID );
                    
                    $noNestingClass = '';
                    if(!post_type_exists($post_data->post_type))
                        $post_type_data = get_post_type_object($post_data->post_type);
                        
                    if (isset($post_type_data->hierarchical) && $post_type_data->hierarchical !== TRUE && $is_woocommerce === FALSE)
                        $noNestingClass = ' no-nesting';
                    
                    $is_sticky  =   FALSE;
                    if(isset($sort_view_settings['_sticky_data']) && is_array($sort_view_settings['_sticky_data']) && array_search($post_data->ID, $sort_view_settings['_sticky_data']) !== FALSE)
                        $is_sticky  =   TRUE;
                    
                    $output .= $indent . '<li class="post_type_li'.$noNestingClass.'" id="item_'.$post_data->ID.'">';
                    
                    if($is_sticky)
                        {
                            $output .=  '<div class="a_sticky"><input type="text" onblur="APTO.sticky_change(this)" name="p_sticky_val" value="'. array_search($post_data->ID, $sort_view_settings['_sticky_data']) .'" class="sticky-input"></div>';   
                        }
                    
                    $output .=  '<div class="item';
                    
                    if($is_sticky)
                        $output .= ' is-sticky';
                    
                    $output .=  '"><div class="post_type_thumbnail"';
                    
                    if ($sort_settings['_show_thumbnails']  == 'yes')
                        $output .= ' style="display: block"';
                        
                    $output .= '>'. $image_html .'</div>';
                    
                    $item_output    =   '';
                    $item_output .= '<div class="options">';
                    
                    $option_items                   = array();
                    $option_items['move_top']       = '<span class="option move_top" title="Move to Top" onClick="apto_move_element(jQuery(this).closest(\'.post_type_li\'), \'top\')">&nbsp;</span>';
                    $option_items['move_bottom']    = '<span class="option move_bottom" title="Move to Bottom" onClick="apto_move_element(jQuery(this).closest(\'.post_type_li\'), \'bottom\')">&nbsp;</span>';
                    $option_items['sticky']         = '<span class="option sticky" title="Make Sticky" onClick="APTO.sticky_toggle(jQuery(this).closest(\'.post_type_li\'), \'bottom\')">&nbsp;</span>';
                    $option_items['edit']           = '<span class="option edit" title="Edit" onClick="window.location = \''. get_bloginfo('wpurl') .'/wp-admin/post.php?post='.$post_data->ID.'&action=edit\'">&nbsp;</span>';
                    
                    $option_items                   = apply_filters('apto_reorder_item_additional_options', $option_items, $post_data);
                    
                    $item_output .= implode(" ", $option_items);
                    
                    $item_output .= '</div>';
                    
                    
                    $item_output .= '<span class="i_description">'.apply_filters( 'the_title', $post_data->post_title, $post_data->ID );
                    
                    $additiona_details  = ' ('.$post_data->ID.')';
                    $additiona_details  = apply_filters('apto_reorder_item_additional_details', $additiona_details, $post_data);
                    $item_output        .= $additiona_details;
                    
                    if ($post_data->post_status != 'publish')
                        $item_output .= ' <span class="item-status">'.$post_data->post_status.'</span>';
                        
                    $sticky_list = get_option('sticky_posts');
                    
                    if(is_array($sticky_list) && count($sticky_list) > 0)
                        {
                            if(in_array($post_data->ID, $sticky_list))
                                $item_output .= ' <span class="item-status">Sticky</span>';
                        }
                     
                    $item_output .= '</span>';
                    
                    
                    
                    $item_output .= '</div>';
                    
                    $output .= $item_output;
                }

            /**
            * Ends the element output, if needed.
            *
            * @see Walker::end_el()
            *
            * @since 3.0.0
            *
            * @param string $output Passed by reference. Used to append additional content.
            * @param object $item   Page data object. Not used.
            * @param int    $depth  Depth of page. Not Used.
            * @param array  $args   An array of arguments. @see wp_nav_menu()
            */
            function end_el(&$output, $post_data, $depth = 0, $args = array()) 
                {
                    $output .= "</li>\n";
                }
            
                
            function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args = array(), &$output ) 
                {
                    if ( !$element )
                        return;

                    $id_field = $this->db_fields['id'];

                    $element = get_post($element);
                    
                    //display this element
                    if ( is_array( $args[0] ) )
                        $args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
                    $cb_args = array_merge( array(&$output, $element, $depth), $args);
                    call_user_func_array(array($this, 'start_el'), $cb_args);

                    $id = $element->$id_field;

                    // descend only when the depth is right and there are childrens for this element
                    if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) 
                        {

                            foreach( $children_elements[ $id ] as $child )
                                {

                                    if ( !isset($newlevel) ) 
                                        {
                                            $newlevel = true;
                                            //start the child delimiter
                                            $cb_args = array_merge( array(&$output, $depth), $args);
                                            call_user_func_array(array($this, 'start_lvl'), $cb_args);
                                        }
                                    $this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
                                }
                            unset( $children_elements[ $id ] );
                        }

                    if ( isset($newlevel) && $newlevel )
                        {
                            //end the child delimiter
                            $cb_args = array_merge( array(&$output, $depth), $args);
                            call_user_func_array(array($this, 'end_lvl'), $cb_args);
                        }

                    //end this element
                    $cb_args = array_merge( array(&$output, $element, $depth), $args);
                    call_user_func_array(array($this, 'end_el'), $cb_args);
                }

        }


    /**
    * 
    * Walker_CategoryDropdown extension for sort area Taxonomy selections
    * 
    */
    class APTO_Walker_TaxonomiesTermsDropdownCategories extends Walker_CategoryDropdown
        {
            function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
                    $pad = str_repeat('&nbsp;', $depth * 2);
                    $cat_name = apply_filters('list_cats', $category->name, $category);
          
                    $link_argv  =   array(
                                            'sort_id'           =>  $args['sortID'],
                                            'taxonomy'          =>  $category->taxonomy,
                                            'term_id'           =>  $category->term_id
                                            );
                    
                    if($args['apto_interface']->is_shortcode_interface === FALSE)
                        {
                            $link_argv['page'] =   'apto_' . $args['apto_interface']->interface_helper->get_current_menu_location_slug();
                            $value  =    $args['apto_interface']->interface_helper->get_tab_link($link_argv) ;
                        }
                        else
                        {
                            global $post;
                            $link_argv['base_url']      =   get_permalink($post->ID);
                            $value  =    $args['apto_interface']->interface_helper->get_item_link($link_argv) ;                            
                        }

                    $output .= "\t<option class=\"level-$depth\" value=\"" .$value."\"";
                    if ( $category->term_id === (string) $args['selected'] )
                        { 
                            $output .= ' selected="selected"';
                        }
                    $output .= '>';
                    $output .= $pad . $cat_name;
                    
                    if ( $args['show_count'] )
                        $output .= '&nbsp;&nbsp;('. $category->count .')';

                    $output .= "</option>\n";
                }
        }
        
        
    /**
    * 
    * Walker extension for Taxonomy query rules
    * 
    */
    class APTO_Walker_TermsDropdownCategories extends Walker_CategoryDropdown
        {
            function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) { 
                    $pad = str_repeat('&nbsp;', $depth * 3);

                    $cat_name = apply_filters('list_cats', $category->name, $category);
                    $output .= "\t<option class=\"level-$depth\" value=\"".$category->term_id."\"";
                    
                    if(is_array($args['selected']))
                        {
                            if(in_array($category->term_id, $args['selected']))
                                $output .= ' selected="selected"';
                        }
                        else
                        {
                            if ( $category->term_id == $args['selected'] )
                                $output .= ' selected="selected"';
                        }
                    
                    $output .= '>';
                    $output .= $pad.$cat_name;
                    
                    if ( $args['show_count'] )
                        $output .= '&nbsp;&nbsp;('. $category->count .')';
                    
                    $output .= "</option>\n";
                }
        }

?>