<div class="row">
  <div class="col-6">
<h2 class="secTitle">Upcoming Events:</h2>

<?php
  $today = date('Ymd');
  $homePageEvents = new WP_Query(array(
    'posts_per_page' => 4,
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


            <div class="center">
              <a class="btn btn-success" href="<?php echo get_post_type_archive_link('event'); ?>">View All Events</a>
              <a class="btn btn-outline-info" href="#">Book an Event</a>
            </div>
  </div>
