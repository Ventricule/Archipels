<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	
		<p class="detail">
			<a href="#<?php echo get_the_ID(); ?>" class="non-souligne" title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</p>
		<!-- /post title -->
	
<?php endwhile; ?>

<?php else: ?>

		<p><?php _e( '—', 'html5blank' ); ?></p>

<?php endif; ?>