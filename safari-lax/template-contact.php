<?php /* Template Name: Contact */ get_header(); ?>
<section class="section enq"><div class="wrap cgrid">
<div><h1 style="font-size:clamp(40px,6vw,76px)"><?php the_title(); ?></h1>
<div class="entry sub" style="margin-top:20px;color:#B9AE9E"><?php while (have_posts()) : the_post(); the_content(); endwhile; ?></div>
<p style="margin-top:28px"><a href="mailto:<?php echo esc_attr(lax_to()); ?>"><?php echo esc_html(lax_to()); ?></a></p>
<?php if ($p = lax_opt('phone')) echo '<p>' . esc_html($p) . '</p>'; ?></div>
<div><?php echo do_shortcode('[lax_enquiry]'); ?></div>
</div></section>
<?php get_footer();
