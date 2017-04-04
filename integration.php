<?php
/*
 Plugin Name: Buddyforms Woocommerce Simple Auctions Integration
 Plugin URI: http://buddyforms.com/downloads/buddyforms-woocommerce-simple-auctions/
 Description: This plugin adds woocommerce simple auctions fields to frontend buddypress profile interface using buddyforms
 Author: ThemeKraft
 Author URI: https://themekraft.com/buddyforms/
 Version: 1.0.3.1
 License: GPLv2 or later
 Network: false
 */

add_filter( 'buddyforms_formbuilder_fields_options', 'buddyforms_simple_auctions_add_wc_form_element_tab', 2, 3 );
function buddyforms_simple_auctions_add_wc_form_element_tab( $form_fields, $field_type, $field_id ) {
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

function buddyforms_product_write_panel( $thepostid, $customfield ) {
	global $post;

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

	echo '	<div class="form-field auction_dates_fields">
				<label for="_auction_dates_from">' . $required_html_dates_from . __( 'Auction Date from', 'wc_simple_auctions' ) . '</label>
				<input ' . $required_dates_from . ' type="text" class=" bf_datetime_custom " name="_auction_dates_from" id="_auction_dates_from" value="' . $auction_dates_from . '" placeholder="' . _x( 'From&hellip;', 'placeholder', 'wc_simple_auctions' ) . ' YYYY-MM-DD HH:MM" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
				<label for="_auction_dates_to">' . $required_html_dates_to . __( 'Auction Date to', 'wc_simple_auctions' ) . '</label>
				<input ' . $required_dates_to . ' type="text" class="bf_datetime_custom " name="_auction_dates_to" id="_auction_dates_to" value="' . $auction_dates_to . '" placeholder="' . _x( 'To&hellip;', 'placeholder', 'wc_simple_auctions' ) . '  YYYY-MM-DD HH:MM" maxlength="16" />
			</div>';

	do_action( 'woocommerce_product_options_auction' );

	echo "</div>";
	echo "</div>";
}

add_action( 'bf_woocommerce_product_options_general_last', 'buddyforms_product_write_panel', 10, 2 );

function buddyforms_frontend_custom_intialization() {

	wp_enqueue_script( 'frontend-bb-simple-auction', plugins_url( 'integration.js', __FILE__ ), array( 'jquery' ) );
}

add_action( 'buddyforms_front_js_css_enqueue', 'buddyforms_frontend_custom_intialization', 3 );

/**
 * Saves the data inputed into the product boxes, as post meta data
 *
 *
 * @param int $post_id the post (product) identifier
 * @param stdClass $post the post (product)
 *
 */
function buddyforms_product_save_data( $post, $post_id ) {
	global $wpdb, $woocommerce, $woocommerce_errors;
	$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

	if (
		$product_type == 'auction'
	) {
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

add_action( 'buddyforms_update_post_meta', 'buddyforms_product_save_data', 99, 2 );

function buddyforms_product_save_data_after( $post_id ) {
	global $post;
	if ( function_exists( 'get_product' ) && function_exists( 'woo_simple_auction_required' ) ) {
		$product = get_product( $post_id );
		if ( $product->is_type( 'auction' ) ) {
			// do something with external products
			update_post_meta( $post_id, '_manage_stock', 'yes' );
			update_post_meta( $post_id, '_stock', '1' );
			update_post_meta( $post_id, '_backorders', 'no' );
			update_post_meta( $post_id, '_sold_individually', 'yes' );
		}
	}
}

add_action( 'buddyforms_after_save_post', 'buddyforms_product_save_data_after', 100, 1 );

//
// Check the plugin dependencies
//
add_action('init', function(){

	// Only Check for requirements in the admin
	if(!is_admin()){
		return;
	}

	// Require TGM
	require ( dirname(__FILE__) . '/includes/resources/tgm/class-tgm-plugin-activation.php' );

	// Hook required plugins function to the tgmpa_register action
	add_action( 'tgmpa_register', function(){

		// Create the required plugins array
		$plugins['woocommerce'] = array(
			'name'     => 'WooCommerce',
			'slug'     => 'woocommerce',
			'required' => true,
		);

		if ( ! defined( 'BUDDYFORMS_PRO_VERSION' ) ) {
			$plugins['buddyforms'] = array(
				'name'      => 'BuddyForms',
				'slug'      => 'buddyforms',
				'required'  => true,
			);
		}

		$config = array(
			'id'           => 'buddyforms-tgmpa',  // Unique ID for hashing notices for multiple instances of TGMPA.
			'parent_slug'  => 'plugins.php',       // Parent menu slug.
			'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                // Show admin notices or not.
			'dismissable'  => false,               // If false, a user cannot dismiss the nag message.
			'is_automatic' => true,                // Automatically activate plugins after installation or not.
		);

		// Call the tgmpa function to register the required plugins
		tgmpa( $plugins, $config );

	} );
}, 1, 1);