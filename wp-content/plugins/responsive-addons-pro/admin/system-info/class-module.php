<?php
namespace Responsive_Addons_Pro\System_Info;

use Responsive_Addons_Pro\System_Info\Helpers\Report_Helper;
use Responsive_Addons_Pro\System_Info\Reporters\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Responsive Pro system info module.
 *
 * Responsive Pro system info module handler class is responsible for registering and
 * managing Responsive Pro system info reports.
 *
 * @since 2.0.5
 */
class Module {

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @var   (Object) Class object
	 */
	public static $instance = null;

	/**
	 * Set Instance
	 *
	 * @since 1.0.0
	 *
	 * @return object Class object.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get module name.
	 *
	 * Retrieve the system info module name.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'system-info';
	}

	/**
	 * Required user capabilities.
	 *
	 * Holds the user capabilities required to manage Responsive Pro menus.
	 *
	 * @since 2.0.5
	 * @access private
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Responsive Pro system info reports.
	 *
	 * Holds an array of available reports in Responsive Pro system info page.
	 *
	 * @since 2.0.5
	 * @access private
	 *
	 * @var array
	 */
	private static $reports = array(
		'server'          => array(),
		'wordpress'       => array(),
		'theme'           => array(),
		'user'            => array(),
		'plugins'         => array(),
		'network_plugins' => array(),
		'mu_plugins'      => array(),
	);

	/**
	 * Settings.
	 *
	 * Holds the object settings.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Main system info page constructor.
	 *
	 * Initializing Responsive Pro system info page.
	 *
	 * @since 2.0.5
	 * @access public
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Get default settings.
	 *
	 * Retrieve the default settings. Used to reset the report settings on
	 * initialization.
	 *
	 * @since 2.0.5
	 * @access protected
	 *
	 * @return array Default settings.
	 */
	protected function get_init_settings() {
		$settings = array();

		$reporter_properties = Base::get_properties_keys();

		array_push( $reporter_properties, 'category', 'name', 'class_name' );

		$settings['reporter_properties'] = $reporter_properties;

		$settings['reportFilePrefix'] = '';

		return $settings;
	}

	/**
	 * Add actions.
	 *
	 * Register filters and actions for the main system info page.
	 *
	 * @since 2.0.5
	 * @access private
	 */
	private function add_actions() {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 500 );
		add_action( 'wp_ajax_responsive_pro_system_info_download_file', array( $this, 'download_file' ) );
	}

	/**
	 * Register admin menu.
	 *
	 * Add new Responsive Pro system info admin menu.
	 *
	 * Fired by `admin_menu` action.
	 *
	 * @since 2.0.5
	 * @access public
	 */
	public function register_menu() {
		$system_info_text = __( 'System Info', 'responsive-addons-pro' );

		add_submenu_page(
			'responsive_add_ons',
			$system_info_text,
			$system_info_text,
			$this->capability,
			'responsive_addons_pro_system_info',
			array( $this, 'display_system_info' )
		);
	}

	/**
	 * Display page.
	 *
	 * Output the content for the main system info page.
	 *
	 * @since 2.0.5
	 * @access public
	 */
	public function display_system_info() {
		$reports_info = self::get_allowed_reports();

		$reports = $this->load_reports( $reports_info, 'html' );

		$raw_reports = $this->load_reports( $reports_info, 'raw' );

		?>
		<div id="responsive-pro-system-info">
			<h3><?php echo __( 'System Info', 'responsive-addons-pro' ); ?></h3>
			<div><?php $this->print_report( $reports, 'html' ); ?></div>
			<h3><?php echo __( 'Copy & Paste Info', 'responsive-addons-pro' ); ?></h3>
			<div id="responsive-pro-system-info-raw">
				<label id="responsive-pro-system-info-raw-code-label" for="responsive-pro-system-info-raw-code"><?php echo __( 'You can copy the below info as simple text with Ctrl+C / Ctrl+V:', 'responsive-addons-pro' ); ?></label>
				<textarea id="responsive-pro-system-info-raw-code" readonly>
					<?php
						unset( $raw_reports['wordpress']['report']['admin_email'] );

						$this->print_report( $raw_reports, 'raw' );
					?>
				</textarea>
				<script>
					var textarea = document.getElementById( 'responsive-pro-system-info-raw-code' );
					var selectRange = function() {
						textarea.setSelectionRange( 0, textarea.value.length );
					};
					textarea.onfocus = textarea.onblur = textarea.onclick = selectRange;
					textarea.onfocus();
				</script>
			</div>
			<hr>
			<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post">
				<input type="hidden" name="action" value="responsive_pro_system_info_download_file">
				<input type="submit" class="button button-primary" value="<?php echo __( 'Download System Info', 'responsive-addons-pro' ); ?>">
			</form>
		</div>
		<?php
	}

	/**
	 * Download file.
	 *
	 * Download the reports files.
	 *
	 * Fired by `wp_ajax_responsive_pro_system_info_download_file` action.
	 *
	 * @since 2.0.5
	 * @access public
	 */
	public function download_file() {
		if ( ! current_user_can( $this->capability ) ) {
			wp_die( __( 'You don\'t have permissions to download this file', 'responsive-addons-pro' ) );
		}

		$reports_info = self::get_allowed_reports();
		$reports      = $this->load_reports( $reports_info, 'raw' );

		$domain = parse_url( site_url(), PHP_URL_HOST );

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition:attachment; filename=system-info-' . $domain . '-' . gmdate( 'd-m-Y' ) . '.txt' );

		$this->print_report( $reports );

		die;
	}

	/**
	 * Get report class.
	 *
	 * Retrieve the class of the report for any given report type.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @param string $reporter_type The type of the report.
	 *
	 * @return string The class of the report.
	 */
	public function get_reporter_class( $reporter_type ) {
		return __NAMESPACE__ . '\Reporters\\' . ucfirst( $reporter_type );
	}

	/**
	 * Load reports.
	 *
	 * Retrieve the system info reports.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @param array  $reports An array of system info reports.
	 * @param string $format - possible values: 'raw' or empty string, meaning 'html'
	 *
	 * @return array An array of system info reports.
	 */
	public function load_reports( $reports, $format = '' ) {
		$result = array();

		foreach ( $reports as $report_name => $report_info ) {
			$reporter_params = array(
				'name'   => $report_name,
				'format' => $format,
			);

			$reporter_params = array_merge( $reporter_params, $report_info );

			$reporter = $this->create_reporter( $reporter_params );

			if ( ! $reporter instanceof Base ) {
				continue;
			}

			$result[ $report_name ] = array(
				'report' => $reporter->get_report( $format ),
				'label'  => $reporter->get_title(),
			);

			if ( ! empty( $report_info['sub'] ) ) {
				$result[ $report_name ]['sub'] = $this->load_reports( $report_info['sub'] );
			}
		}

		return $result;
	}

	/**
	 * Create a report.
	 *
	 * Register a new report that will be displayed in Responsive Pro system info page.
	 *
	 * @param array $properties Report properties.
	 *
	 * @return \WP_Error|false|Base Base instance if the report was created,
	 *                                       False or WP_Error otherwise.
	 * @since 2.0.5
	 * @access public
	 */
	public function create_reporter( array $properties ) {
		$properties = Report_Helper::prepare_properties( $this->get_settings( 'reporter_properties' ), $properties );

		$reporter_class = $properties['class_name'] ? $properties['class_name'] : $this->get_reporter_class( $properties['name'] );

		$reporter = new $reporter_class( $properties );

		if ( ! ( $reporter instanceof Base ) ) {
			return new \WP_Error( 'Each reporter must to be an instance or sub-instance of `Base` class.' );
		}

		if ( ! $reporter->is_enabled() ) {
			return false;
		}

		return $reporter;
	}

	/**
	 * Print report.
	 *
	 * Output the system info page reports using an output template.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @param array  $reports  An array of system info reports.
	 * @param string $template Output type from the templates folder. Available
	 *                         templates are `raw` and `html`. Default is `raw`.
	 */
	public function print_report( $reports, $template = 'raw' ) {
		static $tabs_count = 0;

		static $required_plugins_properties = array(
			'Name',
			'Version',
			'URL',
			'Author',
		);

		$template_path = __DIR__ . '/templates/' . $template . '.php';

		require $template_path;
	}

	/**
	 * Get allowed reports.
	 *
	 * Retrieve the available reports in Responsive Pro system info page.
	 *
	 * @since 2.0.5
	 * @access public
	 * @static
	 *
	 * @return array Available reports in Responsive Pro system info page.
	 */
	public static function get_allowed_reports() {
		return self::$reports;
	}

	/**
	 * Get Settings.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param string $setting Optional. The key of the requested setting. Default is null.
	 *
	 * @return mixed An array of all settings, or a single value if `$setting` was specified.
	 */
	public function get_settings( $setting = null ) {
		$this->ensure_settings();

		return self::get_items( $this->settings, $setting );
	}

	/**
	 * Ensure settings.
	 *
	 * Ensures that the `$settings` member is initialized
	 *
	 * @since 2.3.0
	 * @access private
	 */
	private function ensure_settings() {
		if ( null === $this->settings ) {
			$this->settings = $this->get_init_settings();
		}
	}

	/**
	 * Get items.
	 *
	 * Utility method that receives an array with a needle and returns all the
	 * items that match the needle. If needle is not defined the entire haystack
	 * will be returned.
	 *
	 * @since 2.3.0
	 * @access protected
	 * @static
	 *
	 * @param array  $haystack An array of items.
	 * @param string $needle   Optional. Needle. Default is null.
	 *
	 * @return mixed The whole haystack or the needle from the haystack when requested.
	 */
	final protected static function get_items( array $haystack, $needle = null ) {
		if ( $needle ) {
			return isset( $haystack[ $needle ] ) ? $haystack[ $needle ] : null;
		}

		return $haystack;
	}
}

/**
 * Initialized by calling 'get_instance()' method
 */
Module::get_instance();
