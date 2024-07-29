<?php
/**
 * Custom Walker_Nav_Menu megamenu walker.
 *
 * @package Responsive Addons Pro
 */

if ( ! class_exists( 'Responsive_Custom_Nav_Walker' ) ) {

	/**
	 * Responsive_Custom_Nav_Walker description
	 */
	class Responsive_Custom_Nav_Walker extends Walker_Nav_Menu {

		/**
		 * Middle logo menu breaking point
		 *
		 * @access  private
		 * @var init
		 */
		private $break_point = null;

		/**
		 * Middle logo menu number of top level items displayed
		 *
		 * @access  private
		 * @var init
		 */
		private $displayed = 0;

		/**
		 * Prefix of megamenu postdata.
		 *
		 * @access  private
		 * @var init
		 */
		private $prefix = '_menu_item_megamenu_resp_';

		/**
		 * Enable/Disable MegaMenu.
		 *
		 * @access  private
		 * @var init
		 */
		private $megamenu;

		/**
		 * MegaMenu Width.
		 *
		 * @access  private
		 * @var init
		 */
		private $megamenu_width;

		/**
		 * MegaMenu Custom Width.
		 *
		 * @access  private
		 * @var init
		 */
		private $megamenu_custom_width;

		/**
		 * MegaMenu Icon Type - Upload or Library.
		 *
		 * @access  private
		 * @var init
		 */
		private $megamenu_menu_item_icon_type;

		/**
		 * MegaMenu Icon Upload.
		 *
		 * @access  private
		 * @var init
		 */
		private $megamenu_menu_item_icon_type_upload;

		/**
		 * MegaMenu Icon Position.
		 *
		 * @access  private
		 * @var init
		 */
		private $megamenu_menu_item_icon_position;

		/**
		 * MegaMenu Icon Size.
		 *
		 * @access  private
		 * @var init
		 */
		private $menu_item_icon_size;

		/**
		 * MegaMenu Icon Spacing.
		 *
		 * @access  private
		 * @var init
		 */
		private $menu_item_icon_spacing;

		/**
		 * MegaMenu HighLight Label.
		 *
		 * @access  private
		 * @var init
		 */

		private $highlight_label;

		/**
		 * MegaMenu HighLight Position.
		 *
		 * @access  private
		 * @var init
		 */
		private $highlight_position;

		/**
		 * MegaMenu HighLight Icon Color.
		 *
		 * @access  private
		 * @var init
		 */
		private $highlight_color;

		/**
		 * MegaMenu HighLight Icon Color.
		 *
		 * @access  private
		 * @var init
		 */
		private $highlight_bg_color;

		/**
		 * MegaMenu Background Color.
		 *
		 * @access  private
		 * @var init
		 */
		private $background_color;

		/**
		 * MegaMenu Background Image.
		 *
		 * @access  private
		 * @var init
		 */
		private $background_image;

		/**
		 * MegaMenu Background Image Position.
		 *
		 * @access  private
		 * @var init
		 */
		private $background_image_position;

		/**
		 * MegaMenu Background Image Size.
		 *
		 * @access  private
		 * @var init
		 */
		private $background_image_size;

		/**
		 * MegaMenu Background Image Repeat.
		 *
		 * @access  private
		 * @var init
		 */
		private $background_image_repeat;

		/**
		 * MegaMenu Background Image Repeat.
		 *
		 * @access  private
		 * @var init
		 */
		private $color_text_or_link;

		/**
		 * MegaMenu Sets the submenu as heading.
		 *
		 * @access  private
		 * @var init
		 */
		private $set_heading;

		/**
		 * MegaMenu Hide Menu Label.
		 *
		 * @access  private
		 * @var init
		 */
		private $hide_menu_label;

		/**
		 * MegaMenu Disable Link.
		 *
		 * @access  private
		 * @var init
		 */
		private $disable_link;

		/**
		 * MegaMenu Submenu Item Icon Type.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_item_icon_type;

		/**
		 * MegaMenu Submenu Item Uploaded Icon.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_item_icon_type_upload;

		/**
		 * MegaMenu Submenu Item Icon Position.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_item_icon_position;

		/**
		 * MegaMenu Submenu Item Icon Size.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_item_icon_size;

		/**
		 * MegaMenu Submenu Item Icon Spacing.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_item_icon_spacing;

		/**
		 * MegaMenu Submenu Highlight Label.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_highlight_label;

		/**
		 * MegaMenu Submenu Highlight Label Separator Color.
		 * Works only when megamenu of parent is active.
		 *
		 * @access  private
		 * @var init
		 */
		private $set_heading_separator_color;

		/**
		 * MegaMenu Submenu Highlight Label Position.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_highlight_label_position;

		/**
		 * MegaMenu Submenu Highlight Label Color.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_highlight_label_color;

		/**
		 * MegaMenu Submenu Highlight Label Background Color.
		 *
		 * @access  private
		 * @var init
		 */
		private $submenu_highlight_label_bg_color;

		/**
		 * MegaMenu Submenu Content Source Type.
		 *
		 * @access  private
		 * @var init
		 */
		private $content_source;

		/**
		 * MegaMenu Submenu Content Source Type.
		 *
		 * @access  private
		 * @var init
		 */
		private $custom_text;

		/**
		 * Starts the list before the elements are added.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu().
		 * @return void          [description].
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );

			if ( 0 === $depth && '' !== $this->megamenu ) {
				$main_menu_style = '';

				if ( '' !== $this->background_color && 'none' === $this->background_image ) {
					$main_menu_style = 'style="background-color: ' . $this->background_color . '"';
				}

				$this->background_image_position = '' === $this->background_image_position ? 'left top' : $this->background_image_position;
				$this->background_image_size     = '' === $this->background_image_size ? 'auto' : $this->background_image_size;
				$this->background_image_repeat   = '' === $this->background_image_repeat ? 'no-repeat' : $this->background_image_repeat;

				if ( '' === $this->background_color && 'none' !== $this->background_image ) {
					$main_menu_style = 'style="background-image: url(\'' . $this->background_image . '\'); background-repeat: ' . $this->background_image_repeat . '; background-position: ' . $this->background_image_position . '; background-size: ' . $this->background_image_size . '"';
				}

				if ( '' !== $this->background_color && 'none' !== $this->background_image ) {
					$main_menu_style = 'style="background-image: linear-gradient(to right, ' . $this->background_color . ', ' . $this->background_color . ' ), url(\'' . $this->background_image . '\'); background-repeat: ' . $this->background_image_repeat . '; background-position: ' . $this->background_image_position . '; background-size: ' . $this->background_image_size . '"';
				}

				if ( 'full' === $this->megamenu_width ) {
					$output         .= "\n$indent<div class=\"responsive-full-megamenu-wrapper\" $main_menu_style>\n";
					$main_menu_style = '';
				}

				$output .= "\n$indent<ul class=\"responsive-megamenu sub-menu" . ( 'full' === $this->megamenu_width ? ' responsive-megamenu-bg-transparent' : '' ) . "\" $main_menu_style>\n";

			} else {
				$output .= "\n$indent<ul class=\"sub-menu responsive-megamenu-child\">\n";
			}
		}

		/**
		 * Modified the menu output.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu().
		 * @param int    $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $wp_query;
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			// Set some vars.
			if ( 0 === $depth ) {
				$this->megamenu                            = get_post_meta( $item->ID, $this->prefix . 'enable_megamenu', true );
				$this->megamenu_width                      = get_post_meta( $item->ID, $this->prefix . 'megamenu_width', true );
				$this->megamenu_custom_width               = get_post_meta( $item->ID, $this->prefix . 'megamenu_custom_width', true );
				$this->megamenu_menu_item_icon_type        = get_post_meta( $item->ID, $this->prefix . 'menu_item_icon_type', true );
				$this->megamenu_menu_item_icon_type        = get_post_meta( $item->ID, $this->prefix . 'menu_item_icon_type', true );
				$this->megamenu_menu_item_icon_type_upload = get_post_meta( $item->ID, $this->prefix . 'menu_item_icon_type_upload', true );
				$this->megamenu_menu_item_icon_position    = get_post_meta( $item->ID, $this->prefix . 'menu_item_icon_position', true );
				$this->menu_item_icon_size                 = get_post_meta( $item->ID, $this->prefix . 'menu_item_icon_size', true );
				$this->menu_item_icon_spacing              = get_post_meta( $item->ID, $this->prefix . 'menu_item_icon_spacing', true );
				$this->highlight_label                     = get_post_meta( $item->ID, $this->prefix . 'highlight_label', true );
				$this->highlight_position                  = get_post_meta( $item->ID, $this->prefix . 'highlight_position', true );
				$this->highlight_color                     = get_post_meta( $item->ID, $this->prefix . 'highlight_color', true );
				$this->highlight_bg_color                  = get_post_meta( $item->ID, $this->prefix . 'highlight_bg_color', true );
				$this->background_color                    = get_post_meta( $item->ID, $this->prefix . 'background_color', true );
				$this->background_image                    = get_post_meta( $item->ID, $this->prefix . 'background_image', true );
				$this->background_image_position           = get_post_meta( $item->ID, $this->prefix . 'background_image_position', true );
				$this->background_image_size               = get_post_meta( $item->ID, $this->prefix . 'background_image_size', true );
				$this->background_image_repeat             = get_post_meta( $item->ID, $this->prefix . 'background_image_repeat', true );
				$this->color_text_or_link                  = get_post_meta( $item->ID, $this->prefix . 'color_text_or_link', true );
			} else {
				$this->set_heading                      = get_post_meta( $item->ID, $this->prefix . 'set_heading', true );
				$this->hide_menu_label                  = get_post_meta( $item->ID, $this->prefix . 'hide_menu_label', true );
				$this->disable_link                     = get_post_meta( $item->ID, $this->prefix . 'disable_link', true );
				$this->submenu_item_icon_type           = get_post_meta( $item->ID, $this->prefix . 'submenu_item_icon_type', true );
				$this->submenu_item_icon_type_upload    = get_post_meta( $item->ID, $this->prefix . 'submenu_item_icon_type_upload', true );
				$this->submenu_item_icon_position       = get_post_meta( $item->ID, $this->prefix . 'submenu_item_icon_position', true );
				$this->submenu_item_icon_size           = get_post_meta( $item->ID, $this->prefix . 'submenu_item_icon_size', true );
				$this->submenu_item_icon_spacing        = get_post_meta( $item->ID, $this->prefix . 'submenu_item_icon_spacing', true );
				$this->submenu_highlight_label          = get_post_meta( $item->ID, $this->prefix . 'submenu_highlight_label', true );
				$this->set_heading_separator_color      = get_post_meta( $item->ID, $this->prefix . 'set_heading_separator_color', true );
				$this->submenu_highlight_label_position = get_post_meta( $item->ID, $this->prefix . 'submenu_highlight_label_position', true );
				$this->submenu_highlight_label_color    = get_post_meta( $item->ID, $this->prefix . 'submenu_highlight_label_color', true );
				$this->submenu_highlight_label_bg_color = get_post_meta( $item->ID, $this->prefix . 'submenu_highlight_label_bg_color', true );
				$this->content_source                   = get_post_meta( $item->ID, $this->prefix . 'content_source', true );
				$this->custom_text                      = get_post_meta( $item->ID, $this->prefix . 'custom_text', true );
			}
			// Set up empty variable.
			$class_names = '';

			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			// Add class if Mega menu has children.
			if ( 0 === $depth && $this->has_children && $this->megamenu ) {
				$classes[] = 'responsive-megamenu-parent responsive-megamenu-li responsive-megamenu-width-' . $this->megamenu_width;
			}

			/**
			 * Filters the arguments for a single nav menu item.
			 *
			 * @since 4.4.0
			 *
			 * @param stdClass $args  An object of wp_nav_menu() arguments.
			 * @param WP_Post  $item  Menu item data object.
			 * @param int      $depth Depth of menu item. Used for padding.
			 */
			$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

			/**
			 * Filters the CSS class(es) applied to a menu item's list item element.
			 *
			 * @since 3.0.0
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
			 * @param WP_Post  $item    The current menu item.
			 * @param stdClass $args    An object of wp_nav_menu() arguments.
			 * @param int      $depth   Depth of menu item. Used for padding.
			 */
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			/**
			 * Filters the ID applied to a menu item's list item element.
			 *
			 * @since 3.0.1
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
			 * @param WP_Post  $item    The current menu item.
			 * @param stdClass $args    An object of wp_nav_menu() arguments.
			 * @param int      $depth   Depth of menu item. Used for padding.
			 */
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$custom_width_attr = '';
			if ( 0 === $depth && $this->has_children && $this->megamenu && 'custom' === $this->megamenu_width && '' !== $this->megamenu_custom_width ) {
				$custom_width_attr = ' data-custom-width="' . esc_attr( $this->megamenu_custom_width ) . '"';
			}

			$output .= $indent . '<li' . $id . $custom_width_attr . $class_names . '>';

			$atts           = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
			$atts['href']   = ! empty( $item->url ) ? $item->url : '';

			/**
			 * Filters the HTML attributes applied to a menu item's anchor element.
			 *
			 * @since 3.6.0
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param array $atts {
			 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
			 *
			 *     @type string $title  Title attribute.
			 *     @type string $target Target attribute.
			 *     @type string $rel    The rel attribute.
			 *     @type string $href   The href attribute.
			 * }
			 * @param WP_Post  $item  The current menu item.
			 * @param stdClass $args  An object of wp_nav_menu() arguments.
			 * @param int      $depth Depth of menu item. Used for padding.
			 */
			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					if ( $depth > 0 && $this->disable_link && 'href' === $attr ) {
						$value = 'javascript:void(0)';
					} else {
						$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					}
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			/** This filter is documented in wp-includes/post-template.php */
			$title = apply_filters( 'the_title', $item->title, $item->ID );

			/**
			 * Filters a menu item's title.
			 *
			 * @since 4.4.0
			 *
			 * @param string   $title The menu item's title.
			 * @param WP_Post  $item  The current menu item.
			 * @param stdClass $args  An object of wp_nav_menu() arguments.
			 * @param int      $depth Depth of menu item. Used for padding.
			 */
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$link_after  = '';
			$link_before = '';

			if ( 0 === $depth && 'upload' === $this->megamenu_menu_item_icon_type ) {

				$this->menu_item_icon_size    = '' === $this->menu_item_icon_size ? 20 : $this->menu_item_icon_size;
				$this->menu_item_icon_spacing = '' === $this->menu_item_icon_spacing ? 5 : $this->menu_item_icon_spacing;

				$menu_icon_spacing = 'style="margin: ' . $this->menu_item_icon_spacing . 'px"';

				$icon_position = ( 'left' === $this->megamenu_menu_item_icon_position || '' === $this->megamenu_menu_item_icon_position ) ? 'before' : 'after';
				$link_icon     = "<img src='{$this->megamenu_menu_item_icon_type_upload}' width='{$this->menu_item_icon_size}'{$menu_icon_spacing} >";

				if ( 'before' === $icon_position ) {
					$link_before = $link_icon;
				} else {
					$link_after = $link_icon;
				}
			}

			if ( $depth > 0 && 'upload' === $this->submenu_item_icon_type ) {

				$this->submenu_item_icon_size    = '' === $this->submenu_item_icon_size ? 20 : $this->submenu_item_icon_size;
				$this->submenu_item_icon_spacing = '' === $this->submenu_item_icon_spacing ? 5 : $this->submenu_item_icon_spacing;

				$submenu_icon_spacing = 'style="margin: ' . $this->submenu_item_icon_spacing . 'px"';

				$icon_position = ( 'left' === $this->submenu_item_icon_position || '' === $this->submenu_item_icon_position ) ? 'before' : 'after';
				$link_icon     = "<img src='{$this->submenu_item_icon_type_upload}' width='{$this->submenu_item_icon_size}'{$submenu_icon_spacing} >";

				if ( 'before' === $icon_position ) {
					$link_before = $link_icon;
				} else {
					$link_after = $link_icon;
				}
			}

			if ( 0 === $depth && '' !== $this->highlight_label ) {
				$highlight_icon_style = 'style="color: ' . $this->highlight_color . '; background-color: ' . $this->highlight_bg_color . '"';
				$highlight_icon       = '<span class="responsive-megamenu-hl-depth-zero" ' . $highlight_icon_style . '>' . $this->highlight_label . '</span>';
				if ( 'left' === $this->highlight_position || '' === $this->highlight_position ) {
					$link_before .= $highlight_icon;
				} else {
					$link_after .= $highlight_icon;
				}
			}

			if ( $depth > 0 && '' !== $this->submenu_highlight_label ) {
				$highlight_icon_style = 'style="color: ' . $this->submenu_highlight_label_color . '; background-color: ' . $this->submenu_highlight_label_bg_color . '"';
				$highlight_icon       = '<span class="responsive-megamenu-hl-depth-non-zero" ' . $highlight_icon_style . '>' . $this->submenu_highlight_label . '</span>';
				if ( 'left' === $this->submenu_highlight_label_position || '' === $this->submenu_highlight_label_position ) {
					$link_before .= $highlight_icon;
				} else {
					$link_after .= $highlight_icon;
				}
			}

			$item_output = $args->before;

			if ( $depth > 0 ) {
				$link_styles  = 'style="';
				$link_classes = 'menu-link';

				if ( $this->hide_menu_label ) {
					$link_styles .= 'display: none; ';
				} else {
					$this->set_heading_separator_color = '' === $this->set_heading_separator_color ? 'border: none' : 'border-bottom: 1px solid ' . $this->set_heading_separator_color;
					$disable_heading_hover             = 'responsive-megamenu-disable-link';

					if ( ! $this->set_heading ) {
						$this->set_heading_separator_color = '';
						$disable_heading_hover             = '';
					}

					if ( '' !== $this->megamenu && '' !== $this->color_text_or_link ) {
						$link_styles .= 'color: ' . $this->color_text_or_link . '; ';
					}

					$link_styles  .= $this->set_heading_separator_color . '"';
					$link_classes .= ' ' . $disable_heading_hover;
				}

				$item_output .= '<a ' . $link_styles . $attributes . ' class="' . $link_classes . '">';
			} else {
				$item_output .= '<a' . $attributes . ' class="menu-link">';
			}

			if ( 0 === $depth ) {
				if ( '' !== $args->link_before ) {
					$link_before = $args->link_before;
				}
				if ( '' !== $args->link_after ) {
					$link_after .= $args->link_after;
				}
			}

			$item_output .= $link_before . $title . $link_after;

			$item_output .= '</a>';

			if ( $this->megamenu && 'default' !== $this->content_source && '' !== $this->custom_text ) {
				$item_output .= '<div class="responsive-megamenu-custom-content responsive-megamenu-custom-text">' . $this->custom_text . '</div>';
			}

			$item_output .= $args->after;

			/*
			 * Filters a menu item's starting output.
			 *
			 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
			 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
			 * no filter for modifying the opening and closing `<li>` for a menu item.
			 *
			 */
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

		}

		/**
		 * Modified the menu end.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu().
		 */
		public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu().
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
		}

	}
}
