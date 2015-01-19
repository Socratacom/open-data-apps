<?php

    class APTO_options_interface
        {
         
            var $licence;
            var $admin_functions;
            
            var $APTO;
         
            function __construct()
                {
                    
                    $this->licence          =   new APTO_licence();
                    $this->admin_functions  =   new APTO_admin_functions();
                    
                    global $APTO;
                    $this->APTO             =   $APTO;
                    
                    if (isset($_GET['page']) && $_GET['page'] == 'apto-options')
                        {
                            if(is_multisite())
                                    {
                                        //add_action( 'network_admin_menu', 'cpt_optionsUpdate', 1 );
                                        add_action( 'init', array($this, 'options_update'), 1 );
                                    }
                                else
                                    {
                                        add_action( 'init', array($this, 'options_update'), 1 );
                                    }
                                    
                        }
                        
                    add_action( 'network_admin_menu', array($this, 'network_admin_menu') );
                    
                    if(!$this->licence->licence_key_verify())
                        add_action('admin_notices', array($this, 'admin_no_key_notices'));
                    
                }
                
            function __destruct()
                {
                
                }
            
            function network_admin_menu()
                {
                    if(!$this->licence->licence_key_verify())
                        $hookID   = add_submenu_page('settings.php', 'Post Types Order', '<img class="menu_pto" src="'. CPTURL .'/images/menu-icon.png" alt="" />Post Types Order', 'manage_options', 'apto-options', array($this, 'licence_form'));
                        else
                        $hookID   = add_submenu_page('settings.php', 'Post Types Order', '<img class="menu_pto" src="'. CPTURL .'/images/menu-icon.png" alt="" />Post Types Order', 'manage_options', 'apto-options', array($this, 'licence_deactivate_form'));
                        
                    add_action('load-' . $hookID , array($this, 'load_dependencies'));
                    add_action('load-' . $hookID , array($this, 'admin_notices'));
                    
                    add_action('admin_print_styles-' . $hookID , array($this, 'admin_print_styles'));
                    add_action('admin_print_scripts-' . $hookID , array($this, 'admin_print_scripts'));
                }
                
            /**
            * Create the plugin options page interface
            * 
            */
            function create_plugin_options()
                {
                    $hookID   = add_options_page('Post Types Order', '<img class="menu_pto" src="'. CPTURL .'/images/menu-icon.png" alt="" />Post Types Order', 'manage_options', 'apto-options', array($this, 'options_interface'));
                    
                    add_action('load-' . $hookID , array($this, 'load_dependencies'));
                    add_action('all_admin_notices' , array($this, 'admin_notices'));
                                        
                    add_action('admin_print_styles-' . $hookID , array($this, 'admin_print_styles'));
                    add_action('admin_print_scripts-' . $hookID , array($this, 'admin_print_scripts'));
                }
                
                
            function options_interface()
                {
                    $options = $this->APTO->functions->get_settings();
                    
                    if(!$this->licence->licence_key_verify() && !is_multisite())
                        {
                            $this->licence_form();
                            return;
                        }
                        
                    if(!$this->licence->licence_key_verify() && is_multisite())
                        {
                            $this->licence_multisite_require_nottice();
                            return;
                        }
                                      
                                ?>
                                  <div class="wrap"> 
                                    <div id="icon-settings" class="icon32"></div>
                                        <h2><?php _e( "General Settings", 'apto' ) ?></h2>
                                       
                                       <?php  
                                            
                                            if(!is_multisite())
                                                $this->licence_deactivate_form();  
                                       ?>
                                       
                                        <form id="form_data" name="form" method="post">   
                                            <br />
                                            <h2 class="subtitle"><?php _e( "Show / Hide re-order interface", 'apto' ) ?></h2>                              
                                            <table class="form-table">
                                                <tbody>
                                                        <?php
                                                        
                                                            foreach ($this->admin_functions->get_available_menu_locations() as $location    =>  $location_data)
                                                                {
                                                                    ?>
                                                                        <tr valign="top">
                                                                            <th scope="row"></th>
                                                                            <td>
                                                                            <label>
                                                                                <select name="show_reorder_interfaces[<?php echo $location ?>]">
                                                                                    <option value="show" <?php if(isset($options['show_reorder_interfaces'][$location]) && $options['show_reorder_interfaces'][$location] == 'show') {echo ' selected="selected"';} ?>>Show</option>
                                                                                    <option value="hide" <?php if(isset($options['show_reorder_interfaces'][$location]) && $options['show_reorder_interfaces'][$location] == 'hide') {echo ' selected="selected"';} ?>>Hide</option>
                                                                                </select> &nbsp;&nbsp;<?php echo $location_data['name'] ?>
                                                                            </label>          
                                                                            </td>
                                                                        </tr>
                                                                    <?php
                                                                }
                                                        
                                                        ?>

                                                </tbody>
                                            </table>
                                            
                                            <br />
                                            <h2 class="subtitle"><?php _e( "General", 'apto' ) ?></h2>                              
                                            <table class="form-table">
                                                <tbody>
                                        
                                                        
                                                    <tr valign="top">
                                                        <th scope="row" style="text-align: right;"><label><?php _e( "Ignore Suppress Filters", 'apto' ) ?></label></th>
                                                        <td>
                                                            <label>
                                                            <input type="checkbox" <?php if (isset($options['ignore_supress_filters']) && $options['ignore_supress_filters'] == "1") {echo ' checked="checked"';} ?> value="1" name="ignore_supress_filters">
                                                            <?php _e("Set FALSE the <b>suppress_filters</b> arguments for get_posts() default WordPress function. Use this feature if sorts Autosort will not work with you, otherwise you should leave un-checked.", 'apto') ?>.</label>
                                                        </td>
                                                    </tr>

                                                    <tr valign="top">
                                                        <th scope="row" style="text-align: right;"><label><?php _e( "Ignore Sticky Posts", 'apto' ) ?></label></th>
                                                        <td>
                                                            <label>
                                                            <input type="checkbox" <?php if (isset($options['ignore_sticky_posts']) && $options['ignore_sticky_posts'] == "1") {echo ' checked="checked"';} ?> value="1" name="ignore_sticky_posts">
                                                            <?php _e("Ignore any Sticky posts, those will not appear on top of the list but per customised order.", 'apto') ?>.</label>
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr valign="top">
                                                        <th scope="row" style="text-align: right;"><label><?php _e( "Create Logs", 'apto' ) ?></label></th>
                                                        <td>
                                                            <label>
                                                            <input type="checkbox" <?php if (isset($options['create_logs']) && $options['create_logs'] == "1") {echo ' checked="checked"';} ?> value="1" name="create_logs">
                                                            <?php _e("Create logs which may be usefull when debug or help to identify applied sort list.", 'apto') ?>.</label>
                                                        </td>
                                                    </tr>
                                                    
                                                    <?php if (is_plugin_active('bbpress/bbpress.php')) { ?>
                                                    <tr valign="top">
                                                        <th scope="row" style="text-align: right;"><label><?php _e( "bbPress Replies", 'apto' ) ?></label></th>
                                                        <td>
                                                            <label>
                                                            <input type="checkbox" <?php if (isset($options['bbpress_replies_reverse_order']) && $options['bbpress_replies_reverse_order'] == "1") {echo ' checked="checked"';} ?> value="1" name="bbpress_replies_reverse_order">
                                                            <?php _e("Reverse the order of bbPress replies, show newest posts first", 'apto') ?>.</label>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>

                                                </tbody>
                                            </table>
                                            
                                            <?php if (isset($options['create_logs']) && $options['create_logs'] == "1") { ?>
                                            <br />
                                            <h2 class="subtitle"><?php _e( "Logs", 'apto' ) ?></h2>                              
                                            <table class="form-table">
                                                <tbody>
                                                    <tr valign="top">
                                                        <td>
                                                            <div id="apto_logs">
                                                            <?php
                                                            
                                                                $apto_logs = get_option('apto_logs');
                                                                if(is_array($apto_logs) && count($apto_logs) > 0)
                                                                    {
                                                                        foreach($apto_logs as $apto_log)
                                                                            {
                                                                                echo '<p><i>'. $apto_log .'</i></p>';   
                                                                            }
                                                                    }
                                                                    else
                                                                        echo '<p><i>No Logs.</i></p>';
                                                            ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?>                   
                                            <p class="submit">
                                                <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Settings', 'apto') ?>">
                                           </p>
                                        
                                            <input type="hidden" name="apto_form_submit" value="true" />
                                            
                                        </form>
                                                                    
                                <?php  
                        echo '</div>';   
                    
                    
                }
            
            function options_update()
                {
                    $options = $this->APTO->functions->get_settings();
                    
                    if (isset($_POST['apto_licence_form_submit']))
                        {
                            $this->licence_form_submit();
                            return;
                        }
                    
                    if (isset($_POST['apto_form_submit']))
                        {
                            global $apto_form_submit_messages;
                            
                            $options['show_reorder_interfaces']     =   $_POST['show_reorder_interfaces'];
                        
                            $options['ignore_supress_filters']  = isset($_POST['ignore_supress_filters'])    ? $_POST['ignore_supress_filters']   : ''; 
                            $options['ignore_sticky_posts']     = isset($_POST['ignore_sticky_posts'])    ? $_POST['ignore_sticky_posts']   : ''; 
                            $options['create_logs']             = isset($_POST['create_logs'])    ? $_POST['create_logs']   : ''; 
                            $options['bbpress_replies_reverse_order']  = isset($_POST['bbpress_replies_reverse_order'])    ? $_POST['bbpress_replies_reverse_order']   : '';
                                                        
                            $this->APTO->functions->update_settings($options);   
                            
                            $apto_form_submit_messages[] = __('Settings Saved', 'apto');
                        }
                }

            function load_dependencies()
                {

                }
                
            function admin_notices()
                {
                    global $apto_form_submit_messages;
            
                    if($apto_form_submit_messages == '')
                        return;
                    
                    $messages = $apto_form_submit_messages;
 
                          
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
                
            function admin_print_scripts()
                {

                }
            
            
            function admin_no_key_notices()
                {
                    if ( !current_user_can('manage_options'))
                        return;
                    
                    $screen = get_current_screen();
                        
                    if(is_multisite())
                        {
                            ?><div class="updated fade"><p><?php _e( "Advanced Post Types Order plugin is inactive, please enter your", 'apto' ) ?> <a href="<?php echo network_admin_url() ?>settings.php?page=apto-options"><?php _e( "Licence Key", 'apto' ) ?></a></p></div><?php
                        }
                        else
                        {
                            if(isset($screen->id) && $screen->id == 'settings_page_apto-options')
                                return;
                            
                            ?><div class="updated fade"><p><?php _e( "Advanced Post Types Order plugin is inactive, please enter your", 'apto' ) ?> <a href="options-general.php?page=apto-options"><?php _e( "Licence Key", 'apto' ) ?></a></p></div><?php
                        }
                }

            function licence_form_submit()
                {
                    global $apto_form_submit_messages; 
                    
                    //check for de-activation
                    if (isset($_POST['apto_licence_form_submit']) && isset($_POST['apto_licence_deactivate']) && wp_verify_nonce($_POST['apto_license_nonce'],'apto_license'))
                        {
                            global $apto_form_submit_messages;
                            
                            $license_data = get_site_option('apto_license');                        
                            $license_key = $license_data['kye'];

                            //build the request query
                            $args = array(
                                                'sl_action'         => 'deactivate',
                                                'licence_key'       => $license_key,
                                                'product_id'        => APTO_PRODUCT_ID,
                                                'secret_key'        => APTO_SECRET_KEY,
                                                'sl_instance'          => APTO_INSTANCE
                                            );
                            $request_uri    = APTO_APP_API_URL . '?' . http_build_query( $args , '', '&');
                            $data           = wp_remote_get( $request_uri );
                            
                            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                                {
                                    $apto_form_submit_messages[] .= __('There was a problem connecting to ', 'apto') . APTO_APP_API_URL;
                                    return;  
                                }
                                
                            $data_body = json_decode($data['body']);
                            if(isset($data_body->status))
                                {
                                    if($data_body->status == 'success' && $data_body->status_code == 's201')
                                        {
                                            //the license is active and the software is active
                                            $apto_form_submit_messages[] = $data_body->message;
                                            
                                            $license_data = get_site_option('apto_license');
                                            
                                            //save the license
                                            $license_data['kye']          = '';
                                            $license_data['last_check']   = time();
                                            
                                            update_site_option('apto_license', $license_data);
                                        }
                                        else
                                        {
                                            $apto_form_submit_messages[] = __('There was a problem deactivating the licence: ', 'apto') . $data_body->message;
                                            
                                            //if message code is e104  force de-activation
                                            if ($data_body->status_code == 'e102' || $data_body->status_code == 'e104')
                                                {
                                                     $license_data = get_site_option('apto_license');
                                            
                                                    //save the license
                                                    $license_data['kye']          = '';
                                                    $license_data['last_check']   = time();
                                                    
                                                    update_site_option('apto_license', $license_data);
                                                }
                                            
                                            return;
                                        }   
                                }
                                else
                                {
                                    $apto_form_submit_messages[] = __('There was a problem with the data block received from ' . APTO_APP_API_URL, 'apto');
                                    return;
                                }
                                
                            return;
                        }   
                    
                    
                    
                    if (isset($_POST['apto_licence_form_submit']) && wp_verify_nonce($_POST['apto_license_nonce'],'apto_license'))
                        {
                            
                            $license_key = isset($_POST['license_key'])? trim($_POST['license_key']) : '';

                            if($license_key == '')
                                {
                                    $apto_form_submit_messages[] = __('Licence Key can\'t be empty', 'apto');
                                    return;
                                }
                                
                            //build the request query
                            $args = array(
                                                'sl_action'         => 'activate',
                                                'licence_key'       => $license_key,
                                                'product_id'        => APTO_PRODUCT_ID,
                                                'secret_key'        => APTO_SECRET_KEY,
                                                'sl_instance'          => APTO_INSTANCE
                                            );
                            $request_uri    = APTO_APP_API_URL . '?' . http_build_query( $args , '', '&');
                            $data           = wp_remote_get( $request_uri );
                            
                            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                                {
                                    $apto_form_submit_messages[] .= __('There was a problem connecting to ', 'apto') . APTO_APP_API_URL;
                                    return;  
                                }
                                
                            $data_body = json_decode($data['body']);
                            if(isset($data_body->status))
                                {
                                    if($data_body->status == 'success' && $data_body->status_code == 's200')
                                        {
                                            //the license is active and the software is active
                                            $apto_form_submit_messages[] = $data_body->message;
                                            
                                            $license_data = get_site_option('apto_license');
                                            
                                            //save the license
                                            $license_data['kye']          = $license_key;
                                            $license_data['last_check']   = time();
                                            
                                            update_site_option('apto_license', $license_data);

                                        }
                                        else
                                        {
                                            $apto_form_submit_messages[] = __('There was a problem activating the licence: ', 'apto') . $data_body->message;
                                            return;
                                        }   
                                }
                                else
                                {
                                    $apto_form_submit_messages[] = __('There was a problem with the data block received from ' . APTO_APP_API_URL, 'apto');
                                    return;
                                }
                        }   
                    
                }
                
            function licence_form()
                {
                    ?>
                        <div class="wrap"> 
                            <div id="icon-settings" class="icon32"></div>
                            <h2><?php _e( "General Settings", 'apto' ) ?></h2>
                            
                            
                            <form id="form_data" name="form" method="post">
                                <h2 class="subtitle"><?php _e( "Software License", 'apto' ) ?></h2>
                                <div class="postbox">
                                    
                                        <?php wp_nonce_field('apto_license','apto_license_nonce'); ?>
                                        <input type="hidden" name="apto_licence_form_submit" value="true" />
                                           
                                        

                                         <div class="section section-text ">
                                            <h4 class="heading">License Key</h4>
                                            <div class="option">
                                                <div class="controls">
                                                    <input type="text" value="" name="license_key" class="text-input">
                                                </div>
                                                <div class="explain"><?php _e( "Enter the License Key you got when bought this product. If you lost the key, you can always retrieve it from", 'apto' ) ?> <a href="http://www.nsp-code.com/premium-plugins/my-account/" target="_blank">My Account</a><br />
                                                <?php _e( "More keys can be generate from", 'apto' ) ?> <a href="http://www.nsp-code.com/premium-plugins/my-account/" target="_blank">My Account</a> 
                                                </div>
                                            </div> 
                                        </div>

                                    
                                </div>
                                
                                <p class="submit">
                                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save', 'apto') ?>">
                                </p>
                            </form> 
                        </div> 
                    <?php  
     
                }
            
            function licence_deactivate_form()
                {
                    $license_data = get_site_option('apto_license');
                    
                    if(is_multisite())
                        {
                            ?>
                                <div class="wrap"> 
                                    <div id="icon-settings" class="icon32"></div>
                                    <h2><?php _e( "General Settings", 'apto' ) ?></h2>
                            <?php
                        }
                    
                    ?>
                        <div id="form_data">
                        <h2 class="subtitle"><?php _e( "Software License", 'apto' ) ?></h2>
                        <div class="postbox">
                            <form id="form_data" name="form" method="post">    
                                <?php wp_nonce_field('apto_license','apto_license_nonce'); ?>
                                <input type="hidden" name="apto_licence_form_submit" value="true" />
                                <input type="hidden" name="apto_licence_deactivate" value="true" />

                                 <div class="section section-text ">
                                    <h4 class="heading"><?php _e( "License Key", 'apto' ) ?></h4>
                                    <div class="option">
                                        <div class="controls">
                                            <p><b><?php echo substr($license_data['kye'], 0, 20) ?>-xxxxxxxx-xxxxxxxx</b> &nbsp;&nbsp;&nbsp;<a class="button-secondary" title="Deactivate" href="javascript: void(0)" onclick="jQuery(this).closest('form').submit();">Deactivate</a></p>
                                        </div>
                                        <div class="explain"><?php _e( "You can generate more keys from", 'apto' ) ?> <a href="http://www.nsp-code.com/premium-plugins/my-account/" target="_blank">My Account</a> 
                                        </div>
                                    </div> 
                                </div>
                             </form>
                        </div>
                        </div> 
                    <?php  
     
                    if(is_multisite())
                        {
                            ?>
                                </div>
                            <?php
                        }
                }
                
            function licence_multisite_require_nottice()
                {
                    ?>
                        <div class="wrap"> 
                            <div id="icon-settings" class="icon32"></div>
                            <h2><?php _e( "General Settings", 'apto' ) ?></h2>

                            <h2 class="subtitle"><?php _e( "Software License", 'apto' ) ?></h2>
                            <div id="form_data">
                                <div class="postbox">
                                    <div class="section section-text ">
                                        <h4 class="heading">License Key Required!</h4>
                                        <div class="option">
                                            <h4>cdscsdcsd sd</h4>
                                            <div class="explain"><?php _e( "Enter your License Key that you got when bought this product. If you lost the key, you can always retrieve that from", 'apto' ) ?> <a href="http://www.nsp-code.com/premium-plugins/my-account/" target="_blank">My Account</a><br />
                                            <?php _e( "You can generate more keys from", 'apto' ) ?> <a href="http://www.nsp-code.com/premium-plugins/my-account/" target="_blank">My Account</a> 
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div> 
                    <?php
                
                }    

                
        }

                                   

?>