<?php
/**
 * Plugin Name:		Postbox to Tab
 * Description: 	Convert postboxes vertical list into a tabbed menu.
 * Author: 				Felipe Paul Martins - Opus Magnum
 * Version: 			1.1
 * Author URI:		https://opusmagnum.ch
 * License:				GPL-2.0+
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:		pbtt
 * Domain Path:		/languages
 *
 * Postbox to Tab is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * Postbox to Tab is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package		postbox-to-tab
 * @author		Felipe Paul Martins <fpm@opusmagnum.ch>
 * @license		GPL-2.0+
 * @link			https://opusmagnum.ch
 */

/* Prevent loading this file directly */
defined( 'ABSPATH' ) || exit;

/**
 * Add default options on plugin activation.
 * @since 1.1
 */
function postbox_to_tab_activate() {

	add_option( 'pbtt_zone', array('normal') );
	add_option( 'pbtt_posttype', array('post', 'page') );
}
register_activation_hook( __FILE__, 'postbox_to_tab_activate' );


if ( !class_exists( 'Postbox_to_tab' ) ) {

	/**
	 * Class Postbox_to_tab
	 * @since 1.0
	 */
	class Postbox_to_tab {

		/**
		 * Class Constructor.
		 * @since  1.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'pbtt_setup' ), 1 );
			add_action( 'plugins_loaded', array( $this, 'pbtt_init' ), 10 );
		}

		/**
		 * Setup ID, Version, Directory path, and URI
		 * @since  1.0
		 */
		public function pbtt_setup() {
			$this->id							= 'pbtt';
			$this->version				= '1.1';
			$this->directory_path	= trailingslashit( plugin_dir_path( __FILE__ ) );
			$this->directory_uri	= trailingslashit( plugin_dir_url(  __FILE__ ) );
		}

		/**
		 * Init the plugin functions
		 * @since	1.0
		 */
		public function pbtt_init() {

			require_once $this->directory_path .'options.php';
			add_action('admin_enqueue_scripts', array( $this, 'pbtt_enqueue_files') );
			add_filter('plugin_action_links_'. plugin_basename(__FILE__), array( $this, 'pbtt_options_link') );
		}

		/**
		 * Enqueuing js/css files only in edition page
		 * @since	1.0
		 * @param	string $hook Hook suffix for the current admin page.
		 */
		public function pbtt_enqueue_files( $hook ) {

			if ( !in_array( $hook, ['post.php', 'post-new.php']) )
				return;

			$posttypes = get_option( 'pbtt_posttype' );
			if ( !(is_array($posttypes) && in_array(get_post_type(), $posttypes)) )
				return;

			wp_enqueue_script( 'pbtt_js', $this->directory_uri . 'js/pbtt.js', array('jquery', 'jquery-ui-sortable'), $this->version );
			wp_enqueue_style( 'pbtt_css', $this->directory_uri . 'css/pbtt.css', false, $this->version );

			if ( is_array($zones = get_option('pbtt_zone')) ) {
				wp_localize_script( 'pbtt_js', 'pbtt', array_flip($zones) );
			}
		}

		/**
		 * Add options action link on plugin page
		 *
		 * @since 1.1
		 *
		 * @param		array	$links	An array of plugin action links
		 * @return	array					Array with extra setting link
		 */
		public function pbtt_options_link( $links ) {
			$links[] = sprintf( '<a href="tools.php?page=pbtt">%s</a>', __("Options", 'pbtt') );
			return $links;
		}
	}
}

new Postbox_to_tab();
