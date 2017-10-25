<?php
/**
 * Plugin Name: Codeable #62926
 * Plugin URI: https://app.codeable.io/tasks/62926/workroom
 * Description: Codeable #62926.
 * Version: 1.0.0
 * Author: Nabil Kadimi
 * Author URI: https://codeable.io/developers/nabil-kadimi/
 * Text Domain: starter
 * License: GPL2
 *
 * @package starter
 */

// Avoid direct calls to this file.
if ( ! function_exists( 'add_action' ) ) {
	die();
}

/**
 * Starter class.
 */
class Starter {

	/**
	 * Class instance
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Plugin slug.
	 *
	 * @var String
	 */
	public $plugin_slug;

	/**
	 * Plugin version.
	 *
	 * @var String
	 */
	public $plugin_version;

	/**
	 * Plugin directory path.
	 *
	 * @var String
	 */
	public $plugin_dir_path;

	/**
	 * Relative plugin directory path.
	 *
	 * @var String
	 */
	public $plugin_basename;

	/**
	 * Plugin directory URL.
	 *
	 * @var String
	 */
	public $plugin_dir_url;

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Cloner
	 */
	public function __clone() {
	}

	/**
	 * Returns a new or the existing instance of this class
	 *
	 * @return Object
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new static;
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Initializes plugin
	 */
	protected function init() {
		$this->plugin_basename = plugin_basename( __FILE__ );
		$this->plugin_dir_path = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( __FILE__ );
		$this->plugin_slug = self::camel_case_to_snake_case( __CLASS__ );
		$this->autoload();
		$this->activate();
		$this->enqueue_public_assets();
		$this->l10n();
		$this->shortcodes();
	}

	/**
	 * Requires Composer generated autoload file and .php files in the directory `inc`
	 */
	protected function autoload() {
		$autoload_file_path = $this->plugin_dir_path . 'vendor/autoload.php';
		if ( file_exists( $autoload_file_path ) ) {
			require $autoload_file_path;
			$paths = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->plugin_dir_path . 'inc' ), RecursiveIteratorIterator::SELF_FIRST );
			foreach ( $paths as $path => $unused ) {
				if ( preg_match( '/\/[\w-]+(?!\.inc)\.php$/', $path ) ) {
					require $path;
				}
			}
		} else {
			// @codingStandardsIgnoreStart
			wp_die( sprintf( __( 'Plugin <strong>%1$s</strong> not installed yet, run the `<strong><code>composer install</code></strong>` command on a terminal from within the plugin directory and activate the plugin again from the <a href="%1$s">plugins page</a>.', '{{starter}}' ), $this->plugin_slug, admin_url( 'plugins.php' ) ) ); // XSS OK.
			// @codingStandardsIgnoreEnd
		}
	}

	/**
	 * Runs on plugin actication
	 */
	protected function activate() {
		register_activation_hook( __FILE__, function() {
			set_transient( $this->plugin_slug, 1, self::in( '15 minutes' ) );
		});
	}

	/**
	 * Loads textdomain.
	 *
	 * Important: textdomain must always e hardcoded in l10n/i18n functions (`__()`, `_e`, ...).
	 */
	protected function l10n() {
		add_action( 'plugins_loaded', function() {
			load_plugin_textdomain( $this->plugin_slug, false, dirname( $this->plugin_basename ) . '/lang' );
		} );
	}

	/**
	 * Requires a plugin
	 *
	 * @param  String $name    Plugin name.
	 * @param  Array  $options TGMPA compatible options.
	 */
	protected function require_plugin( $name, $options = [] ) {
		add_action('tgmpa_register', function() use ( $name, $options ) {
			$options['name'] = $name;
			$options['slug'] = ! empty( $options['slug'] )
				? $options['slug']
				: strtolower( preg_replace( '/[^\w\d]+/', '-', $name ) );
			$options['required'] = true;
			tgmpa( [ $options ] );
		});
	}

	/**
	 * Adds plugin shortcodes.
	 */
	protected function shortcodes() {
		add_shortcode($this->plugin_slug, function( $atts ) {
			$args = shortcode_atts( array(
				'dummy' => 'dummy',
			), $atts );
			$output = Kint::dump( $args );
			return $output;
		} );
	}

	/**
	 * Enqueue styles as scripts
	 *
	 * @todo Improve documentation.
	 */
	protected function enqueue_public_assets() {
		$this->enqueue_asset( 'public/css/frontend-main.css' );
		$this->enqueue_asset( 'public/js/frontend-main.js' );
		$this->enqueue_asset( 'public/css/backend-main.css', [
			'is_admin' => true,
		] );
		$this->enqueue_asset( 'public/js/backend-main.js', [
			'is_admin' => true,
		] );
	}

	/**
	 * Enqueues an asset.
	 *
	 * @todo Improve documentation.
	 * @param  String $path The path relative to the plugin directory.
	 * @param  Array  $args Same as what you would provide to wp_enqueue_script or wp_enqueue_style with the addition of is_admin which enqueue the asset on the backend.
	 */
	protected function enqueue_asset( $path, $args = [] ) {
		$default_args = [
			'is_admin' => false,
			'handle' => $this->plugin_slug,
			'deps' => null,
			'ver' => $this->plugin_version,
			'in_footer' => null,
			'media' => null,
		];

		$args += $default_args;
		$args['abspath'] = $this->plugin_dir_path . $path;
		$args['src'] = $this->plugin_dir_url . $path;
		$parts = explode( '.', $path );
		$extension = end( $parts );

		if ( ! file_exists( $args['abspath'] ) ) {
			$this->watchdog( sprintf( 'File <code>%s</code> does not exist', $path ), 'notice' );
			return;
		}

		if ( ! in_array( $extension, [ 'css', 'js' ], true ) ) {
			$this->watchdog( sprintf( 'File <code>%s</code> cannot be enqueued', $path ), 'notice' );
			return;
		}

		add_action( ( $args['is_admin'] ? 'admin' : 'wp' ) . '_enqueue_scripts', function() use ( $args, $extension ) {
			switch ( $extension ) {
				case 'css':
					return wp_enqueue_style( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );
				case 'js':
					return wp_enqueue_script( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );
				default:
					break;
			}
		} );
	}

	/**
	 * Enqueues an asset.
	 *
	 * @todo Improve documentation.
	 * @param  String $path The path relative to the plugin directory.
	 * @param  Array  $args Same as what you would provide to wp_enqueue_script or wp_enqueue_style with the addition of is_admin which enqueue the asset on the backend.
	 */
	protected function admin_enqueue_asset( $path, $args = [] ) {
		$args['is_admin'] = true;
		return $this->enqueue_asset( $path, $args );
	}

	/**
	 * Converts a string from camelCase to snake_case
	 *
	 * @param  String $str camelCase.
	 * @return String      snake_case.
	 */
	static function camel_case_to_snake_case( $str ) {
		preg_match_all( '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches );
		foreach ( $matches[0] as &$match ) {
			if ( strtoupper( $match ) === $match ) {
				$match = strtolower( $match );
			} else {
				$match = lcfirst( $match );
			}
		}
		return implode( '_', $matches[0] );
	}

	/**
	 * Returns number of seconds to given time
	 *
	 * @param  String $time Time.
	 * @return int          Seconds to time.
	 */
	static function in( $time ) {
		return strftime( $time ) - time();
	}

	/**
	 * Logs whatever you want
	 *
	 * @param  String $msg  A message.
	 * @param  String $type A type.
	 * @todo  Write method
	 */
	protected function watchdog( $msg, $type = 'notice' ) {
		if ( in_array( $type, [ 'deprecated', 'notice', 'warning', 'error' ], true ) ) {
			// The method does nothing yet.
			;
		}
	}
}

/**
 * Shortcut.
 */
function starter() {
	return Starter::get_instance();
}

/**
 * Fire plugin.
 */
starter();
