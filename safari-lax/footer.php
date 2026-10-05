</main>
<footer class="site-footer"><div class="wrap">
<span>SAFARI LAX &middot; Private Africa, Unhurried.</span>
<span>
<a href="mailto:<?php echo esc_attr(get_theme_mod('lax_foot', 'info@safarilax.world')); ?>"><?php echo esc_html(get_theme_mod('lax_foot', 'info@safarilax.world')); ?></a>
<?php if ($p = lax_opt('phone')) echo ' &middot; ' . esc_html($p); ?>
<?php if ($pv = get_privacy_policy_url()) echo ' &middot; <a href="' . esc_url($pv) . '">Privacy</a>'; ?>
<?php if ($i = lax_opt('instagram')) echo ' &middot; <a href="' . esc_url($i) . '" rel="noopener">Instagram</a>'; ?>
</span>
</div></footer>
<?php wp_footer(); ?>
</body></html>
