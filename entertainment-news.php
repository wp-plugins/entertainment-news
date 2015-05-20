<?php
/*
   Plugin Name: Entertainment News
   Plugin URI: http://wordpress.org/extend/plugins/entertainment-news/
   Version: 1.2
   Author: One Fold Media
   Author URI: http://onefoldmedia.com/
   Description: Need content? The Entertainment News plugin will display unique articles for your site.
   Text Domain: entertainment-news
   Requires at least: 4.0
   Tested up to: 4.2.2
   License: GPLv3
  */

/*
	Plugin Copyright (C) 2015 one fold media (eddie@onefoldmedia.com)

    "WordPress Plugin Template" Copyright (C) 2015 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This following part of this file is part of WordPress Plugin Template for WordPress.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

$EntertainmentNews_minimalRequiredPhpVersion = '5.3';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function EntertainmentNews_noticePhpVersionWrong() {
    global $EntertainmentNews_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Entertainment News" requires a newer version of PHP to be running.',  'entertainment-news').
            '<br/>' . __('Minimal version of PHP required: ', 'entertainment-news') . '<strong>' . $EntertainmentNews_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'entertainment-news') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function EntertainmentNews_PhpVersionCheck() {
    global $EntertainmentNews_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $EntertainmentNews_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'EntertainmentNews_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function EntertainmentNews_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('entertainment-news', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// First initialize i18n
EntertainmentNews_i18n_init();


// Next, run the version check.
// If it is successful, continue with initialization for this plugin
if (EntertainmentNews_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('entertainment-news_init.php');
    EntertainmentNews_init(__FILE__);
}
