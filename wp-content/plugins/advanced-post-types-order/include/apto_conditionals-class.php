<?php


    class APTO_conditionals
        {
            var $rules  = array();
            
            function __construct()
                {   
                    $this->add_rule(array(
                                            'id'                    =>  'is_home',
                                            'title'                 =>  'Home',
                                            'admin_html'            =>  array($this, 'conditional_rule_is_home_admin_html'),
                                            'query_check_callback'  =>  array($this, 'conditional_rule_is_home_query_check'),
                                            'comparison'            =>  array('IS', 'IS NOT')
                                            ));
                                            
                    $this->add_rule(array(
                                            'id'                    =>  'is_page',
                                            'title'                 =>  'Page',
                                            'admin_html'            =>  array($this, 'conditional_rule_is_page_admin_html'),
                                            'query_check_callback'  =>  array($this, 'conditional_rule_is_page_query_check'),
                                            'comparison'            =>  array('IS', 'IS NOT')
                                            ));
                                            
                    $this->add_rule(array(
                                            'id'                    =>  'is_feed',
                                            'title'                 =>  'Feed',
                                            'admin_html'            =>  array($this, 'conditional_rule_is_feed_admin_html'),
                                            'query_check_callback'  =>  array($this, 'conditional_rule_is_feed_query_check'),
                                            'comparison'            =>  array('IS', 'IS NOT')
                                            ));
                                            
                    $this->add_rule(array(
                                            'id'                    =>  'is_url',
                                            'title'                 =>  'URL',
                                            'admin_html'            =>  array($this, 'conditional_rule_is_url_admin_html'),
                                            'query_check_callback'  =>  array($this, 'conditional_rule_is_url_query_check'),
                                            'comparison'            =>  array('IS', 'IS NOT', 'CONTAIN')
                                            ));
                                            
                    do_action('apto_conditionals_add', $this);
                    
                }
                
            function add_rule($options)
                {
                    //check if id already exists
                    if(isset($conditional_rules[$options['id']]))
                        return FALSE;
                        
                    $this->rules[$options['id']] =  array(
                                                                    'title'                 =>  $options['title'],
                                                                    'admin_html'            =>  $options['admin_html'],
                                                                    'query_check_callback'  =>  $options['query_check_callback'],
                                                                    'comparison'            =>  $options['comparison']
                                                                    );
                                                                    
                    return TRUE;
                }
            
            /**
            * Return rule comparison available values
            *     
            * @param mixed $rule_id
            */
            function get_rule_comparison($rule_id)
                {
                    
                    return ($this->rules[$rule_id]['comparison']);
                }
                
                
            function conditional_rule_is_home_admin_html($options)
                {
                    //no output is required
                    
                }

            function conditional_rule_is_home_query_check($comparison, $value, $query)
                {
                    //check against the main query
                    global $wp_the_query;
                    
                    $condition_status = FALSE;
                    
                    if(!isset($wp_the_query->query)  || is_null($wp_the_query->query))
                        $ref_query  =   $query;
                        else
                        $ref_query  =   $wp_the_query;
                        
                    if($ref_query->is_home)
                        $condition_status   =   TRUE;
                        
                    if($comparison == 'IS NOT')
                        $condition_status   =   ($condition_status) ?  FALSE : TRUE;
                           
                    return $condition_status;    
                }
                
                
            function conditional_rule_is_page_admin_html($options)
                {
                    $args = array(
                                        'name'          =>  'conditional_rules['.$options['group_id'].']['.$options['row_id'].'][conditional_value]',
                                        'depth'         => 0,
                                        'title_li'      => '',
                                        'echo'          => 0,
                                        'sort_column'   => 'menu_order, post_title',
                                        'post_type'     => 'page',
                                        'post_status'   => 'publish' ,
                                        'selected'      => $options['selected_value'] 
                                    );   
                    $html = wp_dropdown_pages($args);
                    
                    return $html;   
                }

            function conditional_rule_is_page_query_check($comparison, $value, $query)
                {
                    //check against the main query
                    global $wp_the_query;
                    
                    $condition_status = false;
                    
                    if(!isset($wp_the_query->query)  || is_null($wp_the_query->query))
                        $ref_query  =   $query;
                        else
                        $ref_query  =   $wp_the_query;
                        
                    if($ref_query->is_page($value))
                        $condition_status   =   TRUE;
                        
                    if($comparison == 'IS NOT')
                        $condition_status   =   ($condition_status) ?  FALSE : TRUE;
                           
                    return $condition_status;   
                }
                
                
            function conditional_rule_is_feed_admin_html($options)
                {
                    //no output is required   
                }

            function conditional_rule_is_feed_query_check($comparison, $value, $query)
                {
                    //check against the main query
                    global $wp_the_query;
                    
                    $condition_status = false;
                    
                    if(!isset($wp_the_query->query)  || is_null($wp_the_query->query))
                        $ref_query  =   $query;
                        else
                        $ref_query  =   $wp_the_query;
                        
                    if($ref_query->is_feed())
                        $condition_status   =   TRUE;
                        
                    if($comparison == 'IS NOT')
                        $condition_status   =   ($condition_status) ?  FALSE : TRUE;
                           
                    return $condition_status;   
                }
                
                
            function conditional_rule_is_url_admin_html($options)
                {
                    $html = '<input type="text" name="conditional_rules['.$options['group_id'].']['.$options['row_id'].'][conditional_value]" class="text" value="'. htmlspecialchars($options['selected_value']) .'">';
                    
                    return $html;   
                }
                
            function conditional_rule_is_url_query_check($comparison, $value, $query)
                {
                    $condition_status = false;
                    
                    $protocol   = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
                    $host       = $_SERVER['HTTP_HOST'];
                    $script     = $_SERVER['REQUEST_URI'];
                    $params     = $_SERVER['QUERY_STRING'];
                    $currentUrl = $protocol . '://' . $host . $script . '?' . $params;
                    
                    switch ($comparison)
                        {
                            case 'IS':
                                            if($currentUrl  ==  $value)
                                                $condition_status   =   TRUE;
                                            break;

                            case 'IS NOT':
                                            $condition_status   =   ($condition_status) ?  FALSE : TRUE;
                                            break;
                            
                            case 'CONTAIN':
                                            if(strpos($currentUrl, $value) !== FALSE)
                                                $condition_status   =   TRUE;
                                            break;
                        }
                           
                    return $condition_status;   
                }
            
        }
                
?>