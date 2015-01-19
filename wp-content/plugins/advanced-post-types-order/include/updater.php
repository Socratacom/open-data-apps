<?php

    class APTO_CodeAutoUpdate
        {
            # URL to check for updates, this is where the index.php script goes
            public $api_url;

            # Type of package to be updated
            public $package_type;

            public $code_slug;
            public $plugin_file;
            public $current_version;

            public function APTO_CodeAutoUpdate($api_url, $slug) 
                {
                    $this->api_url          = $api_url;
                    $this->package_type     = 'stable';
                    $this->code_slug        = $slug;
                    $this->plugin_file      = $slug .'/'. $slug . '.php';
                    $this->current_version  = APTO_VERSION;
                }

  
            public function check_for_plugin_update($checked_data) 
                {
                    if (empty($checked_data->checked) || !isset($checked_data->checked[$this->plugin_file]))
                        return $checked_data;
                    
                    $request_args = array(
                                                'slug'          => $this->code_slug,
                                                'version'       => $checked_data->checked[$this->plugin_file],
                                                'package_type'  => $this->package_type,
                                            );

                    $request_string = $this->prepare_request('basic_check', $request_args);
                    if($request_string === FALSE)
                        return $checked_data; 
                        
                    // Start checking for an update
                    $request_uri    = $this->api_url . '?' . http_build_query( $request_string , '', '&'); 
                    $data           = wp_remote_get( $request_uri );
                
                    if(is_wp_error( $data ) || $data['response']['code'] != 200)
                        return $checked_data;   
                        
                    $response = unserialize($data['body']);

                    if (is_object($response) && !empty($response)) // Feed the update data into WP updater
                        $checked_data->response[$this->plugin_file] = $response;
                        
                    return $checked_data;
                }
   
                
            public function plugins_api_call($def, $action, $args) 
                {
                    if (!is_object($args)  || !isset($args->slug) || $args->slug != $this->code_slug)
                        return false;

                    $args->version = $this->current_version;
                    $args->package_type = $this->package_type;
                    
                    $request_string = $this->prepare_request($action, $args);
                    if($request_string === FALSE)
                        return new WP_Error('plugins_api_failed', __('An error occour when try to identify the pluguin.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'));;
                        
                    $request_uri    = $this->api_url . '?' . http_build_query( $request_string , '', '&'); 
                    $data           = wp_remote_get( $request_uri );
                
                    if(is_wp_error( $data ) || $data['response']['code'] != 200)
                        return new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $data->get_error_message());   
                        
                    $res = unserialize($data['body']);

                     if ($res === false)
                        $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);

                    return $res;
                }

            public function prepare_request($action, $args) 
                {

                    global $wp_version;

                    $wp_info = array(
                                        'site-url'  => site_url(),
                                        'version'   => $wp_version,
                                    );

                    $slug       = (is_object($args) && isset($args->slug)) ? $args->slug : $args['slug'];
                    $version    = (is_object($args) && isset($args->version)) ? $args->version : $args['version'];
                                    
                    $license_data = get_site_option('apto_license');
                        
                    return array(
                                    'sl_action'         =>  $action, 
                                    'slug'              =>  $slug,
                                    'version'           =>  APTO_VERSION,
                                    'request'           =>  serialize($args),   
                                    'product_id'        =>  APTO_PRODUCT_ID,
                                    'licence_key'       =>  $license_data['kye'],
                                    'secret_key'        =>  APTO_SECRET_KEY,
                                    
                                    'wp-version'        =>  $wp_version,
                                    'sl_instance'       =>  APTO_INSTANCE
                    );
                }
        }

    function APTO_run_updater()
        {
            global $APTO;
                
            //no need to run if there is no valid licence key
            if(!$APTO->licence->licence_key_verify())
                return;
            
            $license_data = get_site_option('apto_license');
            
            $wp_plugin_auto_update = new APTO_CodeAutoUpdate(APTO_APP_API_URL, APTO_SLUG);
                            
            // Take over the update check
            add_filter('pre_set_site_transient_update_plugins', array($wp_plugin_auto_update, 'check_for_plugin_update'));

            // Take over the Plugin info screen
            add_filter('plugins_api', array($wp_plugin_auto_update, 'plugins_api_call'), 10, 3);
        }
    add_action( 'after_setup_theme', 'APTO_run_updater' );   


?>