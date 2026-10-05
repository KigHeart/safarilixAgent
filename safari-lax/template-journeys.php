<?php /* Template Name: Journeys */ get_header(); ?>
<div class="wrap"><h1 class="page-title"><?php the_title(); ?></h1>
<div class="entry sub"><?php while (have_posts()) : the_post(); the_content(); endwhile; ?></div></div>
<section class="section panel" style="margin-top:40px"><div class="wrap"><div class="jlist" style="margin-top:0">
<?php $q = new WP_Query(['post_type' => 'journey', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC']);
while ($q->have_posts()) : $q->the_post(); get_template_part('template-parts/journey-row'); endwhile; wp_reset_postdata(); ?>
</div></div></section>
<?php get_footer();
