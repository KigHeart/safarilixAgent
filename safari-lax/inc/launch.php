<?php
if (!defined('ABSPATH')) exit;

/* Self-hosted fonts: preload, and mark JS-capable browsers so the mobile menu degrades safely */
add_action('wp_head', function () {
    echo "<script>document.documentElement.classList.add('js')</script>\n";
    foreach (['cormorant', 'jost'] as $f) {
        printf("<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n", esc_url(get_template_directory_uri() . '/assets/fonts/' . $f . '.woff2'));
    }
}, 1);
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('lax-nav', get_template_directory_uri() . '/assets/nav.js', [], wp_get_theme()->get('Version'), ['in_footer' => true, 'strategy' => 'defer']);
});

/* Structured data for search engines */
add_action('wp_head', function () {
    if (!is_front_page()) return;
    $d = ['@context' => 'https://schema.org', '@type' => 'TravelAgency', 'name' => get_bloginfo('name'), 'url' => home_url('/'),
          'description' => get_bloginfo('description'), 'email' => get_theme_mod('lax_foot', 'info@safarilax.world'), 'areaServed' => 'Kenya'];
    if ($p = lax_opt('phone')) $d['telephone'] = $p;
    echo '<script type="application/ld+json">' . wp_json_encode($d, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
});

/* Hardening */
add_filter('xmlrpc_enabled', '__return_false');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
add_action('template_redirect', function () {
    if (!is_user_logged_in() && (isset($_GET['author']) || is_author())) { wp_safe_redirect(home_url('/'), 301); exit; }
});
add_filter('rest_endpoints', function ($e) {
    if (!is_user_logged_in()) { unset($e['/wp/v2/users'], $e['/wp/v2/users/(?P<id>[\d]+)']); }
    return $e;
});
add_filter('login_errors', function () { return 'Those details are not right.'; });

/* Email through the real mailbox: set these in wp-config.php (see README). Not needed if an SMTP plugin is used. */
add_action('phpmailer_init', function ($m) {
    if (!defined('LAX_SMTP_HOST')) return;
    $m->isSMTP();
    $m->Host = LAX_SMTP_HOST;
    $m->Port = defined('LAX_SMTP_PORT') ? LAX_SMTP_PORT : 587;
    $m->SMTPSecure = defined('LAX_SMTP_SECURE') ? LAX_SMTP_SECURE : 'tls';
    if (defined('LAX_SMTP_USER')) { $m->SMTPAuth = true; $m->Username = LAX_SMTP_USER; $m->Password = defined('LAX_SMTP_PASS') ? LAX_SMTP_PASS : ''; }
    $m->Sender = lax_opt('from', 'bookings@safarilax.world');
});

/* Test button on the settings page */
add_action('admin_post_lax_test_mail', function () {
    if (!current_user_can('manage_options') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'lax_test')) wp_die('Not allowed');
    $ok = wp_mail(lax_to(), 'Safari LAX test email', 'If you can read this, enquiry emails will reach you.', ['From: Safari LAX Website <' . lax_opt('from', 'bookings@safarilax.world') . '>']);
    wp_safe_redirect(add_query_arg('lax_test', $ok ? 'ok' : 'fail', admin_url('options-general.php?page=lax-settings')));
    exit;
});

/* Admin warnings */
add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) return;
    if (isset($_GET['lax_test'])) {
        $ok = $_GET['lax_test'] === 'ok';
        printf('<div class="notice notice-%s"><p>%s</p></div>', $ok ? 'success' : 'error', $ok ? 'Test email sent. Check the enquiry inbox.' : 'The test email could not be sent. Check your SMTP settings.');
    }
    $bad = get_posts(['post_type' => 'lax_enquiry', 'post_status' => 'private', 'numberposts' => 1, 'fields' => 'ids', 'meta_key' => 'mail_ok', 'meta_value' => '0']);
    if ($bad) printf('<div class="notice notice-warning"><p><strong>Safari LAX:</strong> an enquiry was saved but its email did not send. Fix your email settings, then see <a href="%s">Enquiries</a>.</p></div>', esc_url(admin_url('edit.php?post_type=lax_enquiry')));
});
