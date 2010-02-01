<?php
/*
* Plugin Name: WordpreSMS
* Plugin URI: http://www.wordpresms.com/
* Version: v0.06
* Author: <a href="http://www.coandcouk.com/">Christoph Burgdorfer</a>
* Description: Allow users to retrieve your content via SMS
* 
* Changelog:
* v0.06: add character counter to box
* v0.05: add token authentification
--------------------------------------------------------------------------------
technology: (c) 2010 Robert Windisch (http://inpsyde.com) and Christoph Burgdorfer (http://www.coANDcoUK.com)
released from Robert Windisch (c) 2009 in cooperation with Christoph Burgdorfer for unlimited use by coANDco UK Ltd. 
*/

define('WORDPRESMS_MAX_SMS_TEXT_LENGTH', 600);

if(!class_exists("wordpreSMS")) {
	
	/**
	* wordpreSMS class
	*/
	class wordpreSMS {		

		var $adminOptionsName = "wordpreSMSAdminOptions";

		function wordpreSMS() {
			add_action('admin_head', array(&$this, 'cc_wordpreSMS_addHeaderCode'));
			register_activation_hook(__FILE__, array( &$this, 'plugin_install' ));
			add_action( 'generate_rewrite_rules', array(&$this,'on_generate_rewrite_rules'));
			add_action( 'wp_ajax_wordpresms', array(&$this, 'wp_ajax_wordpresms'));
			add_action( 'wp_ajax_nopriv_wordpresms', array(&$this, 'wp_ajax_wordpresms'));
			// Actions
			add_action('admin_menu', array(&$this, 'cc_wordpreSMS_ap'));
			add_action('save_post', array(&$this, 'cc_wordpreSMS_save_postdata' )); 	// Use the save_post action to do something with the data entered
		}

		function cc_wordpreSMS_addHeaderCode() {
			echo '<script language="JavaScript">
function cc_wordpreSMS_textCounter(field,cntfield,maxlimit) {
	if (field.value.length > maxlimit) { field.value = field.value.substring(0, maxlimit); } else { cntfield.value = maxlimit - field.value.length; }
}
</script>';
		}

		function cc_wordpreSMS_ap() {
			/* Initialize the admin panel  */
			if(function_exists('add_options_page')) {
				add_options_page('WordpreSMS', 'WordpreSMS', 9, basename(__FILE__), array(&$this, 'printAdminPage')); // 9 stands for super users and admins
			}
		
			if(function_exists("add_meta_box")) {
				add_meta_box( 'cc_wordpreSMS_sectionid', __( 'WordpreSMS', 'wordpreSMS' ), array( &$this, 'cc_wordpreSMS_inner_custom_box' ), 'post', 'advanced', 'high');
				add_meta_box( 'cc_wordpreSMS_sectionid', __( 'WordpreSMS', 'wordpreSMS' ), array( &$this, 'cc_wordpreSMS_inner_custom_box' ), 'page', 'advanced', 'high');
			} 
			else {
				add_action('dbx_post_advanced', array(&$this, 'cc_wordpreSMS_old_custom_box') );
		    	add_action('dbx_page_advanced', array(&$this, 'cc_wordpreSMS_old_custom_box') );
			}
		}
		
		function on_generate_rewrite_rules ($wp_rewrite) {
      		$wp_rewrite->non_wp_rules = array( 'wordpresms/do/(.*)' => 'wp-admin/admin-ajax.php?action=wordpresms&search=$1' ) + $wp_rewrite->non_wp_rules; 
		}
		
		function wp_ajax_wordpresms() {
			
			$ccOptions = get_option($this->adminOptionsName);
			switch ( $ccOptions['method'] ) {

				case 'tagsearch': {
					$post = $GLOBALS['wp_query']->query( array( 'tag' => $_REQUEST['search'] ) );
					break;
				}
				case 'textsearch': {
					$post = $GLOBALS['wp_query']->query( array( 's' => $_REQUEST['search'] ) );
					break;
				}
				case 'lastpost': {
					$post = $GLOBALS['wp_query']->query(array('showposts'=>1));
					break;	
				}
			}
			if ( 0 < count ( $post ) ) {
				$smstext = get_post_meta ( $post[0]->ID , '_cc_wordpreSMS_SMStext', true );
				if ( '' == $smstext ) {
					$smstext = $post[0] -> post_content;
				}
			}
			// echo '<?xml version="1.0" ?'.'><reply><text>' . $smstext . '</text><token>'. $ccOptions['token'] . '</token></reply>';
			echo $smstext;
			die();
		}
		
		function plugin_install() {
			global $wp_rewrite;
			
			$wp_rewrite->flush_rules();

			$this->getAdminOptions();
		}

		// retrieve or set admin options
		function getAdminOptions()
		{
			
			// define defaults
			$ccAdminOptions = array(
					'token' => '',
					'method' => 'lastpost',
				);
				
			// check db for options
			$ccOptions = get_option($this->adminOptionsName);
			
			// if options were in DB, override defaults
			if(!empty($ccOptions)) {
				foreach($ccOptions as $key => $option) {
					$ccAdminOptions[$key] = $option;
				}
			}
			
			// store options to the wordpress database
			update_option($this->adminOptionsName, $ccAdminOptions);
			
			// return for use
			return $ccAdminOptions;
		}
		
		/* print the admin panel or save the admin data */
		function printAdminPage()
		{
			$ccOptions = $this->getAdminOptions();
			
			// settings are being saved
			if(isset($_POST['update_ccWordpreSMSSettings'])) {
				$updateremote = false;
				if(isset($_POST['ccWordpreSMS_method'])) {
					$ccOptions['method'] = $_POST['ccWordpreSMS_method'];
				}

				if(isset($_POST['ccWordpreSMS_token'])) {
					if ( '' == $ccOptions['token'] ) {
						$updateremote = true;
					}
					$ccOptions['token'] = apply_filters('content_save_pre', $_POST['ccWordpreSMS_token']);
				}
				
				/* update adminOptionsName with $ccOptions which have been retrieved from $_POST */
				update_option($this->adminOptionsName, $ccOptions);
				if ( $updateremote ) {
					$check_remote =  wp_remote_get('http://wordpresms.com/smsapis/register/?token='.$ccOptions['token'].'&blogurl='.urlencode(get_option('siteurl')),array('timeout' => 2,'user-agent' => 'Mozilla' ));
					if ( 'true' == $check_remote['body'] ) {
						?><div class="updated"><p><strong><?php _e("Token registered", 'wordpreSMS') ?></strong></p></div><?php
					} else {
						?><div class="updated"><p><strong><?php _e("Error contacting WordpreSMS.com. Please contact the Support.", 'wordpreSMS') ?></strong></p></div><?php
					}
				}
				?><div class="updated"><p><strong><?php _e("Settings Saved", 'wordpreSMS') ?></strong></p></div><?php
			} ?>

			<div class="wrap">
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<h2>WordpreSMS Configuration</h2>
					<h3>Token</h3>
					<?php _e('Please get your token from <a href="http://www.wordpresms.com" target="_blank">WordpreSMS.com</a>.', 'wordpreSMS') ?>	<br/>
					<input type="text" name="ccWordpreSMS_token" size="100" value="<?php _e(apply_filters('format_to_edit', $ccOptions['token']), 'wordpreSMS') ?>" /><br />
					<br />
					
					<h3><?php _e('Delivery Method', 'wordpreSMS') ?></h3>
					
					<p>
						<label for="ccWordpreSMS_method_lastpost" name="ccWordpreSMS_method">
							<input type="radio" id="ccWordpreSMS_method_lastpost" name="ccWordpreSMS_method" value="lastpost" <?php if($ccOptions['method'] == "lastpost") { _e('checked="checked"', 'wordpreSMS'); } ?> />
						<?php _e('Users will always get the latest post.', 'wordpreSMS') ?>
						</label><br /><br />
						
						<label for="ccWordpreSMS_method_tagsearch" name="ccWordpreSMS_method">
							<input type="radio" id="ccWordpreSMS_method_tagsearch" name="ccWordpreSMS_method" value="tagsearch" <?php if($ccOptions['method'] == "tagsearch") { _e('checked="checked"', 'wordpreSMS'); } ?> />
						<?php _e('Users will receive the content with the closest matching tag.', 'wordpreSMS') ?>
						</label><br /><br />						
						
						<label for="ccWordpreSMS_method_textsearch" name="ccWordpreSMS_method">
							<input type="radio" id="ccWordpreSMS_method_textsearch" name="ccWordpreSMS_method" value="textsearch" <?php if($ccOptions['method'] == "textsearch") { _e('checked="checked"', 'wordpreSMS'); } ?> />
						<?php _e('Users will receive the content with the closest matching text search result.', 'wordpreSMS') ?>
						</label><br />
					</p>
										
					<div class="submit"><input type="submit" name="update_ccWordpreSMSSettings" value="<?php _e('Update Settings', 'wordpreSMS') ?>" /></div>
					
				</form>
			</div>
		<?php
		} // end printAdminPage
		
		/* When the post is saved, saves our custom data */
		function cc_wordpreSMS_save_postdata( $post_id ) {

			// verify this came from the our screen and with proper authorization,
		  	// because save_post can be triggered at other times
			if ( !wp_verify_nonce( $_POST['cc_wordpreSMS_noncename'], plugin_basename(__FILE__) )) {
			    return $post_id;  
			}
	
			// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
			// to do anything
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
				return $post_id;
		  
			// Check permissions
			if ( 'page' == $_POST['post_type'] ) {
				if ( !current_user_can( 'edit_page', $post_id ) )
					return $post_id;
			} 
			else {
				if ( !current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				}
			}
	
			// OK, we're authenticated: we need to find and save the data
	
			$mydata = $_POST['cc_wordpreSMS_SMStext'];
	
			// Do something with $mydata 
			// probably using add_post_meta(), update_post_meta(), or 
			// a custom table (see Further Reading section below)
			update_post_meta($post_id, '_cc_wordpreSMS_SMStext', $mydata, false);
			
			return $mydata;
		}
		
		/* Prints the edit form for pre-WordPress 2.5 post/page */
		function cc_wordpreSMS_old_custom_box($post_id) {

			echo '<div class="dbx-b-ox-wrapper">' . "\n";
			echo '<fieldset id="cc_wordpreSMS_fieldsetid" class="dbx-box">' . "\n";
			echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . __( 'SMS Text', 'cc_wordpreSMS_textdomain' ) . "</h3></div>";   
			echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

			// output editing form
			cc_wordpreSMS_inner_custom_box();

			// end wrapper
			echo "</div></div></fieldset></div>\n";
		}
		
		/* Prints the inner fields for the custom post/page section */
		function cc_wordpreSMS_inner_custom_box($post) {

		  	// Use nonce for verification
			echo '<input type="hidden" name="cc_wordpreSMS_noncename" id="cc_wordpreSMS_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

			// The actual fields for data entry	
			echo '<label for="cc_worpreSMS_SMStext">' . __("SMS Text", 'wordpreSMS' ) . '</label><br />';
			echo '<textarea rows="2" cols="40" id="cc_wordpreSMS_SMStext" name="cc_wordpreSMS_SMStext" style="width: 98%;" onKeyDown="cc_wordpreSMS_textCounter(document.getElementById(\'cc_wordpreSMS_SMStext\'),document.getElementById(\'remainingLength\'),' . WORDPRESMS_MAX_SMS_TEXT_LENGTH . ')" onKeyUp="cc_wordpreSMS_textCounter(document.getElementById(\'cc_wordpreSMS_SMStext\'),document.getElementById(\'remainingLength\'),' . WORDPRESMS_MAX_SMS_TEXT_LENGTH . ')">'. (get_post_meta($post->ID, '_cc_wordpreSMS_SMStext', true) ? get_post_meta($post->ID, '_cc_wordpreSMS_SMStext', true) : "") .'</textarea><br />';
			echo '<input readonly type="text" name="remainingLength" id="remainingLength" size="3" maxlength="3" value="' . WORDPRESMS_MAX_SMS_TEXT_LENGTH . '">';
			_e(' characters left. This text is being returned if users request that blog post via SMS. If empty, the original text will be returned.', 'wordpreSMS');
		}
	}
}


if(class_exists("wordpreSMS")) {
	new wordpreSMS();
}
?>