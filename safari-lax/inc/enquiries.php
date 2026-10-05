<?php
if (!defined('ABSPATH')) exit;

/* Every enquiry is saved in the admin before any email is sent, so nothing is lost if mail fails. */
add_action('init', function () {
    register_post_type('lax_enquiry', [
        'labels' => ['name' => 'Enquiries', 'singular_name' => 'Enquiry', 'menu_name' => 'Enquiries', 'edit_item' => 'Enquiry', 'search_items' => 'Search enquiries', 'not_found' => 'No enquiries yet'],
        'public' => false, 'show_ui' => true, 'show_in_menu' => true, 'show_in_rest' => false,
        'menu_icon' => 'dashicons-email-alt', 'menu_position' => 25,
        'supports' => ['title'], 'capability_type' => 'post',
        'capabilities' => ['create_posts' => 'do_not_allow'], 'map_meta_cap' => true,
    ]);
});
add_filter('manage_lax_enquiry_posts_columns', function () {
    return ['cb' => '<input type="checkbox">', 'title' => 'Guest', 'lax_email' => 'Email', 'lax_dates' => 'Dates', 'lax_guests' => 'Guests', 'date' => 'Received'];
});
add_action('manage_lax_enquiry_posts_custom_column', function ($col, $id) {
    $map = ['lax_email' => 'email', 'lax_dates' => 'dates', 'lax_guests' => 'guests'];
    if (isset($map[$col])) echo esc_html(get_post_meta($id, $map[$col], true));
}, 10, 2);
add_action('add_meta_boxes', function () {
    add_meta_box('lax_enq', 'Enquiry details', function ($post) {
        $g = function ($k) use ($post) { return get_post_meta($post->ID, $k, true); };
        $email = $g('email');
        echo '<p><strong>Email:</strong> <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></p>';
        echo '<p><strong>Dates:</strong> ' . esc_html($g('dates')) . '</p>';
        echo '<p><strong>Guests:</strong> ' . esc_html($g('guests')) . '</p>';
        echo '<p><strong>Message:</strong><br>' . nl2br(esc_html($g('message'))) . '</p>';
        echo '<p><strong>Email to team:</strong> ' . ($g('mail_ok') === '1' ? 'sent' : 'not sent (check your SMTP settings)') . '</p>';
        echo '<p><a class="button button-primary" href="mailto:' . esc_attr($email) . '?subject=' . rawurlencode('Your Safari LAX enquiry') . '">Reply by email</a></p>';
    }, 'lax_enquiry', 'normal', 'high');
});

/* Form shortcode */
add_shortcode('lax_enquiry', function () {
    $s = isset($_GET['enquiry']) ? sanitize_key($_GET['enquiry']) : '';
    $to = lax_to();
    $msg = [
        'sent'    => ['Thank you. We will be in touch shortly.', ''],
        'invalid' => ['Please add your name, a valid email address, and tick the consent box.', 'err'],
        'wait'    => ['Please wait a moment before sending another enquiry.', 'err'],
        'error'   => ['Something went wrong. Please try again, or write to ' . $to . '.', 'err'],
        'failed'  => ['We could not send your enquiry. Please write to ' . $to . '.', 'err'],
    ];
    $privacy = lax_opt('privacy') ?: get_privacy_policy_url();
    ob_start();
    echo '<div id="enquiry">';
    if (isset($msg[$s])) printf('<p class="notice %s" role="status">%s</p>', esc_attr($msg[$s][1]), esc_html($msg[$s][0]));
    if ($s !== 'sent') { ?>
    <form class="lax" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="lax_enquiry">
        <?php wp_nonce_field('lax_enquiry', '_n'); ?>
        <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="two">
            <label>Your name<input required name="name" autocomplete="name"></label>
            <label>Email<input required type="email" name="email" autocomplete="email"></label>
        </div>
        <div class="two">
            <label>Preferred travel dates<input name="dates" placeholder="e.g. August 2027"></label>
            <label>Guests<select name="guests"><option>2</option><option>3 to 4</option><option>5 to 8</option><option>9 or more</option></select></label>
        </div>
        <label>Where does your imagination take you?<textarea rows="4" name="msg"></textarea></label>
        <label class="check"><input type="checkbox" required name="consent" value="1"> <span>I agree to be contacted about my enquiry.<?php if ($privacy) echo ' <a href="' . esc_url($privacy) . '">Privacy policy</a>.'; ?></span></label>
        <button class="btn" type="submit">Send enquiry</button>
    </form>
    <?php }
    echo '</div>';
    return ob_get_clean();
});

function lax_redirect($status) {
    $back = remove_query_arg('enquiry', wp_get_referer() ?: home_url('/'));
    wp_safe_redirect(add_query_arg('enquiry', $status, $back) . '#enquiry');
    exit;
}

function lax_handle_enquiry() {
    if (!isset($_POST['_n']) || !wp_verify_nonce($_POST['_n'], 'lax_enquiry')) lax_redirect('error');
    if (!empty($_POST['website'])) lax_redirect('sent'); // honeypot: bots think it worked
    $key = 'lax_rl_' . md5($_SERVER['REMOTE_ADDR'] ?? 'x');
    if (get_transient($key)) lax_redirect('wait');

    $name   = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $email  = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $dates  = sanitize_text_field(wp_unslash($_POST['dates'] ?? ''));
    $guests = sanitize_text_field(wp_unslash($_POST['guests'] ?? ''));
    $text   = sanitize_textarea_field(wp_unslash($_POST['msg'] ?? ''));
    if ($name === '' || !is_email($email) || empty($_POST['consent'])) lax_redirect('invalid');
    set_transient($key, 1, 60);

    // 1. Save first
    $id = wp_insert_post(['post_type' => 'lax_enquiry', 'post_status' => 'private', 'post_title' => $name], true);
    $saved = !is_wp_error($id) && $id;
    if ($saved) {
        foreach (['email' => $email, 'dates' => $dates, 'guests' => $guests, 'message' => $text] as $k => $v) update_post_meta($id, $k, $v);
    }

    // 2. Email the team
    $from = lax_opt('from', 'bookings@safarilax.world');
    $body = "New private enquiry\n\nName: $name\nEmail: $email\nDates: $dates\nGuests: $guests\n\nMessage:\n$text\n";
    $headers = ['Content-Type: text/plain; charset=UTF-8', 'From: Safari LAX Website <' . $from . '>', 'Reply-To: ' . $name . ' <' . $email . '>'];
    $mail_ok = wp_mail(lax_to(), 'New private enquiry from ' . $name, $body, $headers);
    if ($saved) update_post_meta($id, 'mail_ok', $mail_ok ? '1' : '0');

    // 3. Optional thank-you to the guest
    if ($mail_ok && lax_opt('autoreply', 1)) {
        $reply = lax_opt('autoreply_text', "Dear {name},\n\nThank you for writing to Safari LAX. A member of our Kenya team will reply to you personally, usually within one working day.\n\nWarm regards,\nThe Safari LAX Kenya Team");
        wp_mail($email, 'Thank you for your enquiry', str_replace('{name}', $name, $reply), ['Content-Type: text/plain; charset=UTF-8', 'From: Safari LAX <' . $from . '>', 'Reply-To: ' . lax_to()]);
    }

    lax_redirect(($saved || $mail_ok) ? 'sent' : 'failed');
}
add_action('admin_post_nopriv_lax_enquiry', 'lax_handle_enquiry');
add_action('admin_post_lax_enquiry', 'lax_handle_enquiry');
