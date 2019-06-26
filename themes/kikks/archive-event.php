<?php get_header();



?>


<div class="bgCharImg">
    <div class="container">
        <div class="row">
          <div class="col-12 center">
            <h1 class="display-4 pageH1">All Upcoming Events</h1>
        </div>
      </div>
    </div>
</div>
<div class="container">






<div class="row justify-content-md-center mt-5">
  <div class="col-6 center">


<?php
  $today = date('Ymd');
  $homePageEvents = new WP_Query(array(
    'posts_per_page' => -1,
    'post_type' => 'event',
    'meta_key' => 'event_date',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => array(
      array(
        'key' => 'event_date',
        'compare' => '>=',
        'value' => $today,
        'type' => 'numeric'
      )
    )
  ));


  while($homePageEvents->have_posts()) {
    $homePageEvents->the_post(); ?>


    <div class="mainWrapBox2 shadowMain">
      <div class="datebox2">
        <p><?php
            $date = get_field('event_date', false, false);
            $eventDate = new DateTime($date);
            echo $eventDate -> format('M');
         ?> <br><?php echo $eventDate -> format('d');  ?></p>
      </div>
      <div class="titlebox2">
        <h2><?php the_title(); ?></h2>
        <p><?php the_field("event_address_line_1"); ?><br><?php the_field("event_address_line_2"); ?></p>
      </div>
        <div class="imgbox2">
          <img id="eventIMG" src="<?php the_field("event_location_image") ?>" alt="">
        </div>
      </div>
<!-- Testing Content-->


<!--Testing Content-->

  <?php }
 ?>



  </div>
</div>

<?php get_footer(); ?>
