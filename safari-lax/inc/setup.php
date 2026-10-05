<?php
if (!defined('ABSPATH')) exit;

/* On activation: create the six pages, set the homepage, build the menu, add sample journeys. Runs once; never overwrites existing pages. */
add_action('after_switch_theme', function () {
    $pages = [
        'home'     => ['Home', '', '<p class="lede">We do not sell itineraries. We hold the pace, so that you never have to.</p><p class="sub">Safari LAX designs private journeys for a small number of guests each year. Every day is composed with care, with generous time in each place, discreet hosts and nothing rushed. Replace this text with the brand narrative.</p>'],
        'about'    => ['About', '', '<p class="lede">A Kenyan team, a small number of guests, and no hurry.</p><p>Tell the Safari LAX story here: who you are, where you are from and what you believe a journey should feel like.</p><h2>How we work</h2><p>Describe the way a journey is designed, from the first conversation to the farewell.</p>'],
        'journeys' => ['Journeys', 'template-journeys.php', '<p>Three starting points. Each is rebuilt around you before it is ever confirmed.</p>'],
        'services' => ['Services', '', '<h3>Journey design</h3><p>One conversation, then a proposal written for you.</p><h3>Private guides and vehicles</h3><p>Your own guide and vehicle throughout.</p><h3>Camps and lodges</h3><p>Only places we have stayed in ourselves.</p><h3>Celebrations and families</h3><p>Anniversaries and multi-generational trips, handled invisibly.</p><h3>Across Africa</h3><p>Kenya is home. Further afield, the same care.</p><h3>Care while you travel</h3><p>One team on call from first greeting to last farewell.</p>'],
        'gallery'  => ['Gallery', '', '<p>Add a Gallery block here and choose your photographs.</p>'],
        'privacy-policy' => ['Privacy Policy', '', '<p><strong>Draft template: complete the bracketed items and have it reviewed before publishing.</strong></p><h2>Who we are</h2><p>[Legal company name], trading as Safari LAX, [registered address]. Contact: [privacy email].</p><h2>What we collect</h2><p>When you send a private enquiry we collect your name, email address, preferred travel dates, number of guests and the message you write. This website does not use advertising cookies or analytics.</p><h2>Why we use it</h2><p>To reply to your enquiry and plan your journey. We only contact you about your enquiry, based on the consent you give when you send it.</p><h2>Who can see it</h2><p>Our team, and the providers that host this website and deliver our email. We do not sell your data.</p><h2>How long we keep it</h2><p>[Retention period, for example 24 months after your last contact], then we delete it.</p><h2>Your rights</h2><p>You may ask to see, correct or delete your data at any time by writing to [privacy email].</p>'],
        'contact'  => ['Contact', 'template-contact.php', '<p>Tell us a little. A member of the Kenya team will reply personally, usually within one working day.</p>'],
    ];
    $ids = [];
    foreach ($pages as $slug => $p) {
        $existing = get_page_by_path($slug);
        if ($existing) { $ids[$slug] = $existing->ID; continue; }
        $id = wp_insert_post(['post_type' => 'page', 'post_status' => ($slug === 'privacy-policy' ? 'draft' : 'publish'), 'post_title' => $p[0], 'post_name' => $slug, 'post_content' => $p[2]]);
        if ($id && !is_wp_error($id)) {
            if ($p[1]) update_post_meta($id, '_wp_page_template', $p[1]);
            $ids[$slug] = $id;
        }
    }
    if (!empty($ids['privacy-policy'])) update_option('wp_page_for_privacy_policy', $ids['privacy-policy']);
    if (!empty($ids['home'])) { update_option('show_on_front', 'page'); update_option('page_on_front', $ids['home']); }
    if (in_array(get_option('blogdescription'), ['', 'Just another WordPress site'], true)) update_option('blogdescription', 'Private Africa, Unhurried.');

    if (!wp_get_nav_menu_object('Primary')) {
        $menu = wp_create_nav_menu('Primary');
        if (!is_wp_error($menu)) {
            foreach (['about', 'journeys', 'services', 'gallery', 'contact'] as $s) {
                if (!empty($ids[$s])) wp_update_nav_menu_item($menu, 0, ['menu-item-object-id' => $ids[$s], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish']);
            }
            $locs = get_theme_mod('nav_menu_locations', []);
            $locs['primary'] = $menu;
            set_theme_mod('nav_menu_locations', $locs);
        }
    }

    if (!get_posts(['post_type' => 'journey', 'numberposts' => 1, 'post_status' => 'any'])) {
        $samples = [
            ['The Mara, Slowly', 'Maasai Mara, Kenya', 'Seven nights between a private conservancy camp and a quiet riverside tent. Early drives, long lunches, no queues at any sighting.'],
            ['The Northern Frontier', 'Laikipia and Samburu', 'Horseback, camel and walking safaris across open country, ending at a family-run lodge with the whole house to yourselves.'],
            ['Coast, Without a Clock', 'Lamu and the Kenyan coast', 'A private dhow, a restored Swahili house and days with no fixed plan. A gentle close to a longer journey.'],
        ];
        foreach ($samples as $i => $s) {
            $id = wp_insert_post(['post_type' => 'journey', 'post_status' => 'publish', 'post_title' => $s[0], 'post_excerpt' => $s[2], 'post_content' => '<p>' . $s[2] . ' Replace with the full journey description.</p>', 'menu_order' => $i]);
            if ($id && !is_wp_error($id)) update_post_meta($id, 'lax_where', $s[1]);
        }
    }
    flush_rewrite_rules();
});
