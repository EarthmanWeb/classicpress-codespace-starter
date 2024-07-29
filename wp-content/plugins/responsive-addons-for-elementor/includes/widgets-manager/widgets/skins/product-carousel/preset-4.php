<?php
/**
 * Template Name: Preset 4
 *
 * @package Responsive_Addons_For_Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$product = wc_get_product( get_the_ID() );
if ( ! $product ) {
	error_log( '$product not found in ' . __FILE__ );
	return;
}

$sale_badge_align  = isset( $settings['rael_pc_sale_badge_alignment'] ) ? $settings['rael_pc_sale_badge_alignment'] : '';
$sale_badge_preset = isset( $settings['rael_pc_sale_badge_preset'] ) ? $settings['rael_pc_sale_badge_preset'] : '';
$sale_text         = ! empty( $settings['rael_pc_sale_text'] ) ? $settings['rael_pc_sale_text'] : 'Sale!';
$stockout_text     = ! empty( $settings['rael_pc_stockout_text'] ) ? $settings['rael_pc_stockout_text'] : 'Stock Out';

$show_rating          = isset( $settings['rael_pc_rating'] ) && 'yes' === $settings['rael_pc_rating'];
$show_quick_view      = isset( $settings['rael_pc_quick_view'] ) && 'yes' === $settings['rael_pc_quick_view'];
$show_image_clickable = isset( $settings['rael_pc_image_clickable'] ) && 'yes' === $settings['rael_pc_image_clickable'];
$show_price           = isset( $settings['rael_pc_price'] ) && 'yes' === $settings['rael_pc_price'];
$show_excerpt         = isset( $settings['rael_pc_excerpt'] ) && ( 'yes' === $settings['rael_pc_excerpt'] && has_excerpt() );
$widget_id            = isset( $settings['rael_pc_widget_id'] ) ? $settings['rael_pc_widget_id'] : null;
$quick_view_setting   = array(
	'widget_id'  => $widget_id,
	'product_id' => $product->get_id(),
	'page_id'    => $settings['rael_page_id'],
);

if ( true === wc_get_loop_product_visibility( $product->get_id() ) || $product->is_visible() ) {
	?>
	<li <?php post_class( array( 'product', 'swiper-slide' ) ); ?>>
		<div class="rael-product-carousel">
			<div class="rael-product-carousel__overlay"></div>
			<div class="rael-pc__product-image-wrapper">
				<div class="rael-pc__product-image">
					<?php
					echo ( ! $product->managing_stock() && ! $product->is_in_stock() ? '<span class="rael-pc__onsale rael-pc__out-of-stock ' . esc_attr( $sale_badge_preset ) . ' ' . esc_attr( $sale_badge_align ) . '">' . esc_html( $stockout_text ) . '</span>' : ( $product->is_on_sale() ? '<span class="rael-pc__onsale ' . esc_attr( $sale_badge_preset ) . ' ' . esc_attr( $sale_badge_align ) . '">' . esc_html( $sale_text ) . '</span>' : '' ) );
					if ( $show_image_clickable ) {
						echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
					}
					echo wp_kses_post( $product->get_image( $settings['rael_pc_image_size_size'], array( 'loading' => 'eager' ) ) );

					if ( $show_image_clickable ) {
						echo '</a>';
					}
					?>
				</div>
			</div>
			<div class="rael-pc__overlay-content">
				<div class="rael-pc__product-details-wrapper">
					<div class="rael-pc__product-details">
						<div class="rael-pc__product-title-wrapper">
							<?php
							if ( $settings['rael_pc_show_title'] ) {
								echo '<div class="rael-pc__product-title">';
								echo '<' . esc_attr( $settings['rael_pc_title_tag'] ) . '>';
								if ( empty( $settings['rael_pc_title_length'] ) ) {
									echo esc_html( $product->get_title() );
								} else {
									echo wp_kses_post( implode( ' ', array_slice( explode( ' ', $product->get_title() ), 0, $settings['rael_pc_title_length'] ) ) );
								}
								echo '</' . esc_attr( $settings['rael_pc_title_tag'] ) . '>';
								echo '</div>';
							}
							?>
							<?php
							if ( $show_rating ) {
								echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) );
							}
							?>
						</div>
						<?php
						if ( $show_price ) {
							echo '<div class="rael-pc__product-price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
						}
						?>
					</div>

					<?php
					if ( $show_excerpt ) {
						echo '<div class="rael-pc__excerpt">';
						echo '<p>' . wp_kses_post(
							wp_trim_words(
								strip_shortcodes( get_the_excerpt() ),
								$settings['rael_pc_excerpt_length'],
								$settings['rael_pc_excerpt_expansion_indicator']
							)
						) . '</p>';
						echo '</div>';
					}
					?>

					<div class="rael-pc__buttons-wrapper">
						<ul class="rael-pc__icons-wrapper rael-pc-box-style">
							<li class="add-to-cart"><?php woocommerce_template_loop_add_to_cart(); ?></li>
							<?php if ( $show_quick_view ) { ?>
								<li class="rael-pc__product-quick-view">
									<a id="rael_pc_quick_view_<?php echo esc_attr( uniqid() ); ?>" data-quickview-setting="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $quick_view_setting ), ENT_QUOTES ) ); ?>"
									class="open-popup-link">
										<i class="fas fa-eye"></i>
									</a>
								</li>
							<?php } ?>
							<li class="view-details" title="Details"><?php echo '<a href="' . esc_url( $product->get_permalink() ) . '"><i class="fas fa-link"></i></a>'; ?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</li>
	<?php
}
