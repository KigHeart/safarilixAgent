<?php get_header();
$img = get_theme_mod('lax_hero_img') ? wp_get_attachment_image_url(get_theme_mod('lax_hero_img'), 'full') : '';
?>
<section class="hero"<?php if ($img) echo ' style="background-image:url(' . esc_url($img) . ')"'; ?>>
<div class="wrap">
<h1><?php echo esc_html(get_theme_mod('lax_h1a', 'Private Africa,')); ?><em><?php echo esc_html(get_theme_mod('lax_h1b', 'Unhurried.')); ?></em></h1>
<p><?php echo esc_html(get_theme_mod('lax_sub', 'Tailor-made journeys through Kenya and across the continent, shaped around you alone.')); ?></p>
<a class="btn" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php echo esc_html(get_theme_mod('lax_cta', 'Begin a private enquiry')); ?></a>
</div></section>
<section class="section"><div class="wrap entry">
<?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
</div></section>
<?php $q = new WP_Query(['post_type' => 'journey', 'posts_per_page' => 3, 'orderby' => 'menu_order title', 'order' => 'ASC']);
if ($q->have_posts()) : ?>
<section class="section panel"><div class="wrap">
<h2 style="font-size:clamp(40px,6vw,76px)">Journeys</h2>
<div class="jlist"><?php while ($q->have_posts()) : $q->the_post(); get_template_part('template-parts/journey-row'); endwhile; wp_reset_postdata(); ?></div>
<p style="margin-top:36px"><a class="btn" href="<?php echo esc_url(home_url('/journeys/')); ?>">All journeys</a></p>
</div></section>
<?php endif; ?>
<section class="section enq"><div class="wrap" style="text-align:left">
<h2 style="font-size:clamp(36px,5vw,64px);max-width:16em">Tell us where your imagination takes you.</h2>
<p style="margin-top:28px"><a class="btn" href="<?php echo esc_url(home_url('/contact/')); ?>">Begin a private enquiry</a></p>
</div></section>
<?php get_footer();
