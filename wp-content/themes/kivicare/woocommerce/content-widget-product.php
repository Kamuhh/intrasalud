<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 15.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
} ?>
<li class="kivicare-top-product-list">

	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<?php echo '<div class="kivicare-top-product-img">'.$product->get_image().'</div>'; ?>

	<div class="kivicare-top-product-wrapper">

		<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<span class="product-title">
				<?php echo wp_kses_post( $product->get_name() ); ?>
			</span>
		</a>

		<?php if ( ! empty( $show_rating ) ) : ?>
			<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
		<?php endif; ?>

		<?php echo wp_kses($product->get_price_html(), 'kivicare'); ?>

		<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>

	</div>

</li>
