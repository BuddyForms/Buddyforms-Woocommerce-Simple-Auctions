<?php
 /*
  Plugin Name: Buddyforms Woocommerce simple auctions integration
  Plugin URI: #
  Description: This plugin adds woocommerce simple auctions fields to frontend buddypress profile interface using buddyforms
  Author: Nitish Dhir, Sven Lehnert
  Author URI: http://themekraft.com/members/svenl77/
  License: GPLv2 or later
  Network: false
  */

add_filter('buddyforms_formbuilder_fields_options', 'buddyforms_simple_auctions_add_wc__form_element_tab', 2, 3);
function buddyforms_simple_auctions_add_wc__form_element_tab($form_fields,$field_type,$field_id){
	global $post;

	$buddyform = get_post_meta($post->ID, '_buddyforms_options', true);

	$hook_field_types = array(
		'WooCommerce',
		);

	if (!in_array($field_type, $hook_field_types))
		return $form_fields;

	// Item condition
	$item_condition = isset($buddyform['form_fields'][$field_id]['item_condition']) ? $buddyform['form_fields'][$field_id]['item_condition'] : 'display';
	$form_fields['Auctions']['item_condition']	= new Element_Select( '<b>' . __('Item Condition', 'buddyforms') . '</b>', "buddyforms_options[form_fields][".$field_id."][item_condition]", array( 'display '=> __('Display', 'buddyforms'), 'new' => __('New', 'buddyforms'), 'used'=> __('Used', 'buddyforms')), array('value' => $item_condition));

	// Auction Type
	$auction_type = isset($buddyform['form_fields'][$field_id]['auction_type']) ? $buddyform['form_fields'][$field_id]['auction_type'] : 'display';
	$form_fields['Auctions']['auction_type']	= new Element_Select( '<b>' . __('Auction Type', 'buddyforms') . '</b>', "buddyforms_options[form_fields][".$field_id."][auction_type]", array( 'display '=> __('Display', 'buddyforms'), 'normal'  => __('Normal', 'buddyforms'), 'reverse' =>__('Reverse', 'buddyforms') ), array('value' => $auction_type));

	// Auction Proxy
	$auction_proxy = isset($buddyform['form_fields'][$field_id]['auction_proxy']) ? $buddyform['form_fields'][$field_id]['auction_proxy'] : 'display';
	$form_fields['Auctions']['auction_proxy']	= new Element_Select( '<b>' . __('Auction Proxy', 'buddyforms') . '</b>', "buddyforms_options[form_fields][".$field_id."][auction_proxy]", array( 'display '=> __('Display', 'buddyforms'), 'enable'  => __('Enable', 'buddyforms'), 'disable' =>__('Disable', 'buddyforms') ), array('value' => $auction_proxy));

	// Start Price
	$start_price = isset($buddyform['form_fields'][$field_id]['start_price']) ? $buddyform['form_fields'][$field_id]['start_price'] : 'none';
	$form_fields['Auctions']['auction_proxy']	= new Element_Select( '<b>' . __('Start Price', 'buddyforms') . '</b>', "buddyforms_options[form_fields][".$field_id."][start_price]", array( 'none '=> __('Select', 'buddyforms'), 'required'  => __('Required', 'buddyforms'), 'hidden' =>__('Hidden', 'buddyforms') ), array('value' => $auction_proxy));

	$form_fields['Auctions']['start_price']		= new Element_Textbox( '<b>' . __('Start Price', 'buddyforms') . '</b>', "buddyforms_options[form_fields][".$field_id."][start_price]", array('value' => $start_price));

	return $form_fields;

}

 function buddyforms_product_write_panel(){
	 global $post;

	 $auction_item_condition   = array( 'new' => __('New', 'wc_simple_auctions'), 'used'=> __('Used', 'wc_simple_auctions') );
	 $auction_types            = array( 'normal'  => __('Normal', 'wc_simple_auctions'), 'reverse' =>__('Reverse', 'wc_simple_auctions') );

	 // Pull the video tab data out of the database
		$tab_data = maybe_unserialize(get_post_meta($post->ID, 'woo_auction_tab', true));
		if(empty($tab_data)){
			$tab_data[] = array();
		}
		echo '<div class="options_group auction_tab show_if_auction hide_if_grouped hide_if_external hide_if_variable hide_if_simple">';
		echo '<div id="auction_tab" class="panel ">';
		
		woocommerce_wp_select( array( 'id' => '_auction_item_condition', 'label' => __( 'Item condition', 'wc_simple_auctions' ), 'options' => apply_filters( 'simple_auction_item_condition',$auction_item_condition) ) );
		woocommerce_wp_select( array( 'id' => '_auction_type', 'label' => __( 'Auction type', 'wc_simple_auctions' ), 'options' => $auction_types ) );
		woocommerce_wp_checkbox( array( 'id' => '_auction_proxy', 'wrapper_class' => '', 'label' => __('Proxy bidding?', 'wc_simple_auctions' ), 'description' => __( 'Enable proxy bidding', 'wc_simple_auctions' ), 'cbvalue'=>'yes', 'checked'=>'true' ) );
		woocommerce_wp_text_input( array( 'id' => '_auction_start_price', 'class' => 'wc_input_price short', 'label' => __( 'Start Price', 'wc_simple_auctions' ) . ' ('.get_woocommerce_currency_symbol().')', 'type' => 'number', 'custom_attributes' => array(
			'step' 	=> 'any',
			'min'	=> '0'
		) ) );
		woocommerce_wp_text_input( array( 'id' => '_auction_bid_increment', 'class' => 'wc_input_price short', 'label' => __( 'Bid increment', 'wc_simple_auctions' ) . ' ('.get_woocommerce_currency_symbol().')', 'type' => 'number', 'custom_attributes' => array(
			'step' 	=> 'any',
			'min'	=> '0'
		) ) );
		woocommerce_wp_text_input( array( 'id' => '_auction_reserved_price', 'class' => 'wc_input_price short', 'label' => __( 'Reserve price', 'wc_simple_auctions' ) . ' ('.get_woocommerce_currency_symbol().')', 'type' => 'number', 'custom_attributes' => array(
			'step' 	=> 'any',
			'min'	=> '0'
		),'desc_tip' => 'true', 
								'description' => __( 'A reserve price is the lowest price at which you are willing to sell your item. If you don’t want to sell your item below a certain price, you can set a reserve price. The amount of your reserve price is not disclosed to your bidders, but they will see that your auction has a reserve price and whether or not the reserve has been met. If a bidder does not meet that price, you are not obligated to sell your item. ', 'wc_simple_auctions' ) ) );
		woocommerce_wp_text_input( 
								array( 
								'id' => '_regular_price', 
								'class' => 'wc_input_price short', 
								'label' => __( 'Buy it now price', 'wc_simple_auctions' ) . ' ('.get_woocommerce_currency_symbol().')', 
								'type' => 'number', 
								'custom_attributes' => array('step' => 'any', 'min'	=> '0'),
								'desc_tip' => 'true', 
								'description' => __( 'Buy it now disappears when bid exceeds the Buy now price for normal auction, or is lower than reverse auction', 'wc_simple_auctions' ) 
		 ) );
		
		$auction_dates_from 	= ( $date = get_post_meta( $post->ID, '_auction_dates_from', true ) ) ?  $date  : '';
		$auction_dates_to 	= ( $date = get_post_meta( $post->ID, '_auction_dates_to', true ) ) ?  $date  : '';
						
		echo '	<div class="form-field auction_dates_fields">
					<label for="_auction_dates_from">' . __( 'Auction Dates', 'wc_simple_auctions' ) . '</label>
					<input type="text" class=" bf_datetime_custom " name="_auction_dates_from" id="_auction_dates_from" value="' . $auction_dates_from . '" placeholder="' . _x( 'From&hellip;', 'placeholder', 'wc_simple_auctions' ) . ' YYYY-MM-DD HH:MM" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
					<input type="text" class="bf_datetime_custom " name="_auction_dates_to" id="_auction_dates_to" value="' . $auction_dates_to . '" placeholder="' . _x( 'To&hellip;', 'placeholder', 'wc_simple_auctions' ) . '  YYYY-MM-DD HH:MM" maxlength="16" />
				</div>';
				
		do_action( 'woocommerce_product_options_auction' );
				
		echo "</div>";
		echo "</div>";
	}
add_action('bf_woocommerce_product_options_general_last','buddyforms_product_write_panel');
	
function buddyforms_frontend_custom_intialization(){
	wp_enqueue_script( 'frontend-bb-simple-auction',plugins_url('integration.js', __FILE__) );
}
add_action('buddyforms_front_js_css_enqueue','buddyforms_frontend_custom_intialization',3);

/**
 * Saves the data inputed into the product boxes, as post meta data
 * 
 * 
 * @param int $post_id the post (product) identifier
 * @param stdClass $post the post (product)
 * 
 */
function buddyforms_product_save_data($post,$post_id){
	global $wpdb, $woocommerce, $woocommerce_errors;
	$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

if (
 $product_type == 'auction' ) {
 	update_post_meta( $post_id, '_manage_stock', 'yes'  );
 	update_post_meta( $post_id, '_stock', '1'  );
 	update_post_meta( $post_id, '_backorders', 'no'  );
	update_post_meta( $post_id, '_sold_individually', 'yes'  );
	update_post_meta( $post_id, '_auction_item_condition', stripslashes( $_POST['_auction_item_condition'] ) );
	update_post_meta( $post_id, '_auction_type', stripslashes( $_POST['_auction_type'] ) );
	if(isset($_POST['_auction_proxy'])){
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
add_action('buddyforms_update_post_meta','buddyforms_product_save_data', 99, 2);

function buddyforms_product_save_data_after($post_id){
	global $post;
	if( function_exists('get_product') ){
		$product = get_product( $post_id );
		if( $product->is_type( 'auction' ) ){
			// do something with external products
			update_post_meta( $post_id, '_manage_stock', 'yes'  );
		 	update_post_meta( $post_id, '_stock', '1'  );
		 	update_post_meta( $post_id, '_backorders', 'no'  );
			update_post_meta( $post_id, '_sold_individually', 'yes'  );
		}
	}
}
add_action('buddyforms_after_save_post', 'buddyforms_product_save_data_after', 100, 1);
?>