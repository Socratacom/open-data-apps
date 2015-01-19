<?php   
        
    define('APTO_PRODUCT_ID',           'APTO');
    define('APTO_SECRET_KEY',           '*#ioK@ud8*&#2');
    $protocols = array ("https://" , "http://");
    define('APTO_INSTANCE',             str_replace($protocols, "", network_site_url()));
           
    class APTO_licence
        {
         
            function __construct()
                {
                    $this->licence_deactivation_check();   
                }
                
            function __destruct()
                {
                    
                }
                
            function licence_key_verify()
                {
                    $license_data = get_site_option('apto_license');
                             
                    if(!isset($license_data['kye']) || $license_data['kye'] == '')
                        return FALSE;
                        
                    return TRUE;
                }
                
                
            function licence_deactivation_check()
                {
                    if(!$this->licence_key_verify())
                        return;
                    
                    $license_data = get_site_option('apto_license');
                    
                    if(isset($license_data['last_check']))
                        {
                            if(time() < ($license_data['last_check'] + 86400))
                                {
                                    return;
                                }
                        }
                    
                    $license_key = $license_data['kye'];
                    $args = array(
                                                'sl_action'         => 'status-check',
                                                'licence_key'       => $license_key,
                                                'product_id'        => APTO_PRODUCT_ID,
                                                'secret_key'        => APTO_SECRET_KEY,
                                                'sl_instance'          => APTO_INSTANCE
                                            );
                    $request_uri    = APTO_APP_API_URL . '?' . http_build_query( $args , '', '&');
                    $data           = wp_remote_get( $request_uri );
                    
                    if(is_wp_error( $data ) || $data['response']['code'] != 200)
                        return;   
                    
                    $data_body = json_decode($data['body']);
                    if(isset($data_body->status))
                        {
                            if($data_body->status == 'success')
                                {
                                    if($data_body->status_code == 's203' || $data_body->status_code == 's204')
                                        {
                                            $license_data['kye']          = '';
                                        }
                                }
                                
                            if($data_body->status == 'error')
                                {
                                    $license_data['kye']          = '';
                                } 
                        }
                    
                    $license_data['last_check']   = time();    
                    update_site_option('apto_license', $license_data);
                    
                }
            
            
        }
            

        
    
?>