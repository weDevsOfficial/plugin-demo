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

/*
define( 'DISABLE_THEME', true );
define( 'DISABLE_USERS', true );
define( 'DISABLE_SETTINGS', true );
define( 'DISABLE_TOOLS', true );
define( 'DISABLE_PLUGINS', true );
 */

/**
 * Demo class for the weDevs plugins.
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

        register_activation_hook( __FILE__, array( $this, 'create_user' ) );
    }

    /**
     * In this method, we set any filters or actions and start modifying
     * our user to have the correct permissions for demo usage.
     *
     * @since 1.0.0
     */
    public function init() {

        add_action( 'woocommerce_login_form_start', array( $this, 'login_message' ) );
        add_action( 'admin_head', [ $this, 'remove_help_tabs' ] );

        add_action( 'admin_menu', array( $this, 'remove_menu_items' ) );
        add_action( 'wp_loaded', array( $this, 'cheatin' ) );

        add_action( 'admin_init', array( $this, 'admin_init' ), 11 );
        add_filter( 'login_redirect', array( $this, 'redirect' ) );

        add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ), 11 );

        add_action( 'admin_notices', [ $this, 'product_widget' ] );
        add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar' ) );
        add_filter( 'admin_footer_text', array( $this, 'footer' ) );

        add_filter( 'login_message', array( $this, 'login_message' ) );
        add_filter( 'map_meta_cap', [ $this, 'remove_caps' ], 10, 2 );
    }

    public function remove_caps( $caps, $cap ) {

        if ( defined( 'DISABLE_THEME' ) && DISABLE_THEME == true ) {
            if ( in_array( $cap, [
                    'switch_themes',
                    'edit_theme_options',
                    'update_themes',
                    'edit_themes',
                    'upload_files',
                ] ) ) {
                $caps = ['do_not_allow'];
            }
        }

        if ( defined( 'DISABLE_SETTINGS' ) && DISABLE_SETTINGS == true ) {
            if ( in_array( $cap, [
                    // 'manage_options',
                    'update_core',
                    'edit_files',
                ] ) ) {
                $caps = ['do_not_allow'];
            }
        }

        if ( defined( 'DISABLE_TOOLS' ) && DISABLE_TOOLS == true ) {
            if ( in_array( $cap, [
                    'erase_others_personal_data',
                    'export_others_personal_data',
                    'import',
                    'export',
                    'view_site_health_checks',
                ] ) ) {
                $caps = ['do_not_allow'];
            }
        }

        if ( defined( 'DISABLE_PLUGINS' ) && DISABLE_PLUGINS == true ) {
            if ( in_array( $cap, [
                    'upload_plugins',
                    'install_plugins',
                    'delete_plugins',
                    'update_plugins',
                    'edit_plugins',
                    'activate_plugins',
                ] ) ) {
                $caps = ['do_not_allow'];
            }
        }

        if ( defined( 'DISABLE_USERS' ) && DISABLE_USERS == true ) {
            if ( in_array( $cap, [
                'list_users',
                'edit_users',
                'add_users',
                'create_users',
                'delete_users',
                'promote_users',
                'remove_users',
            ] ) ) {
                $caps = ['do_not_allow'];
            }
        }

        return $caps;
    }

    function die_access() {
        wp_die( 'Sorry, you can not delete in demo mode!' );
    }

    /**
     * Create the default demo user on activation
     *
     * @return void
     */
    public function create_user() {
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

    function remove_help_tabs() {
        $screen = get_current_screen();
        $screen->remove_help_tabs();
    }

    /**
     * Remove the ability for users to mess with the screen options panel.
     *
     * @since 1.0.0
     */
    public function admin_init() {
        add_filter( 'screen_options_show_screen', '__return_false' );
        remove_action( 'welcome_panel', 'wp_welcome_panel' );
    }

    function product_widget() {
        $screen = get_current_screen();

        if ( $screen->id !== 'dashboard' ) {
            return;
        }

        $screen->remove_help_tabs();

        $source = str_replace( ['http://', 'https://'], [ '', '' ], site_url() );
        ?>
        <div class="wrap">
            <div class="welcome-panel">
                <div class="welcome-panel-content">
                    <h2><?php _e( 'Welcome to weDevs Demo!' ); ?></h2>
                    <p class="about-description">weDevs is the maker of Dokan Multivendor, WP Project Manager, WP User Frontend, WP ERP, weForms and many more.</p>

                    <div class="welcome-panel-column-container">
                        <div class="welcome-panel-column">
                            <h3><?php _e( 'Get Started' ); ?></h3>
                            <a class="button button-primary button-hero" href="https://wedevs.com/products/" target="_blank"><?php _e( 'Empower Your Site' ); ?></a>

                            <p>
                                Have any questions? Please don't hesitate to <a href="https://wedevs.com/contact/?utm_source=dokandemo&utm_medium=site&utm_campaign=Dokan+Demo" target="_blank">shoot a mail</a>.
                            </p>
                        </div>

                        <div class="welcome-panel-column product-column">
                            <ul class="wedevs-products">
                                <li>
                                    <a href="https://wedevs.com/dokan/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" title="Dokan Multivendor" class="" target="_blank">
                                        <span class="link-image">
                                            <img src="https://wedevs.com/img/Dokan.svg" alt="Dokan Multivendor" />
                                        </span>
                                        <div class="product-link-content">
                                            <span class="link-title">Dokan Multivendor</span>
                                            <span class="link-sub">Build your dream multi vendor marketplace.</span>
                                        </div>
                                    </a>
                                </li>

                                <li>
                                    <a href="https://wedevs.com/wp-user-frontend-pro/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" title="WP User Frontend Pro" class="" target="_blank">
                                        <span class="link-image">
                                            <img src="https://wedevs.com/img/User-Frontend.svg" alt="WP User Frontend Pro" />
                                        </span>
                                        <div class="product-link-content">
                                            <span class="link-title">WP User Frontend Pro</span>
                                            <span class="link-sub">Ultimate Frontend Solution for WordPress.</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://happyaddons.com/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" title="Happy Addons" class="" target="_blank">
                                        <span class="link-image">
                                            <img src="https://wedevs.com/img/happy-addons.png" alt="Happy Addons" />
                                        </span>
                                        <div class="product-link-content">
                                            <span class="link-title">Happy Addons</span>
                                            <span class="link-sub">Powerful elementor widgets to create websites.</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="welcome-panel-column product-column">
                            <ul class="wedevs-products">
                                <li>
                                    <a href="https://wedevs.com/wp-project-manager-pro/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" title="WP Project Manager Pro" class="" target="_blank">
                                        <span class="link-image">
                                            <img src="https://wedevs.com/img/Project-Manager.svg" alt="WP Project Manager Pro" />
                                        </span>
                                        <div class="product-link-content">
                                            <span class="link-title">WP Project Manager Pro</span>
                                            <span class="link-sub">Project Management tool for your team.</span>
                                        </div>
                                    </a>
                                </li>

                                <li>
                                    <a href="https://wperp.com/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" title="WP ERP" class="" target="_blank">
                                        <span class="link-image">
                                            <img src="https://wedevs.com/img/ERP.svg" alt="WP ERP" />
                                        </span>
                                        <div class="product-link-content">
                                            <span class="link-title">WP ERP</span>
                                            <span class="link-sub">Automate your business or company operation.</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://wedevs.com/wepos/?utm_source=<?php echo $source; ?>&utm_medium=WP+Dashboard+Products&utm_campaign=Plugin+Demo" title="wePOS" class="" target="_blank">
                                        <span class="link-image">
                                            <img src="https://wedevs.com/img/wepos.svg" alt="wePOS" />
                                        </span>
                                        <div class="product-link-content">
                                            <span class="link-title">wePOS</span>
                                            <span class="link-sub">Fastest POS System for WooCommerce.</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .welcome-panel { margin-top: 30px; }

            h1,
            .welcome-panel .error {
                display: none;
            }

            ul.wedevs-products {
                width: 100%;
                overflow: hidden;
            }

            ul.wedevs-products img {
                max-width: 64px;
            }

            ul.wedevs-products span {
                color: #777;
                font-size: 13px;
            }

            ul.wedevs-products li {
                padding: 0;
                margin: 0;
            }

            ul.wedevs-products li a {
                display: flex;
                padding: 10px;
            }

            ul.wedevs-products li a:hover {
                background: #eff4f4;
            }

            ul.wedevs-products .link-image {
                width: 35px;
                display: inline-table;
            }

            .link-image img {
                max-width: 35px;
                width: 35px;
                height: auto;
                border-radius: 50%;
                /*box-shadow: 0 5px 15px 0 rgba(254,139,119,.3);*/
            }

            ul.wedevs-products li .product-link-content {
                margin-left: 15px;
            }

            ul.wedevs-products li .product-link-content .link-title {
                display: block;
                padding-top: 0;
                font-size: 16px;
                font-weight: 700;
                margin-bottom: 5px;
                color: #000;
            }

            #dashboard-widgets {
                display: none;
            }

            .welcome-panel-column.product-column {
                margin-top: 10px;
            }
        </style>
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
        $remove_menu_items = array(
            __( 'Profile' ),
            __( 'Tools'),
        );

        if ( defined( 'DISABLE_SETTINGS' ) && DISABLE_SETTINGS == true ) {
            $remove_menu_items[] = __( 'Settings' );
        }

        while ( prev( $menu ) ) {
            $item = explode( ' ', $menu[key( $menu )][0] );
            if ( in_array( $item[0] != null ? $item[0] : '', $remove_menu_items ) )
                unset( $menu[key( $menu )] );
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
        $not_allowed = array(
            'link-manager.php',
            'link-add.php',
            'theme-editor.php',
            'plugins.php',
            'plugin-install.php',
            'plugin-editor.php',
            'profile.php',
        );

        if ( defined( 'DISABLE_SETTINGS' ) && DISABLE_SETTINGS == true ) {
            $not_allowed[] = 'options-general.php';
        }

        /** If we find a user is trying to access a forbidden page, redirect them back to the dashboard */
        if ( in_array( $pagenow, $not_allowed ) ) {
            wp_safe_redirect( get_admin_url() );
            exit;
        }

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

        return sprintf( __( 'You are currently enjoying a demo of our product. Want the real thing? <a href="%s" title="Click here to purchase a license!" target="_blank">Click here to purchase a license!</a>' ), 'https://wedevs.com/products/?utm_source=' . $source . '&utm_medium=wp_dashboard_footer&utm_campaign=Plugin+Demo' );
    }

}

new WeDevs_Plugin_Demo();
