<?php
/**
 * Plugin Name: Global CTAs
 * Description: Manage global CTA sections with ACF fields
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Requires at least: 5.0
 * Text Domain: global-ctas
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'GLOBAL_CTA_VERSION', '1.0.0' );
define( 'GLOBAL_CTA_PLUGIN_FILE', __FILE__ );
define( 'GLOBAL_CTA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GLOBAL_CTA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin class
 */
class Global_CTAs {

    /**
     * Single instance of the plugin
     */
    private static $instance = null;

    /**
     * Get single instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        register_activation_hook( GLOBAL_CTA_PLUGIN_FILE, array( $this, 'activate' ) );
        register_deactivation_hook( GLOBAL_CTA_PLUGIN_FILE, array( $this, 'deactivate' ) );
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Check if ACF is active
        if ( ! class_exists( 'ACF' ) ) {
            add_action( 'admin_notices', array( $this, 'acf_missing_notice' ) );
            return;
        }

        $this->load_dependencies();
        $this->setup_hooks();
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once GLOBAL_CTA_PLUGIN_DIR . 'includes/class-cta-fields.php';
    }

    /**
     * Setup WordPress hooks
     */
    private function setup_hooks() {
        // Initialize components
        CTA_Fields::get_instance();

        // Add template functions
        add_action( 'primer_before_footer', array( $this, 'render_cta_sections' ) );
    }

    /**
     * Render CTA sections on frontend
     */
    public function render_cta_sections() {
        // Only show on specified page types (same logic as theme)
        if ( ! ( is_singular( array( 'page', 'post', 'team_member' ) ) || 
                 is_post_type_archive( 'team_member' ) || 
                 is_home() || 
                 is_front_page() ) ) {
            return;
        }

        $ctas = get_field( 'global_ctas', 'option' );
        
        if ( ! $ctas || ! is_array( $ctas ) ) {
            return;
        }

        // Sort CTAs by priority (lower numbers first)
        usort( $ctas, function( $a, $b ) {
            $priority_a = isset( $a['priority'] ) ? intval( $a['priority'] ) : 10;
            $priority_b = isset( $b['priority'] ) ? intval( $b['priority'] ) : 10;
            return $priority_a - $priority_b;
        } );

        foreach ( $ctas as $cta ) {
            if ( ! $cta['enabled'] ) {
                continue;
            }

            // Get page-level toggle settings - check ACF fields on all editable pages
            if ( is_post_type_archive() || is_category() || is_tag() || is_date() || is_author() ) {
                // Archive pages without individual ACF fields - always show CTAs
                $show_black_cta = true;
                $show_red_cta = true;
            } else {
                // All editable pages (posts, pages, homepage, etc.) - respect ACF toggle fields
                $show_black_cta_field = get_field('show_black_cta');
                $show_red_cta_field = get_field('show_red_cta');
                
                // If field exists (not null/empty), use its value; otherwise default to true
                $show_black_cta = $show_black_cta_field !== null ? $show_black_cta_field : true;
                $show_red_cta = $show_red_cta_field !== null ? $show_red_cta_field : true;
            }

            // Respect page-level toggle settings based on color scheme
            $color_scheme = $cta['color_scheme'] ?: 'black';
            
            if ( $color_scheme === 'black' && ! $show_black_cta ) {
                continue;
            }
            if ( $color_scheme === 'red' && ! $show_red_cta ) {
                continue;
            }

            $this->render_single_cta( $cta );
        }
    }

    /**
     * Render a single CTA section
     */
    private function render_single_cta( $cta ) {
        $color_scheme = $cta['color_scheme'] ?: 'black';
        $heading = $cta['heading'] ?: '';
        $links = $cta['links'] ?: array();

        if ( empty( $heading ) && empty( $links ) ) {
            return;
        }

        ?>
        <section class="cta-section cta-<?php echo esc_attr( $color_scheme ); ?>" aria-labelledby="<?php echo esc_attr( $color_scheme ); ?>-cta-heading">
            <div class="cta-container">
                <?php if ( $heading ) : ?>
                    <h2 class="cta-heading" id="<?php echo esc_attr( $color_scheme ); ?>-cta-heading"><?php echo esc_html( $heading ); ?></h2>
                <?php endif; ?>
                
                <?php if ( $links ) : ?>
                    <nav class="cta-links" role="navigation" aria-label="Call to action links">
                        <?php foreach ( $links as $link ) : ?>
                            <?php if ( ! empty( $link['link_text'] ) && ! empty( $link['link_url'] ) ) : ?>
                                <?php 
                                $is_external = strpos( $link['link_url'], 'http' ) === 0;
                                $target = $is_external ? ' target="_blank" rel="noopener noreferrer"' : '';
                                ?>
                                <a href="<?php echo esc_url( $link['link_url'] ); ?>" 
                                   class="cta-link cta-link-underline"<?php echo $target; ?>>
                                    <?php echo esc_html( $link['link_text'] ); ?>
                                    <?php if ( $is_external ) : ?>
                                        <span class="screen-reader-text"> (opens in new tab)</span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options if they don't exist
        if ( ! get_option( 'global_cta_version' ) ) {
            update_option( 'global_cta_version', GLOBAL_CTA_VERSION );
        }
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up if needed
    }

    /**
     * Show notice if ACF is not active
     */
    public function acf_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php esc_html_e( 'Global CTAs', 'global-ctas' ); ?></strong>
                <?php esc_html_e( 'requires Advanced Custom Fields (ACF) to be installed and activated.', 'global-ctas' ); ?>
            </p>
        </div>
        <?php
    }
}

// Initialize plugin
Global_CTAs::get_instance();