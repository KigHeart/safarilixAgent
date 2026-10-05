<?php get_header(); while (have_posts()) : the_post(); ?>
<div class="wrap"><h1 class="page-title"><?php the_title(); ?></h1>
<?php if ($w = get_post_meta(get_the_ID(), 'lax_where', true)) echo '<p class="where" style="color:var(--gold);font:italic 24px var(--serif)">' . esc_html($w) . '</p>'; ?></div>
<?php if (has_post_thumbnail()) echo '<div class="wrap" style="margin-top:24px">' . get_the_post_thumbnail(null, 'full') . '</div>'; ?>
<section class="section" style="padding-top:40px"><div class="wrap entry"><?php the_content(); ?>
<p style="margin-top:40px"><a class="btn" href="<?php echo esc_url(home_url('/contact/')); ?>">Enquire about this journey</a></p></div></section>
<?php endwhile; get_footer();
