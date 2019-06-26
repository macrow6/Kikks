<div class="col-6">
  <div class="socialBox center">
    <p class="FBconnect">Connect with Us</p>
    <i class="fa fa-facebook-square ml-2"></i>
  </div>
    <h2 class="secTitle">Featured Characters:</h2>
  <div class="card-group shadowMain">



    <?php
      $homePageCharacters = new WP_Query(array(
        'posts_per_page' => 3,
        'post_type' => 'character',
        'orderby' => 'rand',
        'order' => 'ASC'
      ));


      while($homePageCharacters->have_posts()) {
        $homePageCharacters->the_post();

        ?>





    <div class="card">
      <img src="<?php the_field('character_image') ?>" class="card-img-top kikksCardImg" alt="...">
      <div class="card-body">
        <h5 class="card-title"><?php  the_title(); ?></h5>
      </div>
    </div>
<?php } ?>
</div>
<div class="center bookNow">
  <a class="btn btn-success" href="">Book a Character Now</a>
  <a class="btn btn-outline-info" href="<?php echo get_post_type_archive_link('character'); ?>">View All Charaters</a>
</div>


  <!----------------------------------->
