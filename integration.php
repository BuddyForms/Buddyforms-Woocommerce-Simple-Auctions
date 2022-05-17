<?php
/*
 * Plugin Name: Buddyforms Woocommerce Simple Auctions Integration
 * Plugin URI: http://buddyforms.com/downloads/buddyforms-woocommerce-simple-auctions/
 * Description: This plugin adds woocommerce simple auctions fields to frontend buddypress profile interface using buddyforms
 * Version: 1.2.4
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/buddyforms/
 * License: GPLv2 or later
 * Network: false
 * Text Domain: buddyforms
 * Svn: buddyforms-woocommerce-simple-auction
 * @package bf_woo_simple_auction
 *
 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'bf_woo_simple_auction' ) ) {
	
	class bf_woo_simple_auction {
		
		/**
		 * Instance of this class
		 *
		 * @var $instance bf_woo_elem
		 */
		protected static $instance = null;
		
		private function __construct() {
			$this->constants();
			$this->load_plugin_textdomain();
			require_once BF_WOO_SIMPLE_AUCTION_CLASSES_PATH . 'bf_woo_simple_auction_requirements.php';
			new bf_woo_simple_auction_requirements();
			
			if ( bf_woo_simple_auction_requirements::is_woocommerce_simple_auction_active() && bf_woo_simple_auction_requirements::is_woocommerce_active() &&
			     bf_woo_simple_auction_requirements::is_woo_elem_active()) {
				require_once BF_WOO_SIMPLE_AUCTION_CLASSES_PATH . 'bf_woo_simple_auction_manager.php';
				new bf_woo_simple_auction_manager();
			}
		}
		
		private function constants() {
			define( 'BF_WOO_SIMPLE_AUCTION_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'BF_WOO_SIMPLE_AUCTION_BASE_NAMEBASE_FILE', trailingslashit( wp_normalize_path( plugin_dir_path( __FILE__ ) ) ) . 'loader.php' );
			define( 'BF_WOO_SIMPLE_AUCTION_JS_PATH', plugin_dir_url( __FILE__ ) . 'assets/js/' );
			define( 'BF_WOO_SIMPLE_AUCTION_CSS_PATH', plugin_dir_url( __FILE__ ) . 'assets/css/' );
			define( 'BF_WOO_SIMPLE_AUCTION_VIEW_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR );
			define( 'BF_WOO_SIMPLE_AUCTION_CLASSES_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR );
		}
		
		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			
			return self::$instance;
		}
		
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'bf_woo_simple_auction_locale', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}
		
	}
	
	add_action( 'plugins_loaded', array( 'bf_woo_simple_auction', 'get_instance' ), 1 );
}