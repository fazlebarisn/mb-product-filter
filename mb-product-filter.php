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

if ( ! defined( 'CANSOFT_FILTER_DIR_PATH' ) ) {
	define( 'CANSOFT_FILTER_DIR_PATH', __DIR__ );
}

define( 'CANSOFT_FILTER_FILE' , __FILE__ );
define( 'CANSOFT_FILTER_URL' , plugins_url( '' , CANSOFT_FILTER_FILE ) );
define( 'CANSOFT_FILTER_BASENAME' , plugin_basename(__FILE__) );

require_once CANSOFT_FILTER_DIR_PATH . '/functions.php';
