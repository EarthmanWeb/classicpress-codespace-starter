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
<!-- require white label here -->
<div class="col-md-10 responsive-theme-setting-border-left">
<?php 
    require_once RESPONSIVE_ADDONS_DIR . '/admin/partials/getting-started/responsive-app-connection-setting.php';
    require_once RESPONSIVE_ADDONS_DIR . '/admin/partials/getting-started/responsive-white-label.php';
 ?>
</div>
<?php
