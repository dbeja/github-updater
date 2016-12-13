<?php
/**
 * GitHub Updater
 *
 * @package   GitHub_Updater
 * @author    Andy Fragen
 * @license   GPL-2.0+
 * @link      https://github.com/afragen/github-updater
 */

use Fragen\GitHub_Updater\Base;


// Add WP-CLI commands.
WP_CLI::add_command( 'github-updater', 'GitHub_Updater_CLI' );

/**
 * Manage GitHub Updater commands.
 *
 * Class GitHub_Updater_CLI
 */
class GitHub_Updater_CLI extends WP_CLI_Command {

	/**
	 * @var \Fragen\GitHub_Updater\Base
	 */
	private $base;

	/**
	 * GitHub_Updater_CLI constructor.
	 */
	public function __construct() {
		$this->base = new Base();
	}

	/**
	 * Clear GitHub Updater transients.
	 *
	 * ## OPTIONS
	 *
	 * <delete>
	 * : delete the cache
	 *
	 * ## EXAMPLES
	 *
	 *     wp github-updater cache delete
	 *
	 * @param array $args Array of arguments.
	 *
	 * @subcommand cache
	 */
	public function cache( $args ) {
		list( $action ) = $args;
		if ( 'delete' === $action ) {
			$this->base->delete_all_transients();
			WP_CLI::success( sprintf( esc_html__( 'GitHub Updater cache has been cleared.', 'github-updater' ) ) );
		} else {
			WP_CLI::error( sprintf( esc_html__( 'Incorrect command syntax, see %s for proper syntax.', 'github-updater' ), '`wp help github-updater cache`' ) );
		}
	}

	/**
	 * Reset GitHub Updater REST API key.
	 *
	 * ## EXAMPLES
	 *
	 *     wp github-updater reset_api_key
	 *
	 * @subcommand reset_api_key
	 */
	public function reset_api_key() {
		delete_site_option( 'github_updater_api_key' );
		$this->base->ensure_api_key_is_set();
		$api_key = get_site_option( 'github_updater_api_key' );
		$api_url = add_query_arg( array(
			'action' => 'github-updater-update',
			'key'    => $api_key,
		), admin_url( 'admin-ajax.php' ) );

		WP_CLI::success( sprintf( esc_html__( 'GitHub Updater REST API key has been reset.', 'github-updater' ) ) );
		WP_CLI::success( sprintf( esc_html__( 'The current RESTful endpoint is `%s`', 'github-updater' ), $api_url ) );
	}

}