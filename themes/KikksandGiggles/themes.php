<h2 class="secTitle">Featured Themes:</h2>
<div class="card-group shadowMain">


  <?php
    $homePageThemes = new WP_Query(array(
      'posts_per_page' => 3,
      'post_type' => 'theme',
      'orderby' => 'rand',
      'order' => 'ASC'
    ));


    while($homePageThemes->have_posts()) {
      $homePageThemes->the_post(); ?>




  <div class="card">
    <img src="<?php the_field('theme_image') ?>" class="card-img-top kikksCardImg" alt="...">
    <div class="card-body">
      <h5 class="card-title"><?php the_title(); ?></h5>
    </div>
  </div>


<?php } ?>
</div>
 <div class="center bookNow">
   <a class="btn btn-success" href="#">Book a Theme Now</a>
   <a class="btn btn-outline-info" href="<?php echo get_post_type_archive_link('theme'); ?>">View All Themes</a>
 </div>
</div>
</div><!--row-->
