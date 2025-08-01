<?php
/**
 * Order tracking form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/form-tracking.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 15.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;
?>

<div class="track-form-wrapper">
    <form action="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" method="post" class="woocommerce-form woocommerce-form-track-order track_order">

        <?php
        /**
         * Action hook fired at the beginning of the form-tracking form.
         *
         * @since 6.5.0
         */
        do_action( 'woocommerce_order_tracking_form_start' );
        ?>

	    <p><?php esc_html_e( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'kivicare' ); ?></p>


        <p class="form-row form-row-first"> <label for="orderid"><?php esc_html_e( 'Order ID', 'kivicare' ); ?> </label>
            <input class="input-text" type="text" name="orderid" id="orderid" value="<?php echo isset( $_REQUEST['orderid'] ) ? esc_attr( wp_unslash( $_REQUEST['orderid'] ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'Enter your order id. Found in your order confirmation email.', 'kivicare' ); ?>" />
        </p> <?php // @codingStandardsIgnoreLine ?>
        <p class="form-row form-row-last"> <label for="order_email"><?php esc_html_e( 'Billing email', 'kivicare' ); ?> </label>
            <input class="input-text" type="text" name="order_email" id="order_email" value="<?php echo isset( $_REQUEST['order_email'] ) ? esc_attr( wp_unslash( $_REQUEST['order_email'] ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'Email you used during checkout.', 'kivicare' ); ?>" />
        </p> <?php // @codingStandardsIgnoreLine ?>

        <div class="clear"></div>

        <?php
        /**
         * Action hook fired in the middle of the form-tracking form (before the submit button).
         *
         * @since 6.5.0
         */
        do_action( 'woocommerce_order_tracking_form' );
        ?>

        <p class="form-row track-btn mb-0">
            <button type="submit" class="button iq-new-btn-style iq-button-style-2 button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="track" value="<?php esc_attr_e( 'Track', 'kivicare' ); ?>">
                <span class="iq-btn-text-holder"><?php esc_html_e( 'Track Order', 'kivicare' ); ?></span>
                <span class="iq-btn-icon-holder"><i aria-hidden="true" class="ion ion-plus"></i></span>
            </button>
        </p>
        <?php wp_nonce_field( 'woocommerce-order_tracking', 'woocommerce-order-tracking-nonce' ); ?>

        <?php
        /**
         * Action hook fired at the end of the form-tracking form (after the submit button).
         *
         * @since 6.5.0
         */
        do_action( 'woocommerce_order_tracking_form_end' );
        ?>
    </form>
</div>

