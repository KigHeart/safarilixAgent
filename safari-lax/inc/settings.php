<?php
if (!defined('ABSPATH')) exit;

function lax_opt($key, $default = '') {
    $o = get_option('lax_settings', []);
    return (isset($o[$key]) && $o[$key] !== '') ? $o[$key] : $default;
}
function lax_to() { return lax_opt('to', 'bookings@safarilax.world'); }

add_action('admin_menu', function () {
    add_options_page('Safari LAX', 'Safari LAX', 'manage_options', 'lax-settings', 'lax_settings_page');
});
add_action('admin_init', function () {
    register_setting('lax_settings_group', 'lax_settings', ['sanitize_callback' => function ($in) {
        return [
            'to'             => sanitize_email($in['to'] ?? ''),
            'from'           => sanitize_email($in['from'] ?? ''),
            'autoreply'      => empty($in['autoreply']) ? 0 : 1,
            'autoreply_text' => sanitize_textarea_field($in['autoreply_text'] ?? ''),
            'phone'          => sanitize_text_field($in['phone'] ?? ''),
            'instagram'      => esc_url_raw($in['instagram'] ?? ''),
            'privacy'        => esc_url_raw($in['privacy'] ?? ''),
        ];
    }]);
});

function lax_settings_page() {
    if (!current_user_can('manage_options')) return;
    $default_reply = "Dear {name},\n\nThank you for writing to Safari LAX. A member of our Kenya team will reply to you personally, usually within one working day.\n\nWarm regards,\nThe Safari LAX Kenya Team";
    ?>
    <div class="wrap"><h1>Safari LAX settings</h1>
    <form method="post" action="options.php">
    <?php settings_fields('lax_settings_group'); ?>
    <table class="form-table" role="presentation">
        <tr><th>Send enquiries to</th><td><input type="email" class="regular-text" name="lax_settings[to]" value="<?php echo esc_attr(lax_to()); ?>"></td></tr>
        <tr><th>Send emails from</th><td><input type="email" class="regular-text" name="lax_settings[from]" value="<?php echo esc_attr(lax_opt('from', 'bookings@safarilax.world')); ?>"><p class="description">Must be a real mailbox on your domain, matching your SMTP plugin.</p></td></tr>
        <tr><th>Reply to the guest automatically</th><td><label><input type="checkbox" name="lax_settings[autoreply]" value="1" <?php checked(lax_opt('autoreply', 1)); ?>> Send a thank-you email</label></td></tr>
        <tr><th>Thank-you message</th><td><textarea class="large-text" rows="6" name="lax_settings[autoreply_text]"><?php echo esc_textarea(lax_opt('autoreply_text', $default_reply)); ?></textarea><p class="description">{name} becomes the guest's name.</p></td></tr>
        <tr><th>Phone or WhatsApp (footer)</th><td><input type="text" class="regular-text" name="lax_settings[phone]" value="<?php echo esc_attr(lax_opt('phone')); ?>"></td></tr>
        <tr><th>Instagram link (footer)</th><td><input type="url" class="regular-text" name="lax_settings[instagram]" value="<?php echo esc_attr(lax_opt('instagram')); ?>"></td></tr>
        <tr><th>Privacy policy link</th><td><input type="url" class="regular-text" name="lax_settings[privacy]" value="<?php echo esc_attr(lax_opt('privacy')); ?>"><p class="description">Shown beside the consent tick-box on the enquiry form.</p></td></tr>
    </table>
    <?php submit_button(); ?>
    </form>
    <h2>Check email</h2>
    <p><a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=lax_test_mail'), 'lax_test')); ?>">Send a test email to the enquiry address</a></p>
    </div>
    <?php
}
