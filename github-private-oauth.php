<?php
/*
Plugin Name: WP Private GitHub Plugin Updater
Plugin URI: https://github.com/jkudish/WordPress-GitHub-Plugin-Updater
Description: Semi-automated test for the GitHub Plugin Updater
Version: 0.1
Author: Joachim Kudish
Author URI: http://jkudish.com/
License: GPLv2
*/

/**
 * Note: the version # above is purposely low in order to be able to test the updater
 * The real version # is below
 * @package GitHubUpdater
 * @author Joachim Kudish @link http://jkudish.com
 * @since 1.3
 * @version 1.5
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Configuration assistant for updating from private repositories.
 * Do not include this in your plugin once you get your access token.
 *
 * @see /wp-admin/plugins.php?page=github-updater
 */
class WPGitHubUpdaterSetup{

	function __construct() {

		add_action( 'admin_init', array( $this, 'settings_fields' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'network_admin_menu', array( $this, 'add_page' ) );

		add_action( 'wp_ajax_set_github_oauth_key', array( $this, 'ajax_set_github_oauth_key') );
		add_action( 'load-plugins_page_github-updater', array( $this, 'maybe_authorize') );
	}

	/**
	 * Add the options page
	 *
	 * @return none
	 */
	function add_page() {
		add_plugins_page ( __( 'GitHub Updates', 'github_plugin_updater' ), __( 'GitHub Updates', 'github_plugin_updater' ), 'update_plugins', 'github-updater', array( $this, 'admin_page' ) );
	}

	/**
	 * Add fields and groups to the settings page
	 *
	 * @return none
	 */
	public function settings_fields() {

		register_setting( 'ghupdate', 'ghupdate', array($this, 'settings_validate') );

		// Sections: ID, Label, Description callback, Page ID
		add_settings_section( 'ghupdate_private', 'Private Repositories', array($this, 'private_description'), 'github-updater' );

		// Private Repo Fields: ID, Label, Display callback, Menu page slug, Form section, callback arguements
		add_settings_field(
			'client_id', 'Client ID', array($this, 'input_field'), 'github-updater', 'ghupdate_private',
			array(
				'id' => 'client_id',
				'type' => 'text',
				'description' => '',
			)
		);
		add_settings_field(
			'client_secret', 'Client Secret', array($this, 'input_field'), 'github-updater', 'ghupdate_private',
			array(
				'id' => 'client_secret',
				'type' => 'text',
				'description' => '',
			)
		);
		add_settings_field(
			'access_token', 'Access Token', array($this, 'token_field'), 'github-updater', 'ghupdate_private',
			array(
				'id' => 'access_token',
			)
		);

	}

	public function private_description() {

		$name     = preg_replace( '|^https?://|', '', home_url() );
		$url      = home_url();
		$callback = get_site_url( null, '', 'admin' );

		?>
		<p>Updating from private repositories requires a one-time application setup and authorization.</p>
		<p>Follow these steps:</p>
		<ol>
			<li><a href="https://github.com/settings/applications/new" target="_blank">Create an application on GitHub.com</a> using the following values:
				<ul>
					<li><strong>Name:</strong> <code><?php echo $name; ?></code></li>
					<li><strong>URL:</strong> <code><?php echo $url; ?></code></li>
					<li><strong>Callback URL:</strong> <code><?php echo $callback; ?></code></li>
				</ul>
			</li>
			<li>You'll be provided with a <strong>Client ID</strong> and a <strong>Client Secret</strong>. Copy the values into the fields below.</li>
			<li>Click 'Authorize with GitHub'.</li>
		</ol>
		<?php
	}

	public function input_field( $args ) {
		extract($args);
		$gh = get_option('ghupdate');
		$value = $gh[$id];
		?>
		<input value="<?php esc_attr_e($value)?>" name="<?php esc_attr_e($id) ?>" id="<?php esc_attr_e($id) ?>" type="text" class="regular-text" />
		<?php echo $description ?>
		<?php
	}

	public function token_field( $args ) {
		extract($args);
		$gh = get_option('ghupdate');
		$value = $gh[$id];

		if ( empty($value) ) {
			?>
			<p>Input Client ID and Client Secret, then click 'Authorize with GitHub'.</p>
			<input value="<?php esc_attr_e($value)?>" name="<?php esc_attr_e($id) ?>" id="<?php esc_attr_e($id) ?>" type="hidden" />
			<?php
		}else{
			?>
			<input value="<?php esc_attr_e($value)?>" name="<?php esc_attr_e($id) ?>" id="<?php esc_attr_e($id) ?>" type="text" class="regular-text" />
			<?php
		}
		?>
		<?php
	}

	public function settings_validate( $input ) {
		if ( empty( $input ) ) {
			$input = $_POST;
		}
		if ( !is_array($input) ) {
			return false;
		}
		$gh = get_option('ghupdate');
		$valid = array();

		$valid['client_id']     = strip_tags( stripslashes( $input['client_id'] ) );
		$valid['client_secret'] = strip_tags( stripslashes( $input['client_secret'] ) );
		$valid['access_token']  = strip_tags( stripslashes( $input['access_token'] ) );

		if ( empty( $valid['client_id']) ) {
			add_settings_error( 'client_id', 'no-client-id', __('Please input a Client ID before authorizing.', 'github_plugin_updater'), 'error' );
		}
		if ( empty( $valid['client_secret']) ) {
			add_settings_error( 'client_secret', 'no-client-secret', __('Please input a Client Secret before authorizing.', 'github_plugin_updater'), 'error' );
		}

		return $valid;
	}

	/**
	 * Output the setup page
	 *
	 * @return none
	 */
	function admin_page() {
		?>
		<div class="wrap ghupdate-admin">

			<div class="head-wrap">
				<?php screen_icon('plugins'); ?>
				<h2><?php _e( 'Setup GitHub Updates' , 'github_plugin_updater' ); ?></h2>
			</div>

			<div class="postbox-container primary">
				<form method="post" id="ghupdate" action="options.php">
					<?php
						settings_errors();
						settings_fields('ghupdate'); // includes nonce
						do_settings_sections( 'github-updater' );
						submit_button( __( 'Authorize with GitHub', 'github_plugin_updater' ) )
					?>
				</form>
			</div>

		</div>
		<?php
	}

	public function maybe_authorize() {
		$gh = get_option('ghupdate');
		if ( 'false' == $_GET['authorize'] || 'true' != $_GET['settings-updated'] || empty($gh['client_id']) || empty($gh['client_secret']) ) {
			return;
		}

		$redirect_uri = urlencode(admin_url('admin-ajax.php?action=set_github_oauth_key'));

		// Send user to GitHub for account authorization

		# https://github.com/login/oauth/authorize?scopes=repo&client_id=a126bc95237ff7299c6d

		$query = 'https://github.com/login/oauth/authorize';
		$query_args = array(
			'scope' => 'repo',
			'client_id' => $gh['client_id'],
			'redirect_uri' => $redirect_uri,
		);
		$query = add_query_arg($query_args, $query);
		wp_redirect( $query );

		exit;

	}

	public function ajax_set_github_oauth_key() {
		$gh = get_option('ghupdate');

		$query = admin_url( 'plugins.php' );
		$query = add_query_arg( array('page' => 'github-updater'), $query );

		if ( isset($_GET['code']) ) {
			// Receive authorized token
			$query = 'https://github.com/login/oauth/access_token';
			$query_args = array(
				'client_id'     => $gh['client_id'],
				'client_secret' => $gh['client_secret'],
				'code'          => stripslashes( $_GET['code'] ),
			);
			$query = add_query_arg( $query_args, $query );
			$response = wp_remote_get( $query, array('sslverify' => false) );
			parse_str( $response['body'] ); // populates $access_token, $token_type

			if ( isset( $access_token ) and !empty( $access_token ) ) {
				$gh['access_token'] = $access_token;
				update_option( 'euapi_github_access_token', $access_token );
				update_option('ghupdate', $gh );
				$query = add_query_arg( array(
					'page'       => 'github-updater',
					'authorized' => 'true'
				), admin_url( 'plugins.php' ) );
				wp_redirect( $query );
				exit;
			}

		}

		$query = add_query_arg( array('authorize'=>'false'), $query );
		wp_redirect($query);
		exit;

	}

}

add_action('init', create_function('', 'global $WPGitHubUpdaterSetup; $WPGitHubUpdaterSetup = new WPGitHubUpdaterSetup();') );
