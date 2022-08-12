<?php
/**
 * @package    WordPress
 * @subpackage Woocommerce, BuddyForms
 * @author     ThemKraft Dev Team
 * @copyright  2017, Themekraft
 * @link       http://buddyforms.com/downloads/buddyforms-woocommerce-form-elements/
 * @license    GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class bf_woo_simple_auction_manager {

	protected static $version   = '1.0.3.1';
	private static $plugin_slug = 'bf_woo_simple_auction';

	public function __construct() {
		require_once BF_WOO_SIMPLE_AUCTION_CLASSES_PATH . 'bf_woo_simple_auction_log.php';
		new bf_woo_simple_auction_log();
		try {
			require_once BF_WOO_SIMPLE_AUCTION_CLASSES_PATH . 'bf_woo_simple_auction_integration.php';
			new bf_woo_simple_auction_integration();
		} catch ( Exception $ex ) {
			bf_woo_elem_log::log(
				array(
					'action'         => get_class( $this ),
					'object_type'    => bf_woo_elem_manager::get_slug(),
					'object_subtype' => 'loading_dependency',
					'object_name'    => $ex->getMessage(),
				)
			);

		}
	}

	public static function get_slug() {
		return self::$plugin_slug;
	}

	public static function get_version() {
		return self::$version;
	}
}
