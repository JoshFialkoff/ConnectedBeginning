<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'connected-beginnings', get_stylesheet_directory() . '/languages' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', __( 'Connected Beginnings Theme', 'connected-beginnings' ) );
define( 'CHILD_THEME_URL', 'http://www.connectedbeginnings.org/' );
define( 'CHILD_THEME_VERSION', '1.0.5' );

//* Add HTML5 markup structure
add_theme_support( 'html5' );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'connected_beginnings_enqueue_scripts_styles' );
function connected_beginnings_enqueue_scripts_styles() {

    wp_enqueue_script( 'connected-beginnings-responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menu.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
    wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700|Raleway:400,500', array(), null );
}

//* Add support for custom header
add_theme_support( 'custom-header', array(
    'default-text-color'     => '000000',
    'header-selector'        => '.site-title a',
    'header-text'            => false,
    'height'                 => 120,
    'width'                  => 320,
) );

//* Add support for custom background
add_theme_support( 'custom-background', array(
    'default-color'         => 'ffffff',
    'default-image'         => get_stylesheet_directory_uri() . '/images/header-banner.png',
    'wp-head-callback'      => 'connected_beginnings_background_callback',
) );

//* Add custom background callback 
function connected_beginnings_background_callback() { 
    $background = get_background_image();  
    $color = get_background_color();

    if ( ! $background && ! $color )  
        return; 

    echo sprintf( 
        "<style type='text/css'>.custom-background .site-header-banner { background: %s %s %s %s %s; } </style>",
        $background ? 'url('. esc_url($background) .')' : '',
        $color ? '#'. esc_attr($color) : 'transparent', 
        get_theme_mod( 'background_repeat', 'repeat' ), 
        get_theme_mod( 'background_position_x', 'left' ), 
        get_theme_mod( 'background_attachment', 'scroll' ) 
    );
} 

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

//* Unregister secondary sidebar
unregister_sidebar( 'sidebar-alt' );

//* Unregister secondary navigation menu
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Primary Navigation Menu', 'connected-beginnings' ) ) );

//* Unregister secondary sidebar 
remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );

//* Add custom body class to the head
add_filter( 'body_class', 'connected_beginnings_custom_body_class' );
function connected_beginnings_custom_body_class( $classes ) {
    $classes[] = 'connected-beginnings';
    return $classes;
}

//* Hook before header widget area above header
add_action( 'genesis_before_header', 'connected_beginnings_before_header' );
function connected_beginnings_before_header() {
    genesis_widget_area( 'before-header', array(
        'before' => '<div class="before-header widget-area"><div class="wrap">',
        'after'  => '</div></div>',
    ) );
}

//* Hook site header banner area after header
add_action( 'genesis_after_header', 'connected_beginnings_site_header_banner' );
function connected_beginnings_site_header_banner() {
    if ( ! is_front_page() ) return;
    genesis_widget_area( 'site-banner-after-header', array(
        'before' => '<div class="site-header-banner"><div class="wrap"><div class="widget-area">',
        'after'  => '</div></div></div>',
    ) );
}

//* Hook welcome message widget area before content
add_action( 'genesis_before_loop', 'connected_beginnings_welcome_message' );
function connected_beginnings_welcome_message() {
    if ( ! is_front_page() || get_query_var( 'paged' ) >= 2 ) return;
    genesis_widget_area( 'welcome-message', array(
        'before' => '<div class="welcome-message widget-area">',
        'after'  => '</div>',
    ) );
}

//* Modify the WordPress read more link
add_filter( 'the_content_more_link', 'connected_beginnings_read_more' );
function connected_beginnings_read_more() {
    return '<a class="more-link" href="' . get_permalink() . '">' . __( 'Continue Reading', 'connected-beginnings' ) . '</a>';
}

//* Modify the content limit read more link
add_action( 'genesis_before_loop', 'connected_beginnings_more' );
function connected_beginnings_more() {
    add_filter( 'get_the_content_more_link', 'connected_beginnings_read_more' );
}

add_action( 'genesis_after_loop', 'connected_beginnings_remove_more' );
function connected_beginnings_remove_more() {
    remove_filter( 'get_the_content_more_link', 'connected_beginnings_read_more' );
}

//* Remove entry meta in entry footer
add_action( 'genesis_before_entry', 'connected_beginnings_remove_entry_meta' );
function connected_beginnings_remove_entry_meta() {
    if ( ! is_single() ) {
        remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
        remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
        remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
    }
}

//* Hook after entry widget after entry content
add_action( 'genesis_after_entry', 'connected_beginnings_after_entry', 9 );
function connected_beginnings_after_entry() {
    if ( is_singular( 'post' ) )
        genesis_widget_area( 'after-entry', array(
            'before' => '<div class="after-entry widget-area">',
            'after'  => '</div>',
        ) );
}

//* Modify the size of the Gravatar in the author box
add_filter( 'genesis_author_box_gravatar_size', 'connected_beginnings_author_box_gravatar' );
function connected_beginnings_author_box_gravatar( $size ) {
    return 180;
}

//* Modify the size of the Gravatar in the entry comments
add_filter( 'genesis_comment_list_args', 'connected_beginnings_comments_gravatar' );
function connected_beginnings_comments_gravatar( $args ) {
    $args['avatar_size'] = 100;
    return $args;
}

//* Hook split sidebar and bottom sidebar widget areas below primary sidebar
add_action( 'genesis_after_sidebar_widget_area', 'connected_beginnings_extra_sidebars' );
function connected_beginnings_extra_sidebars() {
    if ( is_active_sidebar( 'split-sidebar-left' ) || is_active_sidebar( 'split-sidebar-right' ) ) {
        echo '<div class="split-sidebars">';
        genesis_widget_area( 'split-sidebar-left', array(
            'before' => '<div class="split-sidebar-left widget-area">',
            'after'  => '</div>',
        ) );
        genesis_widget_area( 'split-sidebar-right', array(
            'before' => '<div class="split-sidebar-right widget-area">',
            'after'  => '</div>',
        ) );
        echo '</div>';
    }

    genesis_widget_area( 'bottom-sidebar', array(
        'before' => '<div class="bottom-sidebar widget-area">',
        'after'  => '</div>',
    ) );
}

//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'connected_beginnings_remove_comment_form_allowed_tags' );
function connected_beginnings_remove_comment_form_allowed_tags( $defaults ) {
    $defaults['comment_notes_after'] = '';
    return $defaults;
}

//* Register widget areas
genesis_register_sidebar( array(
    'id'          => 'before-header',
    'name'        => __( 'Before Header', 'connected-beginnings' ),
    'description' => __( 'This is the before header widget area.', 'connected-beginnings' ),
) );
genesis_register_sidebar( array(
    'id'          => 'site-banner-after-header',
    'name'        => __( 'After Header', 'connected-beginnings' ),
    'description' => __( 'This is the after header site banner widget area.', 'connected-beginnings' ),
) );
genesis_register_sidebar( array(
    'id'          => 'welcome-message',
    'name'        => __( 'Welcome Message', 'connected-beginnings' ),
    'description' => __( 'This is the welcome message widget area.', 'connected-beginnings' ),
) );
genesis_register_sidebar( array(
    'id'          => 'after-entry',
    'name'        => __( 'After Entry', 'connected-beginnings' ),
    'description' => __( 'This is the after entry
			genesis_register_sidebar( array(
    'id'          => 'after-entry',
    'name'        => __( 'After Entry', 'connected-beginnings' ),
    'description' => __( 'This is the after entry widget area.', 'connected-beginnings' ),
) );

genesis_register_sidebar( array(
    'id'          => 'split-sidebar-left',
    'name'        => __( 'Split Sidebar Left', 'connected-beginnings' ),
    'description' => __( 'This is the left split sidebar widget area.', 'connected-beginnings' ),
) );

genesis_register_sidebar( array(
    'id'          => 'split-sidebar-right',
    'name'        => __( 'Split Sidebar Right', 'connected-beginnings' ),
    'description' => __( 'This is the right split sidebar widget area.', 'connected-beginnings' ),
) );

genesis_register_sidebar( array(
    'id'          => 'bottom-sidebar',
    'name'        => __( 'Bottom Sidebar', 'connected-beginnings' ),
    'description' => __( 'This is the bottom sidebar widget area.', 'connected-beginnings' ),
) );

//* Add UMDI logo to the site description
add_action( 'genesis_site_description', 'cbti_do_umdi_logo', 11 );
function cbti_do_umdi_logo() {
    printf(
        '<div class="umdi-logo"><a href="%s">%s</a></div>',
        esc_url( 'http://www.donahue.umassp.edu/' ),
        esc_html__( 'UMass Donahue Institute', 'connected-beginnings' )
    );
}
