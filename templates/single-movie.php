<?php
get_header(); ?>
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
<div class="movie-page">

	<!-- HEADER -->
		<?php include __DIR__ . '/parts/movie-header.php'; ?>
	<!-- BODY -->
		<?php include __DIR__ . '/parts/movie-body.php'; ?>


		<!-- CONTENT -->
		<div class="movie-content">

			<div class="movie-description">
				<?php the_content(); ?>
			</div>
	
		<?php include __DIR__ . '/parts/movie-info.php'; ?>
	</div>
	

		<?php
	endwhile;
endif;

