<?php
/*
Plugin Name: weDevs Plugin Demo
Plugin URI: https://wedevs.com/
Description: The demo plugin for weDevs products
Author: Tareq Hasan
Author URI: https://tareq.co
Version: 1.0.0
License: GNU General Public License v2.0 or later
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

/**
 * Demo class for the Soliloquy for WordPress plugin.
 *
 * It is a final class so it cannot be extended.
 *
 */
class WeDevs_Plugin_Demo {

    /**
     * Constructor. Loads the class.
     *
     * @since 1.0.0
     */
    public function __construct() {

        /** Load the class */
        $this->load();
    }

    /**
     * Hooks all interactions into WordPress to kickstart the class.
     *
     * @since 1.0.0
     */
    private function load() {

        /** Hook everything into plugins_loaded */
        add_action( 'plugins_loaded', array( $this, 'init' ) );

        register_activation_hook( __FILE__, array( $this, 'remove_capabilities' ) );
    }

    /**
     * In this method, we set any filters or actions and start modifying
     * our user to have the correct permissions for demo usage.
     *
     * @since 1.0.0
     */
    public function init() {

        add_action( 'woocommerce_login_form_start', array( $this, 'login_message' ) );

        /** Don't process anything unless the current user is a demo user */
        if ( $this->is_demo_user() ) {

            /** Load hooks and filters */
            // add_action( 'wp_loaded', array( $this, 'cheatin' ) );
            add_action( 'admin_init', array( $this, 'admin_init' ), 11 );
            add_filter( 'login_redirect', array( $this, 'redirect' ) );

            add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ), 11 );

            // add_action( 'admin_menu', array( $this, 'remove_menu_items' ) );
            // add_action( 'admin_notices', array( $this, 'notices' ) );
            // add_action( 'untrashed_post', array( $this, 'trash' ) );
            add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar' ) );
            add_action( 'admin_footer', array( $this, 'jquery' ) );
            add_filter( 'admin_footer_text', array( $this, 'footer' ) );

            add_filter( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ) );

            // add_action( 'delete_attachment', array( $this, 'die_access' ) );
            // add_action( 'wp_trash_post', array( $this, 'die_access' ) );
            // add_action( 'before_delete_post', array( $this, 'die_access' ) );

            // remove trash row actions
            // add_filter( 'page_row_actions', [ $this, 'remove_trash_row_actions' ], 10, 2 );
            // add_filter( 'post_row_actions', [ $this, 'remove_trash_row_actions' ], 10, 2 );
            // add_filter( 'product_row_actions', [ $this, 'remove_trash_row_actions' ], 10, 2 );
        }

        add_filter( 'login_message', array( $this, 'login_message' ) );
    }

    function die_access() {
        wp_die( 'Sorry, you can not delete in demo mode!' );
    }

    public function remove_capabilities() {
        $user = get_role( 'administrator' );

        // update capabitilies
        $user->remove_cap( 'update_core' );
        $user->remove_cap( 'edit_files' );

        // plugin capabilities
        $user->remove_cap( 'install_plugins' );
        $user->remove_cap( 'update_plugins' );
        $user->remove_cap( 'edit_plugins' );
        $user->remove_cap( 'upload_plugins' );
        $user->remove_cap( 'delete_plugins' );

        // user capabilities
        $user->remove_cap( 'create_users' );
        $user->remove_cap( 'promote_users' );
        $user->remove_cap( 'remove_users' );

        // theme capabilities
        $user->remove_cap( 'install_themes' );
        $user->remove_cap( 'update_themes' );
        $user->remove_cap( 'delete_themes' );
        $user->remove_cap( 'edit_themes' );

        // reset the demo user password
        $demo_user = get_user_by( 'login', 'demo' );

        if ( $demo_user ) {
            wp_update_user( array(
                'user_pass' => 'demo'
            ) );

        } else {

            wp_create_user( 'demo', 'demo', 'demo@plugindemo.com' );
        }
    }

    /**
     * Make sure users don't try to access an admin page that they shouldn't.
     *
     * @since 1.0.0
     *
     * @global string $pagenow The current page slug
     */
    public function cheatin() {

        global $pagenow;

        /** Paranoia security to make sure the demo user cannot access any page other than what we specify */
        $not_allowed = array( 'update-core.php', 'link-manager.php', 'link-add.php', 'theme-editor.php', 'plugins.php', 'plugin-install.php', 'plugin-editor.php', 'users.php', 'user-new.php', 'profile.php', 'options-general.php', 'options-permalink.php' );

        /** If we find a user is trying to access a forbidden page, redirect them back to the dashboard */
        if ( in_array( $pagenow, $not_allowed ) ) {
            wp_safe_redirect( get_admin_url() );
            exit;
        }

    }

    /**
     * Remove row trash action link for demo user
     *
     * @param  array $actions
     * @param  WP_Post $post
     *
     * @return array
     */
    public function remove_trash_row_actions( $actions, $post ) {

        if ( $post->post_author != get_current_user_id() ) {
            unset( $actions['trash'] );
        }

        return $actions;
    }

    /**
     * Remove the ability for users to mess with the screen options panel.
     *
     * @since 1.0.0
     */
    public function admin_init() {

        add_filter( 'screen_options_show_screen', '__return_false' );
    }

    function dashboard_widget() {
        wp_add_dashboard_widget( 'wedevs_dashboard_plugins', __( 'Our Products', 'wedevs' ), array( $this, 'product_widget' ) );
        add_meta_box( 'wedevs_dashboard_contact', __( 'Have any questions?', 'wedevs' ), array( $this, 'contact_widget' ), 'dashboard', 'side', 'high' );
    }

    function product_widget() {
        $source = str_replace( ['http://', 'https://'], [ '', '' ], site_url() );
        ?>
        <ul class="wedevs-products">
            <li>
                <a href="https://wedevs.com/wp-user-frontend-pro/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" target="_blank"><img src="https://wedevs-com-wedevs.netdna-ssl.com/wp-content/uploads/2017/07/wpuf-300x250.png" alt="WP User Frontend Pro"></a>
                <span>The most popular Frontend Post Submission plugin for Custom Post Type</span>
            </li>
            <li>
                <a href="https://wedevs.com/dokan/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" target="_blank"><img src="https://wedevs-com-wedevs.netdna-ssl.com/wp-content/uploads/2017/07/dokan-multivendor-300x250.png" alt="Dokan Multi-vendor Marketplace"></a>
                <span>The complete WooCommerce powered multi vendor eCommerce solution for WordPress.</span>
            </li>
            <li>
                <a href="https://wedevs.com/wp-project-manager/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" target="_blank"><img src="https://wedevs-com-wedevs.netdna-ssl.com/wp-content/uploads/2017/07/project-manager-300x250.png" alt="WP Project Manager Pro"></a>
                <span>Task and Project management with your team and clients on your WordPress powered site</span>
            </li>
            <!--<li>
                <a href="https://wperp.com/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" target="_blank"><img src="https://ps.w.org/erp/assets/icon-256x256.png?rev=1373388" alt="WordPress ERP"></a>
                <span>Enterprise Management with free CRM, HRM, Accounting and industry targeted extensions</span>
            </li>-->
        </ul>

        <style>
            ul.wedevs-products {
                width: 100%;
                overflow: hidden;
            }

            ul.wedevs-products img {
                max-width: 100%;
            }

            ul.wedevs-products span {
                color: #777;
                font-size: 13px;
            }

            ul.wedevs-products li {
                width: 48%;
                float: left;
                margin-bottom: 3%;
            }

            ul.wedevs-products li:nth-child(2n+1) {
                margin-right: 4%;
            }
        </style>
        <?php
    }

    function contact_widget() {
        ?>
        <h2>Do you have any questions?</h2>

        <div style="padding-top: 10px;">
            Please don't hesitate to <a href="https://wedevs.com/contact/?utm_source=dokandemo&utm_medium=site&utm_campaign=Dokan+Demo" target="_blank">shoot a mail</a>.
        </div>
        <?php
    }

    /**
     * Redirect the user to the Dashboard page upon logging in.
     *
     * @since 1.0.0
     *
     * @param string $redirect_to Default redirect URL (profile page)
     * @return string $redirect_to Amended redirect URL (dashboard)
     */
    public function redirect( $redirect_to ) {

        return get_admin_url();
    }

    /**
     * Customize the login message with the demo username and password.
     *
     * @since 1.0.0
     *
     * @param string $message The default login message
     * @return string $message Amended login message
     */
    public function login_message( $message ) {

        $message = '<div style="font-size: 15px; text-align: center; border: 1px solid rgb(204, 204, 204); padding: 10px; margin: 0px 0px 15px;">';
        $message .= '<p>Use the demo login credentials below:</p><br />';
        $message .= '<strong>Username: </strong> <span style="color: #cc0000;">demo</span><br />';
        $message .= '<strong>Password: </strong> <span style="color: #cc0000;">demo</span><br /><br />';
        $message .= '</div>';

        echo $message;

    }

    /**
     * If the user is not an admin, set the dashboard screen to one column
     * and remove the default dashbord widgets.
     *
     * @since 1.0.0
     *
     * @global string $pagenow The current page slug
     */
    public function remove_dashboard_widgets() {
        global $pagenow, $wp_meta_boxes;

        $layout = get_user_option( 'screen_layout_dashboard', get_current_user_id() );

        /** Set the screen layout to one column in the dashboard */
        if ( 'index.php' == $pagenow && 1 !== $layout ) {
            update_user_option( get_current_user_id(), 'screen_layout_dashboard', 1, true );
        }

        remove_action( 'admin_notices', 'update_nag', 3 );

        // woocommerce specific
        if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_recent_reviews'] ) ) {
            unset($wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_recent_reviews']);
        }

        if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_status'] ) ) {
            unset($wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_status']);
        }

        /** Remove dashboard widgets from view */
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
    }

    /**
     * Remove certain menu items from view so demo user cannot mess with them.
     *
     * @since 1.0.0
     *
     * @global array $menu Current array of menu items
     */
    public function remove_menu_items() {

        global $menu;
        end( $menu );

        /** Remove the first menu separator */
        unset( $menu[4] );

        /** Now remove the menu items we don't want our user to see */
        $remove_menu_items = array( __( 'Profile' ), __( 'Users' ), __( 'Plugins' ), __( 'Tools'), __( 'Settings' ), __( 'Appearance' ) );

        while ( prev( $menu ) ) {
            $item = explode( ' ', $menu[key( $menu )][0] );
            if ( in_array( $item[0] != null ? $item[0] : '', $remove_menu_items ) )
                unset( $menu[key( $menu )] );
        }

    }

    /**
     * Modify the admin bar to remove unnecessary links.
     *
     * @since 1.0.0
     *
     * @global object $wp_admin_bar The admin bar object
     */
    public function admin_bar() {

        global $wp_admin_bar;

        /** Remove admin bar menu items that demo users don't need to see or access */
        $wp_admin_bar->remove_menu( 'wp-logo' );
        $wp_admin_bar->remove_menu( 'updates' );
        $wp_admin_bar->remove_menu( 'new-content' );
        $wp_admin_bar->remove_menu( 'comments' );
        $wp_admin_bar->remove_menu( 'user-info' );
        $wp_admin_bar->remove_menu( 'edit-profile' );
    }

    /**
     * We can't filter the Profile URL for the main account link in the admin bar, so we
     * replace it using jQuery instead. We also remove the "+ New" item from the admin bar.
     *
     * This method also adds some extra text to spice up the currently empty dashboard area.
     * Call it plugin marketing if you will. :-)
     *
     * @since 1.0.0
     */
    public function jquery() {

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                /** Remove items from the admin bar first */
                $('#wp-admin-bar-my-account a:first').attr('href', '<?php echo get_admin_url(); ?>');
                $('#wp-admin-bar-view').remove();

                /** Customize the Dashboard area */
                // $('.index-php #normal-sortables').fadeIn('fast', function(){
                //     /** Change width of the container */
                //     $(this).css({ 'height' : 'auto' });

                //     /** Store HTML output in a variable */
                //     var output = '';

                //     /** Build the HTML */
                //     output += '<div class="soliloquy-logo">Soliloquy - The Best Responsive WordPress Slider Plugin. Period.</div>';
                //     output += '<p style="font-size: 15px; font-weight: bold; line-height: 22px;">Within this demo area, you have the ability to create, edit, publish and delete sliders, just like you would on a real install of Soliloquy. Just follow the simple instructions below to get started:</p>';
                //     output += '<ol style="font-size: 13px">';
                //     output += '<li>Navigate to Soliloquy > Add New to create a new slider.</li>';
                //     output += '<li>Give your slider a title and then click on the "Click Here to Upload Images" button to begin uploading your images.</li>';
                //     output += '<li>Play around with the settings, sort your images, add some image meta, and get a feel for how the plugin works.</li>';
                //     output += '<li>Publish your slider and go to the home page to see it active.</li>';
                //     output += '<li><a href="http://soliloquywp.com/pricing/" title="Purchase Soliloquy because it is simply the best! :-)" target="_blank">Purchase Soliloquy because it is simply the best! :-)</a></li>';
                //     output += '</ol>';
                //     output += '<p style="color: #cc0000; font-size: 15px; font-weight: bold; line-height: 22px; margin-bottom: 40px;">Since this is a demo, there is a limit of 5 slider instances at any given time, and all slider instances and associated data are wiped every 12 hours.</p>';

                //     /** Output the HTML */
                //     $(this).html(output);
                // });
            });
        </script>
        <style type="text/css">.soliloquy-logo { background: url(<?php echo plugins_url( 'login-logo.png', __FILE__ ); ?>) no-repeat scroll 0 0; display: block; height: 64px; margin: 40px auto 30px; text-align: center; text-indent: -9999px; width: 312px; } @media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (-o-min-device-pixel-ratio: 3/2), only screen and (min--moz-device-pixel-ratio: 1.5), only screen and (min-device-pixel-ratio: 1.5) { .soliloquy-logo { background-image: url(<?php echo plugins_url( 'login-logo@2x.png', __FILE__ ); ?>); background-size: 312px 64px; } }</style>
        <?php

    }

    /**
     * Modify the footer text for the demo area.
     *
     * @since 1.0.0
     *
     * @param string $text The default footer text
     * @return string $text Amended footer text
     */
    public function footer( $text ) {
        $source = str_replace( ['http://', 'https://'], [ '', '' ], site_url() );

        return sprintf( __( 'You are currently enjoying a demo of our product. Want the real thing? <a href="%s" title="Click here to purchase a license!" target="_blank">Click here to purchase a license!</a>' ), 'https://wedevs.com/product-category/plugins/?utm_source=' . $source . '&utm_medium=wp_dashboard_footer&utm_campaign=Plugin+Demo' );
    }

    /**
     * Helper function for determining whether the current user is a demo user or not.
     *
     * @since 1.0.0
     *
     * @return bool Whether or not the user is a demo
     */
    private function is_demo_user() {

        return true;

        $current_user = wp_get_current_user();

        if ( $current_user->user_login == 'demo' ) {
            return true;
        }

        return false;
    }

}

new WeDevs_Plugin_Demo();
