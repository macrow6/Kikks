<?php
get_header();
	while(have_posts()) {
		the_post(); ?>
<div class="container">
	<div class="row">
		<div class="col">


			<h2><?php the_title(); ?></h2>
			<p><?php the_content(); ?></p>
			<hr>
		</div>
	</div>
</div>




	<?php }
get_footer();
 ?>
