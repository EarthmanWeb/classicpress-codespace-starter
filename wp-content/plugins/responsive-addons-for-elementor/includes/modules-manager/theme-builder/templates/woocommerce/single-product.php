<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * However, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see      https://docs.woocommerce.com/document/template-structure/
 * @author   Cyberchimps
 * @version  1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Hook for print notices.
 * woocommerce_before_single_product hook.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo wp_kses_post( get_the_password_form() );
	return;
}
global $post;
?>

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="rael-theme-single-product-content">
		<?php
			/**
			 * Hook for product content.
			 *
			 * @hooked get_product_content_elementor()
			 * @hooked get_product_default_data
			 *
			 * @since 1.8.0
			 *
			 * rael_template_woocommerce_product_content.
			 */
			do_action( 'rael_template_woocommerce_product_content', $post );
		?>
	</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
