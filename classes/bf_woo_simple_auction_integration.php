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

class bf_woo_simple_auction_integration {
	
	private $loaded_script = false;
	private $save_with_woocommerce_field = false;
	/** @var WooCommerce_simple_auction */
	private $simple_auction = null;
	private $buddy_form_query;
	
	public function __construct() {
		add_filter( 'bf_woo_element_woo_implemented_tab', array( $this, 'implemented_tab' ), 10, 1 );
		add_filter( 'buddyforms_formbuilder_fields_options', array( $this, 'buddyforms_simple_auctions_add_wc_form_element_tab' ), 2, 3 );
		add_action( 'buddyforms_update_post_meta', array( $this, 'buddyforms_product_save_data' ), 99, 2 );
		add_action( 'buddyforms_after_save_post', array( $this, 'buddyforms_product_save_data_after' ), 992, 1 );
		add_filter( 'buddyforms_create_edit_form_display_element', array( $this, 'form_display_element' ), 10, 2 );
		add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ), 99 );
		add_action( 'buddyforms_the_loop_start', array( $this, 'buddyforms_the_loop_start' ), 10, 1 );
	}
	
	public function implemented_tab( $existing ) {
		
		return array_merge( $existing, array( 'auction_tab' ) );
	}
	
	public function buddyforms_the_loop_start( $args ) {
		if ( ! empty( $args['meta_key'] ) && $args['meta_key'] == '_bf_form_slug' ) {
			$this->buddy_form_query = true;
		}
	}
	
	public function pre_get_posts( $q ) {
		if ( $this->buddy_form_query && isset( $q->query['auction_arhive'] ) OR ( ! isset( $q->query['auction_arhive'] ) && ( isset( $q->query['post_type'] ) && $q->query['post_type'] == 'product' && ! $q->is_main_query() ) ) ) {
			if ( isset( $q->query_vars['tax_query'] ) ) {
				unset( $q->query_vars['tax_query'] );
			}
		}
	}
	
	public function form_display_element( $form, $form_args ) {
		extract( $form_args );
		if ( ! isset( $customfield['type'] ) ) {
			return $form;
		}
		if ( ( $customfield['type'] == 'woocommerce' || $customfield['type'] == 'product-gallery' ) && is_user_logged_in() ) {
			if ( ! $this->loaded_script ) {
				$this->simple_auction = new WooCommerce_simple_auction();
				$this->add_scripts( $this->simple_auction, $customfield );
			}
		}
		
		return $form;
	}
	
	/**
	 * @param WooCommerce_simple_auction $simple_auction
	 * @param array $custom_field
	 */
	private function add_scripts( $simple_auction, $custom_field ) {
		wp_register_script(
			'simple-auction-admin',
			$simple_auction->plugin_url . '/js/simple-auction-admin.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'timepicker-addon' ),
			$simple_auction->version,
			true
		);
		wp_enqueue_script( 'simple-auction-admin' );
		wp_enqueue_script(
			'timepicker-addon',
			$simple_auction->plugin_url . '/js/jquery-ui-timepicker-addon.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ),
			$simple_auction->version,
			true
		);
		wp_enqueue_style( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'bf_woo_simple_auction', BF_WOO_SIMPLE_AUCTION_CSS_PATH . 'bf_woo_simple_auction.css' );
		wp_register_script( 'bf_woo_simple_auction', BF_WOO_SIMPLE_AUCTION_JS_PATH . 'bf_woo_simple_auction.js', array( 'jquery' ), bf_woo_simple_auction_manager::get_version(), true );
		wp_enqueue_script( 'bf_woo_simple_auction' );
		wp_localize_script( 'bf_woo_simple_auction', 'bf_woo_simple_auction', $custom_field );
		$this->loaded_script = true;
	}
	
	public function buddyforms_simple_auctions_add_wc_form_element_tab( $form_fields, $field_type, $field_id ) {
		global $post;
		
		$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );
		
		$hook_field_types = array( 'woocommerce' );
		
		if ( ! in_array( $field_type, $hook_field_types ) ) {
			return $form_fields;
		}
		
		// Item condition
		$auction_item_condition                             = isset( $buddyform['form_fields'][ $field_id ]['_auction_item_condition'] ) ? $buddyform['form_fields'][ $field_id ]['_auction_item_condition'] : 'display';
		$form_fields['Auctions']['_auction_item_condition'] = new Element_Select( '<b>' . __( 'Item Condition', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][_auction_item_condition]", array(
			'display' => __( 'Display', 'buddyforms' ),
			'new'     => __( 'New', 'buddyforms' ),
			'used'    => __( 'Used', 'buddyforms' )
		), array( 'value' => $auction_item_condition ) );
		
		// Auction Type
		$auction_type                             = isset( $buddyform['form_fields'][ $field_id ]['_auction_type'] ) ? $buddyform['form_fields'][ $field_id ]['_auction_type'] : 'display';
		$form_fields['Auctions']['_auction_type'] = new Element_Select( '<b>' . __( 'Auction Type', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][_auction_type]", array(
			'display' => __( 'Display', 'buddyforms' ),
			'normal'  => __( 'Normal', 'buddyforms' ),
			'reverse' => __( 'Reverse', 'buddyforms' )
		), array( 'value' => $auction_type ) );
		
		// Auction Proxy
		$auction_proxy                             = isset( $buddyform['form_fields'][ $field_id ]['_auction_proxy'] ) ? $buddyform['form_fields'][ $field_id ]['_auction_proxy'] : 'display';
		$form_fields['Auctions']['_auction_proxy'] = new Element_Select( '<b>' . __( 'Auction Proxy', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][_auction_proxy]", array(
			'display' => __( 'Display', 'buddyforms' ),
			'enable'  => __( 'Enable', 'buddyforms' ),
			'disable' => __( 'Disable', 'buddyforms' )
		), array( 'value' => $auction_proxy ) );
		
		// Start Price
		$start_price                                     = isset( $buddyform['form_fields'][ $field_id ]['_auction_start_price'] ) ? $buddyform['form_fields'][ $field_id ]['_auction_start_price'] : 'none';
		$form_fields['Auctions']['_auction_start_price'] = new Element_Select( '<b>' . __( 'Start Price', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][_auction_start_price]", array(
			'none'     => __( 'None', 'buddyforms' ),
			'required' => __( 'Required', 'buddyforms' ),
			'hidden'   => __( 'Hidden', 'buddyforms' )
		), array( 'value' => $start_price ) );
		
		// Bid increment auction_bid_increment
		$auction_bid_increment                             = isset( $buddyform['form_fields'][ $field_id ]['_auction_bid_increment'] ) ? $buddyform['form_fields'][ $field_id ]['_auction_bid_increment'] : 'none';
		$form_fields['Auctions']['_auction_bid_increment'] = new Element_Select( '<b>' . __( 'Bid increment', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][_auction_bid_increment]", array(
			'none'     => __( 'None', 'buddyforms' ),
			'required' => __( 'Required', 'buddyforms' ),
			'hidden'   => __( 'Hidden', 'buddyforms' )
		), array( 'value' => $auction_bid_increment ) );
		
		// Reserve price auction_reserved_price
		$auction_reserved_price                             = isset( $buddyform['form_fields'][ $field_id ]['_auction_reserved_price'] ) ? $buddyform['form_fields'][ $field_id ]['_auction_reserved_price'] : 'none';
		$form_fields['Auctions']['_auction_reserved_price'] = new Element_Select( '<b>' . __( 'Reserve price', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][_auction_reserved_price]", array(
			'none'     => __( 'None', 'buddyforms' ),
			'required' => __( 'Required', 'buddyforms' ),
			'hidden'   => __( 'Hidden', 'buddyforms' )
		), array( 'value' => $auction_reserved_price ) );
		
		// Buy it now price
		$regular_price                             = isset( $buddyform['form_fields'][ $field_id ]['_regular_price'] ) ? $buddyform['form_fields'][ $field_id ]['_regular_price'] : 'none';
		$form_fields['Auctions']['_regular_price'] = new Element_Select( '<b>' . __( 'Buy it now price', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][_regular_price]", array(
			'none'     => __( 'None', 'buddyforms' ),
			'required' => __( 'Required', 'buddyforms' ),
			'hidden'   => __( 'Hidden', 'buddyforms' )
		), array( 'value' => $regular_price ) );
		
		// Auction Dates auction_dates_from
		$auction_dates_from                            = isset( $buddyform['form_fields'][ $field_id ]['auction_dates_from'] ) ? $buddyform['form_fields'][ $field_id ]['auction_dates_from'] : 'required';
		$form_fields['Auctions']['auction_dates_from'] = new Element_Checkbox( '<b>' . __( 'Auction Date from', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][auction_dates_from]", array( 'required' => __( 'Required', 'buddyforms' ) ), array( 'value' => $auction_dates_from ) );
		
		// auction_dates_to
		$auction_dates_to                            = isset( $buddyform['form_fields'][ $field_id ]['auction_dates_to'] ) ? $buddyform['form_fields'][ $field_id ]['auction_dates_to'] : 'required';
		$form_fields['Auctions']['auction_dates_to'] = new Element_Checkbox( '<b>' . __( 'Auction Dates to', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][auction_dates_to]", array( 'required' => __( 'Required', 'buddyforms' ) ), array( 'value' => $auction_dates_to ) );
		
		return $form_fields;
	}
	
	/**
	 * Saves the data inputed into the product boxes, as post meta data
	 *
	 *
	 * @param int $post_id   the post (product) identifier
	 * @param stdClass $post the post (product)
	 *
	 */
	public function buddyforms_product_save_data( $post, $post_id ) {
		global $wpdb, $woocommerce, $woocommerce_errors;
		if ( $post['type'] == 'woocommerce' && ! empty( $this->simple_auction ) ) {
			$this->save_with_woocommerce_field = true;
			$this->simple_auction->product_save_data( $post_id, null );
		}
	}
	
	public function buddyforms_product_save_data_after( $post_id ) {
		if ( $this->save_with_woocommerce_field ) {
			global $post;
			if ( function_exists( 'wc_get_product' ) ) {
				$product = wc_get_product( $post_id );
				if ( ! empty( $product ) && is_object( $product ) && $product->is_type( 'auction' ) ) {
					update_post_meta( $post_id, '_visibility', 'visible' );
				}
			}
		}
	}
}