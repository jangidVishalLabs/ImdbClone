<?php
	$roles = get_post_meta( get_the_ID(), '_person_roles', true );
	$dob   = get_post_meta( get_the_ID(), '_person_dob', true );
?>
<header class="person-header">
		<h1><?php the_title(); ?></h1>

		<?php if ( $roles ) : ?>
			<p class="person-roles"><?php echo esc_html( implode(', ', (array) $roles ) ); ?></p>

		<?php endif; ?>

		<?php if ( $dob ) : ?>
			<p class="person-dob">Born: <?php echo esc_html( $dob ); ?></p>
		<?php endif; ?>
	</header>