<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main">Skip to content</a>
<header class="site-header"><div class="wrap">
<?php if (has_custom_logo()) : the_custom_logo(); else : ?>
<a class="mark" href="<?php echo esc_url(home_url('/')); ?>">SAFARI&nbsp;LAX</a>
<?php endif; ?>
<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Menu"><span></span></button>
<nav class="site-nav" id="site-nav" aria-label="Main">
<?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => function () {
    echo '<ul>';
    foreach (['about' => 'About', 'journeys' => 'Journeys', 'services' => 'Services', 'gallery' => 'Gallery', 'contact' => 'Contact'] as $slug => $label)
        printf('<li><a href="%s">%s</a></li>', esc_url(home_url("/$slug/")), esc_html($label));
    echo '</ul>';
}]); ?>
</nav>
</div></header>
<main id="main">
