<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     15.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

echo apply_filters(
	'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
	sprintf(
		'<a href="%s" data-quantity="%s" class="kivicare-box-shadow kivicare-morden-btn ajax_add_to_cart add_to_cart_button" %s>%s</a>',
		esc_url( $product->add_to_cart_url() ),
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		'<svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path fill-rule="evenodd" clip-rule="evenodd" d="M4.27385 4.95616L4.77635 10.9328C4.81302 11.3928 5.18885 11.7378 5.64802 11.7378H5.65135H14.7439H14.7455C15.1797 11.7378 15.5505 11.4145 15.6122 10.9853L16.4039 5.51949C16.4222 5.38949 16.3897 5.25949 16.3105 5.15449C16.2322 5.04866 16.1172 4.98033 15.9872 4.96199C15.813 4.96866 8.58552 4.95866 4.27385 4.95616ZM5.64631 12.9878C4.54881 12.9878 3.61964 12.1311 3.53047 11.0353L2.76714 1.95695L1.51131 1.74028C1.17048 1.68028 0.942975 1.35778 1.00131 1.01695C1.06131 0.676117 1.39047 0.45445 1.72381 0.507784L3.45714 0.807784C3.73631 0.85695 3.94881 1.08862 3.97297 1.37195L4.16881 3.70612C16.0655 3.71112 16.1038 3.71695 16.1613 3.72362C16.6255 3.79112 17.0338 4.03362 17.3121 4.40695C17.5905 4.77945 17.7071 5.23862 17.6405 5.69862L16.8496 11.1636C16.7005 12.2036 15.7971 12.9878 14.7471 12.9878H14.743H5.65297H5.64631Z" fill="#130F26"/>
		<path fill-rule="evenodd" clip-rule="evenodd" d="M13.4077 8.03638H11.0977C10.7518 8.03638 10.4727 7.75638 10.4727 7.41138C10.4727 7.06638 10.7518 6.78638 11.0977 6.78638H13.4077C13.7527 6.78638 14.0327 7.06638 14.0327 7.41138C14.0327 7.75638 13.7527 8.03638 13.4077 8.03638Z" fill="#130F26"/>
		<path fill-rule="evenodd" clip-rule="evenodd" d="M5.28815 15.2516C5.53898 15.2516 5.74148 15.4541 5.74148 15.7049C5.74148 15.9558 5.53898 16.1591 5.28815 16.1591C5.03648 16.1591 4.83398 15.9558 4.83398 15.7049C4.83398 15.4541 5.03648 15.2516 5.28815 15.2516Z" fill="#130F26"/>
		<path fill-rule="evenodd" clip-rule="evenodd" d="M5.28732 16.784C4.69232 16.784 4.20898 16.2998 4.20898 15.7048C4.20898 15.1098 4.69232 14.6265 5.28732 14.6265C5.88232 14.6265 6.36648 15.1098 6.36648 15.7048C6.36648 16.2998 5.88232 16.784 5.28732 16.784" fill="#130F26"/>
		<path fill-rule="evenodd" clip-rule="evenodd" d="M14.6877 16.784C14.0927 16.784 13.6094 16.2998 13.6094 15.7048C13.6094 15.1098 14.0927 14.6265 14.6877 14.6265C15.2835 14.6265 15.7677 15.1098 15.7677 15.7048C15.7677 16.2998 15.2835 16.784 14.6877 16.784" fill="#130F26"/>
		</svg>'
	),
	$product,
	$args,
);


