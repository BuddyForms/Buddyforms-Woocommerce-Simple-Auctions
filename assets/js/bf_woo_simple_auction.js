/*
 * @package WordPress
 * @subpackage BuddyPress, Woocommerce, BuddyForms
 * @author ThemKraft Dev Team
 * @copyright 2017, Themekraft
 * @link http://buddyforms.com/downloads/buddyforms-woocommerce-form-elements/
 * @license GPLv2 or later
 */
jQuery(function ($) {
	var select_product_type = jQuery('select#product-type'),
		_auction_tab = $('#auction_tab'),
		_auction_item_condition = _auction_tab.find('#_auction_item_condition'),
		_auction_type = _auction_tab.find('#_auction_type'),
		_auction_proxy = _auction_tab.find('#_auction_proxy'),
		_auction_start_price = _auction_tab.find('#_auction_start_price'),
		_auction_start_price_field = _auction_tab.find('._auction_start_price_field'),
		_auction_bid_increment = _auction_tab.find('#_auction_bid_increment'),
		_auction_bid_increment_field = _auction_tab.find('._auction_bid_increment_field'),
		_auction_reserved_price = _auction_tab.find('#_auction_reserved_price'),
		_auction_reserved_price_field = _auction_tab.find('._auction_reserved_price_field'),
		_regular_price = _auction_tab.find('#_regular_price'),
		_regular_price_field = _auction_tab.find('._regular_price_field'),
		_auction_dates_from = _auction_tab.find('#_auction_dates_from'),
		_auction_dates_to = _auction_tab.find('#_auction_dates_to');

	function determine_when_is_required(current_type) {
		set_default_required();
		switch (current_type) {
			case 'auction':
				if (bf_woo_simple_auction._auction_start_price !== undefined && bf_woo_simple_auction._auction_start_price === 'required') {
					_auction_start_price.attr("required", true);
				}
				if (bf_woo_simple_auction._auction_bid_increment !== undefined && bf_woo_simple_auction._auction_bid_increment === 'required') {
					_auction_bid_increment.attr("required", true);
				}
				if (bf_woo_simple_auction._auction_reserved_price !== undefined && bf_woo_simple_auction._auction_reserved_price === 'required') {
					_auction_reserved_price.attr("required", true);
				}
				if (bf_woo_simple_auction._regular_price !== undefined && bf_woo_simple_auction._regular_price === 'required') {
					_regular_price.attr("required", true);
				}
				if (bf_woo_simple_auction.auction_dates_from !== undefined && bf_woo_simple_auction.auction_dates_from[0] !== undefined &&
					bf_woo_simple_auction.auction_dates_from[0] === 'required') {
					_auction_dates_from.attr("required", true);
				}
				if (bf_woo_simple_auction.auction_dates_to !== undefined && bf_woo_simple_auction.auction_dates_to[0] !== undefined &&
					bf_woo_simple_auction.auction_dates_to[0] === 'required') {
					_auction_dates_to.attr("required", true);
				}
				break;
		}
	}

	function set_default_required() {
		_auction_start_price.removeAttr('required');
		_auction_bid_increment.removeAttr('required');
		_auction_reserved_price.removeAttr('required');
		_regular_price.removeAttr('required');
		_auction_dates_from.removeAttr('required');
		_auction_dates_to.removeAttr('required');
	}

	//Trigger if the product type if changed
	jQuery(document).on('woocommerce-product-type-change', function (obj, select_val) {
		determine_when_is_required(select_val);
	});

	determine_when_is_required(select_product_type.val());

	//Item Condition
	if (bf_woo_simple_auction._auction_item_condition !== undefined && bf_woo_simple_auction._auction_item_condition !== 'display') {
		_auction_item_condition.val(bf_woo_simple_auction._auction_item_condition).change();
	}

	//Auction Type
	if (bf_woo_simple_auction._auction_type !== undefined && bf_woo_simple_auction._auction_type !== 'display') {
		_auction_type.val(bf_woo_simple_auction._auction_type).change();
	}

	//Auction Proxy
	if (bf_woo_simple_auction._auction_proxy !== undefined && bf_woo_simple_auction._auction_proxy !== 'display') {
		if (bf_woo_simple_auction._auction_proxy === 'enable') {
			_auction_proxy.attr('checked', true).change();
		}
		else{
			_auction_proxy.removeAttr('checked');
		}
	}

	//Start Price
	if (bf_woo_simple_auction._auction_start_price !== undefined && bf_woo_simple_auction._auction_start_price === 'hidden') {
		_auction_start_price_field.hide();
	}

	//Bid Increment
	if (bf_woo_simple_auction._auction_bid_increment !== undefined && bf_woo_simple_auction._auction_bid_increment === 'hidden') {
		_auction_bid_increment_field.hide();
	}

	//Reserve Price
	if (bf_woo_simple_auction._auction_reserved_price !== undefined && bf_woo_simple_auction._auction_reserved_price === 'hidden') {
		_auction_reserved_price_field.hide();
	}

	//Regular Price
	if (bf_woo_simple_auction._regular_price !== undefined && bf_woo_simple_auction._regular_price === 'hidden') {
		_regular_price_field.hide();
	}

});