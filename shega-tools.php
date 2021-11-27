<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Shega_Tool
 *
 * @wordpress-plugin
 * Plugin Name:       Shega Tools
 * Plugin URI:        https://shega.co/tools/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Shega
 * Author URI:        https://ba5liel.github.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shega-tool
 * Domain Path:       /languages
 */


require_once( __DIR__ . '/includes/class-PHPFormBuilder.php' );
require_once( __DIR__ . '/admin/class-WPSimpleFormAdmin.php' );

// Main Plugin Class
if ( ! class_exists( 'WPSimpleForm' ) ) {
    class WPSimpleForm {
        public function __construct() {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_shortcode( 'shega-tool-tax', array( $this, 'form' ) );
            add_action( 'admin_post_nopriv_wpsf_contact_form', array( $this, 'form_handler' ) );
            add_action( 'admin_post_wpsf_contact_form', array( $this, 'form_handler' ) );
        }
        
        public function enqueue_scripts() {
            wp_enqueue_style( 'wpsimpleform', plugins_url( '/public/css/style.css', __FILE__ ), array(), 0.1 );
        }
        
        public function form($atts) {
            global $post;
            
            $atts = shortcode_atts(
            array(
              'add_honeypot' => false,
            ), $atts, 'wpsimpleform' );
          
       
            
        // Shortcodes should not output data directly
        ob_start(); 
        
        // Status message
        $status = filter_input( INPUT_GET, 'status', FILTER_VALIDATE_INT );
        
        if ( $status == 1 ) {
            printf( '<div class="wp-simpleform message success"><p>%s</p></div>', __( 'Submitted successfully!', 'wp-simple-form' ) );
        }
        
        // Build the form
       ?>
       <h1>Hellow wwwww</h1>
       <?php
        // Return and clean buffer contents
        return ob_get_clean();
        }
        
        public function form_handler() {
            $post = $_POST;
            
            // Verify nonce
            if ( ! isset( $post['wp_nonce'] ) || ! wp_verify_nonce( $post['wp_nonce'], 'submit_wp_simple_form') ) {
                wp_die( __( "Cheatin' uh?", 'wp-simple-form' ) );
            }
            
            // Verify required fields
            $required_fields = array( 'name', 'email', 'message' );
            
            foreach ( $required_fields as $field ) {
                if ( empty( $post[$field] ) ) {
                    wp_die( __( "Name, email and message fields are required.", 'wp-simple-form' ) );
                }
            }
            
            // Build post arguments
            $postarr = array(
                'post_author' => 1,
                'post_title' => sanitize_text_field( $post['name'] ),
                'post_content' => sanitize_textarea_field( $post['message'] ),
                'post_type' => 'wpsf_contact_form',
                'post_status' => 'publish',
                'meta_input' => array(
                    'submission_email' => sanitize_email( $post['email'] ),
                    'submission_website' => sanitize_text_field( $post['website'] ),
                )
            );
            
            // Insert the post
            $postid = wp_insert_post( $postarr, true );

            if ( is_wp_error( $postid ) ) {
                wp_die( __( "There was problem with your submission. Please try again.", 'wp-simple-form' ) );
            }
            
            // Send emails to admins
            $to = array();
            $post_edit_url = sprintf( '%s?post=%s&action=edit', admin_url( 'post.php' ), $postid );
            $admins = get_users( array( 'role' => 'administrator' ) );
            
            foreach ( $admins as $admin ) {
                $to[] = $admin->user_email;
            }
            
            // Build the email
            $subject = __( 'New feedback!', 'wp-simple-form' );
            $message = sprintf( '<p>%s</p>', __( 'Here are the details:', 'wp-simple-form' ) ) ;
            $message .= sprintf( '<p>%s: %s<br>', __( 'Name', 'wp-simple-form' ), sanitize_text_field( $post['name'] ) );
            $message .= sprintf( '<p>%s: %s<p>', __( 'Name', 'wp-simple-form' ), sanitize_textarea_field( $post['message'] ) );
            $message .= sprintf( '<p>%s: <a href="%s">%s</a>', __( 'View/edit the full message here', 'wp-simple-form' ), $post_edit_url, $post_edit_url );
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            // Send the email
            wp_mail( $to, $subject, $message, $headers );
            
            // Redirect back to page
            wp_redirect( add_query_arg( 'status', '1', get_permalink( $post['redirect_id'] ) ) );
        }
    }
}

$wpsimpleform = new WPSimpleForm;