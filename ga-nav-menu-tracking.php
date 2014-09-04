<?php
/*
Plugin Name: Google Analytics Nav Menu Tracking
Plugin URI: http://secretstache.com
Description: Adds tracking events to your WordPress navigation menu items
Version: 1.0.6
Author: Paul de Wouters
Author URI: http://paulwp.com
License: GPL2

    Copyright 2013  Secret Stache Media (email: paul@secretstache.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( "GA_Nav_Tracking" ) ) :

	class GA_Nav_Tracking {

		function __construct() {

			define( 'SSM_GA_VERSION', '1.0.5' );
			// Include required files
			if ( is_admin() ) {
				$this->admin_includes();
			}

			// load the textdomain
			add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );

			// switch the admin walker
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ), 10, 2 );

			// save the menu item meta
			add_action( 'wp_update_nav_menu_item', array( $this, 'nav_update' ), 10, 3 );

			// exclude items via filter instead of via custom Walker
			if ( ! is_admin() ) {
				add_filter( 'walker_nav_menu_start_el', array( $this, 'add_tracking' ), 10, 4 );
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		}

		/**
		 * Include required admin files.
		 *
		 * @access public
		 * @return void
		 */
		function admin_includes() {
			/* include the custom admin walker */
			include_once( plugin_dir_path( __FILE__ ) . 'inc/class.Walker_GA_Menu_Tracking.php' );
		}

		/**
		 * Make Plugin Translation-ready
		 * CALLBACK FUNCTION FOR:  add_action( 'plugins_loaded', array( $this,'load_text_domain'));
		 * @since 1.0
		 */
		function load_text_domain() {
			load_plugin_textdomain( 'ga-nav-menu-tracking', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Override the Admin Menu Walker
		 * @since 1.0
		 */
		function edit_nav_menu_walker( $walker, $menu_id ) {
			return 'Walker_Nav_Menu_GA_Menu_Tracking';
		}

		/**
		 * Save the custom field data
		 */
		function nav_update( $menu_id, $menu_item_db_id, $args ) {

			// verify this came from our screen and with proper authorization.
			if ( ! isset( $_POST['ga-nav-menu-tracking-nonce'] ) || ! wp_verify_nonce( $_POST['ga-nav-menu-tracking-nonce'], 'nav-menu-nonce-name' ) )
				return;

			$saved_data = array();

			if ( isset( $_POST['menu-item-category'][$menu_item_db_id] ) ) {
				$saved_data['category'] = stripslashes(strip_tags($_POST['menu-item-category'][$menu_item_db_id]));
			}
			if ( isset( $_POST['menu-item-action'][$menu_item_db_id] ) ) {
				$saved_data['action'] = stripslashes(strip_tags($_POST['menu-item-action'][$menu_item_db_id]));
			}
			if ( isset( $_POST['menu-item-label'][$menu_item_db_id] ) ) {
				$saved_data['label'] = stripslashes(strip_tags($_POST['menu-item-label'][$menu_item_db_id]));
			}

			if ( isset( $_POST['menu-item-value'][$menu_item_db_id] ) ) {
				$saved_data['value'] = intval($_POST['menu-item-value'][$menu_item_db_id]);
			}

			if ( isset( $_POST['menu-item-noninteraction'][$menu_item_db_id] ) ) {
				$saved_data['noninteraction'] = $_POST['menu-item-noninteraction'][$menu_item_db_id];
			}

			if ( ! empty( $saved_data['category'] ) ) {
				update_post_meta( $menu_item_db_id, '_nav_menu_ga_category', $saved_data['category'] );
			} else{
				delete_post_meta($menu_item_db_id,'_nav_menu_ga_category' );
			}
			if ( ! empty( $saved_data['action'] ) ) {
				update_post_meta( $menu_item_db_id, '_nav_menu_ga_action', $saved_data['action'] );
			} else{
				delete_post_meta($menu_item_db_id,'_nav_menu_ga_action' );
			}
			if ( ! empty( $saved_data['label'] ) ) {
				update_post_meta( $menu_item_db_id, '_nav_menu_ga_label', $saved_data['label'] );
			} else{
				delete_post_meta($menu_item_db_id,'_nav_menu_ga_label' );
			}

			if ( ! empty( $saved_data['value'] ) ) {
				update_post_meta( $menu_item_db_id, '_nav_menu_ga_value', $saved_data['value'] );
			} else{
				delete_post_meta($menu_item_db_id,'_nav_menu_ga_value' );
			}

			if ( isset( $saved_data['noninteraction'] ) ) {
				update_post_meta( $menu_item_db_id, '_nav_menu_ga_non_interaction', $saved_data['noninteraction'] );
			} else{
				delete_post_meta($menu_item_db_id,'_nav_menu_ga_non_interaction' );
			}

		}

		/**
		 * outputs a span tag with the _trackEvent function parameters for use by the
		 * event handler script
		 */
		function add_tracking( $item_output, $item, $depth, $args ) {
			// Ref: https://developers.google.com/analytics/devguides/collection/gajs/eventTrackerGuide
			$method = '_trackEvent';
			$category = get_post_meta( $item->ID, '_nav_menu_ga_category', true );
			$action = get_post_meta( $item->ID, '_nav_menu_ga_action', true );
			$label = get_post_meta( $item->ID, '_nav_menu_ga_label', true );
			$value = get_post_meta( $item->ID, '_nav_menu_ga_value', true );
			$non_interaction = get_post_meta( $item->ID, '_nav_menu_ga_non_interaction', true );
			if(get_post_meta( $item->ID, '_nav_menu_ga_non_interaction', true )){
				$non_interaction = true;
			} else {
				$non_interaction = false;
			}
			return $item_output
			       . '<span style="display:none;" class="ga-tracking"'
			       . ' data-method="' . esc_attr( $method ) . '"'
			       . ' data-category="' . esc_attr( $category ) . '"'
			       . ' data-action="' . esc_attr( $action ) . '"'
			       . ' data-label="' . esc_attr( $label ) . '"'
			       . ' data-value="' . esc_attr( $value ) . '"'
			       . ' data-noninteraction="' .  esc_attr( $non_interaction ) . '"'
			       . '>'
			       . '</span>';

		}

		/**
		 * Load the javwscript
		 */
		function register_scripts() {
			wp_register_script( 'ga-tracking', plugins_url( '/js/plugin.js', __FILE__ ), array( 'jquery' ), SSM_GA_VERSION, true );
			wp_enqueue_script( 'ga-tracking' );
		}

	} // end class

endif; // class_exists check


/**
 * Launch the whole plugin
 */
global $GA_Nav_Tracking;
$GA_Nav_Tracking = new GA_Nav_Tracking();
