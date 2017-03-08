<?php
/**
 * Activation handler
 *
 * @package AffiliateWP\ActivationHandler
 * @since   1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * AffiliateWP Activation Handler Class
 *
 * @since 1.0.0
 */
class AffiliateWP_Activation {

	/**
	 * Plugin name.
	 *
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
    public $plugin_name;

	/**
	 * Plugin path.
	 *
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
    public $plugin_path;

	/**
	 * Main plugin filename.
	 *
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
    public $plugin_file;

	/**
	 * Whether AffiliateWP is installed.
	 *
	 * @access public
	 * @since  1.0.0
	 * @var    bool
	 */
    public $has_affiliatewp;

    /**
     * Sets up the activation class.
     *
     * @access public
     * @since  1.0.0
     *
     * @param string $plugin_path Plugin path.
     * @param string $plugin_file Main plugin filename.
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        if ( isset( $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] ) ) {
            $this->plugin_name = str_replace( 'AffiliateWP - ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );
        } else {
            $this->plugin_name = __( 'This plugin', 'affiliatewp-plugin-template' );
        }

        // Is AffiliateWP installed?
        foreach ( $plugins as $plugin_path => $plugin ) {
            if ( $plugin['Name'] == 'AffiliateWP' ) {
                $this->has_affiliatewp = true;
                break;
            }
        }
    }


    /**
     * Processes plugin deactivation.
     *
     * @access public
     * @since  1.0.0
     */
    public function run() {
        // Display notice.
        add_action( 'admin_notices', array( $this, 'missing_affiliatewp_notice' ) );
    }

    /**
     * Displays a notice if AffiliateWP isn't installed.
     *
     * @access public
     * @since  1.0.0
     *
     * @return string The notice markup to display.
     */
    public function missing_affiliatewp_notice() {

        if ( $this->has_affiliatewp ) {

			/* translators: 1: Plugin name, 2: AffiliateWP */
			echo '<div class="error"><p>' . sprintf( __( '%1$s requires %2$s. Please activate it to continue.', 'affiliatewp-plugin-template' ), $this->plugin_name, '<a href="https://affiliatewp.com/" target="_new">AffiliateWP</a>' ) . '</p></div>';

        } else {

        	/* translators: 1: Plugin name, 2: AffiliateWP */
        	echo '<div class="error"><p>' . sprintf( __( '%1$s requires %2$s. Please install it to continue.', 'affiliatewp-plugin-template' ), $this->plugin_name, '<a href="https://affiliatewp.com/" target="_new">AffiliateWP</a>' ) . '</p></div>';

        }
    }
}
