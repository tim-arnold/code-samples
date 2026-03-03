<?php

/**
 * Handle ACF field registration and management for CTAs
 */
class WFund_CTA_Fields {

    /**
     * Single instance
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
        add_action( 'acf/init', array( $this, 'register_fields' ) );
        add_action( 'acf/init', array( $this, 'setup_acf_json' ) );
    }

    /**
     * Setup ACF JSON save/load paths
     */
    public function setup_acf_json() {
        // Save ACF JSON files to plugin directory
        add_filter( 'acf/settings/save_json', array( $this, 'acf_json_save_point' ) );
        
        // Load ACF JSON files from plugin directory
        add_filter( 'acf/settings/load_json', array( $this, 'acf_json_load_point' ) );
    }

    /**
     * Set ACF JSON save path
     */
    public function acf_json_save_point( $path ) {
        return WFUND_CTA_PLUGIN_DIR . 'acf-json';
    }

    /**
     * Add plugin ACF JSON path to load paths
     */
    public function acf_json_load_point( $paths ) {
        $paths[] = WFUND_CTA_PLUGIN_DIR . 'acf-json';
        return $paths;
    }

    /**
     * Register ACF fields programmatically
     */
    public function register_fields() {
        if ( ! function_exists( 'acf_add_options_page' ) ) {
            return;
        }

        // Add options page as top-level menu item
        acf_add_options_page( array(
            'page_title'  => __( 'Global CTAs', 'wfund-global-ctas' ),
            'menu_title'  => __( 'Global CTAs', 'wfund-global-ctas' ),
            'menu_slug'   => 'wfund-global-ctas',
            'capability'  => 'manage_options',
            'icon_url'    => 'dashicons-megaphone',
            'position'    => 30, // After Comments (25) and before Appearance (60)
        ) );

        // Register field groups
        $this->register_cta_fields();
    }

    /**
     * Register CTA fields
     */
    private function register_cta_fields() {
        acf_add_local_field_group( array(
            'key' => 'group_wfund_global_ctas',
            'title' => 'Global CTAs',
            'fields' => array(
                array(
                    'key' => 'field_global_ctas',
                    'label' => 'Call-to-Action Sections',
                    'name' => 'global_ctas',
                    'type' => 'repeater',
                    'instructions' => 'Configure global CTA sections that appear at the bottom of pages.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'collapsed' => 'field_cta_heading',
                    'min' => 0,
                    'max' => 5,
                    'layout' => 'block',
                    'button_label' => 'Add CTA Section',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_cta_enabled',
                            'label' => 'Enabled',
                            'name' => 'enabled',
                            'type' => 'true_false',
                            'instructions' => 'Toggle this CTA section on or off.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '20',
                                'class' => '',
                                'id' => '',
                            ),
                            'message' => '',
                            'default_value' => 1,
                            'ui' => 1,
                            'ui_on_text' => 'On',
                            'ui_off_text' => 'Off',
                        ),
                        array(
                            'key' => 'field_cta_heading',
                            'label' => 'Heading',
                            'name' => 'heading',
                            'type' => 'text',
                            'instructions' => 'Main heading for this CTA section.',
                            'required' => 1,
                            'conditional_logic' => 0, // Always visible
                            'wrapper' => array(
                                'width' => '80',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => 'e.g. Join the W Fund',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 100,
                        ),
                        array(
                            'key' => 'field_cta_color_scheme',
                            'label' => 'Color Scheme',
                            'name' => 'color_scheme',
                            'type' => 'select',
                            'instructions' => 'Choose the color scheme for this CTA section.',
                            'required' => 1,
                            'conditional_logic' => array(
                                array(
                                    array(
                                        'field' => 'field_cta_enabled',
                                        'operator' => '==',
                                        'value' => '1',
                                    ),
                                ),
                            ),
                            'wrapper' => array(
                                'width' => '66.66',
                                'class' => '',
                                'id' => '',
                            ),
                            'choices' => array(
                                'black' => 'Black (Dark background)',
                                'red' => 'Red (Red background)',
                            ),
                            'default_value' => 'black',
                            'allow_null' => 0,
                            'multiple' => 0,
                            'ui' => 1,
                            'ajax' => 0,
                            'return_format' => 'value',
                        ),
                        array(
                            'key' => 'field_cta_priority',
                            'label' => 'Display Order',
                            'name' => 'priority',
                            'type' => 'number',
                            'instructions' => 'Lower numbers appear first.',
                            'required' => 0,
                            'conditional_logic' => array(
                                array(
                                    array(
                                        'field' => 'field_cta_enabled',
                                        'operator' => '==',
                                        'value' => '1',
                                    ),
                                ),
                            ),
                            'wrapper' => array(
                                'width' => '33.33',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 10,
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => 1,
                            'max' => 100,
                            'step' => 1,
                        ),
                        array(
                            'key' => 'field_cta_links',
                            'label' => 'Links',
                            'name' => 'links',
                            'type' => 'repeater',
                            'instructions' => 'Add action links for this CTA section.',
                            'required' => 0,
                            'conditional_logic' => array(
                                array(
                                    array(
                                        'field' => 'field_cta_enabled',
                                        'operator' => '==',
                                        'value' => '1',
                                    ),
                                ),
                            ),
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'collapsed' => 'field_cta_link_text',
                            'min' => 0,
                            'max' => 3,
                            'layout' => 'table',
                            'button_label' => 'Add Link',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_cta_link_text',
                                    'label' => 'Link Text',
                                    'name' => 'link_text',
                                    'type' => 'text',
                                    'instructions' => 'Text to display for this link button',
                                    'required' => 1,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '50',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'placeholder' => 'e.g. Learn More',
                                    'maxlength' => 50,
                                ),
                                array(
                                    'key' => 'field_cta_link_url',
                                    'label' => 'Link URL',
                                    'name' => 'link_url',
                                    'type' => 'text',
                                    'instructions' => 'URL destination for this link (supports relative paths like /about or full URLs)',
                                    'required' => 1,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '50',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'placeholder' => '/page-slug or https://example.com',
                                    'prepend' => '',
                                    'append' => '',
                                    'maxlength' => 255,
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'wfund-global-ctas',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ) );
    }
}