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
	
	public function __construct() {
		add_filter( 'buddyforms_formbuilder_fields_options', array( $this, 'buddyforms_simple_auctions_add_wc_form_element_tab' ), 2, 3 );
//		add_action( 'bf_woocommerce_product_options_general_last', array( $this, 'buddyforms_product_write_panel' ), 10, 2 );
		add_action( 'buddyforms_front_js_css_enqueue', array( $this, 'buddyforms_frontend_custom_intialization' ), 3 );
		add_action( 'buddyforms_update_post_meta', array( $this, 'buddyforms_product_save_data' ), 99, 2 );
		add_action( 'buddyforms_after_save_post', array( $this, 'buddyforms_product_save_data_after' ), 100, 1 );
//		add_filter( 'bf_woo_element_woo_implemented_tab', array( $this, 'woo_implemented_tab' ), 10, 1 );
		add_filter( 'buddyforms_create_edit_form_display_element', array( $this, 'form_display_element' ), 10, 2 );
	}
	
	public function form_display_element( $form, $form_args ) {
		extract( $form_args );
		if ( ! isset( $customfield['type'] ) ) {
			return $form;
		}
		if ( ( $customfield['type'] == 'woocommerce' || $customfield['type'] == 'product-gallery' ) && is_user_logged_in() ) {
			if ( ! $this->loaded_script ) {
				$simple_auction = new WooCommerce_simple_auction();
				$this->add_scripts( $simple_auction );
			}
		}
		
		return $form;
	}
	
	/**
	 * @param WooCommerce_simple_auction $simple_auction
	 *
	 */
	private function add_scripts( $simple_auction ) {
		wp_register_script(
			'simple-auction-admin',
			$simple_auction->plugin_url . '/js/simple-auction-admin.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'timepicker-addon' ),
			'1',
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
		$this->loaded_script = true;
	}
	
	public function woo_implemented_tab( $tabs ) {
		return array_merge( $tabs, array( 'auction_tab' ) );
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
	
	public function buddyforms_product_write_panel( $thepostid, $customfield ) {
		global $post;
		$required               = $required_html = '';
		$auction_item_condition = array(
			'new'  => __( 'New', 'wc_simple_auctions' ),
			'used' => __( 'Used', 'wc_simple_auctions' )
		);
		$auction_types          = array(
			'normal'  => __( 'Normal', 'wc_simple_auctions' ),
			'reverse' => __( 'Reverse', 'wc_simple_auctions' )
		);
		
		// Pull the video tab data out of the database
		$tab_data = maybe_unserialize( get_post_meta( $post->ID, 'woo_auction_tab', true ) );
		if ( empty( $tab_data ) ) {
			$tab_data[] = array();
		}
		echo '<div class="options_group auction_tab show_if_auction hide_if_grouped hide_if_external hide_if_variable hide_if_simple">';
		echo '<div id="auction_tab" class=" ">';
		
		// _auction_item_condition
		if ( isset( $customfield['_auction_item_condition'] ) && $customfield['_auction_item_condition'] == 'display' ) {
			woocommerce_wp_select( array(
				'custom_attributes' => $required,
				'id'                => '_auction_item_condition',
				'label'             => $required_html . __( 'Item condition', 'wc_simple_auctions' ),
				'options'           => apply_filters( 'simple_auction_item_condition', $auction_item_condition )
			) );
		} else {
			echo '<input type="hidden" name="_auction_item_condition" value="' . $customfield['_auction_item_condition'] . '" />';
		}
		
		// _auction_type
		if ( isset( $customfield['_auction_type'] ) && $customfield['_auction_type'] == 'display' ) {
			woocommerce_wp_select( array(
				'custom_attributes' => $required,
				'id'                => '_auction_type',
				'label'             => $required_html . __( 'Auction type', 'wc_simple_auctions' ),
				'options'           => $auction_types
			) );
		} else {
			echo '<input type="hidden" name="_auction_type" value="' . $customfield['_auction_item_condition'] . '" />';
		}
		
		// _auction_proxy
		if ( isset( $customfield['_auction_proxy'] ) && $customfield['_auction_proxy'] == 'display' ) {
			woocommerce_wp_checkbox( array(
				'custom_attributes' => $required,
				'id'                => '_auction_proxy',
				'label'             => $required_html . __( 'Proxy bidding?', 'wc_simple_auctions' ),
				'description'       => __( 'Enable proxy bidding', 'wc_simple_auctions' ),
				'cbvalue'           => 'yes',
				'checked'           => 'true'
			) );
		} else {
			echo '<input type="hidden" name="_auction_proxy" value="' . $customfield['_auction_proxy'] . '" />';
		}
		
		// _auction_start_price
		if ( isset( $customfield['_auction_start_price'] ) && $customfield['_auction_start_price'] != 'hidden' ) {
			$required      = $customfield['_auction_start_price'] == 'required' ? array( 'required' => '' ) : '';
			$required_html = $customfield['_auction_start_price'] == 'required' ? '<span class="required">* </span>' : '';
			
			woocommerce_wp_text_input( array(
				'custom_attributes' => $required,
				'id'                => '_auction_start_price',
				'class'             => 'wc_input_price short',
				'label'             => $required_html . __( 'Start Price', 'wc_simple_auctions' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0'
				)
			) );
		} else {
			echo '<input type="hidden" name="_auction_start_price" value="' . $customfield['_auction_start_price'] . '" />';
		}
		
		// _auction_bid_increment
		if ( isset( $customfield['_auction_bid_increment'] ) && $customfield['_auction_bid_increment'] != 'hidden' ) {
			$required      = $customfield['_auction_bid_increment'] == 'required' ? array( 'required' => '' ) : '';
			$required_html = $customfield['_auction_bid_increment'] == 'required' ? '<span class="required">* </span>' : '';
			
			woocommerce_wp_text_input( array(
				'custom_attributes' => $required,
				'id'                => '_auction_bid_increment',
				'class'             => 'wc_input_price short',
				'label'             => $required_html . __( 'Bid increment', 'wc_simple_auctions' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0'
				)
			) );
		} else {
			echo '<input type="hidden" name="_auction_bid_increment" value="' . $customfield['_auction_bid_increment'] . '" />';
		}
		
		// _auction_start_price
		if ( isset( $customfield['_auction_reserved_price'] ) && $customfield['_auction_reserved_price'] != 'hidden' ) {
			$required      = $customfield['_auction_reserved_price'] == 'required' ? array( 'required' => '' ) : '';
			$required_html = $customfield['_auction_reserved_price'] == 'required' ? '<span class="required">* </span>' : '';
			
			woocommerce_wp_text_input( array(
				'custom_attributes' => $required,
				'id'                => '_auction_reserved_price',
				'class'             => 'wc_input_price short',
				'label'             => $required_html . __( 'Reserve price', 'wc_simple_auctions' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0'
				),
				'desc_tip'          => 'true',
				'description'       => __( 'A reserve price is the lowest price at which you are willing to sell your item. If you don’t want to sell your item below a certain price, you can set a reserve price. The amount of your reserve price is not disclosed to your bidders, but they will see that your auction has a reserve price and whether or not the reserve has been met. If a bidder does not meet that price, you are not obligated to sell your item. ', 'wc_simple_auctions' )
			) );
			
		} else {
			echo '<input type="hidden" name="_auction_reserved_price" value="' . $customfield['_auction_reserved_price'] . '" />';
		}
		
		// _auction_start_price
		if ( isset( $customfield['_regular_price'] ) && $customfield['_regular_price'] != 'hidden' ) {
			$required      = $customfield['_regular_price'] == 'required' ? array( 'required' => '' ) : '';
			$required_html = $customfield['_regular_price'] == 'required' ? '<span class="required">* </span>' : '';
			
			woocommerce_wp_text_input(
				array(
					'custom_attributes' => $required,
					'id'                => '_regular_price',
					'class'             => 'wc_input_price short',
					'label'             => $required_html . __( 'Buy it now price', 'wc_simple_auctions' ) . ' (' . get_woocommerce_currency_symbol() . ')',
					'type'              => 'number',
					'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
					'desc_tip'          => 'true',
					'description'       => __( 'Buy it now disappears when bid exceeds the Buy now price for normal auction, or is lower than reverse auction', 'wc_simple_auctions' )
				) );
		} else {
			echo '<input type="hidden" name="_regular_price" value="' . $customfield['_regular_price'] . '" />';
		}
		
		$auction_dates_from       = ( $date = get_post_meta( $post->ID, '_auction_dates_from', true ) ) ? $date : '';
		$required_dates_from      = $customfield['auction_dates_from'][0] == 'required' ? 'required' : '';
		$required_html_dates_from = $customfield['auction_dates_from'][0] == 'required' ? '<span class="required">* </span>' : '';
		
		$auction_dates_to       = ( $date = get_post_meta( $post->ID, '_auction_dates_to', true ) ) ? $date : '';
		$required_dates_to      = $customfield['auction_dates_to'][0] == 'required' ? 'required' : '';
		$required_html_dates_to = $customfield['auction_dates_to'][0] == 'required' ? '<span class="required">* </span>' : '';
		
		echo '<div class="form-field auction_dates_fields">
				<label for="_auction_dates_from">' . $required_html_dates_from . __( 'Auction Date from', 'wc_simple_auctions' ) . '</label>
				<input ' . $required_dates_from . ' type="text" class=" bf_datetime_custom " name="_auction_dates_from" id="_auction_dates_from" value="' . $auction_dates_from . '" placeholder="' . _x( 'From&hellip;', 'placeholder', 'wc_simple_auctions' ) . ' YYYY-MM-DD HH:MM" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
				<label for="_auction_dates_to">' . $required_html_dates_to . __( 'Auction Date to', 'wc_simple_auctions' ) . '</label>
				<input ' . $required_dates_to . ' type="text" class="bf_datetime_custom " name="_auction_dates_to" id="_auction_dates_to" value="' . $auction_dates_to . '" placeholder="' . _x( 'To&hellip;', 'placeholder', 'wc_simple_auctions' ) . '  YYYY-MM-DD HH:MM" maxlength="16" />
			</div>';
		
		do_action( 'woocommerce_product_options_auction' );
		
		echo "</div>";
		echo "</div>";
	}
	
	public function buddyforms_frontend_custom_intialization() {

//		wp_enqueue_script( 'frontend-bb-simple-auction', BF_WOO_SIMPLE_AUCTION_JS_PATH . 'integration.js', array( 'jquery' ) );
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
		if ( $post['type'] == 'woocommerce' ) {
			$this->save_with_woocommerce_field = true;
			$product_type                      = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );
			
			if ( $product_type == 'auction' ) {
				update_post_meta( $post_id, '_manage_stock', 'yes' );
				update_post_meta( $post_id, '_stock', '1' );
				update_post_meta( $post_id, '_backorders', 'no' );
				update_post_meta( $post_id, '_sold_individually', 'yes' );
				update_post_meta( $post_id, '_auction_item_condition', stripslashes( $_POST['_auction_item_condition'] ) );
				update_post_meta( $post_id, '_auction_type', stripslashes( $_POST['_auction_type'] ) );
				if ( isset( $_POST['_auction_proxy'] ) ) {
					update_post_meta( $post_id, '_auction_proxy', stripslashes( $_POST['_auction_proxy'] ) );
				} else {
					delete_post_meta( $post_id, '_auction_proxy' );
				}
				update_post_meta( $post_id, '_auction_start_price', stripslashes( $_POST['_auction_start_price'] ) );
				update_post_meta( $post_id, '_auction_bid_increment', stripslashes( $_POST['_auction_bid_increment'] ) );
				update_post_meta( $post_id, '_auction_reserved_price', stripslashes( $_POST['_auction_reserved_price'] ) );
				update_post_meta( $post_id, '_regular_price', stripslashes( $_POST['_regular_price'] ) );
				update_post_meta( $post_id, '_auction_dates_from', stripslashes( $_POST['_auction_dates_from'] ) );
				update_post_meta( $post_id, '_auction_dates_to', stripslashes( $_POST['_auction_dates_to'] ) );
				//echo get_post_meta($post_id, '_stock',true);
			}
		}
	}
	
	public function buddyforms_product_save_data_after( $post_id ) {
		if ( $this->save_with_woocommerce_field ) {
			global $post;
			if ( function_exists( 'wc_get_product' ) ) {
				$product = wc_get_product( $post_id );
				if ( ! empty( $product ) && is_object( $product ) && $product->is_type( 'auction' ) ) {
					// do something with external products
					update_post_meta( $post_id, '_manage_stock', 'yes' );
					update_post_meta( $post_id, '_stock', '1' );
					update_post_meta( $post_id, '_backorders', 'no' );
					update_post_meta( $post_id, '_sold_individually', 'yes' );
				}
			}
		}
	}
}