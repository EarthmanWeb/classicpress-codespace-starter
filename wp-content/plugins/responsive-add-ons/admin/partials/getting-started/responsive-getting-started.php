<?php
/**
 * Provide a admin area view for the plugin
 *
 * Getting Started Settings Tab
 *
 * @link       https://cyberchimps.com/
 * @since      4.8.8
 *
 * @package responsive
 */

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="container-fluid">
	<div class="row">
		<!-- require menu here -->
        <?php require_once RESPONSIVE_ADDONS_DIR . '/admin/partials/getting-started/responsive-getting-started-menus.php'; ?>
		<!-- require feature here -->
		<?php require_once RESPONSIVE_ADDONS_DIR . '/admin/partials/getting-started/responsive-getting-started-features.php'; ?>
	</div>
</div>
