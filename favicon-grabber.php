<?php

/**
 * Favicon Grabber
 *
 * @package     FaviconGrabber
 * @author      Henri Susanto
 * @copyright   2022 Henri Susanto
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Favicon Grabber
 * Plugin URI:  https://github.com/susantohenri/wp-favicon-grabber
 * Description: WordPress Plugin to grab favicon
 * Version:     1.0.0
 * Author:      Henri Susanto
 * Author URI:  https://github.com/susantohenri/
 * Text Domain: FaviconGrabber
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

add_shortcode('favicon-grabber', function () {

    if (isset($_POST['grab-favicon'])) {
        $html = file_get_contents($_POST['url']);

        $link = explode('rel="icon"', $html);
        $link = explode('href="', $link[1]);
        $link = explode('"', $link[1]);
        $link = $link[0];

        echo "<img src='{$link}'>";
    }

    return "
        <form method='POST'>
            <input type='text' name='url'>
            <input type='submit' name='grab-favicon'>
        </form>
    ";
});