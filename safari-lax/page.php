<?php get_header(); while (have_posts()) : the_post(); ?>
<div class="wrap"><h1 class="page-title"><?php the_title(); ?></h1></div>
<section class="section" style="padding-top:20px"><div class="wrap entry"><?php the_content(); ?></div></section>
<?php endwhile; get_footer();
