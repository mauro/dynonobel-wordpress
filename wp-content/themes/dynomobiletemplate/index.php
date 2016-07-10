<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<header id="post-<?php the_ID(); ?>">
			<div class="hero">
				<div class="container">
					<h1>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
					</h1>
				</div>
			</div>
		</header>
		<article>
			<div class="container">
				<?php the_content(); ?>
			</div>
		</article>

	<?php endwhile; ?>
	<?php else : ?>
	
		<header id="post-not-found">
			<div class="hero">
				<div class="container">
					<h1>
						Nothing Found
					</h1>
				</div>
			</div>
		</header>
		
	
	<?php endif; ?>

<?php get_footer(); ?>
