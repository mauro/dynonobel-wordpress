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
	
		<div class="hero">
			<h1>No Content Found</h1>
		</div>
		<div class="container">
			Sorry!
		</div>
	
	<?php endif; ?>

<?php get_footer(); ?>
