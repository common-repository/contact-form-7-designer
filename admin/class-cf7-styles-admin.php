<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tinygiantstudios.co.uk
 * @since      1.0.0
 *
 * @package    CF7_Styles
 * @subpackage CF7_Styles/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CF7_Styles
 * @subpackage CF7_Styles/admin
 */
class CF7_Styles_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name       The name of this plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/cf7-styles-admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/cf7-styles-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }
    
    /**
     * Check that Redux Framework plugin is installed
     *
     * @since    1.0.0
     */
    public function cf7_styles_plugins_loaded()
    {
        if ( true !== $this->cf7_styles_plugin_dependencies_met() ) {
            return;
        }
    }
    
    /**
     * Display admin notice when Redux Framework plugin is not active.
     *
     * @since    1.0.0
     */
    public function cf7_styles_plugin_dependencies_notice()
    {
        $return = $this->cf7_styles_plugin_dependencies_met( true );
        
        if ( true !== $return && current_user_can( 'activate_plugins' ) ) {
            $dependency_notice = $return;
            printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $dependency_notice ) );
        }
    
    }
    
    /**
     * Check whether the plugin dependencies met.
     *
     * @since    1.0.0
     * @param    boolean $return_dep_notice       The state of dependent plugin installations/activations.
     */
    private function cf7_styles_plugin_dependencies_met( $return_dep_notice = false )
    {
        $return = false;
        // Check if Redux is installed.
        
        if ( !class_exists( 'ReduxFramework' ) ) {
            
            if ( $return_dep_notice ) {
                $install_url = wp_nonce_url( add_query_arg( array(
                    'action' => 'install-plugin',
                    'plugin' => 'redux-framework',
                ), admin_url( 'update.php' ) ), 'install-plugin_woocommerce' );
                /* translators: %1$s: Opening Strong Tag, %2$s: Closing Strong Tag, %3$s: Opening Redux Framework link tag, %4$s: Closing link tag */
                $return = sprintf(
                    esc_html__( '%1$sContact Form 7 Designer is inactive.%2$s The %3$sRedux Framework plugin%4$s must be active for CF7 Designer to work.', 'cf7-styles' ),
                    '<strong>',
                    '</strong>',
                    '<a href="https://wordpress.org/plugins/redux-framework/" target="_blank">',
                    '</a>'
                );
            }
            
            return $return;
        }
        
        
        if ( !class_exists( 'WPCF7' ) ) {
            
            if ( $return_dep_notice ) {
                $install_url = wp_nonce_url( add_query_arg( array(
                    'action' => 'install-plugin',
                    'plugin' => 'contact-form-7',
                ), admin_url( 'update.php' ) ), 'install-plugin_woocommerce' );
                /* translators: %1$s: Opening Strong Tag, %2$s: Closing Strong Tag, %3$s: Opening Redux Framework link tag, %4$s: Closing link tag */
                $return = sprintf(
                    esc_html__( '%1$sContact Form 7 Designer is inactive.%2$s The %3$sContact Form 7 plugin%4$s must be active for Contact Form 7 Designer to work.', 'cf7-styles' ),
                    '<strong>',
                    '</strong>',
                    '<a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">',
                    '</a>'
                );
            }
            
            return $return;
        }
        
        return true;
    }
    
    /**
     * Display pro upgrade notice.
     *
     * @since    2.0.0
     */
    public function upgrade_notice()
    {
        /* translators: %1$s: Opening Upgrade now anchor tag, %2$s: Closing anchor Tag */
        $upgrade_msg = sprintf( esc_html__( 'This feature is in the Pro version only. Values saved here will not take effect on the front end. %1$sUpgrade Now%2$s', 'cf7-styles' ), '<a href="' . cf7_styles_freemius()->get_upgrade_url() . '" target="_blank" class="cf7-styles-upgrade-button">', '</a>' );
        return $upgrade_msg;
    }
    
    /**
     * Load Redux Framework.
     *
     * @since    1.0.0
     */
    public function cf7_load_redux_framework()
    {
        if ( !class_exists( 'ReduxFramework' ) ) {
            return;
        }
        
        if ( !class_exists( 'ReduxFramework' ) && file_exists( plugin_dir_path( __DIR__ ) . 'redux-framework/ReduxCore/framework.php' ) ) {
            require_once plugin_dir_path( __DIR__ ) . 'redux-framework/ReduxCore/framework.php';
            remove_filter(
                'plugin_row_meta',
                array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks' ),
                null,
                2
            );
            remove_action( 'admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );
        }
        
        $opt_name = 'cf7_styles';
        // TYPICAL -> Change these values as you need/desire.
        $args = array(
            'opt_name'                  => $opt_name,
            'display_name'              => esc_html__( 'Contact Form 7 Designer', 'cf7-styles' ),
            'display_version'           => CF7_STYLES_VERSION,
            'menu_type'                 => 'menu',
            'allow_sub_menu'            => true,
            'menu_title'                => esc_html__( 'Contact Designer', 'cf7-styles' ),
            'page_title'                => esc_html__( 'Contact Form 7 Designer', 'cf7-styles' ),
            'disable_google_fonts_link' => true,
            'admin_bar'                 => false,
            'admin_bar_icon'            => 'dashicons-portfolio',
            'admin_bar_priority'        => 60,
            'global_variable'           => $opt_name,
            'dev_mode'                  => false,
            'customizer'                => false,
            'open_expanded'             => false,
            'disable_save_warn'         => false,
            'page_priority'             => 31,
            'page_parent'               => 'admin.php',
            'page_permissions'          => 'manage_options',
            'menu_icon'                 => 'dashicons-email',
            'last_tab'                  => '',
            'page_icon'                 => 'dashicons-email',
            'page_slug'                 => $opt_name,
            'save_defaults'             => true,
            'default_show'              => false,
            'default_mark'              => '*',
            'show_import_export'        => false,
            'transient_time'            => 60 * MINUTE_IN_SECONDS,
            'output'                    => true,
            'output_tag'                => true,
            'footer_credit'             => esc_html__( 'Developed by TinyGiantStudios', 'cf7-styles' ),
            'use_cdn'                   => true,
            'admin_theme'               => 'wp',
            'flyout_submenus'           => true,
            'font_display'              => 'swap',
            'hints'                     => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
            'color'   => 'red',
            'shadow'  => true,
            'rounded' => false,
            'style'   => '',
        ),
            'tip_position'  => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
            'tip_effect'    => array(
            'show' => array(
            'effect'   => 'slide',
            'duration' => '500',
            'event'    => 'mouseover',
        ),
            'hide' => array(
            'effect'   => 'slide',
            'duration' => '500',
            'event'    => 'click mouseleave',
        ),
        ),
        ),
            'database'                  => '',
            'network_admin'             => true,
            'search'                    => true,
        );
        Redux::setArgs( $opt_name, $args );
        // General CF7 Styles.
        Redux::setSection( $opt_name, array(
            'title'  => __( 'General Styles', 'cf7-styles' ),
            'desc'   => __( 'Also known as the traditional input field, this section allows for the quick styling of text fields for Contact Form 7', 'cf7-styles' ),
            'icon'   => 'el-icon-wrench',
            'fields' => array(
            // Background Section..
            array(
                'id'       => 'cf7_general_backgrounds',
                'type'     => 'section',
                'title'    => __( 'General Backgrounds', 'cf7-styles' ),
                'subtitle' => __( 'This section adds general background options for Contact Form 7.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'cf7_general_form_background',
                'type'     => 'background',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form', '.wpcf7 .wpcf7-form' ),
                'title'    => __( 'Form Background', 'cf7-styles' ),
                'subtitle' => __( 'Set the background of the entire form area.', 'cf7-styles' ),
                'preview'  => false,
            ),
            array(
                'id'       => 'cf7_general_section_background',
                'type'     => 'background',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form p', '.wpcf7 .wpcf7-form p' ),
                'title'    => __( 'Section Background', 'cf7-styles' ),
                'subtitle' => __( 'Set the background for all the different field section. Please make sure all sections are wrapped with the paragraph tag as in the demo form that comes with Contact Form 7.', 'cf7-styles' ),
                'preview'  => false,
            ),
            array(
                'id'       => 'cf7_general_section_hover_background',
                'type'     => 'background',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form p:hover', '.wpcf7 .wpcf7-form p:hover' ),
                'title'    => __( 'Section Background: Hover', 'cf7-styles' ),
                'subtitle' => __( 'Set the background for all the different field section when hovering over them. Please make sure all sections are wrapped with the paragraph tag as in the demo form that comes with Contact Form 7.', 'cf7-styles' ),
                'preview'  => false,
            ),
            array(
                'id'       => 'cf7_general_button_background',
                'type'     => 'background',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form .wpcf7-submit', '.wpcf7 .wpcf7-form .wpcf7-submit' ),
                'title'    => __( 'Submit Button Background', 'cf7-styles' ),
                'subtitle' => __( 'Set the background for the form submit button.', 'cf7-styles' ),
                'preview'  => false,
            ),
            array(
                'id'       => 'cf7_general_buttonhover_background',
                'type'     => 'background',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form .wpcf7-submit:hover', '.wpcf7 .wpcf7-form .wpcf7-submit:hover' ),
                'title'    => __( 'Submit Button Background Hover', 'cf7-styles' ),
                'subtitle' => __( 'Set the background for the form submit button on hover.', 'cf7-styles' ),
                'preview'  => false,
            ),
            // Borders Section..
            array(
                'id'       => 'cf7_general_borders',
                'type'     => 'section',
                'title'    => __( 'Borders', 'cf7-styles' ),
                'subtitle' => __( 'This section adds border styles for the input field section.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'cf7_general_form_border',
                'type'     => 'border',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form', '.wpcf7 .wpcf7-form' ),
                'title'    => __( 'Form Border', 'cf7-styles' ),
                'subtitle' => __( 'Set the border of the entire form.', 'cf7-styles' ),
                'all'      => false,
                'top'      => true,
                'right'    => true,
                'left'     => true,
                'bottom'   => true,
            ),
            array(
                'id'       => 'cf7_general_section_border',
                'type'     => 'border',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form p', '.wpcf7 .wpcf7-form p' ),
                'title'    => __( 'Field Section Border', 'cf7-styles' ),
                'subtitle' => __( 'Set the border around each field section. Please make sure all sections are wrapped with the paragraph tag as in the demo form that comes with Contact Form 7.', 'cf7-styles' ),
                'all'      => false,
                'top'      => true,
                'right'    => true,
                'left'     => true,
                'bottom'   => true,
            ),
            array(
                'id'       => 'cf7_general_button_border',
                'type'     => 'border',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form .wpcf7-submit', '.wpcf7 .wpcf7-form .wpcf7-submit' ),
                'title'    => __( 'Submit Button Border', 'cf7-styles' ),
                'subtitle' => __( 'Set the border for the form submit button.', 'cf7-styles' ),
                'all'      => false,
                'top'      => true,
                'right'    => true,
                'left'     => true,
                'bottom'   => true,
            ),
            // Padding & Margin..
            array(
                'id'       => 'cf7__general_spacing',
                'type'     => 'section',
                'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                'subtitle' => __( 'This section adds padding / margin options for the entire form.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_general_form_padding',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form', '.wpcf7 .wpcf7-form' ),
                'mode'           => 'padding',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Form Padding', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the padding used for the entire form.', 'cf7-styles' ),
            ),
            array(
                'id'             => 'cf7_general_form_margin',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form', '.wpcf7 .wpcf7-form' ),
                'mode'           => 'margin',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Form Margin', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the margin used for the entire form.', 'cf7-styles' ),
            ),
            array(
                'id'             => 'cf7_general_section_padding',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form p', '.wpcf7 .wpcf7-form p' ),
                'mode'           => 'padding',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Section Padding', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the padding used for each form field section. Please make sure all sections are wrapped with the paragraph tag as in the demo form that comes with Contact Form 7.', 'cf7-styles' ),
            ),
            array(
                'id'             => 'cf7_general_section_margin',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form p', '.wpcf7 .wpcf7-form p' ),
                'mode'           => 'margin',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Section Margin', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the margin used for each form field section. Please make sure all sections are wrapped with the paragraph tag as in the demo form that comes with Contact Form 7.', 'cf7-styles' ),
            ),
            array(
                'id'             => 'cf7_general_button_padding',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form .wpcf7-submit', '.wpcf7 .wpcf7-form .wpcf7-submit' ),
                'mode'           => 'padding',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Submit Button Padding', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the padding used for the form submit button.', 'cf7-styles' ),
            ),
            array(
                'id'             => 'cf7_general_button_margin',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form .wpcf7-submit', '.wpcf7 .wpcf7-form .wpcf7-submit' ),
                'mode'           => 'margin',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Submit Button Margin', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the margin used for the form submit button.', 'cf7-styles' ),
            ),
            // Fonts..
            array(
                'id'       => 'cf7_general_fonts',
                'type'     => 'section',
                'title'    => __( 'Fonts', 'cf7-styles' ),
                'subtitle' => __( 'This section sets font rules for the entire form.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_general_form_font',
                'type'           => 'typography',
                'title'          => __( 'Form Fonts', 'cf7-styles' ),
                'google'         => true,
                'font-backup'    => true,
                'font-style'     => true,
                'subsets'        => true,
                'font-size'      => true,
                'line-height'    => true,
                'color'          => true,
                'preview'        => true,
                'all_styles'     => true,
                'text-transform' => true,
                'output'         => array(
                '#cf7-styles .wpcf7 .wpcf7-form',
                '#cf7-styles .wpcf7 .wpcf7-form p',
                '.wpcf7 .wpcf7-form',
                '.wpcf7 .wpcf7-form p'
            ),
                'units'          => 'px',
                'subtitle'       => __( 'Set the fonts for the entire form.', 'cf7-styles' ),
            ),
            array(
                'id'             => 'cf7_general_button_font',
                'type'           => 'typography',
                'title'          => __( 'Submit Button Font', 'cf7-styles' ),
                'google'         => true,
                'font-backup'    => true,
                'font-style'     => true,
                'subsets'        => true,
                'font-size'      => true,
                'line-height'    => true,
                'color'          => true,
                'preview'        => true,
                'all_styles'     => true,
                'text-transform' => true,
                'output'         => array( '#cf7-styles .wpcf7-submit', '.wpcf7 .wpcf7-form .wpcf7-submit' ),
                'units'          => 'px',
                'subtitle'       => __( 'Set the font to be used for the form submit button.', 'cf7-styles' ),
            ),
            // Dimensions..
            array(
                'id'       => 'cf7_general_dimensions',
                'type'     => 'section',
                'title'    => __( 'Dimensions', 'cf7-styles' ),
                'subtitle' => __( 'This section sets dimension rules for the entire form.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_general_form_dimension',
                'type'           => 'dimensions',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => 'true',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form', '.wpcf7 .wpcf7-form' ),
                'title'          => __( 'Form Width', 'cf7-styles' ),
                'subtitle'       => __( 'Allow your users to set the overall width of the form', 'cf7-styles' ),
                'width'          => true,
                'height'         => false,
            ),
            array(
                'id'             => 'cf7_general_button_dimension',
                'type'           => 'dimensions',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => 'true',
                'output'         => array( '#cf7-styles .wpcf7-submit', '.wpcf7 .wpcf7-form .wpcf7-submit' ),
                'title'          => __( 'Submit Button Width', 'cf7-styles' ),
                'subtitle'       => __( 'Allow your users to set the width / height of the form submit button', 'cf7-styles' ),
                'width'          => true,
                'height'         => true,
            ),
        ),
        ) );
        // Input Field Styles.
        Redux::setSection( $opt_name, array(
            'title'  => __( 'Text Field', 'cf7-styles' ),
            'desc'   => __( 'Also known as the traditional input fields, this section allows for the quick styling of text, email, URL and contact numbers fields for Contact Form 7.', 'cf7-styles' ),
            'icon'   => 'el-icon-minus',
            'fields' => array(
            // Background Section..
            array(
                'id'       => 'cf7_input_section_backgrounds',
                'type'     => 'section',
                'title'    => __( 'Backgrounds', 'cf7-styles' ),
                'subtitle' => __( 'This section adds background styles for the input field section.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'cf7_input_field_background',
                'type'     => 'background',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form input.wpcf7-text', '.wpcf7 .wpcf7-form input.wpcf7-text' ),
                'title'    => __( 'Input Field Background', 'cf7-styles' ),
                'subtitle' => __( 'Set the background of the text text element (input field)', 'cf7-styles' ),
                'preview'  => false,
            ),
            // Borders Section..
            array(
                'id'       => 'cf7_input_section_borders',
                'type'     => 'section',
                'title'    => __( 'Borders', 'cf7-styles' ),
                'subtitle' => __( 'This section adds border styles for the input field section.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'cf7_input_field_border',
                'type'     => 'border',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form input.wpcf7-text', '.wpcf7 .wpcf7-form input.wpcf7-text' ),
                'title'    => __( 'Input Field Border', 'cf7-styles' ),
                'subtitle' => __( 'Set the border of each text text element (input field)', 'cf7-styles' ),
                'all'      => false,
                'top'      => true,
                'right'    => true,
                'left'     => true,
                'bottom'   => true,
            ),
            // Padding & Margin..
            array(
                'id'       => 'cf7_input_section_spacing',
                'type'     => 'section',
                'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                'subtitle' => __( 'This section adds padding and margin for the input field section.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_input_field_padding',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form input.wpcf7-text', '.wpcf7 .wpcf7-form input.wpcf7-text' ),
                'mode'           => 'padding',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Input Field Padding', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the padding used for the text text (input) field.', 'cf7-styles' ),
            ),
            array(
                'id'             => 'cf7_input_field_margin',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form input.wpcf7-text', '.wpcf7 .wpcf7-form input.wpcf7-text' ),
                'mode'           => 'margin',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Input Field Margin', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the margin used for the text text (input) field.', 'cf7-styles' ),
            ),
            // Fonts..
            array(
                'id'       => 'cf7_input_section_fonts',
                'type'     => 'section',
                'title'    => __( 'Fonts', 'cf7-styles' ),
                'subtitle' => __( 'This section sets font rules for the text text (input) field.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_input_field_font',
                'type'           => 'typography',
                'title'          => __( 'Input Field', 'cf7-styles' ),
                'google'         => true,
                'font-backup'    => true,
                'font-style'     => true,
                'subsets'        => true,
                'font-size'      => true,
                'line-height'    => true,
                'color'          => true,
                'preview'        => true,
                'all_styles'     => true,
                'text-transform' => true,
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form input.wpcf7-text', '.wpcf7 .wpcf7-form input.wpcf7-text' ),
                'units'          => 'px',
                'subtitle'       => __( 'Applies to all input fields generated by Contact Form 7.', 'cf7-styles' ),
            ),
            // Dimensions..
            array(
                'id'       => 'cf7_input_section_dimensions',
                'type'     => 'section',
                'title'    => __( 'Dimensions', 'cf7-styles' ),
                'subtitle' => __( 'This section adds dimension options for the text text (input) field.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_input_field_dimension',
                'type'           => 'dimensions',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => 'true',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form input.wpcf7-text', '.wpcf7 .wpcf7-form input.wpcf7-text' ),
                'title'          => __( 'Width / Height', 'cf7-styles' ),
                'subtitle'       => __( 'Allow your users to set the width / height of Contact Form 7 input fields.', 'cf7-styles' ),
            ),
        ),
        ) );
        // Textarea Field Styles.
        Redux::setSection( $opt_name, array(
            'title'  => __( 'Textarea', 'cf7-styles' ),
            'desc'   => __( 'This section allows for the quick styling of textarea fields for Contact Form 7', 'cf7-styles' ),
            'icon'   => 'el-icon-stop',
            'fields' => array(
            // Background Section..
            array(
                'id'       => 'cf7_textarea_section_backgrounds',
                'type'     => 'section',
                'title'    => __( 'Backgrounds', 'cf7-styles' ),
                'subtitle' => __( 'This section adds background styles for the textarea field section.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'cf7_textarea_field_background',
                'type'     => 'background',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form textarea.wpcf7-textarea', '.wpcf7 .wpcf7-form textarea.wpcf7-textarea' ),
                'title'    => __( 'Textarea Field Background', 'cf7-styles' ),
                'subtitle' => __( 'Set the background of the textarea field.', 'cf7-styles' ),
                'preview'  => false,
            ),
            // Borders Section..
            array(
                'id'       => 'cf7_textarea_section_borders',
                'type'     => 'section',
                'title'    => __( 'Borders', 'cf7-styles' ),
                'subtitle' => __( 'This section adds border styles for the textarea section.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'cf7_textarea_field_border',
                'type'     => 'border',
                'output'   => array( '#cf7-styles .wpcf7 .wpcf7-form textarea.wpcf7-textarea', '.wpcf7 .wpcf7-form textarea.wpcf7-textarea' ),
                'title'    => __( 'Textarea Field Border', 'cf7-styles' ),
                'subtitle' => __( 'Set the border of each textarea field.', 'cf7-styles' ),
                'all'      => false,
                'top'      => true,
                'right'    => true,
                'left'     => true,
                'bottom'   => true,
            ),
            // Padding & Margin..
            array(
                'id'       => 'cf7_textarea_section_spacing',
                'type'     => 'section',
                'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                'subtitle' => __( 'This section adds padding and margin for the textarea section.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_textarea_field_padding',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form textarea.wpcf7-textarea', '.wpcf7 .wpcf7-form textarea.wpcf7-textarea' ),
                'mode'           => 'padding',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Textarea Field Padding', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the padding used for the textarea field.', 'cf7-styles' ),
            ),
            array(
                'id'             => 'cf7_textarea_field_margin',
                'type'           => 'spacing',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form textarea.wpcf7-textarea', '.wpcf7 .wpcf7-form textarea.wpcf7-textarea' ),
                'mode'           => 'margin',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => false,
                'display_units'  => true,
                'title'          => __( 'Textarea Field Margin', 'cf7-styles' ),
                'subtitle'       => __( 'Specifies the margin used for the textarea field.', 'cf7-styles' ),
            ),
            // Fonts..
            array(
                'id'       => 'cf7_textarea_section_fonts',
                'type'     => 'section',
                'title'    => __( 'Fonts', 'cf7-styles' ),
                'subtitle' => __( 'This section sets font rules for the textarea field.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_textarea_field_font',
                'type'           => 'typography',
                'title'          => __( 'Textarea Field', 'cf7-styles' ),
                'google'         => true,
                'font-backup'    => true,
                'font-style'     => true,
                'subsets'        => true,
                'font-size'      => true,
                'line-height'    => true,
                'color'          => true,
                'preview'        => true,
                'all_styles'     => true,
                'text-transform' => true,
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form textarea.wpcf7-textarea', '.wpcf7 .wpcf7-form textarea.wpcf7-textarea' ),
                'units'          => 'px',
                'subtitle'       => __( 'Applies to all textarea generated by Contact Form 7.', 'cf7-styles' ),
            ),
            // Dimensions..
            array(
                'id'       => 'cf7_textarea_section_dimensions',
                'type'     => 'section',
                'title'    => __( 'Dimensions', 'cf7-styles' ),
                'subtitle' => __( 'This section adds dimension options for the textarea field.', 'cf7-styles' ),
                'indent'   => true,
            ),
            array(
                'id'             => 'cf7_textarea_field_dimension',
                'type'           => 'dimensions',
                'units'          => array( 'em', 'px', '%' ),
                'units_extended' => 'true',
                'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form textarea.wpcf7-textarea', '.wpcf7 .wpcf7-form textarea.wpcf7-textarea' ),
                'title'          => __( 'Width / Height', 'cf7-styles' ),
                'subtitle'       => __( 'Allow your users to set the width / height of Contact Form 7 textarea fields.', 'cf7-styles' ),
            ),
        ),
        ) );
        // FREE: Section shown in free version only.
        if ( cf7_styles_freemius()->is_free_plan() ) {
            // Only executed if the user in a trial mode or has a valid license.
            
            if ( !cf7_styles_freemius()->can_use_premium_code() ) {
                // FREE: Spinbox Number Field Styles.
                Redux::setSection( $opt_name, array(
                    'title'  => __( 'Numbers - Spinbox', 'cf7-styles' ),
                    'icon'   => 'el-icon-braille',
                    'class'  => 'pro-upgrade',
                    'fields' => array(
                    // Pro Warning..
                    array(
                        'id'     => 'opt-notice-critical-numbers-spinbox',
                        'type'   => 'info',
                        'notice' => true,
                        'style'  => 'critical',
                        'title'  => __( 'Pro Feature', 'cf7-styles' ),
                        'desc'   => $this->upgrade_notice(),
                    ),
                    // Background Section..
                    array(
                        'id'       => 'cf7_spinbox_section_backgrounds',
                        'type'     => 'section',
                        'title'    => __( 'Backgrounds', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds background styles for the spinbox number field sections.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_spinbox_field_background',
                        'type'     => 'background',
                        'title'    => __( 'Spinbox Number Field Background', 'cf7-styles' ),
                        'subtitle' => __( 'Set the background of the spinbox number element.', 'cf7-styles' ),
                        'preview'  => false,
                    ),
                    // Borders Section..
                    array(
                        'id'       => 'cf7_spinbox_section_borders',
                        'type'     => 'section',
                        'title'    => __( 'Borders', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds border styles for the spinbox number section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_spinbox_field_border',
                        'type'     => 'border',
                        'title'    => __( 'Spinbox Number Field Border', 'cf7-styles' ),
                        'subtitle' => __( 'Set the border of each spinbox number element.', 'cf7-styles' ),
                        'all'      => false,
                        'top'      => true,
                        'right'    => true,
                        'left'     => true,
                        'bottom'   => true,
                    ),
                    // Padding & Margin..
                    array(
                        'id'       => 'cf7_spinbox_section_spacing',
                        'type'     => 'section',
                        'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds padding and margin for the spinbox number element.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_spinbox_field_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Spinbox Number Field Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the spinbox number element.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_spinbox_field_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Spinbox Number Field Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the spinbox number element.', 'cf7-styles' ),
                    ),
                    // Fonts..
                    array(
                        'id'       => 'cf7_spinbox_section_fonts',
                        'type'     => 'section',
                        'title'    => __( 'Fonts', 'cf7-styles' ),
                        'subtitle' => __( 'This section sets font rules for the spinbox number element.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_spinbox_field_font',
                        'type'           => 'typography',
                        'title'          => __( 'Spinbox Input Field', 'cf7-styles' ),
                        'google'         => true,
                        'font-backup'    => true,
                        'font-style'     => true,
                        'subsets'        => true,
                        'font-size'      => true,
                        'line-height'    => true,
                        'color'          => true,
                        'preview'        => true,
                        'all_styles'     => true,
                        'text-transform' => true,
                        'units'          => 'px',
                        'subtitle'       => __( 'Applies to all spinbox number elements generated by Contact Form 7.', 'cf7-styles' ),
                    ),
                    // Dimensions..
                    array(
                        'id'       => 'cf7_spinbox_section_dimensions',
                        'type'     => 'section',
                        'title'    => __( 'Dimensions', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds dimension options for thespinbox number element.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_spinbox_field_dimension',
                        'type'           => 'dimensions',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => 'true',
                        'title'          => __( 'Width / Height', 'cf7-styles' ),
                        'subtitle'       => __( 'Allow your users to set the width / height of Contact Form 7 spinbox number element.', 'cf7-styles' ),
                    ),
                ),
                ) );
                // FREE: Date Styles.
                Redux::setSection( $opt_name, array(
                    'title'  => __( 'Date', 'cf7-styles' ),
                    'icon'   => 'el-icon-calendar',
                    'class'  => 'pro-upgrade',
                    'fields' => array(
                    // Pro Warning..
                    array(
                        'id'     => 'opt-notice-critical-date',
                        'type'   => 'info',
                        'notice' => true,
                        'style'  => 'critical',
                        'title'  => __( 'Pro Feature', 'cf7-styles' ),
                        'desc'   => $this->upgrade_notice(),
                    ),
                    // Background Section..
                    array(
                        'id'       => 'cf7date_section_backgrounds',
                        'type'     => 'section',
                        'title'    => __( 'Backgrounds', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds background styles for the date fields.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_date_field_background',
                        'type'     => 'background',
                        'title'    => __( 'Date Dropdown Background', 'cf7-styles' ),
                        'subtitle' => __( 'Set the background of the date input fields.', 'cf7-styles' ),
                        'preview'  => false,
                    ),
                    // Borders Section..
                    array(
                        'id'       => 'cf7_date_section_borders',
                        'type'     => 'section',
                        'title'    => __( 'Borders', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds border styles for the date fields.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_date_field_border',
                        'type'     => 'border',
                        'title'    => __( 'Date Dropdown Border', 'cf7-styles' ),
                        'subtitle' => __( 'Set the border of each date field.', 'cf7-styles' ),
                        'all'      => false,
                        'top'      => true,
                        'right'    => true,
                        'left'     => true,
                        'bottom'   => true,
                    ),
                    // Padding & Margin..
                    array(
                        'id'       => 'cf7_date_section_spacing',
                        'type'     => 'section',
                        'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds padding and margin for each of the date dropdown fields.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_date_field_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Date Dropdown Field Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for each of the date dropdown fields.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_date_field_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Date Dropdown Field Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for each the date dropdown fields.', 'cf7-styles' ),
                    ),
                    // Fonts..
                    array(
                        'id'       => 'cf7_date_section_fonts',
                        'type'     => 'section',
                        'title'    => __( 'Fonts', 'cf7-styles' ),
                        'subtitle' => __( 'This section sets font rules for the date dropdown fields.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_date_field_font',
                        'type'           => 'typography',
                        'title'          => __( 'Date Dropdown Field', 'cf7-styles' ),
                        'google'         => true,
                        'font-backup'    => true,
                        'font-style'     => true,
                        'subsets'        => true,
                        'font-size'      => true,
                        'line-height'    => true,
                        'color'          => true,
                        'preview'        => true,
                        'all_styles'     => true,
                        'text-transform' => true,
                        'output'         => array( '#cf7-styles .wpcf7 .wpcf7-form input.wpcf7-date', '.wpcf7 .wpcf7-form input.wpcf7-date' ),
                        'units'          => 'px',
                        'subtitle'       => __( 'Applies to all date dropdown fields generated by Contact Form 7.', 'cf7-styles' ),
                    ),
                    // Dimensions..
                    array(
                        'id'       => 'cf7_date_section_dimensions',
                        'type'     => 'section',
                        'title'    => __( 'Dimensions', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds dimension options for the date dropdown fields.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_date_field_dimension',
                        'type'           => 'dimensions',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => 'true',
                        'title'          => __( 'Width / Height', 'cf7-styles' ),
                        'subtitle'       => __( 'Allow your users to set the width / height of Contact Form 7 date dropdown fields.', 'cf7-styles' ),
                    ),
                ),
                ) );
                // FREE: Dropdown Styles.
                Redux::setSection( $opt_name, array(
                    'title'  => __( 'Dropdown', 'cf7-styles' ),
                    'icon'   => 'el-icon-lines',
                    'class'  => 'pro-upgrade',
                    'fields' => array(
                    // Pro Warning..
                    array(
                        'id'     => 'opt-notice-critical-dropdown',
                        'type'   => 'info',
                        'notice' => true,
                        'style'  => 'critical',
                        'title'  => __( 'Pro Feature', 'cf7-styles' ),
                        'desc'   => $this->upgrade_notice(),
                    ),
                    // Background Section..
                    array(
                        'id'       => 'cf7_select_section_backgrounds',
                        'type'     => 'section',
                        'title'    => __( 'Backgrounds', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds background styles for the dropdown section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_select_field_background',
                        'type'     => 'background',
                        'title'    => __( 'Dropdown Background', 'cf7-styles' ),
                        'subtitle' => __( 'Set the background of the dropdown section.', 'cf7-styles' ),
                        'preview'  => false,
                    ),
                    // Borders Section..
                    array(
                        'id'       => 'cf7_select_section_borders',
                        'type'     => 'section',
                        'title'    => __( 'Borders', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds border styles for the dropdown section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_select_field_border',
                        'type'     => 'border',
                        'title'    => __( 'Dropdown Menu Border', 'cf7-styles' ),
                        'subtitle' => __( 'Set the border of each dropdown field.', 'cf7-styles' ),
                        'all'      => false,
                        'top'      => true,
                        'right'    => true,
                        'left'     => true,
                        'bottom'   => true,
                    ),
                    // Padding & Margin..
                    array(
                        'id'       => 'cf7_select_section_spacing',
                        'type'     => 'section',
                        'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds padding and margin for the dropdown field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_select_field_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Dropdown Menu Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the dropdown field.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_select_field_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Dropdown Menu Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the dropdown field.', 'cf7-styles' ),
                    ),
                    // Fonts..
                    array(
                        'id'       => 'cf7_select_section_fonts',
                        'type'     => 'section',
                        'title'    => __( 'Fonts', 'cf7-styles' ),
                        'subtitle' => __( 'This section sets font rules for the dropdown (select) field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_select_field_font',
                        'type'           => 'typography',
                        'title'          => __( 'Dropdown Field', 'cf7-styles' ),
                        'google'         => true,
                        'font-backup'    => true,
                        'font-style'     => true,
                        'subsets'        => true,
                        'font-size'      => true,
                        'line-height'    => true,
                        'color'          => true,
                        'preview'        => true,
                        'all_styles'     => true,
                        'text-transform' => true,
                        'units'          => 'px',
                        'subtitle'       => __( 'Applies to all dropdown fields (select) generated by Contact Form 7.', 'cf7-styles' ),
                    ),
                    // Dimensions..
                    array(
                        'id'       => 'cf7_select_section_dimensions',
                        'type'     => 'section',
                        'title'    => __( 'Dimensions', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds dimension options for the dropdown (select) field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_select_field_dimension',
                        'type'           => 'dimensions',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => 'true',
                        'title'          => __( 'Width / Height', 'cf7-styles' ),
                        'subtitle'       => __( 'Allow your users to set the width / height of Contact Form 7 dropdown (select) fields.', 'cf7-styles' ),
                    ),
                ),
                ) );
                // FREE: Checkbox Styles.
                Redux::setSection( $opt_name, array(
                    'title'  => __( 'Checkboxes', 'cf7-styles' ),
                    'icon'   => 'el-icon-check',
                    'class'  => 'pro-upgrade',
                    'fields' => array(
                    // Pro Warning..
                    array(
                        'id'     => 'opt-notice-critical-checkbox',
                        'type'   => 'info',
                        'notice' => true,
                        'style'  => 'critical',
                        'title'  => __( 'Pro Feature', 'cf7-styles' ),
                        'desc'   => $this->upgrade_notice(),
                    ),
                    // Padding & Margin..
                    array(
                        'id'       => 'cf7_checkbox_section_spacing',
                        'type'     => 'section',
                        'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds padding and margin for the checkbox field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_checkbox_label_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Checkbox Label Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the checkbox label.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_checkbox_label_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Checkbox Label Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the checkbox label.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_checkbox_field_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Checkbox Option Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the checkbox option.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_checkbox_field_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Checkbox Option Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the checkbox option.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_checkbox_item_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Checkbox List Item Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the entire checkbox list item.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_checkbox_item_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Checkbox List Item Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the entire checkbox list item.', 'cf7-styles' ),
                    ),
                    // Fonts..
                    array(
                        'id'       => 'cf7_checkbox_section_fonts',
                        'type'     => 'section',
                        'title'    => __( 'Fonts', 'cf7-styles' ),
                        'subtitle' => __( 'This section sets font rules for the checkbox field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_checkbox_label_font',
                        'type'           => 'typography',
                        'title'          => __( 'Checkbox Label', 'cf7-styles' ),
                        'google'         => true,
                        'font-backup'    => true,
                        'font-style'     => true,
                        'subsets'        => true,
                        'font-size'      => true,
                        'line-height'    => true,
                        'color'          => true,
                        'preview'        => true,
                        'all_styles'     => true,
                        'text-transform' => true,
                        'units'          => 'px',
                        'subtitle'       => __( 'Applies to all dropdown (select) field labels generated by Contact Form 7.', 'cf7-styles' ),
                    ),
                ),
                ) );
                // FREE: Radio Button Styles.
                Redux::setSection( $opt_name, array(
                    'title'  => __( 'Radio Buttons', 'cf7-styles' ),
                    'icon'   => 'el-icon-record',
                    'class'  => 'pro-upgrade',
                    'fields' => array(
                    // Pro Warning..
                    array(
                        'id'     => 'opt-notice-critical-radio',
                        'type'   => 'info',
                        'notice' => true,
                        'style'  => 'critical',
                        'title'  => __( 'Pro Feature', 'cf7-styles' ),
                        'desc'   => $this->upgrade_notice(),
                    ),
                    // Padding & Margin..
                    array(
                        'id'       => 'cf7_radio_section_spacing',
                        'type'     => 'section',
                        'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds padding and margin for the radio button field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_radio_label_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Radio Button Label Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the radio button label.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_radio_label_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Radio Button Label Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the radio button label.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_radio_field_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Radio Button Option Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the radio button option.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_radio_field_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Radio Button Option Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the radio button option.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_radio_item_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Radio Button List Item Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the entire radio button list item.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_radio_item_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Radio Button List Item Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the entire radio button list item.', 'cf7-styles' ),
                    ),
                    // Fonts.
                    array(
                        'id'       => 'cf7_radio_section_fonts',
                        'type'     => 'section',
                        'title'    => __( 'Fonts', 'cf7-styles' ),
                        'subtitle' => __( 'This section sets font rules for the checkbox field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_radio_label_font',
                        'type'           => 'typography',
                        'title'          => __( 'Radio Button Label', 'cf7-styles' ),
                        'google'         => true,
                        'font-backup'    => true,
                        'font-style'     => true,
                        'subsets'        => true,
                        'font-size'      => true,
                        'line-height'    => true,
                        'color'          => true,
                        'preview'        => true,
                        'all_styles'     => true,
                        'text-transform' => true,
                        'units'          => 'px',
                        'subtitle'       => __( 'Applies to all radio button labels generated by Contact Form 7.', 'cf7-styles' ),
                    ),
                ),
                ) );
                // FREE: Quiz Field Styles.
                Redux::setSection( $opt_name, array(
                    'title'  => __( 'Quiz', 'cf7-styles' ),
                    'icon'   => 'el-icon-ok',
                    'class'  => 'pro-upgrade',
                    'fields' => array(
                    // Pro Warning.
                    array(
                        'id'     => 'opt-notice-critical-quiz',
                        'type'   => 'info',
                        'notice' => true,
                        'style'  => 'critical',
                        'title'  => __( 'Pro Feature', 'cf7-styles' ),
                        'desc'   => $this->upgrade_notice(),
                    ),
                    // Background Section.
                    array(
                        'id'       => 'cf7_quiz_section_backgrounds',
                        'type'     => 'section',
                        'title'    => __( 'Backgrounds', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds background styles for the input field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_quiz_field_background',
                        'type'     => 'background',
                        'title'    => __( 'Quiz Field Background', 'cf7-styles' ),
                        'subtitle' => __( 'Set the background of the quiz element', 'cf7-styles' ),
                        'preview'  => false,
                    ),
                    // Borders Section.
                    array(
                        'id'       => 'cf7_quiz_section_borders',
                        'type'     => 'section',
                        'title'    => __( 'Borders', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds border styles for the input field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_quiz_field_border',
                        'type'     => 'border',
                        'title'    => __( 'Quiz Field Border', 'cf7-styles' ),
                        'subtitle' => __( 'Set the border of each quiz element.', 'cf7-styles' ),
                        'all'      => false,
                        'top'      => true,
                        'right'    => true,
                        'left'     => true,
                        'bottom'   => true,
                    ),
                    // Padding & Margin.
                    array(
                        'id'       => 'cf7_quiz_section_spacing',
                        'type'     => 'section',
                        'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds padding and margin for the quiz field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_quiz_field_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Quiz Field Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the text text (input) field.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_quiz_field_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Quiz Field Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the quiz field.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_quiz_label_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Quiz Label Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the text text (input) field.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_quiz_label_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Quiz Label Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the quiz field.', 'cf7-styles' ),
                    ),
                    // Fonts.
                    array(
                        'id'       => 'cf7_quiz_section_fonts',
                        'type'     => 'section',
                        'title'    => __( 'Fonts', 'cf7-styles' ),
                        'subtitle' => __( 'This section sets font rules for the quiz field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_quiz_field_font',
                        'type'           => 'typography',
                        'title'          => __( 'Quiz Field', 'cf7-styles' ),
                        'google'         => true,
                        'font-backup'    => true,
                        'font-style'     => true,
                        'subsets'        => true,
                        'font-size'      => true,
                        'line-height'    => true,
                        'color'          => true,
                        'preview'        => true,
                        'all_styles'     => true,
                        'text-transform' => true,
                        'units'          => 'px',
                        'subtitle'       => __( 'Applies to all quiz fields generated by Contact Form 7.', 'cf7-styles' ),
                    ),
                    // Dimensions.
                    array(
                        'id'       => 'cf7_quiz_section_dimensions',
                        'type'     => 'section',
                        'title'    => __( 'Dimensions', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds dimension options for the quiz field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_quiz_field_dimension',
                        'type'           => 'dimensions',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => 'true',
                        'title'          => __( 'Width / Height', 'cf7-styles' ),
                        'subtitle'       => __( 'Allow your users to set the width / height of Contact Form 7 quiz fields.', 'cf7-styles' ),
                    ),
                ),
                ) );
                // FREE: Captcha Styles.
                Redux::setSection( $opt_name, array(
                    'title'  => __( 'Captcha', 'cf7-styles' ),
                    'icon'   => 'el-icon-barcode',
                    'class'  => 'pro-upgrade',
                    'fields' => array(
                    // Pro Warning.
                    array(
                        'id'     => 'opt-notice-critical-captcha',
                        'type'   => 'info',
                        'notice' => true,
                        'style'  => 'critical',
                        'title'  => __( 'Pro Feature', 'cf7-styles' ),
                        'desc'   => $this->upgrade_notice(),
                    ),
                    // Background Section.
                    array(
                        'id'       => 'cf7_captcha_section_backgrounds',
                        'type'     => 'section',
                        'title'    => __( 'Backgrounds', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds background styles for the Captcha field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_captcha_field_background',
                        'type'     => 'background',
                        'title'    => __( 'Captcha Field Background', 'cf7-styles' ),
                        'subtitle' => __( 'Set the background of the Captcha element (input field)', 'cf7-styles' ),
                        'preview'  => false,
                    ),
                    // Borders Section.
                    array(
                        'id'       => 'cf7_captcha_section_borders',
                        'type'     => 'section',
                        'title'    => __( 'Borders', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds border styles for the Captcha field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'       => 'cf7_captcha_field_border',
                        'type'     => 'border',
                        'title'    => __( 'Captcha Field Border', 'cf7-styles' ),
                        'subtitle' => __( 'Set the border of each Captcha element.', 'cf7-styles' ),
                        'all'      => false,
                        'top'      => true,
                        'right'    => true,
                        'left'     => true,
                        'bottom'   => true,
                    ),
                    array(
                        'id'       => 'cf7_captcha_image_border',
                        'type'     => 'border',
                        'title'    => __( 'Captcha Field Border', 'cf7-styles' ),
                        'subtitle' => __( 'Set the border of each Captcha image .', 'cf7-styles' ),
                        'all'      => false,
                        'top'      => true,
                        'right'    => true,
                        'left'     => true,
                        'bottom'   => true,
                    ),
                    // Padding & Margin.
                    array(
                        'id'       => 'cf7_captcha_section_spacing',
                        'type'     => 'section',
                        'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds padding and margin for the Captcha field section.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_captcha_field_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Captcha Field Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the Captcha field.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_captcha_field_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Captcha Field Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the Captcha field.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_captcha_image_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Captcha Image Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the Captcha image.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_captcha_image_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Captcha Image Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the Captcha image.', 'cf7-styles' ),
                    ),
                    // Fonts.
                    array(
                        'id'       => 'cf7_captcha_section_fonts',
                        'type'     => 'section',
                        'title'    => __( 'Fonts', 'cf7-styles' ),
                        'subtitle' => __( 'This section sets font rules for the Captcha field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_captcha_field_font',
                        'type'           => 'typography',
                        'title'          => __( 'Captcha Field', 'cf7-styles' ),
                        'google'         => true,
                        'font-backup'    => true,
                        'font-style'     => true,
                        'subsets'        => true,
                        'font-size'      => true,
                        'line-height'    => true,
                        'color'          => true,
                        'preview'        => true,
                        'all_styles'     => true,
                        'text-transform' => true,
                        'units'          => 'px',
                        'subtitle'       => __( 'Applies to all Captcha fields generated by Contact Form 7.', 'cf7-styles' ),
                    ),
                    // Dimensions.
                    array(
                        'id'       => 'cf7_captcha_section_dimensions',
                        'type'     => 'section',
                        'title'    => __( 'Dimensions', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds dimension options for the Captcha field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_captcha_field_dimension',
                        'type'           => 'dimensions',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => 'true',
                        'title'          => __( 'Width / Height', 'cf7-styles' ),
                        'subtitle'       => __( 'Allow your users to set the width / height of Contact Form 7 Captcha fields.', 'cf7-styles' ),
                    ),
                ),
                ) );
                // FREE: File Upload Styles.
                Redux::setSection( $opt_name, array(
                    'title'  => __( 'File Upload', 'cf7-styles' ),
                    'icon'   => 'el-icon-stackoverflow',
                    'class'  => 'pro-upgrade',
                    'fields' => array(
                    // Pro Warning.
                    array(
                        'id'     => 'opt-notice-critical-file-upload',
                        'type'   => 'info',
                        'notice' => true,
                        'style'  => 'critical',
                        'title'  => __( 'Pro Feature', 'cf7-styles' ),
                        'desc'   => $this->upgrade_notice(),
                    ),
                    // Padding & Margin.
                    array(
                        'id'       => 'cf7_upload_section_spacing',
                        'type'     => 'section',
                        'title'    => __( 'Padding / Margin', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds padding and margin for the upload file element.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_upload_field_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Upload File Padding', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the padding used for the uploaded file element.', 'cf7-styles' ),
                    ),
                    array(
                        'id'             => 'cf7_upload_field_margin',
                        'type'           => 'spacing',
                        'mode'           => 'margin',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => false,
                        'display_units'  => true,
                        'title'          => __( 'Upload File Margin', 'cf7-styles' ),
                        'subtitle'       => __( 'Specifies the margin used for the uploaded file element.', 'cf7-styles' ),
                    ),
                    // Fonts.
                    array(
                        'id'       => 'cf7_upload_section_fonts',
                        'type'     => 'section',
                        'title'    => __( 'Fonts', 'cf7-styles' ),
                        'subtitle' => __( 'This section sets font rules for the file upload field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_upload_field_font',
                        'type'           => 'typography',
                        'title'          => __( 'Uploaded File Field', 'cf7-styles' ),
                        'google'         => true,
                        'font-backup'    => true,
                        'font-style'     => true,
                        'subsets'        => true,
                        'font-size'      => true,
                        'line-height'    => true,
                        'color'          => true,
                        'preview'        => true,
                        'all_styles'     => true,
                        'text-transform' => true,
                        'units'          => 'px',
                        'subtitle'       => __( 'Applies to all upload file fields generated by Contact Form 7.', 'cf7-styles' ),
                    ),
                    // Dimensions.
                    array(
                        'id'       => 'cf7_upload_section_dimensions',
                        'type'     => 'section',
                        'title'    => __( 'Dimensions', 'cf7-styles' ),
                        'subtitle' => __( 'This section adds dimension options for the file upload field.', 'cf7-styles' ),
                        'indent'   => true,
                    ),
                    array(
                        'id'             => 'cf7_upload_field_dimension',
                        'type'           => 'dimensions',
                        'units'          => array( 'em', 'px', '%' ),
                        'units_extended' => 'true',
                        'title'          => __( 'Width / Height', 'cf7-styles' ),
                        'subtitle'       => __( 'Allow your users to set the width / height of Contact Form 7 file upload fields.', 'cf7-styles' ),
                    ),
                ),
                ) );
            }
        
        }
    }

}