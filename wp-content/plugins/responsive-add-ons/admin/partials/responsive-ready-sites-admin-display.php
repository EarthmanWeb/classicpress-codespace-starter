<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://cyberchimps.com/
 * @since      1.0.0
 *
 * @package    Responsive Ready Sites
 */

require_once RESPONSIVE_ADDONS_DIR . 'includes/class-responsive-add-ons-app-auth.php';
require_once RESPONSIVE_ADDONS_DIR . 'includes/settings/class-responsive-add-ons-settings.php';
$cc_app_auth = new Responsive_Add_Ons_App_Auth();
$settings    = get_option( 'reads_app_settings' );
$status      = new Responsive_Add_Ons_Settings();
$plan_status = $status->get_plan();
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="responsive-ready-site-preview"></div>
<div id="responsive-ready-site-pages-preview"></div>
<div id="responsive-ready-sites-import-options"></div>
<div id="responsive-ready-sites-import-progress"></div>
<div id="responsive-ready-sites-page-import-progress"></div>
<div id="responsive-ready-sites-import-done-congrats"></div>
<div id="responsive-ready-sites-admin-page">
	<?php if ( get_transient( 'responsive_ready_sites_display_connect_success' ) ) : ?>
		<div class="responsive-templates-app-auth-sucess-msg">
			<p class="auth-success-msg"><?php esc_html_e( 'Congratulations! Your website is now connected to Cyberchimps Responsive. You can start importing Templates.', 'responsive-addons' ); ?><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'responsive-addons' ); ?></span></button></p>
		</div>
		<?php delete_transient( 'responsive_ready_sites_display_connect_success' ); ?>
	<?php endif; ?>
	<div class="responsive-sites-header" id="responsive-sites-header">
		<span class="ready-site-list-title"><?php esc_html_e( 'Responsive Starter Templates', 'responsive-addons' ); ?></span>
	</div>
	<?php

	$business_subcategories   = array(
		array(
			'name' => 'Advertising & Marketing',
			'slug' => 'advertising-marketing',
		),
		array(
			'name' => 'Real Estate & Construction',
			'slug' => 'real-estate-construction',
		),
		array(
			'name' => 'Cars & Automotive',
			'slug' => 'cars-automotive',
		),
		array(
			'name' => 'Consulting & Coaching',
			'slug' => 'consulting-coaching',
		),
		array(
			'name' => 'Finance & Law',
			'slug' => 'finance-law',
		),
		array(
			'name' => 'Farming & Gardening',
			'slug' => 'farming-gardening',
		),
		array(
			'name' => 'Transport',
			'slug' => 'transport',
		),
		array(
			'name' => 'Pet & Animal',
			'slug' => 'pet-animal',
		),
		array(
			'name' => 'Architecture & Interior',
			'slug' => 'architecture-interior',
		),
		array(
			'name' => 'Technology & Apps',
			'slug' => 'technology-apps',
		),
	);
	$health_subcategories     = array(
		array(
			'name' => 'Doctor',
			'slug' => 'doctor',
		),
		array(
			'name' => 'Hospital',
			'slug' => 'hospital',
		),
		array(
			'name' => 'Dentist & Dental',
			'slug' => 'dentist-dental',
		),
		array(
			'name' => 'Medical & Clinic',
			'slug' => 'medical-clinic',
		),
		array(
			'name' => 'Therapist & Psychologist',
			'slug' => 'therapist-psychologist',
		),
		array(
			'name' => 'Gym & Fitness',
			'slug' => 'gym-fitness',
		),
		array(
			'name' => 'Yoga',
			'slug' => 'yoga',
		),
	);
	$fashion_subcategories    = array(
		array(
			'name' => 'Fashion',
			'slug' => 'fashion',
		),
		array(
			'name' => 'Shoes & Footwear',
			'slug' => 'shoes-footwear',
		),
		array(
			'name' => 'Salon & Spa',
			'slug' => 'salon-spa',
		),
		array(
			'name' => 'Makeup & Cosmetics',
			'slug' => 'makeup-cosmetics',
		),
	);
	$restaurant_subcategories = array(
		array(
			'name' => 'Food',
			'slug' => 'food',
		),
		array(
			'name' => 'Cafe & Bakery',
			'slug' => 'cafe-bakery',
		),
		array(
			'name' => 'Bar & Club',
			'slug' => 'bar-club',
		),
		array(
			'name' => 'Restaurant',
			'slug' => 'restaurant',
		),
		array(
			'name' => 'Catering & Chef',
			'slug' => 'catering-chef',
		),
	);
	$travel_subcategories     = array(
		array(
			'name' => 'Apartments & Hostels',
			'slug' => 'apartments-hostels',
		),
		array(
			'name' => "Hotels & BB's",
			'slug' => 'hotels-b&bs',
		),
	);
	$services_subcategories   = array(
		array(
			'name' => 'Accounting',
			'slug' => 'accounting',
		),
		array(
			'name' => 'Insurance',
			'slug' => 'insurance',
		),
		array(
			'name' => 'Roofing',
			'slug' => 'roofing',
		),
		array(
			'name' => 'Cleaning',
			'slug' => 'cleaning',
		),
		array(
			'name' => 'Electrician',
			'slug' => 'electrician',
		),
		array(
			'name' => 'Plumbing',
			'slug' => 'plumbing',
		),
		array(
			'name' => 'Courier',
			'slug' => 'courier',
		),
		array(
			'name' => 'Author & Writer',
			'slug' => 'author-writer',
		),
		array(
			'name' => 'Landscaping',
			'slug' => 'landscaping',
		),
		array(
			'name' => 'Hair & Stylist',
			'slug' => 'hair-stylist',
		),
	);
	$creative_subcategories   = array(
		array(
			'name' => 'Photography',
			'slug' => 'photography',
		),
		array(
			'name' => 'Artist',
			'slug' => 'artist',
		),
		array(
			'name' => 'Musician & Singer',
			'slug' => 'musician-singer',
		),
		array(
			'name' => 'Travel',
			'slug' => 'travel-documentary',
		),
		array(
			'name' => 'Art & Illustration',
			'slug' => 'art-illustration',
		),
		array(
			'name' => 'Designing',
			'slug' => 'designing',
		),
		array(
			'name' => 'Graphic & Web',
			'slug' => 'graphic-web',
		),
		array(
			'name' => 'Podcast',
			'slug' => 'podcast',
		),
		array(
			'name' => 'Music & Industry',
			'slug' => 'music-industry',
		),
		array(
			'name' => 'Film & TV',
			'slug' => 'film-tv',
		),

	);
	$community_subcategories = array(
		array(
			'name' => 'Church',
			'slug' => 'church',
		),
		array(
			'name' => 'Charity & NonProfit',
			'slug' => 'charity-nonprofit',
		),
		array(
			'name' => 'Schools & Universities',
			'slug' => 'schools-universities',
		),
		array(
			'name' => 'Preschool & Kindergarten',
			'slug' => 'preschool-kindergarten',
		),
		array(
			'name' => 'Online Education',
			'slug' => 'online-education',
		),
		array(
			'name' => 'Wedding',
			'slug' => 'wedding',
		),
		array(
			'name' => 'Holidays & Celebrations',
			'slug' => 'holidays-celebrations',
		),
		array(
			'name' => 'Conferences & Meetups',
			'slug' => 'conferences-meetups',
		),
	);
	$ecommerce_subcategories = array(
		array(
			'name' => 'Book Store',
			'slug' => 'book-store',
		),
		array(
			'name' => 'Beauty & Wellness',
			'slug' => 'beauty-wellness',
		),
		array(
			'name' => 'Fashion & Clothing Store',
			'slug' => 'fashion-clothing-store',
		),
		array(
			'name' => 'Home & Furniture',
			'slug' => 'home-furniture',
		),
		array(
			'name' => 'Electronics',
			'slug' => 'electronics',
		),
		array(
			'name' => 'Jewellery & Accessories',
			'slug' => 'jewellery-accessories',
		),
		array(
			'name' => 'Sports & Outdoors',
			'slug' => 'sports-outdoors',
		),
		array(
			'name' => 'Membership',
			'slug' => 'membership',
		),
		array(
			'name' => 'Online Course & LMS',
			'slug' => 'online-course-lms',
		),
		array(
			'name' => 'Food Ordering',
			'slug' => 'food-ordering',
		),
	);
	$blog_subcategories      = array(
		array(
			'name' => 'Personal & Blog',
			'slug' => 'personal-blog',
		),
		array(
			'name' => 'News',
			'slug' => 'news',
		),
		array(
			'name' => 'Landing Page',
			'slug' => 'landing-page',
		),
		array(
			'name' => 'Magazine',
			'slug' => 'magazine',
		),
		array(
			'name' => 'Portfolios',
			'slug' => 'portfolios',
		),
		array(
			'name' => "Resumes & CV's",
			'slug' => 'resumes-cvs',
		),
		array(
			'name' => 'Multipurpose',
			'slug' => 'multipurpose',
		),
		array(
			'name' => 'One Page',
			'slug' => 'one-page',
		),
	);
	$expand_more_svg         = '
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
							<mask id="mask0_5372_12373" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20">
							<rect width="20" height="20" fill="#D9D9D9"/>
							</mask>
							<g mask="url(#mask0_5372_12373)">
							<path d="M10.0004 12.218C9.90534 12.218 9.81319 12.2019 9.72398 12.1699C9.63477 12.1378 9.55118 12.0828 9.47319 12.0048L5.71517 8.24682C5.59445 8.1261 5.53623 7.98187 5.5405 7.81413C5.54477 7.64639 5.60727 7.50217 5.728 7.38144C5.84872 7.26072 5.99509 7.20036 6.16709 7.20036C6.33909 7.20036 6.48545 7.26072 6.60617 7.38144L10.0004 10.7757L13.4075 7.36863C13.5282 7.24791 13.6724 7.18968 13.8402 7.19394C14.0079 7.19822 14.1521 7.26072 14.2728 7.38144C14.3936 7.50217 14.4539 7.64853 14.4539 7.82055C14.4539 7.99255 14.3936 8.13891 14.2728 8.25963L10.5276 12.0048C10.4497 12.0828 10.3674 12.1378 10.2809 12.1699C10.1943 12.2019 10.1008 12.218 10.0004 12.218Z" fill="#334155"/>
							</g>
						</svg>';
	?>
	<div id="responsive-sites__category-filter" class="dropdown-check-list" tabindex="100">
		<span class="responsive-sites__category-filter-anchor" data-slug="" style="display: none;"><?php esc_html_e( 'All', 'responsive-addons' ); ?></span>
		<div class="rst-business-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="business">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Business', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Business', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $business_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-health-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="health-wellness">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Health', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Health', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $health_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-health-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="fashion-style">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Fashion', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Fashion', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $fashion_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-restaurant-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="restaurants-food">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Restaurants', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Restaurants', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $restaurant_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-travel-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="travel-tourism">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Travel', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Travel', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $travel_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-services-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="services">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Services', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Services', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $services_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-creative-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="creative">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Creative', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Creative', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $creative_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-community-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="community">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Community', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Community', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $community_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-ecommerce-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="ecommerce">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Ecommerce', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Ecommerce', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $ecommerce_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="rst-blog-category-group rst-menu-parent-category-group">
			<div class="rst-menu-parent-category responsive-sites_category" data-slug="blog">
				<span class="rst-menu-parent-category-title">
					<span><?php esc_html_e( 'Blog', 'responsive-addons' ); ?></span><?php echo $expand_more_svg; ?>
				</span>
			</div>
			<div class="rst-menu-child-category-group">
				<span class="rst-menu-category-section-title"><?php esc_html_e( 'Blog', 'responsive-addons' ); ?></span>
				<?php
				foreach ( $blog_subcategories as $key => $value ) {
					?>
					<div class="rst-menu-child-category responsive-sites_category" data-slug="<?php echo esc_attr( $value['slug'] ); ?>"><span><?php echo esc_html( $value['name'] ); ?></span></div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<div class="theme-browser rendered">
		<div id="responsive-sites" class="themes wp-clearfix"></div>
	</div>
</div>

<?php
/**
 * TMPL - List
 */
?>

<script type="text/template" id="tmpl-responsive-sites-list">
	<# for ( key in data ) { #>
		<div class="theme inactive ra-site-single {{ data[ key ].status }} {{ data[ key ].class }}" tabindex="0" aria-describedby="responsive-theme-action responsive-theme-name"
			data-demo-id="{{{ data[ key ].id }}}"
			data-demo-url="{{{ data[ key ]['site_url'] }}}"
			data-demo-slug="{{{  data[ key ].slug }}}"
			data-demo-name="{{{  data[ key ].title.rendered }}}"
			data-active-site="{{{  data.active_site }}}"
			data-demo-type="{{{ data[ key ].demo_type }}}"
			data-wpforms-path="{{{ data[ key ].wpforms_path }}}"
			data-allow-pages="{{{ data[ key ].allow_pages }}}"
			data-check_plugins_installed="{{{ data[ key ].check_plugins_installed }}}"
			data-screenshot="{{{ data[ key ]['featured_image_url'] }}}"
			data-required-plugins="{{ JSON.stringify(data[ key ]['required_plugins']) }}"
			data-pages="{{ JSON.stringify(data[ key ]['pages'] )}}"
			data-required-pro-plugins="{{ JSON.stringify(data[ key ]['required_pro_plugins']) }}"
			data-require-flex-box-container="{{{data[key].container_template}}}"
			data-favorite-status="{{{ data[ key ].favorite_status }}}">
			<input type="hidden" class="site_options_data" value="{{ JSON.stringify(data[ key ][ 'site_options_data' ]) }}">
		<div class="inner">
					<span class="site-preview" data-href="{{ data[ key ]['responsive-site-url'] }}?TB_iframe=true&width=600&height=550" data-title="data title">
						<div class="theme-screenshot" style="background-image: url('{{ data[ key ]['featured_image_url'] }}');"></div>
					</span>
			<span class="demo-type {{{ data[ key ].demo_type }}}">{{{ data[ key ].demo_type }}}</span>
			<# if (data[ key ].slug === data.active_site ) { #>
				<span class="current_active_site"><?php esc_html_e( 'Currently Active', 'responsive-addons' ); ?></span>
			<# } #>
			<div class="theme-id-container">
				<h3 class="theme-name" id="responsive-theme-name">{{{ data[ key ].title.rendered }}}</h3>
					<div class="responsive-single-site-favorite-div">
						<div id="rst-favorite-btn" class="rst-favorite-btn {{{ data[ key ].favorite_status }}}" data="false">
							<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
								<path d="M11.3263 20.4948L11.3256 20.4942C8.63349 18.053 6.43917 16.0597 4.91203 14.1914C3.39039 12.3298 2.5835 10.6533 2.5835 8.85417C2.5835 5.91215 4.87988 3.625 7.81266 3.625C9.47401 3.625 11.0768 4.40098 12.1207 5.61731L12.5002 6.05938L12.8796 5.61731C13.9235 4.40098 15.5263 3.625 17.1877 3.625C20.1205 3.625 22.4168 5.91215 22.4168 8.85417C22.4168 10.6533 21.6099 12.3298 20.0883 14.1914C18.5612 16.0597 16.3668 18.053 13.6747 20.4942L13.674 20.4948L12.5002 21.5634L11.3263 20.4948Z" fill="white" stroke="#EF4444"/>
								</svg>
						</div>
					</div>
			</div>
			<div class="guided-overlay step-three" id="step-three">
				<p class="guide-text">Click the "Preview" button to view the website template and click import.</p>
				<div class="guided-overlay-buttons">
					<button id="step-three-previous">Previous</button>
					<button id="step-three-finish" class="finish-tour">Finish Tour</button>
				</div>
			</div>
		</div>
	</div>
	<# } #>
</script>
<?php
/** Site suggestion block */
?>
<script type="text/template" id="tmpl-responsive-sites-suggestions">
	<div class="responsive-sites-suggestions">
		<div class="inner">
			<h3><?php esc_html_e( 'Sorry No Results Found.', 'responsive-addons' ); ?></h3>
			<div class="content">
				<div class="description">
					<p>
						<?php
						__( 'Can\'t find a Responsive Starter Template that suits your purpose ?' );
						?>
						<br><a target="_blank" href="mailto:support@cyberchimps.com?Subject=New%20Site%20Suggestion">
						<?php
						__( 'Suggest A Site' )
						?>
						</a>
					</p>
				</div>
			</div>
		</div>
	</div>
</script>
<?php
/** Single Demo Preview */
?>

<script type="text/template" id="tmpl-responsive-ready-site-preview">
	<div class="responsive-ready-site-preview theme-install-overlay wp-full-overlay collapsed"
		data-demo-id="{{{data.id}}}"
		data-demo-url="{{{data.demo_url}}}"
		data-demo-api="{{{data.demo_api}}}"
		data-demo-name="{{{data.name}}}"
		data-active-site="{{{data.active_site}}}"
		data-demo-type="{{{data.demo_type}}}"
		data-wpforms-path="{{{data.wpforms_path}}}"
		data-check_plugins_installed="{{{data.check_plugins_installed}}}"
		data-demo-slug="{{{data.slug}}}"
		data-screenshot="{{{data.screenshot}}}"
		data-required-plugins="{{data.required_plugins}}"
		data-required-pro-plugins="{{data.required_pro_plugins}}"
		data-require-flexbox-container="{{data.require_flexbox_container}}"
		data-pages="{{data.pages}}"
		data-app-auth="{{data.has_app_auth}}">
		<input type="hidden" class="responsive-site-options" value="{{data.site_options_data}}" >
		<div class="wp-full-overlay-header">
			<div class="responsive-single-demo-preview">
				<div class="responsive-sites-demo-details">
						<div class="responsive-sites-demo-preview-logo-wrap">
							<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/responsive-starter-templates-thumbnail.svg' ); ?>">
						</div>
					<span class="responsive-site-demo-name">{{data.name}}</span>
					<span class="responsive-site-demo-type responsive-site-demo-type-{{{data.demo_type}}}">{{data.demo_type}}</span>
				</div>
				<div class="responsive-addons-import-btns">
					<button class="responsive-addons-go-back-btn responsive-addons"><span class="responsive-addons-go-back-btn-text"><?php esc_html_e( 'Go Back', 'responsive-addons' ); ?></span></button>
					<# if ( data.is_responsive_addons_pro_installed && data.is_responsive_addons_pro_license_active  ) { #>
	
						<button class="button button-primary responsive-addons responsive-demo-import-options-{{{data.demo_type}}}"><?php esc_html_e( 'Import Site', 'responsive-addons' ); ?></button>
		
						<# if ( data.allow_pages ) { #>
	
						<button class="button button-primary responsive-addons responsive-page-import-options-{{{data.demo_type}}}"><?php esc_html_e( 'Import Template', 'responsive-addons' ); ?></button>
	
						<# } #>
					<# } else if ( data.has_app_auth ) { 
						<?php if ( $plan_status === 'free' ) : ?>
							if( data.demo_type === 'free'){
							#>
		
							<button class="button button-primary responsive-addons responsive-addons-demo-import-options"><?php esc_html_e( 'Import Site', 'responsive-addons' ); ?></button>
			
								<# if ( data.allow_pages ) { #>
			
								<button class="button button-primary responsive-addons responsive-addons-page-import-options"><?php esc_html_e( 'Import Template', 'responsive-addons' ); ?></button>
			
							<# }} else{ #>
									<button class="button button-primary responsive-addons responsive-demo-import-unlock-preminum-templates raddons-upgrade-the-plan"><?php esc_html_e( 'Import Site', 'responsive-addons' ); ?></button>
								<# if ( data.allow_pages ) { #>
									<button class="button button-primary responsive-addons  responsive-page-import-unlock-preminum-templates raddons-upgrade-the-plan"><?php esc_html_e( 'Import Template', 'responsive-addons' ); ?></button>
								<#
							}}
						 #> 

					<# 
							<?php
						else :
							?>
						 #>
						<button class="button button-primary responsive-addons responsive-addons-demo-import-options"><?php esc_html_e( 'Import Site', 'responsive-addons' ); ?></button>
			
								<# if ( data.allow_pages ) { #>
			
									<button class="button button-primary responsive-addons responsive-addons-page-import-options"><?php esc_html_e( 'Import Template', 'responsive-addons' ); ?></button>
			
								<# } #>
					<# <?php endif; ?>
				}
					
					 else {
						if( data.demo_type === 'free'){
						#>
	
							<button class="button button-primary responsive-addons responsive-demo-import-options-no-auth"><?php esc_html_e( 'Import Site', 'responsive-addons' ); ?></button>
		
							<# if ( data.allow_pages ) { #>

							<button class="button button-primary responsive-addons responsive-page-import-options-no-auth"><?php esc_html_e( 'Import Template', 'responsive-addons' ); ?></button>

							<# } #>
						<# }
						else { #>
							<button class="button button-primary responsive-addons responsive-demo-import-options-no-auth-unlock-access responsive-demo-import-unlock-preminum-templates"><?php esc_html_e( 'Import Site', 'responsive-addons' ); ?></button>
		
							<# if ( data.allow_pages ) { #>

							<button class="button button-primary responsive-addons responsive-page-import-options-no-auth-unlock-access responsive-page-import-unlock-preminum-templates"><?php esc_html_e( 'Import Template', 'responsive-addons' ); ?></button>

							<# 
						}}	
						 #> 
	
					<# }
					
					#>
				</div>
				<div class="responsive-addons-modal responsive-addons-app-connect-modal" style="display: none;">
					<div class="responsive-addons-app-modal-content">
						<span id="responsive-addons-app-modal-close"><img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/modal-close.svg' ); ?>"></span>
						<h2><?php esc_html_e( 'Connect Your Website to Cyberchimps Responsive', 'responsive-addons' ); ?></h2>
						<p><?php esc_html_e( 'Create a free account to connect with Cyberchimps Responsive.', 'responsive-addons' ); ?></p>
						<button type="button" class="rst-start-auth rst-start-auth-new"><?php esc_html_e( 'New? Create a free account', 'responsive-addons' ); ?><span id="loader"></span></button>
						<p class=""><?php esc_html_e( 'Already have an account? ', 'responsive-addons' ); ?><span class="rst-start-auth rst-start-auth-exist"><?php esc_html_e( 'Connect your existing account', 'responsive-addons' ); ?><span id="loader"></span></span></p>
					</div>
				</div>
				
				<!-- Unlock Premium Template Access Popup -->
				<div class="responsive-addons-modal responsive-addons-app-unlock-access-modal" style="display: none;">
					<div class="responsive-addons-app-unlock-access-modal-content">
						<div id="responsive-addons-app-unlock-template-header">
							<p class="responsive-addons-app-unlock-template-heading"><?php esc_html_e( 'Premium template requires a Personal plan subscription or higher!', 'responsive-addons' ); ?></p>
							<span id="responsive-addons-app-unlock-template-modal-close"><img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/unlock-template-close-btn.svg' ); ?>"></span>
						</div>
						<div class="responsive-addons-app-unlock-access-modal-body">						
							<h2><?php esc_html_e( 'Connect Your Website to Cyberchimps Responsive', 'responsive-addons' ); ?></h2>
							<p><?php esc_html_e( 'Create a free account to connect with Cyberchimps Responsive.', 'responsive-addons' ); ?></p>
							<button type="button" class="raddons-upgrade-the-plan"><?php esc_html_e( 'Unlock Premium Template Access at just $1.97/month', 'responsive-addons' ); ?><span style="margin-left: 8px" class="dashicons dashicons-lock"></span><span id="loader"></span></button>
							<p style="color:#000000; padding-bottom: 15px;"class=""><?php esc_html_e( 'Already have an account on app.cyberchimps.com? ', 'responsive-addons' ); ?><span class="rst-start-auth rst-start-auth-exist"><?php esc_html_e( 'Connect your existing account', 'responsive-addons' ); ?><span id="loader"></span></span></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="wp-full-overlay-main">
			<iframe src="{{{data.demo_url}}}" title="<?php esc_attr_e( 'Preview', 'responsive-addons' ); ?>"></iframe>
		</div>
	</div>
</script>

<?php
/** Theme Import Options Page */
?>
<script type="text/template" id="tmpl-responsive-ready-sites-import-progress-page">
	<div class="responsive-ready-sites-advanced-options-wrap ready-site-import-progress-step wp-full-overlay collapsed"
			data-demo-id="{{{data.id}}}"
			data-demo-url="{{{data.demo_url}}}"
			data-demo-api="{{{data.demo_api}}}"
			data-demo-name="{{{data.name}}}"
			data-demo-type="{{{data.demo_type}}}"
			data-demo-slug="{{{data.slug}}}"
			data-screenshot="{{{data.screenshot}}}"
			data-required-plugins="{{data.required_plugins}}"
			data-pages="{{data.pages}}"
			data-require-flexbox-container="{{data.require_flexbox_container}}"
			data-required-pro-plugins="{{data.required_pro_plugins}}">
			<input type="hidden" class="responsive-site-options" value="{{data.site_options_data}}" >
			<input type="hidden" class="demo_site_id" value="{{{ data.id }}}">
			<div class="wp-full-overlay-header">
				<div class="responsive-advanced-options-wrap">
					<div class="responsive-sites-demo-details">
							<div class="responsive-sites-demo-preview-logo-wrap">
								<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/responsive-starter-templates-thumbnail.svg' ); ?>">
							</div>
					</div>
					<div class="responsive-addons-import-btns">
						<a href="<?php echo esc_url( home_url( $_SERVER['REQUEST_URI'] ) ); ?>" class="rst-exit-to-dashboard"><img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/exit-to-dashboard.svg' ); ?>"></a>
					</div>
				</div>
			</div>
			<div class="wp-full-overlay-main">
				<div class="responsive-ready-sites-import-progress-container">
					<div class="site-import-options">
						<div class="responsive-ready-sites-advanced-options">
							<h2 class="ready-sites-import-progress-title">We are Building your Website</h2>
							<div class="sites-import-process-errors" style="display: none">
								<div class="import-process-error">
									<div class="current-importing-status-error-title"></div>
								</div>
							</div>
							<div class="ready-sites-import-progress-info">
								<div class="ready-sites-import-progress-info-text"></div>
								<div class="ready-sites-import-progress-info-percent"></div>
							</div>
							<div class="ready-sites-import-progress-bar-wrap">
								<div class="ready-sites-import-progress-bar-bg">
									<div class="ready-sites-import-progress-bar"></div>
								</div>
								<div class="ready-sites-import-progress-gap"><span></span><span></span><span></span></div>
							</div>
						</div>
						<div class="ready-sites-import-progress-img-wrapper">
							<div class="ready-sites-import-progress-img">
								<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/import-progress.gif' ); ?>">
							</div>
						</div>
					</div>
				</div>
				<div class="result_preview" style="display: none">
				</div>
			</div>
		</div>
</script>


<script type="text/template" id="tmpl-responsive-ready-sites-import-options-page">
	<div class="responsive-ready-sites-advanced-options-wrap wp-full-overlay collapsed"
			data-demo-id="{{{data.id}}}"
			data-demo-url="{{{data.demo_url}}}"
			data-demo-api="{{{data.demo_api}}}"
			data-demo-name="{{{data.name}}}"
			data-demo-type="{{{data.demo_type}}}"
			data-demo-slug="{{{data.slug}}}"
			data-screenshot="{{{data.screenshot}}}"
			data-required-plugins="{{data.required_plugins}}"
			data-pages="{{data.pages}}"
			data-require-flexbox-container="{{data.require_flexbox_container}}"
			data-required-pro-plugins="{{data.required_pro_plugins}}">
			<input type="hidden" class="responsive-site-options" value="{{data.site_options_data}}" >
			<input type="hidden" class="demo_site_id" value="{{{ data.id }}}">
			<div class="wp-full-overlay-header">
				<div class="responsive-advanced-options-wrap">
					<div class="responsive-sites-demo-details">
							<div class="responsive-sites-demo-preview-logo-wrap">
								<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/responsive-starter-templates-thumbnail.svg' ); ?>">
							</div>
					</div>
					<div class="responsive-addons-import-btns">
						<a href="<?php echo esc_url( home_url( $_SERVER['REQUEST_URI'] ) ); ?>" class="rst-exit-to-dashboard"><img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/exit-to-dashboard.svg' ); ?>"></a>
					</div>
				</div>
			</div>
			<div class="wp-full-overlay-main">
				<div class="ready-site-import-options-subscription-wrapper">
					<div class="sites-import-process-errors" style="display: none">
						<div class="import-process-error">
							<div class="current-importing-status-error-title"></div>
						</div>
					</div>
					<#
						var requireContainer= data.require_flexbox_container;

						if(data.required_plugins.includes('Elementor') && requireContainer){ #>
						<?php
						$elementor_option_value = get_option( 'elementor_experiment-container' );

						if ( $elementor_option_value == 'inactive' ) {
							?>
						<div class="elementor-sites-error-msg-container">
							<div class="elementor-sites-error-msg"> 
								<div>
									<svg xmlns="http://www.w3.org/2000/svg" style="vertical-align:-4px" width="16" height="16" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16"><g fill="currentColor"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="m8.93 6.588l-2.29.287l-.082.38l.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319c.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246c-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0z"/></g></svg>
								</div>
								<div class="elementor-error-msg">
									<span> 
										<?php esc_html_e( 'This starter template is built using Elementor flexbox containers. Please mark the Flexbox Container setting as \'active\' in Elementor Features settings.', 'responsive-addons' ); ?>
									</span>
									<a href="
									<?php
									$elementor_settings_url = admin_url( 'admin.php?page=elementor#tab-experiments' );
									echo $elementor_settings_url;
									?>
										">
										<?php esc_html_e( 'Go to settings', 'responsive-addons' ); ?>
									</a>
								</div>
							</div>
						</div>
						<?php } ?>
					<# } #>
					<div class="site-import-options">
						<div class="responsive-ready-sites-advanced-options">
							<h2 class="responsive-import-ready-site-title">Import {{data.name}}</h2>
							<# if ( data.slug === data.active_site ) { #>
								<p><?php esc_html_e( 'This will delete previously imported site', 'responsive-addons' ); ?></p>
							<# } #>
							<ul class="responsive-ready-site-contents">
								<?php
								$current_theme = wp_get_theme();
								if ( ! ( 'Responsive' === $current_theme->get( 'Name' ) ) || ( is_child_theme() && 'Responsive' === $current_theme->parent()->get( 'Name' ) ) ) {
									?>
								<li class="responsive-ready-sites-install-responsive">
									<label class="ready-site-single-import-option">
											<input class="ready-site-import-option-input-checkbox checkbox" type="checkbox" name="reset" checked="checked" id="install_responsive_checkbox">
											<span class="ready-site-import-option-input-slider ready-site-import-option-input-round"></span>
									</label>
									<span class="ready-site-single-import-option-title"><strong><?php esc_html_e( 'Install Responsive Theme', 'responsive-addons' ); ?></strong></span>
								</li>
									<?php
								}
								?>
								<li class="responsive-ready-sites-import-plugins">
									<label class="ready-site-single-import-option disabled">
										<input class="ready-site-import-option-input-checkbox checkbox" type="checkbox" name="plugins" checked="checked" readonly>
										<span class="ready-site-import-option-input-slider ready-site-import-option-input-round"></span>
									</label>
									<span class="ready-site-single-import-option-title"><strong><?php esc_html_e( 'Install Required Plugins', 'responsive-addons' ); ?></strong></span>
									<ul class="required-plugins-list" style="display: none;"></ul>
								</li>
								<li class="responsive-ready-sites-import-xml">
									<label class="ready-site-single-import-option">
										<input class="ready-site-import-option-input-checkbox checkbox" type="checkbox" name="content" checked="checked" class="checkbox">
										<span class="ready-site-import-option-input-slider ready-site-import-option-input-round"></span>
									</label>
									<span class="ready-site-single-import-option-title"><strong><?php esc_html_e( 'Import Content', 'responsive-addons' ); ?></strong></span>
								</li>
								<li class="responsive-ready-sites-import-customizer">
									<label class="ready-site-single-import-option">
										<input class="ready-site-import-option-input-checkbox checkbox" type="checkbox" name="customizer" checked="checked" class="checkbox">
										<span class="ready-site-import-option-input-slider ready-site-import-option-input-round"></span>
									</label>
									<span class="ready-site-single-import-option-title"><strong><?php esc_html_e( 'Import Customizer Settings', 'responsive-addons' ); ?></strong></span>
								</li>
								<li class="responsive-ready-sites-reset-data">
									<label class="ready-site-single-import-option">
										<input class="ready-site-import-option-input-checkbox checkbox" type="checkbox" name="reset" checked="checked" class="checkbox">
										<span class="ready-site-import-option-input-slider ready-site-import-option-input-round"></span>
									</label>
									<span class="ready-site-single-import-option-title"><strong><?php esc_html_e( 'Delete Previous Import', 'responsive-addons' ); ?></strong></span>
								</li>
								<p class="responsive-ready-sites-reset-data-message">
									<?php esc_html_e( 'This option will remove the previous imported content and will perform a fresh and clean install.', 'responsive-addons' ); ?>
								</p>
							</ul>
						</div>
					</div>
					<hr />
					<div class="ready-sites-import-subscription">
						<h3 class="ready-sites-import-subscription-title"><?php esc_html_e( 'Subscribe and Import', 'responsive-addons' ); ?></h3>
						<label for="ready-sites-subscriber-email"><?php esc_html_e( 'Email', 'responsive-addons' ); ?></label>
						<input type="email" id="ready-sites-subscriber-email" name="subscriber_emmail" placeholder="Enter Your Email Address" value="<?php echo isset( $settings['account']['email'] ) ? esc_html( $settings['account']['email'] ) : ''; ?>">
						<input type="hidden" id="ready-sites-importing-template-name" name="ready-sites-template-name" value="{{data.name}}">
						<div class="ready-sites-subscription-user-consent">
							<input type="checkbox" id="ready-sites-subscription-check" name="subscription_check">
							<label for="ready-sites-subscription-check"><?php esc_html_e( 'Yes, count me in!', 'responsive-addons' ); ?></label>
						</div>
					</div>
					<hr />
					<div class="responsive-ready-sites-import-button-wrap">
						<?php if ( $cc_app_auth->has_auth() ) : ?>
							<button class="button responsive-ready-site-import-with-sub responsive-addons-ready-site-import">
								<?php esc_html_e( 'Subscribe & Start Importing', 'responsive-addons' ); ?>
							</button>
							<button class="button responsive-ready-site-import-without-sub responsive-addons-ready-site-import">
								<?php esc_html_e( 'Skip, Start Importing', 'responsive-addons' ); ?>
							</button>
						<?php else : ?>
							<button class="button responsive-ready-site-import-with-sub responsive-ready-site-import-{{{data.demo_type}}}">
								<?php esc_html_e( 'Subscribe & Start Importing', 'responsive-addons' ); ?>
							</button>
							<button class="button responsive-ready-site-import-without-sub responsive-ready-site-import-{{{data.demo_type}}}">
								<?php esc_html_e( 'Skip, Start Importing', 'responsive-addons' ); ?>
							</button>
						<?php endif; ?>
					</div>
				</div>
				<div class="result_preview" style="display: none">
				</div>
			</div>
			<div class="wp-full-overlay-footer">
				<div class="responsive-ready-sites-advanced-options-wrap-footer-btn-wrapper">
					<button class="responsive-addons-go-back-btn responsive-addons"><span class="responsive-addons-go-back-btn-text"><?php esc_html_e( 'Go Back', 'responsive-addons' ); ?></span></button>
				</div>
			<div>
		</div>
		</div>
</script>

<?php
/** Single Template Preview Screen Page */
?>
<script type="text/template" id="tmpl-responsive-ready-sites-import-page-preview-page">
	<div class="responsive-ready-sites-advanced-options-wrap template-preview-page wp-full-overlay collapsed"
		data-demo-api="{{{data.demo_api}}}"
		data-demo-name="{{{data.name}}}"
		data-screenshot="{{{data.screenshot}}}"
		data-demo-type="{{{data.demo_type}}}"
		data-wpforms-path="{{{data.wpforms_path}}}"
		data-required-plugins="{{data.required_plugins}}"
		data-required-pro-plugins="{{data.required_pro_plugins}}">
		<div class="wp-full-overlay-main">
			<div class="sites-import-process-errors" style="display: none">
				<div class="import-process-error">
					<div class="current-importing-status-error-title"></div>
				</div>
			</div>

			<div class="theme-browser rendered">
				<div id="site-pages" class="themes wp-clearfix">
			<div class="single-site-wrap">
				<div class="single-site">
					<div class="single-site-preview-wrap">
						<div class="single-site-preview">
							<img class="theme-screenshot" data-src="" src="{{data.screenshot}}" />
						</div>
					</div>
					<div class="single-site-pages-wrap">
						<div class="responsive-pages-title-wrap">
							<span class="responsive-pages-title"><?php esc_html_e( 'Page Templates', 'responsive-addons' ); ?></span>
						</div>
						<div class="single-site-pages">
							<div id="single-pages">
								<# for (page_id in data.pages)  { #>
								<#
								var required_plugins = [];
								for( id in data.pages[page_id]['free_plugins']) {
									JSON.parse( data.required_plugins ).forEach( function( single_plugin ) {
										if ( data.pages[page_id]['free_plugins'][id] == single_plugin.slug ) {
											required_plugins.push( single_plugin );
										}
									}
								);
								}
								var required_pro_plugins = [];
								for( id in data.pages[page_id]['pro_plugins']) {
									JSON.parse( data.required_pro_plugins ).forEach( function( single_plugin ) {
										if ( data.pages[page_id]['pro_plugins'][id] == single_plugin.slug ) {
											required_pro_plugins.push( single_plugin );
										}
									}
								);
								}
								#>
								<div class="theme responsive-theme site-single" data-page-id="{{data.pages[page_id]['page_id']}}" data-required-pro-plugins="{{ JSON.stringify( required_pro_plugins )}}" data-required-plugins="{{ JSON.stringify( required_plugins )}}" data-includes-wp-forms="{{ data.pages[page_id]['includes_wp_forms'] }}" >
									<div class="inner">
										<#
										var featured_image_class = '';
										var featured_image = data.pages[page_id]['featured_image'] || '';
										if( '' === featured_image ) {
										featured_image = '<?php echo esc_url( RESPONSIVE_ADDONS_DIR . 'inc/assets/images/placeholder.png' ); ?>';
										featured_image_class = ' no-featured-image ';
										}

										var thumbnail_image = data.pages[page_id]['thumbnail-image-url'] || '';
										if( '' === thumbnail_image ) {
										thumbnail_image = featured_image;
										}
										#>
										<span class="site-preview" data-title="{{ data.pages[page_id]['page_title'] }}">
										<div class="theme-screenshot one loading {{ featured_image_class }}" data-src="{{ featured_image }}" data-featured-src="{{ featured_image }}" data-demo-type="{{ data.demo_type }}" style="background-image: url('{{ featured_image }}');"></div>
									</span>
										<div class="theme-id-container">
											<h3 class="theme-name">
												{{{ data.pages[page_id]['page_title'] }}}
											</h3>
										</div>
									</div>
								</div>
								<# } #>
							</div>
						</div>
					</div>
					<div class="single-site-footer">
						<div class="site-action-buttons-wrap">
							<span class="responsive-site-demo-name">{{data.name}}</span>
							<div class="site-action-buttons-right">
								<# if ( ( data.demo_type == "pro" && data.is_responsive_addons_pro_installed && data.is_responsive_addons_pro_license_active ) ) { #>
										<button class="responsive-addons-go-back-btn-pro responsive-addons"><span class="responsive-addons-go-back-btn-text"><?php esc_html_e( 'Go Back', 'responsive-addons' ); ?></span></button>
									<# } else { #>
										<button class="responsive-addons-go-back-btn responsive-addons"><span class="responsive-addons-go-back-btn-text"><?php esc_html_e( 'Go Back', 'responsive-addons' ); ?></span></button>
								<# } #>
								<a href="{{{data.demo_api}}}" class="button button-hero site-preview-button" target="_blank">Preview "{{data.name}}" Site <i class="dashicons dashicons-external"></i></a>
								<?php if ( $cc_app_auth->has_auth() ) : ?>
									<div class="button button-hero button-primary single-page-import-button disabled"><?php esc_html_e( 'Select Template', 'responsive-addons' ); ?></div>
								<?php else : ?>
									<div class="button button-hero button-primary single-page-import-button-{{{ data.demo_type }}} disabled"><?php esc_html_e( 'Select Template', 'responsive-addons' ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
				</div>
			</div>
			<div class="result_preview" style="display: none">
			</div>
		</div>
	</div>
</script>
<?php
/** Single Template Import Options Screen */
?>
<script type="text/template" id="tmpl-responsive-ready-sites-import-single-page-options-page">
	<div class="responsive-ready-sites-advanced-options-wrap single-page-import-options-page wp-full-overlay collapsed"
		data-page-id="{{{data.page_id}}}"
		data-demo-type = "{{{data.demo_type}}}"
		data-is-responsive-addons-pro-installed = "{{{data.is_responsive_addons_pro_installed}}}"
		data-is-responsive-addons-pro-license-active = "{{{data.is_responsive_addons_pro_license_active}}}"
		data-demo-api="{{{data.demo_api}}}"
		data-includes-wp-forms="{{{data.includes_wp_forms}}}"
		data-wpforms-path="{{{data.wpforms_path}}}"
		data-required-plugins="{{ JSON.stringify( data.required_plugins )}}"
		data-require-flexbox-container="{{data.require_flexbox_container}}"
		data-required-pro-plugins="{{ JSON.stringify( data.required_pro_plugins )}}">
		<div class="wp-full-overlay-header">
			<div class="responsive-advanced-options-wrap">
					<div class="responsive-sites-demo-details">
							<div class="responsive-sites-demo-preview-logo-wrap">
								<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/responsive-starter-templates-thumbnail.svg' ); ?>">
							</div>
					</div>
					<div class="responsive-addons-import-btns">
						<a href="<?php echo esc_url( home_url( $_SERVER['REQUEST_URI'] ) ); ?>" class="rst-exit-to-dashboard"><img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/exit-to-dashboard.svg' ); ?>"></a>
					</div>
				</div>
		</div>
		<div class="wp-full-overlay-main">
			<div class="ready-site-import-options-subscription-wrapper">
				<div class="sites-import-process-errors" style="display: none">
					<div class="import-process-error">
						<div class="current-importing-status-error-title"></div>
					</div>
				</div>
				<# 
					var hasElementorPlugin = data.required_plugins.some(function(plugin) {
					return plugin.name === 'Elementor';
					});

					var requireContainer= data.require_flexbox_container;

					if(hasElementorPlugin && requireContainer){ #>
					<?php
					$elementor_option_value = get_option( 'elementor_experiment-container' );

					if ( $elementor_option_value == 'inactive' ) {
						?>
					<div class="elementor-sites-error-msg-container">
						<div class="elementor-sites-error-msg">
							<div>
								<svg xmlns="http://www.w3.org/2000/svg" style="vertical-align:-4px" width="16" height="16" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16"><g fill="currentColor"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="m8.93 6.588l-2.29.287l-.082.38l.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319c.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246c-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0z"/></g></svg>
							</div>
							<div class="elementor-error-msg">
								<span> 
									<?php esc_html_e( 'This starter template is built using Elementor flexbox containers. Please mark the Flexbox Container setting as \'active\' in Elementor Features settings.', 'responsive-addons' ); ?>
								</span>
								<a href="
								<?php
								$elementor_settings_url = admin_url( 'admin.php?page=elementor#tab-experiments' );
								echo $elementor_settings_url;
								?>
									">
									<?php esc_html_e( 'Go to settings', 'responsive-addons' ); ?>
								</a>
							</div>
						</div>
					</div>
					<?php } ?>
				<# } #>
				<div class="site-import-options">
					<div class="responsive-ready-sites-advanced-options">
						<h2 class="responsive-import-ready-site-title">Importing {{data.page_title}} Template</h2>
						<ul class="responsive-ready-site-contents">
							<li class="responsive-ready-sites-import-plugins">
								<label class="ready-site-single-import-option disabled">
									<input class="ready-site-import-option-input-checkbox checkbox" type="checkbox" name="plugins" checked="checked" readonly>
									<span class="ready-site-import-option-input-slider ready-site-import-option-input-round"></span>
								</label>
								<span class="ready-site-single-import-option-title"><strong><?php esc_html_e( 'Install Required Plugins', 'responsive-addons' ); ?></strong></span>
								<ul class="required-plugins-list" style="display: none;"></ul>
							</li>
							<li class="responsive-ready-sites-import-xml">
								<label class="ready-site-single-import-option">
									<input class="ready-site-import-option-input-checkbox checkbox" type="checkbox" name="content" checked="checked" class="checkbox">
									<span class="ready-site-import-option-input-slider ready-site-import-option-input-round"></span>
								</label>
								<span class="ready-site-single-import-option-title"><strong><?php esc_html_e( 'Import Content', 'responsive-addons' ); ?></strong></span>
							</li>
						</ul>
					</div>
				</div>
				<hr />
				<div class="ready-sites-import-subscription">
					<h3 class="ready-sites-import-subscription-title"><?php esc_html_e( 'Subscribe and Import', 'responsive-addons' ); ?></h3>
					<label for="ready-sites-subscriber-email"><?php esc_html_e( 'Email', 'responsive-addons' ); ?></label>
					<input type="email" id="ready-sites-subscriber-email" name="subscriber_emmail" placeholder="Enter Your Email Address" value="<?php echo isset( $settings['account']['email'] ) ? esc_html( $settings['account']['email'] ) : ''; ?>">
					<input type="hidden" id="ready-sites-importing-template-name" name="ready-sites-template-name" value="{{data.name}}">
					<div class="ready-sites-subscription-user-consent">
						<input type="checkbox" id="ready-sites-subscription-check" name="subscription_check">
						<label for="ready-sites-subscription-check"><?php esc_html_e( 'Yes, count me in!', 'responsive-addons' ); ?></label>
					</div>
				</div>
				<hr />
				<div class="responsive-ready-sites-import-button-wrap">
					<?php if ( $cc_app_auth->has_auth() ) : ?>
						<button class="button responsive-ready-site-import-with-sub import-page responsive-ready-page-import">
							<?php esc_html_e( 'Subscribe & Start Importing', 'responsive-addons' ); ?>
						</button>
						<button class="button responsive-ready-site-import-without-sub import-page responsive-ready-page-import">
							<?php esc_html_e( 'Skip, Start Importing', 'responsive-addons' ); ?>
						</button>
					<?php else : ?>
						<button class="button responsive-ready-site-import-with-sub import-page responsive-ready-page-import-{{{data.demo_type}}}">
							<?php esc_html_e( 'Subscribe & Start Importing', 'responsive-addons' ); ?>
						</button>
						<button class="button responsive-ready-site-import-without-sub import-page responsive-ready-page-import-{{{data.demo_type}}}">
							<?php esc_html_e( 'Skip, Start Importing', 'responsive-addons' ); ?>
						</button>
					<?php endif; ?>
				</div>
			</div>
			<div class="result_preview" style="display: none">
			</div>
		</div>
		<div class="wp-full-overlay-footer">
			<div class="responsive-ready-sites-advanced-options-wrap-footer-btn-wrapper">
				<button class="responsive-addons-go-back-btn responsive-addons"><span class="responsive-addons-go-back-btn-text"><?php esc_html_e( 'Go Back', 'responsive-addons' ); ?></span></button>
			</div>
			<div></div>
		</div>
	</div>
</script>

<?php
/** Single Template Import Progress Screen */
?>
<script type="text/template" id="tmpl-responsive-ready-sites-import-single-page-progress-page">
	<div class="responsive-ready-sites-advanced-options-wrap single-page-import-progress-page wp-full-overlay collapsed">
		<div class="wp-full-overlay-header">
			<div class="responsive-advanced-options-wrap">
					<div class="responsive-sites-demo-details">
							<div class="responsive-sites-demo-preview-logo-wrap">
								<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/responsive-starter-templates-thumbnail.svg' ); ?>">
							</div>
					</div>
					<div class="responsive-addons-import-btns">
						<a href="<?php echo esc_url( home_url( $_SERVER['REQUEST_URI'] ) ); ?>" class="rst-exit-to-dashboard"><img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/exit-to-dashboard.svg' ); ?>"></a>
					</div>
				</div>
		</div>
		<div class="wp-full-overlay-main">
			<div class="responsive-ready-sites-import-progress-container">
				<div class="site-import-options">
					<div class="responsive-ready-sites-advanced-options">
						<h2 class="ready-sites-import-progress-title"><?php esc_html_e( 'We are Building your Website', 'responsive-addons' ); ?></h2>
						<div class="sites-import-process-errors" style="display: none">
							<div class="import-process-error">
								<div class="current-importing-status-error-title"></div>
							</div>
						</div>
							<div class="ready-sites-import-progress-info">
								<div class="ready-sites-import-progress-info-text"></div>
								<div class="ready-sites-import-progress-info-percent"></div>
							</div>
							<div class="ready-sites-import-progress-bar-wrap">
								<div class="ready-sites-import-progress-bar-bg">
									<div class="ready-sites-import-progress-bar"></div>
								</div>
								<div class="ready-sites-import-progress-gap"><span></span><span></span><span></span></div>
							</div>
						</div>
						<div class="ready-sites-import-progress-img-wrapper">
							<div class="ready-sites-import-progress-img">
								<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/import-progress.gif' ); ?>">
							</div>
						</div>
					</div>
				</div>
				<div class="result_preview" style="display: none">
				</div>
		</div>
	</div>
</script>

<?php
/** Congratulations Screen */
?>
<script type="text/template" id="tmpl-responsive-ready-sites-import-done-congrats-page">
	<div class="responsive-ready-sites-advanced-options-wrap responsive-site-import-done-congrats wp-full-overlay collapsed">
		<div class="wp-full-overlay-header">
			<div class="responsive-advanced-options-wrap">
					<div class="responsive-sites-demo-details">
							<div class="responsive-sites-demo-preview-logo-wrap">
								<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/responsive-starter-templates-thumbnail.svg' ); ?>">
							</div>
					</div>
					<div class="responsive-addons-import-btns">
						<a href="<?php echo esc_url( home_url( $_SERVER['REQUEST_URI'] ) ); ?>" class="rst-exit-to-dashboard"><img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/exit-to-dashboard.svg' ); ?>"></a>
					</div>
				</div>
		</div>
		<div class="wp-full-overlay-main">
			<div class="responsive-ready-sites-import-progress-container">
				<div class="site-import-options">
					<div class="responsive-ready-sites-advanced-options">
						<div class="responsive-ready-sites-import-done-congrats-title-wrap">
							<h2 class="ready-sites-import-done-congrats-title"><?php esc_html_e( 'Congratulations', 'responsive-addons' ); ?></h2>
							<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/congratulations.png' ); ?>">
						</div>
						<div class="ready-sites-import-progress-info">
							<div class="ready-sites-import-progress-info-text"><?php esc_html_e( 'Your Website is ready and it took just ', 'responsive-addons' ); ?><span class="responsive-ready-sites-import-time-taken">60</span><?php esc_html_e( ' seconds to build.', 'responsive-addons' ); ?></div>
						</div>
						<div class="ready-sites-import-progress-bar-wrap">
							<div class="ready-sites-import-progress-bar-bg">
								<div class="ready-sites-import-progress-bar import-done"></div>
							</div>
							<div class="ready-sites-import-progress-gap"><span></span><span></span><span></span></div>
						</div>
						<div class="responsive-sites-import-done-success-section">
							<div class="responsive-sites-import-done-success">
								<p class="responsive-sites-tweet-text">
									<?php esc_html_e( 'I just built my website in ', 'responsive-addons' ); ?><span class="responsive-ready-sites-import-time-taken">60</span><?php esc_html_e( ' seconds with the Responsive Starter Templates plugin by @cyberchimps. It was so easy!', 'responsive-addons' ); ?>
								</p>
							</div>
							<div class="responsive-sites-twitter-btn-wrap" >
								<a href="https://twitter.com/intent/tweet" target="_blank" id="responsive-sites-twitter-tweet-link">
									<svg xmlns="http://www.w3.org/2000/svg" width="17" height="15" viewBox="0 0 17 15" fill="none">
										<g clip-path="url(#clip0_5382_13511)">
											<path d="M12.8694 0.325745H15.2198L10.086 6.17472L16.1476 14.106H11.3849L7.67368 9.29825L3.40579 14.106H1.05536L6.56032 7.85898L0.746094 0.325745H5.63252L9.00353 4.73544L12.8694 0.325745ZM12.0344 12.6974H13.3333L4.9212 1.64253H3.49857L12.0344 12.6974Z" fill="black"/>
										</g>
										<defs>
											<clipPath id="clip0_5382_13511">
												<rect width="15.4015" height="13.7803" fill="white" transform="translate(0.746094 0.325745)"/>
											</clipPath>
										</defs>
									</svg>
									<p class="tweet-btn"><?php esc_html_e( 'Tweet', 'responsive-addons' ); ?></p>
								</a>
							</div>
						</div>
						<div class="responsive-sites-import-site-btn-wrap">
							<a href="#" class="responsive-sites-imported-site-link" id="responsive-sites-imported-site-link" target="_blank"><?php esc_html_e( 'View Website', 'responsive-addons' ); ?></a>
						</div>
					</div>
					</div>
				</div>
				<div class="result_preview" style="display: none">
				</div>
		</div>
	</div>
</script>
<?php
wp_print_admin_notice_templates();
