<?php
/**
 * Plugin Name:       Mb Product Filter
 * Description:       Custom product filter
 * Requires at least: 6.4.2
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Cansoft
 * Author URI:		  https://www.cansoft.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cansoft
 *
 * @package           cansoft
 */
defined('ABSPATH') or die('Nice Try!');

if ( ! defined( 'MB_FILTER_DIR_PATH' ) ) {
	define( 'MB_FILTER_DIR_PATH', __DIR__ );
}

define( 'MB_FILTER_FILE' , __FILE__ );
define( 'MB_FILTER_URL' , plugins_url( '' , MB_FILTER_FILE ) );
define( 'MB_FILTER_BASENAME' , plugin_basename(__FILE__) );

require_once MB_FILTER_DIR_PATH . '/inc/assets.php';
require_once MB_FILTER_DIR_PATH . '/functions.php';
