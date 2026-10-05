<?php
if (!defined('ABSPATH')) exit;

require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/enquiries.php';
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/launch.php';

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', ['height' => 120, 'width' => 320, 'flex-width' => true, 'flex-height' => true]);
    register_nav_menus(['primary' => 'Primary menu']);
    add_image_size('lax-card', 1200, 750, true);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('lax-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
});

/* Journeys: editable like posts */
add_action('init', function () {
    register_post_type('journey', [
        'labels' => ['name' => 'Journeys', 'singular_name' => 'Journey', 'add_new_item' => 'Add journey', 'edit_item' => 'Edit journey'],
        'public' => true, 'has_archive' => false, 'show_in_rest' => true,
        'menu_icon' => 'dashicons-location-alt', 'menu_position' => 24,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'rewrite' => ['slug' => 'journey'],
    ]);
});
add_action('add_meta_boxes', function () {
    add_meta_box('lax_where', 'Location line', function ($post) {
        wp_nonce_field('lax_where', 'lax_where_n');
        printf('<input type="text" style="width:100%%" name="lax_where" value="%s" placeholder="Maasai Mara, Kenya">', esc_attr(get_post_meta($post->ID, 'lax_where', true)));
    }, 'journey', 'side');
});
add_action('save_post_journey', function ($id) {
    if (!isset($_POST['lax_where_n']) || !wp_verify_nonce($_POST['lax_where_n'], 'lax_where') || !current_user_can('edit_post', $id)) return;
    update_post_meta($id, 'lax_where', sanitize_text_field(wp_unslash($_POST['lax_where'] ?? '')));
});

/* Home page text: Appearance > Customize > Safari LAX Home */
add_action('customize_register', function ($c) {
    $c->add_section('lax_home', ['title' => 'Safari LAX Home', 'priority' => 30]);
    $fields = [
        'lax_h1a'  => ['Headline, first line', 'Private Africa,'],
        'lax_h1b'  => ['Headline, second line', 'Unhurried.'],
        'lax_sub'  => ['Intro line', 'Tailor-made journeys through Kenya and across the continent, shaped around you alone.'],
        'lax_cta'  => ['Button text', 'Begin a private enquiry'],
        'lax_foot' => ['Footer contact email', 'info@safarilax.world'],
    ];
    foreach ($fields as $k => $v) {
        $c->add_setting($k, ['default' => $v[1], 'sanitize_callback' => 'sanitize_text_field']);
        $c->add_control($k, ['label' => $v[0], 'section' => 'lax_home', 'type' => 'text']);
    }
    $c->add_setting('lax_hero_img', ['sanitize_callback' => 'absint']);
    $c->add_control(new WP_Customize_Media_Control($c, 'lax_hero_img', ['label' => 'Hero image', 'section' => 'lax_home', 'mime_type' => 'image']));
});

/* Basic SEO: meta description and social tags */
add_action('wp_head', function () {
    $desc = is_singular() && has_excerpt() ? get_the_excerpt() : get_bloginfo('description');
    if (!$desc) return;
    printf("<meta name=\"description\" content=\"%s\">\n<meta property=\"og:title\" content=\"%s\">\n<meta property=\"og:description\" content=\"%s\">\n<meta property=\"og:type\" content=\"website\">\n",
        esc_attr($desc), esc_attr(wp_get_document_title()), esc_attr($desc));
    if (is_singular() && has_post_thumbnail()) printf("<meta property=\"og:image\" content=\"%s\">\n", esc_url(get_the_post_thumbnail_url(null, 'large')));
}, 5);

/* Tidy: remove emoji scripts and the WordPress version tag */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_generator');

/* One-time rewrite flush after the Journeys type exists, so /journey/... links work straight after install */
add_action('init', function () {
    if (get_option('lax_rewrite_v') !== '1') {
        flush_rewrite_rules(false);
        update_option('lax_rewrite_v', '1');
    }
}, 99);
