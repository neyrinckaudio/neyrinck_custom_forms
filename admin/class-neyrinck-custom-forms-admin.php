<?php

	class Neyrinck_Custom_Forms_Admin {

		
		public function load_actions(){

			add_menu_page('Neyrinck Custom Forms', 'Neyrinck Custom Forms', 'manage_options', 'Settings', array($this, 'NCF_settings_page_handler') );

		}



		public function NCF_settings_page_handler()
		{
		    if ( !current_user_can( 'manage_options' ) )  {
		        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		    }

			
		    // get settings info
		    $settings = $this->get_settings();
		    $server = $settings->db_server;
		    $user = $settings->db_user;
		    $password = $settings->db_password;
		    $database = $settings->db_name;
		    
		    if( isset($_POST[ 'hidden' ]) && $_POST[ 'hidden' ] == 'Y' ) {

		    		$data = [];
			    	$data['db_server'] = $_POST['db_server'];
			    	$data['db_user'] = $_POST['db_user'];
			    	$data['db_password'] = $_POST['db_password'];
			    	$data['db_name'] = $_POST['db_name'];

			    	$this->set_settings($data);

			    	$settings = $this->get_settings();
				    $server = $settings->db_server;
				    $user = $settings->db_user;
				    $password = $settings->db_password;
				    $database = $settings->db_name;

				    if ($settings) $message = 'Settings successfully updated.';
				    else $message = 'Error saving information.';

		    } 


		    ?>
		    <div class='wrap block'>
		        <h3>NEYRINCK CUSTOM FORMS</h3> 

		         <?php if (!empty($notice)): ?>
		        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
		        <?php endif;?>
		        <?php if (!empty($message)): ?>
		        <div id="message" class="updated"><p><?php echo $message ?></p></div>
		        <?php endif;?>
		        
		        
		        <form method="post" action="">
		        <input type="hidden" name="hidden" value="Y">
		        <p>Server <input size="70" type="text" name="db_server" value="<?php echo $server; ?>"></p>
		        <p>Database <input type="text" name="db_name" value="<?php echo $database; ?>"></p>
		        <p>User <input type="text" name="db_user" value="<?php echo $user; ?>"></p>
		        <p>Password <input type="text" name="db_password" value="<?php echo $password; ?>"></p>


		        <p class="submit">
			    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Settings') ?>" />
			    </p>

		        </form>
		       

		         
		    </div>

		    <?php
		}

		public function get_settings(){

		    global $wpdb;
		    $table_name = $wpdb->prefix . 'ncf_settings';
		    $sql = "SELECT * FROM ".$table_name;
		    $results = $wpdb->get_results( $sql);
		    return $results[0];

		}

		private function set_settings($data){

			// check to see if row exist
			if (!$this->get_settings()) $this->doInsertSettings($data);
				else $this->doUpdateSettings($data);

		}


		private function doUpdateSettings($data){

			global $wpdb;
		    $table_name = $wpdb->prefix . 'ncf_settings';
		    $wpdb->update( $table_name, 
		    	array(
		    	      'db_server' => $data['db_server'],
		    	      'db_name' => $data['db_name'],
		    	      'db_user' => $data['db_user'],
		    	      'db_password' => $data['db_password'],

		    	),
		    	array('id'=>1)

		    );

		}

		private function doInsertSettings($data){

			global $wpdb;
		    $table_name = $wpdb->prefix . 'ncf_settings';
		    $wpdb->insert( $table_name, 
		    	array(
		    	      'db_server' => $data['db_server'],
		    	      'db_name' => $data['db_name'],
		    	      'db_user' => $data['db_user'],
		    	      'db_password' => $data['db_password'],

		    	)
		    );
			
		}


	}



?>