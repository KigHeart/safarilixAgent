<article class="j">
<div class="j-img"><a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php if (has_post_thumbnail()) the_post_thumbnail('lax-card'); ?></a></div>
<div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
<?php if ($w = get_post_meta(get_the_ID(), 'lax_where', true)) echo '<div class="where">' . esc_html($w) . '</div>'; ?>
<p class="sub"><?php echo esc_html(get_the_excerpt()); ?></p></div>
</article>
