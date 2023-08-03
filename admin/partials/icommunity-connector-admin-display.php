<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h2><?php echo __( 'IBS Connector Settings', 'icommunity-connector' );?></h2>
<form action="options.php" method="post">
    <?php
    settings_fields('icommunity_connector_plugin_options' );
    do_settings_sections( 'icommunity-connector' );
    submit_button('Save settings','primary','icommunity_save_settings');
    ?>
</form>
