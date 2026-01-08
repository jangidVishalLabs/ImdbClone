<?php
get_header();
the_post();

$person_id = get_the_ID();
$bio       = get_post_meta( $person_id, '_person_bio', true );
?>

<div class="imdb-person-page">

	<!-- HEADER -->
	<?php require __DIR__ . '/parts/person-header.php'; ?>

	<!-- BODY -->
	<div class="movie-body">
		<?php require __DIR__ . '/parts/person-body.php'; ?>
				<div class="movie-content">

			<div class="movie-description">
				<?php echo esc_html( $bio ); ?>
			</div>
			</div>
		<?php require __DIR__ . '/parts/person-movie-card.php'; ?>


</div>

<?php get_footer(); ?>
