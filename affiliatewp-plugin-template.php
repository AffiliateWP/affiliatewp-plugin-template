<?php
/**
 * Plugin Name: AffiliateWP - Plugin Template
 * Plugin URI: https://affiliatewp.com/
 * Description:
 * Author: AffiliateWP, LLC
 * Author URI: https://affiliatewp.com
 * Version: 1.0.0
 * Text Domain: affiliatewp-plugin-template
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements requirements checks and partial activation before bootstrapping the add-on.
 *
 * @since 1.0.0
 * @final
 */
final class AffWP_PT_Requirements_Check {

	/**
	 * Plugin file path.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	private $file = '';

	/**
	 * Plugin basename.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	private $base = '';

	/**
	 * Requirements array.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $requirements = array(

		// PHP
		'php' => array(
			'minimum' => '5.6.0',
			'name'    => 'PHP',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),

		// WordPress
		'wp' => array(
			'minimum' => '5.0',
			'name'    => 'WordPress',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),

		// AffWP
		'affwp' => array(
			'minimum' => '2.2.17',
			'name'    => 'AffiliateWP',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),
	);

	/**
	 * Sets up the plugin requirements.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Setup file & base
		$this->file = __FILE__;
		$this->base = plugin_basename( $this->file );

		// Always load translations.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Load or quit.
		$this->met() ? $this->load() : $this->quit();
	}

	/**
	 * Quit without loading
	 *
	 * @since 1.0.0
	 */
	private function quit() {
		add_action( 'admin_head',                        array( $this, 'admin_head'        ) );
		add_filter( "plugin_action_links_{$this->base}", array( $this, 'plugin_row_links'  ) );
		add_action( "after_plugin_row_{$this->base}",    array( $this, 'plugin_row_notice' ) );
	}

	/** Specific Methods ******************************************************/

	/**
	 * Load normally
	 *
	 * @since 1.0.0
	 */
	private function load() {

		// Maybe include the bundled bootstrapper.
		if ( ! class_exists( 'AffiliateWP_Plugin_Template' ) ) {
			require_once dirname( $this->file ) . '/includes/class-affiliatewp-plugin-template.php';
		}

		// Maybe hook-in the bootstrapper.
		if ( class_exists( 'AffiliateWP_Plugin_Template' ) ) {

			/*
			 * Bootstrap to plugins_loaded before priority 10 to make sure
			 * add-ons are loaded after us.
			 */
			add_action( 'plugins_loaded', array( $this, 'bootstrap' ), 4 );

			// Register the activation hook.
			register_activation_hook( $this->file, array( $this, 'install' ) );
		}
	}

	/**
	 * Runs the install, usually on an activation hook.
	 *
	 * @since 1.0.0
	 */
	public function install() {

		// Bootstrap to include all of the necessary files
		$this->bootstrap();

		// Run other install code ...
	}

	/**
	 * Bootstraps the actual plugin file.
	 *
	 * @since 1.0.0
	 */
	public function bootstrap() {
		AffiliateWP_Plugin_Template::instance( $this->file );
	}

	/**
	 * Plugin specific URL for an external requirements page.
	 *
	 * @since 1.0.0
	 *
	 * @return string URL to an externally-hosted minimum requirements document.
	 */
	private function unmet_requirements_url() {
		return 'https://...';
	}

	/**
	 * Outputs plugin-specific text to quickly explain what's wrong (in the plugins list table).
	 *
	 * @since 1.0.0
	 *
	 * @return string Message to explain that partial activation is in effect.
	 */
	private function unmet_requirements_text() {
		esc_html_e( 'This plugin is not fully active.', 'affiliatewp-plugin-template' );
	}

	/**
	 * Retrieves plugin-specific text to describe a single unmet requirement.
	 *
	 * @since 1.0.0
	 *
	 * @return string Message for a single unmet requirement.
	 */
	private function unmet_requirements_description_text() {
		return esc_html__( 'Requires %s (%s), but (%s) is installed.', 'affiliatewp-plugin-template' );
	}

	/**
	 * Retrieves plugin-specific text to describe a single missing requirement.
	 *
	 * @since 1.0.0
	 *
	 * @return string Message for a single missing requirement.
	 */
	private function unmet_requirements_missing_text() {
		return esc_html__( 'Requires %s (%s), but it appears to be missing.', 'affiliatewp-plugin-template' );
	}

	/**
	 * Retrieves plugin-specific text used to link to an external requirements page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Label to use when linking to the externally-hosted minimum requirements document.
	 */
	private function unmet_requirements_link() {
		return esc_html__( 'Requirements', 'affiliatewp-plugin-template' );
	}

	/**
	 * Retrieves plugin-specific aria label text to describe the requirements link.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function unmet_requirements_label() {
		return esc_html__( 'AffiliateWP - Plugin Template Requirements', 'affiliatewp-plugin-template' );
	}

	/**
	 * Retrieves plugin-specific text used in CSS to identify attribute IDs and classes.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function unmet_requirements_name() {
		return 'affiliatewp-plugin-template-requirements';
	}

	/** Agnostic Methods ******************************************************/

	/**
	 * Outputs an additional row in the plugins list table to display messages.
	 *
	 * @since 1.0.0
	 */
	public function plugin_row_notice() {
		?><tr class="active <?php echo esc_attr( $this->unmet_requirements_name() ); ?>-row">
		<th class="check-column">
			<span class="dashicons dashicons-warning"></span>
		</th>
		<td class="column-primary">
			<?php $this->unmet_requirements_text(); ?>
		</td>
		<td class="column-description">
			<?php $this->unmet_requirements_description(); ?>
		</td>
		</tr><?php
	}

	/**
	 * Outputs unmet requirement descriptions.
	 *
	 * @since 1.0.0
	 */
	private function unmet_requirements_description() {
		foreach ( $this->requirements as $properties ) {
			if ( empty( $properties['met'] ) ) {
				$this->unmet_requirement_description( $properties );
			}
		}
	}

	/**
	 * Outputs specific unmet requirement information.
	 *
	 * @since 1.0.0
	 *
	 * @param array $requirement Requirements.
	 */
	private function unmet_requirement_description( $requirement = array() ) {

		// Requirement exists, but is out of date
		if ( ! empty( $requirement['exists'] ) ) {
			$text = sprintf(
				$this->unmet_requirements_description_text(),
				'<strong>' . esc_html( $requirement['name']    ) . '</strong>',
				'<strong>' . esc_html( $requirement['minimum'] ) . '</strong>',
				'<strong>' . esc_html( $requirement['current'] ) . '</strong>'
			);

			// Requirement could not be found
		} else {
			$text = sprintf(
				$this->unmet_requirements_missing_text(),
				'<strong>' . esc_html( $requirement['name']    ) . '</strong>',
				'<strong>' . esc_html( $requirement['minimum'] ) . '</strong>'
			);
		}

		// Output the description
		echo '<p>' . $text . '</p>';
	}

	/**
	 * Outputs styling for unmet requirements in the plugins list table.
	 *
	 * @since 1.0.0
	 */
	public function admin_head() {

		// Get the requirements row name
		$name = $this->unmet_requirements_name(); ?>

		<style id="<?php echo esc_attr( $name ); ?>">
			.plugins tr[data-plugin="<?php echo esc_html( $this->base ); ?>"] th,
			.plugins tr[data-plugin="<?php echo esc_html( $this->base ); ?>"] td,
			.plugins .<?php echo esc_html( $name ); ?>-row th,
			.plugins .<?php echo esc_html( $name ); ?>-row td {
				background: #fff5f5;
			}
			.plugins tr[data-plugin="<?php echo esc_html( $this->base ); ?>"] th {
				box-shadow: none;
			}
			.plugins .<?php echo esc_html( $name ); ?>-row th span {
				margin-left: 6px;
				color: #dc3232;
			}
			.plugins tr[data-plugin="<?php echo esc_html( $this->base ); ?>"] th,
			.plugins .<?php echo esc_html( $name ); ?>-row th.check-column {
				border-left: 4px solid #dc3232 !important;
			}
			.plugins .<?php echo esc_html( $name ); ?>-row .column-description p {
				margin: 0;
				padding: 0;
			}
			.plugins .<?php echo esc_html( $name ); ?>-row .column-description p:not(:last-of-type) {
				margin-bottom: 8px;
			}
		</style>
		<?php
	}

	/**
	 * Adds the "Requirements" link to the plugin row actions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Plugin row links.
	 * @return array Modified row links array.
	 */
	public function plugin_row_links( $links = array() ) {

		// Add the Requirements link
		$links['requirements'] =
			'<a href="' . esc_url( $this->unmet_requirements_url() ) . '" aria-label="' . esc_attr( $this->unmet_requirements_label() ) . '">'
			. esc_html( $this->unmet_requirements_link() )
			. '</a>';

		// Return links with Requirements link
		return $links;
	}

	/** Checkers **************************************************************/

	/**
	 * Runs the actual dependencies checks and compiles the findings.
	 *
	 * @since 1.0.0
	 */
	private function check() {

		// Loop through requirements
		foreach ( $this->requirements as $dependency => $properties ) {

			// Which dependency are we checking?
			switch ( $dependency ) {

				// PHP
				case 'php' :
					$version = phpversion();
					break;

				// WP
				case 'wp' :
					$version = get_bloginfo( 'version' );
					break;

				case 'affwp':
					$version = get_option( 'affwp_version' );
					break;

				// Unknown
				default :
					$version = false;
					break;
			}

			// Merge to original array
			if ( ! empty( $version ) ) {
				$this->requirements[ $dependency ] = array_merge( $this->requirements[ $dependency ], array(
					'current' => $version,
					'checked' => true,
					'met'     => version_compare( $version, $properties['minimum'], '>=' )
				) );
			}
		}
	}

	/**
	 * Determines if all requirements been met.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if requirements are met, otherwise false.
	 */
	public function met() {

		// Run the check
		$this->check();

		// Default to true (any false below wins).
		$retval  = true;
		$to_meet = wp_list_pluck( $this->requirements, 'met' );

		// Look for unmet dependencies, and exit if so
		foreach ( $to_meet as $met ) {
			if ( empty( $met ) ) {
				$retval = false;
				continue;
			}
		}

		// Return
		return $retval;
	}

	/** Translations **********************************************************/

	/**
	 * Loads the plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {

		/*
		 * Due to the introduction of language packs through translate.wordpress.org,
		 * loading our textdomain is complex.
		 *
		 * In v2.4.6, our textdomain changed from "edd" to "affiliatewp-plugin-template".
		 *
		 * To support existing translation files from before the change, we must
		 * look for translation files in several places and under several names.
		 *
		 * - wp-content/languages/plugins/affiliatewp-plugin-template (introduced with language packs)
		 * - wp-content/languages/edd/ (custom folder we have supported since 1.4)
		 * - wp-content/plugins/affiliatewp-plugin-template/languages/
		 *
		 * In wp-content/languages/edd/ we must look for:
		 * - "affiliatewp-plugin-template-{lang}_{country}.mo"
		 *
		 * In wp-content/languages/edd/ we must look for:
		 * - "edd-{lang}_{country}.mo" as that was the old file naming convention
		 *
		 * In wp-content/languages/plugins/affiliatewp-plugin-template/ we only need to look for:
		 * - "affiliatewp-plugin-template-{lang}_{country}.mo" as that is the new structure
		 *
		 * In wp-content/plugins/affiliatewp-plugin-template/languages/, we must look for:
		 * - both naming conventions. This is done by filtering "load_textdomain_mofile"
		 */
		add_filter( 'load_textdomain_mofile', array( $this, 'load_old_textdomain' ), 10, 2 );

		// Set filter for plugin's languages directory.
		$edd_lang_dir = dirname( $this->base ) . '/languages/';
		$edd_lang_dir = apply_filters( 'edd_languages_directory', $edd_lang_dir );
		$get_locale   = function_exists( 'get_user_locale' )
			? get_user_locale()
			: get_locale();

		/**
		 * Defines the plugin language locale used in Easy Digital Downloads.
		 *
		 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
		 *                  otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'affiliatewp-plugin-template' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'affiliatewp-plugin-template', $locale );

		// Look for wp-content/languages/edd/affiliatewp-plugin-template-{lang}_{country}.mo
		$mofile_global1 = WP_LANG_DIR . "/edd/affiliatewp-plugin-template-{$locale}.mo";

		// Look for wp-content/languages/edd/edd-{lang}_{country}.mo
		$mofile_global2 = WP_LANG_DIR . "/edd/edd-{$locale}.mo";

		// Look in wp-content/languages/plugins/affiliatewp-plugin-template
		$mofile_global3 = WP_LANG_DIR . "/plugins/affiliatewp-plugin-template/{$mofile}";

		// Try to load from first global location
		if ( file_exists( $mofile_global1 ) ) {
			load_textdomain( 'affiliatewp-plugin-template', $mofile_global1 );

			// Try to load from next global location
		} elseif ( file_exists( $mofile_global2 ) ) {
			load_textdomain( 'affiliatewp-plugin-template', $mofile_global2 );

			// Try to load from next global location
		} elseif ( file_exists( $mofile_global3 ) ) {
			load_textdomain( 'affiliatewp-plugin-template', $mofile_global3 );

			// Load the default language files
		} else {
			load_plugin_textdomain( 'affiliatewp-plugin-template', false, $edd_lang_dir );
		}
	}

	/**
	 * Load a .mo file for the old textdomain if one exists.
	 *
	 * @see https://github.com/10up/grunt-wp-plugin/issues/21#issuecomment-62003284
	 */
	public function load_old_textdomain( $mofile, $textdomain ) {

		// Fallback for old text domain
		if ( ( 'affiliatewp-plugin-template' === $textdomain ) && ! file_exists( $mofile ) ) {
			$mofile = dirname( $mofile ) . DIRECTORY_SEPARATOR . str_replace( $textdomain, 'edd', basename( $mofile ) );
		}

		// Return (possibly overridden) mofile
		return $mofile;
	}
}

// Invoke the checker
new AffWP_PT_Requirements_Check();
