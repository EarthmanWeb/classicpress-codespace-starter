<?php
/**
 * Template Name: Default
 *
 * @package Responsive_Addons_For_Elementor
 */

use Elementor\Group_Control_Image_Size;
use Responsive_Addons_For_Elementor\Helper\Helper;
use Responsive_Addons_For_Elementor\WidgetsManager\Widgets\Woocommerce\Responsive_Addons_For_Elementor_Woo_Products;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$product = wc_get_product( get_the_ID() );

if ( ! $product ) {
	return;
}

if ( has_post_thumbnail() ) {
	$settings['rael_products_image_size_customize']      = array(
		'id' => get_post_thumbnail_id(),
	);
	$settings['rael_products_image_size_customize_size'] = $settings['rael_products_image_size_size'];
	$thumbnail_html                                      = Group_Control_Image_Size::get_attachment_image_html( $settings, 'rael_products_image_size_customize' );
}

$title_tag                = isset( $settings['rael_products_title_html_tag'] ) ? Helper::validate_html_tags( $settings['rael_products_title_html_tag'] ) : 'h2';
$should_print_compare_btn = isset( $settings['rael_products_show_compare'] ) && 'yes' === $settings['rael_products_show_compare'];

$grid_style_preset = isset( $settings['rael_products_style_preset'] ) ? $settings['rael_products_style_preset'] : '';
$list_style_preset = isset( $settings['rael_products_list_style_preset'] ) ? $settings['rael_products_list_style_preset'] : '';
$sale_badge_align  = isset( $settings['rael_products_sale_badge_alignment'] ) ? $settings['rael_products_sale_badge_alignment'] : '';
$sale_badge_preset = isset( $settings['rael_products_sale_badge_preset'] ) ? $settings['rael_products_sale_badge_preset'] : '';

$should_print_rating          = isset( $settings['rael_products_rating'] ) && 'yes' === $settings['rael_products_rating'];
$should_print_quick_view      = isset( $settings['rael_products_quick_view'] ) && 'yes' === $settings['rael_products_quick_view'];
$should_print_image_clickable = isset( $settings['rael_products_image_clickable'] ) && 'yes' === $settings['rael_products_image_clickable'];
$should_print_price           = isset( $settings['rael_products_price'] ) && 'yes' === $settings['rael_products_price'];
$should_print_excerpt         = isset( $settings['rael_products_excerpt'] ) && ( 'yes' === $settings['rael_products_excerpt'] && has_excerpt() );
$widget_id                    = isset( $settings['rael_widget_id'] ) ? $settings['rael_widget_id'] : null;

$stock_out_badge_text = ! empty( $settings['rael_products_stockout_text'] ) ? $settings['rael_products_stockout_text'] : __( 'Stock <br/> Out', 'responsive-addons-for-elementor' );
$sale_badge_text      = ! empty( $settings['rael_products_sale_static_text'] ) ? $settings['rael_products_sale_static_text'] : __( 'Sale!', 'responsive-addons-for-elementor' );

$quick_view_setting = array(
	'widget_id'  => $widget_id,
	'product_id' => $product->get_id(),
	'page_id'    => $settings['rael_page_id'],
);

if ( 'rael_product_simple' === $grid_style_preset || 'rael_product_reveal' === $grid_style_preset ) { ?>
	<li class="product">
		<div class="rael-products__product-wrapper">
			<?php

			if ( $should_print_image_clickable ) {
				echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
			}
			?>
			<?php
			echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'eager' ) ) );
			if ( $should_print_image_clickable ) {
				echo '</a>';
			}

			echo '<div class="rael-products__title">
            <a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
			printf( '<%1$s class="woocommerce-loop-product__title">%2$s</%1$s>', esc_html( $title_tag ), esc_html( $product->get_title() ) );
			echo '</a>
            </div>';

			if ( $should_print_rating ) {
				echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) );
			}
			if ( ! $product->is_in_stock() ) {
				printf( '<span class="rael-products__onsale rael-products__outofstock-badge %s %s">%s</span>', esc_attr( $sale_badge_preset ), esc_attr( $sale_badge_align ), esc_html( $stock_out_badge_text ) );
			} elseif ( $product->is_on_sale() ) {
				echo wp_kses_post( Helper::get_sale_badge_html( $product, $settings, $sale_badge_text, $sale_badge_preset, $sale_badge_align ) );
			}

			if ( $should_print_price ) {
				echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
			}
			?>
			<?php
			add_filter(
				'woocommerce_loop_add_to_cart_link',
				function ( $html, $product ) {
					if ( ! $product->is_in_stock() || $product->is_type( 'grouped' ) || $product->is_type( 'variable' ) ) {
						return '';
					}

					return $html;
				},
				10,
				2
			);

			woocommerce_template_loop_add_to_cart();
			if ( $should_print_compare_btn ) {
				Responsive_Addons_For_Elementor_Woo_Products::print_compare_button( $product->get_id() );
			}
			?>
		</div>
	</li>
	<?php
} elseif ( 'rael_product_overlay' === $grid_style_preset ) {
	?>
	<li class="product">
		<div class="rael-products__overlay">
			<?php
			if ( $should_print_image_clickable ) {
				echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
			}
			echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'eager' ) ) );
			if ( $should_print_image_clickable ) {
				echo '</a>';
			}
			?>
			<div class="rael-products__button-wrapper clearfix">
				<?php
					add_filter(
						'woocommerce_loop_add_to_cart_link',
						function ( $html, $product ) {
							if ( ! $product->is_in_stock() || $product->is_type( 'grouped' ) || $product->is_type( 'variable' ) ) {
								return '';
							}

							return $html;
						},
						10,
						2
					);

					woocommerce_template_loop_add_to_cart();

				if ( $should_print_compare_btn ) {
					Responsive_Addons_For_Elementor_Woo_Products::print_compare_button( $product->get_id(), 'icon' );
				}
				?>
			</div>
		</div>
		<?php

		echo '<div class="rael-products__title">
        <a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
		printf( '<%1$s class="woocommerce-loop-product__title">%2$s</%1$s>', esc_html( $title_tag ), esc_html( $product->get_title() ) );
		echo '</a>
        </div>';

		if ( $should_print_rating ) {
			echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) );
		}

		if ( ! $product->is_in_stock() ) {
			printf( '<span class="rael-products__onsale rael-products__outofstock-badge %s %s">%s</span>', esc_attr( $sale_badge_preset ), esc_attr( $sale_badge_align ), esc_html( $stock_out_badge_text ) );
		} elseif ( $product->is_on_sale() ) {
			echo wp_kses_post( Helper::get_sale_badge_html( $product, $settings, $sale_badge_text, $sale_badge_preset, $sale_badge_align ) );
		}

		if ( $should_print_price ) {
			echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
		}
		?>
	</li>
	<?php
} elseif ( ( 'rael_product_preset-5' === $grid_style_preset ) || ( 'rael_product_preset-6' === $grid_style_preset ) || ( 'rael_product_preset-7' === $grid_style_preset ) ) {
	if ( true === wc_get_loop_product_visibility( $product->get_id() ) || $product->is_visible() ) {
		?>
		<li <?php post_class( 'product' ); ?>>
			<div class="rael-products__product-wrapper">
				<div class="rael-products__product-image-wrapper">
					<div class="rael-products__image-wrapper">
						<?php
						if ( ! $product->is_in_stock() ) {
							echo '<span class="rael-products__onsale rael-products__outofstock ' . esc_attr( $sale_badge_preset ) . ' ' . esc_attr( $sale_badge_align ) . '">' . esc_html( $stock_out_badge_text ) . '</span>';
						} elseif ( $product->is_on_sale() ) {
							echo wp_kses_post( Helper::get_sale_badge_html( $product, $settings, $sale_badge_text, $sale_badge_preset, $sale_badge_align ) );
						}

						if ( $should_print_image_clickable ) {
							echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
						}
						echo wp_kses_post( $product->get_image( $settings['rael_products_image_size_size'], array( 'loading' => 'eager' ) ) );

						if ( $should_print_image_clickable ) {
							echo '</a>';
						}
						?>
					</div>
					<div class="rael-products__image-hover-wrapper">
						<?php if ( 'rael_product_preset-5' === $grid_style_preset ) { ?>
							<ul class="rael-products__icons-wrapper block-style">
								<?php if ( $should_print_quick_view ) { ?>
									<li class="rael-products__quick-view">
										<a id="rael_products_quick_view_<?php echo esc_attr( uniqid() ); ?>" data-quickview-setting="<?php echo esc_html( htmlspecialchars( wp_json_encode( $quick_view_setting ), ENT_QUOTES ) ); ?>" class="rael-products__open-popup open-popup-link">
											<i class="fas fa-eye"></i>
										</a>
									</li>
								<?php } ?>
								<?php
								if ( $should_print_compare_btn ) {
									echo '<li class="rael-products__add-to-compare">';
									Responsive_Addons_For_Elementor_Woo_Products::print_compare_button( $product->get_id(), 'icon' );
									echo '</li>';
								}

								if ( $product->is_in_stock() && ! $product->is_type( 'grouped' ) && ! $product->is_type( 'variable' ) ) :
									?>
								<li class="rael-products__add-to-cart">
									<?php

									add_filter(
										'woocommerce_loop_add_to_cart_link',
										function ( $html, $product ) {
											if ( ! $product->is_in_stock() || $product->is_type( 'grouped' ) || $product->is_type( 'variable' ) ) {
												return '';
											}

											return $html;
										},
										10,
										2
									);

									woocommerce_template_loop_add_to_cart();
									?>
								</li>
									<?php
								endif;
								?>
							</ul>
						<?php } elseif ( 'rael_product_preset-7' === $grid_style_preset ) { ?>
							<ul class="rael-products__icons-wrapper block-box-style">
								<li class="rael-products__add-to-cart">
									<?php

										add_filter(
											'woocommerce_loop_add_to_cart_link',
											function ( $html, $product ) {
												if ( ! $product->is_in_stock() || $product->is_type( 'grouped' ) || $product->is_type( 'variable' ) ) {
													return '';
												}

												return $html;
											},
											10,
											2
										);

										woocommerce_template_loop_add_to_cart();
									?>
									</li>
								<?php
								if ( $should_print_compare_btn ) {
									echo '<li class="rael-products__add-to-compare">';
									Responsive_Addons_For_Elementor_Woo_Products::print_compare_button( $product->get_id(), 'icon' );
									echo '</li>';
								}
								?>
								<?php if ( $should_print_quick_view ) { ?>
									<li class="rael-products__quick-view">
										<a id="rael_products_quick_view_<?php echo esc_attr( uniqid() ); ?>" data-quickview-setting="<?php echo esc_html( htmlspecialchars( wp_json_encode( $quick_view_setting ), ENT_QUOTES ) ); ?>" class="rael-products__open-popup open-popup-link">
											<i class="fas fa-eye"></i>
										</a>
									</li>
								<?php } ?>
							</ul>
						<?php } else { ?>
							<ul class="rael-products__icons-wrapper box-style">
								<li class="rael-products__add-to-cart">
									<?php

									add_filter(
										'woocommerce_loop_add_to_cart_link',
										function ( $html, $product ) {
											if ( ! $product->is_in_stock() || $product->is_type( 'grouped' ) || $product->is_type( 'variable' ) ) {
												return '';
											}

											return $html;
										},
										10,
										2
									);

									woocommerce_template_loop_add_to_cart();

									?>
								</li>
								<?php
								if ( $should_print_compare_btn ) {
									echo '<li class="rael-products__add-to-compare">';
									Responsive_Addons_For_Elementor_Woo_Products::print_compare_button( $product->get_id(), 'icon' );
									echo '</li>';
								}
								?>
								<?php if ( $should_print_quick_view ) { ?>
									<li class="rael-products__quick-view">
										<a id="rael_products_quick_view_<?php echo esc_attr( uniqid() ); ?>" data-quickview-setting="<?php echo esc_html( htmlspecialchars( wp_json_encode( $quick_view_setting ), ENT_QUOTES ) ); ?>" class="rael-products__open-popup open-popup-link">
											<i class="fas fa-eye"></i>
										</a>
									</li>
								<?php } ?>
							</ul>
							<?php
						}
						?>
					</div>
				</div>
				<div class="rael-products__product-details-wrapper">
					<?php
					if ( ( 'rael_product_preset-7' === $grid_style_preset ) && $should_print_price ) {
						echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
					}

					if ( $should_print_rating ) {
						echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) );
					}
					?>
					<div class="rael-products__title">
						<?php
						echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
						printf( '<%1$s>%2$s</%1$s>', esc_html( $title_tag ), esc_html( $product->get_title() ) );
						echo '</a>';
						?>
					</div>
					<?php
					if ( ( 'rael_product_preset-7' !== $grid_style_preset ) && $should_print_price ) {
						echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
					}
					?>
				</div>
			</div>
		</li>
		<?php
	}
} elseif ( 'rael_product_preset-8' === $grid_style_preset ) {
	if ( true === wc_get_loop_product_visibility( $product->get_id() ) || $product->is_visible() ) {
		?>
		<li <?php post_class( 'product' ); ?>>
			<div class="rael-products__product-wrapper">
				<div class="rael-products__product-image-wrapper">
					<div class="rael-products__image-wrapper">
						<?php
						if ( $should_print_image_clickable ) {
							echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
						}

						if ( ! $product->is_in_stock() ) {
							echo '<span class="rael-products__onsale rael-products__outofstock ' . esc_attr( $sale_badge_preset ) . ' ' . esc_attr( $sale_badge_align ) . '">' . esc_attr( $stock_out_badge_text ) . '</span>';
						} elseif ( $product->is_on_sale() ) {
							echo wp_kses_post( Helper::get_sale_badge_html( $product, $settings, $sale_badge_text, $sale_badge_preset, $sale_badge_align ) );
						}

						echo wp_kses_post( $product->get_image( $settings['rael_products_image_size_size'], array( 'loading' => 'eager' ) ) );

						if ( $should_print_image_clickable ) {
							echo '</a>';
						}
						?>
					</div>
					<div class="rael-products__image-hover-wrapper">
						<ul class="rael-products__icons-wrapper over-box-style">
							<li class="rael-products__add-to-cart">
								<?php

								add_filter(
									'woocommerce_loop_add_to_cart_link',
									function ( $html, $product ) {
										if ( ! $product->is_in_stock() || $product->is_type( 'grouped' ) || $product->is_type( 'variable' ) ) {
											return '';
										}

										return $html;
									},
									10,
									2
								);

								woocommerce_template_loop_add_to_cart();

								?>
							</li>
							<?php
							if ( $should_print_compare_btn ) {
								echo '<li class="rael-products__add-to-compare">';
								Responsive_Addons_For_Elementor_Woo_Products::print_compare_button( $product->get_id(), 'icon' );
								echo '</li>';
							}
							?>
							<?php if ( $should_print_quick_view ) { ?>
								<li class="rael-products__quick-view">
									<a id="rael_quick_view_<?php echo esc_html( uniqid() ); ?>" data-quickview-setting="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $quick_view_setting ), ENT_QUOTES ) ); ?>" class="rael-products__open-popup open-popup-link">
										<i class="fas fa-eye"></i>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="rael-products__product-details-wrapper">
					<?php
					if ( $should_print_price ) {
						echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
					}
					?>
					<div class="rael-products__title">
						<?php
							echo '<a href="' . esc_attr( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
							printf( '<%1$s>%2$s</%1$s>', esc_html( $title_tag ), esc_html( $product->get_title() ) );
							echo '</a>';
						?>
					</div>
				</div>
			</div>
		</li>
		<?php
	}
} elseif ( ( 'rael_product_list_preset-1' === $list_style_preset ) || ( 'rael_product_list_preset-2' === $list_style_preset ) || ( 'rael_product_list_preset-3' === $list_style_preset ) || ( 'rael_product_list_preset-4' === $list_style_preset ) ) {
	if ( true === wc_get_loop_product_visibility( $product->get_id() ) || $product->is_visible() ) {
		?>
		<li class="product <?php echo esc_attr( $list_style_preset ); ?>">
			<div class="rael-products__product-wrapper">
				<div class="rael-products__product-image-wrapper">
					<div class="rael-products__image-wrapper">
						<?php
						if ( $should_print_image_clickable ) {
							echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
						}

						if ( ! $product->is_in_stock() ) {
							echo '<span class="rael-products__onsale rael-products__outofstock ' . esc_attr( $sale_badge_preset ) . ' ' . esc_attr( $sale_badge_align ) . '">' . esc_html( $stock_out_badge_text ) . '</span>';
						} elseif ( $product->is_on_sale() ) {
							echo wp_kses_post( Helper::get_sale_badge_html( $product, $settings, $sale_badge_text, $sale_badge_preset, $sale_badge_align ) );
						}

						echo wp_kses_post( $product->get_image( $settings['rael_products_image_size_size'], array( 'loading' => 'eager' ) ) );

						if ( $should_print_image_clickable ) {
							echo '</a>';
						}
						?>
					</div>
				</div>
				<div class="rael-products__product-details-wrapper">
					<?php
					if ( 'rael_product_list_preset-2' === $list_style_preset ) {
						echo '<div class="rael-products__product-title">
                                                <a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
												printf( '<%1$s>%2$s</%1$s>', esc_html( $title_tag ), esc_html( $product->get_title() ) );
												echo '</a>
                                              </div>';
						if ( $should_print_excerpt ) {
							echo '<div class="rael-products__excerpt">';
							echo '<p>' . wp_kses_post( wp_trim_words( strip_shortcodes( get_the_excerpt() ), $settings['rael_products_excerpt_length'], $settings['rael_products_excerpt_expansion_indicator'] ) ) . '</p>';
							echo '</div>';
						}
						if ( $should_print_price ) {
							echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
						}

						if ( $should_print_rating ) {
							echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) );
						}
					} elseif ( 'rael_product_list_preset-3' === $list_style_preset ) {
						echo '<div class="rael-products__price-wrapper">';
						if ( $should_print_price ) {
							echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
						}
						if ( $should_print_rating ) {
							echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) );
						}
						echo '</div>
                            <div class="rael-products__title-wrapper">
                                <div class="rael-products__title">
                                  <a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
									printf( '<%1$s>%2$s</%1$s>', esc_html( $title_tag ), esc_html( $product->get_title() ) );
									echo '</a>
                                </div>';
						if ( $should_print_excerpt ) {
							echo '<div class="rael-products__excerpt">';
							echo '<p>' . wp_kses_post(
								wp_trim_words(
									strip_shortcodes(
										get_the_excerpt() ? get_the_excerpt() :
										get_the_content()
									),
									$settings['rael_products_excerpt_length'],
									$settings['rael_products_excerpt_expansion_indicator']
								)
							) . '</p>';
							echo '</div>';
						}
						echo '</div>';
					} elseif ( 'rael_product_list_preset-4' === $list_style_preset ) {

						if ( $should_print_rating ) {
							echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) );
						}

						echo '<div class="rael-products__title">
                                    <a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
									printf( '<%1$s>%2$s</%1$s>', esc_html( $title_tag ), esc_html( $product->get_title() ) );
									echo '</a>
                                    </div>';
						if ( $should_print_excerpt ) {
							echo '<div class="rael-products__excerpt">';
							echo '<p>' . wp_kses_post(
								wp_trim_words(
									strip_shortcodes(
										get_the_excerpt() ? get_the_excerpt() :
										get_the_content()
									),
									$settings['rael_products_excerpt_length'],
									$settings['rael_products_excerpt_expansion_indicator']
								)
							) . '</p>';
							echo '</div>';
						}
						if ( $should_print_price ) {
							echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
						}
					} else {
						echo '<div class="rael-products__title">
                                    <a href="' . esc_url( $product->get_permalink() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
									printf( '<%1$s>%2$s</%1$s>', esc_html( $title_tag ), esc_html( $product->get_title() ) );
									echo '</a>
                                    </div>';

						if ( $should_print_price ) {
							echo '<div class="rael-products__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
						}

						if ( $should_print_rating ) {
							echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) );
						}

						if ( $should_print_excerpt ) {
							echo '<div class="rael-products__excerpt">';
							echo '<p>' . wp_kses_post(
								wp_trim_words(
									strip_shortcodes(
										get_the_excerpt() ? get_the_excerpt() :
										get_the_content()
									),
									$settings['rael_products_excerpt_length'],
									$settings['rael_products_excerpt_expansion_indicator']
								)
							) . '</p>';
							echo '</div>';
						}
					}
					?>

					<ul class="rael-products__icons-wrapper <?php echo esc_attr( $settings['rael_products_action_buttons_preset'] ); ?>">
						<?php
						if ( $should_print_compare_btn ) {
							echo '<li class="rael-products__add-to-compare">';
							wp_kses_post( Responsive_Addons_For_Elementor_Woo_Products::print_compare_button( $product->get_id(), 'icon' ) );
							echo '</li>';
						}
						?>
						<li class="rael-products__add-to-cart">
						<?php
							woocommerce_template_loop_add_to_cart();
						?>
							</li>

						<?php
						if ( $should_print_quick_view ) {
							?>
							<li class="rael-products__quick-view">
								<a id="rael_products_quick_view_<?php echo esc_attr( uniqid() ); ?>" data-quickview-setting="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $quick_view_setting ), ENT_QUOTES ) ); ?>" class="rael-products__open-popup open-popup-link">
									<i class="fas fa-eye"></i>
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</li>
		<?php
	}
} else {

	if ( 'yes' !== $settings['rael_products_rating'] ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}

	add_action(
		'woocommerce_before_shop_loop_item_title',
		function () {
			global $product;
			if ( ! $product->is_in_stock() ) {
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
				echo '<span class="rael-products__outofstock-badge">' . esc_html__( 'Stock ', 'responsive-addons-for-elementor' ) . '<br />' . esc_html__( 'Out', 'responsive-addons-for-elementor' ) . '</span>';
			}
		},
		9
	);

	if ( $should_print_compare_btn ) {
		add_action( 'woocommerce_after_shop_loop_item', array( Responsive_Addons_For_Elementor_Woo_Products::class, 'print_compare_button' ) );
	}

	wc_get_template_part( 'content', 'product' );

	if ( $should_print_compare_btn ) {
		remove_action( 'woocommerce_after_shop_loop_item', array( Responsive_Addons_For_Elementor_Woo_Products::class, 'print_compare_button' ) );
	}
}
