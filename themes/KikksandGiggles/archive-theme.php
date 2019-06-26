<?php get_header();



?>


<div class="bgCharImg">
    <div class="container">
        <div class="row">
          <div class="col-12 center">
            <h1 class="display-4 pageH1">Themes</h1>
        </div>
      </div>
    </div>
</div>
<div class="container">
  <div class="row">
    <div class="col-8">
      <p class="pageP pl-3">Choose from over <?php myThemeCount(); ?> unique themes that will cater to any age group, era, or popular cartoon. Scroll through or search to find the theme you are looking for.</p>
    </div>
    <div class="col-4 center">
        <form class="form-inline mt-5">

        <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>

      </form>
  </div>
</div>





 <div class="album py-5 bg-light">
   <div class="container">

     <div class="row">

       <?php
         $archivePageCharacters = new WP_Query(array(
           'posts_per_page' => -1,
           'post_type' => 'theme',
           'orderby' => 'title',
           'order' => 'ASC'
         ));


         while($archivePageCharacters->have_posts()) {
           $archivePageCharacters->the_post(); ?>


       <div class="col-md-4">
         <div class="card mb-4 shadowMain">
           <img src="<?php the_field('theme_image'); ?>" class="card-img-top kikksCardImgArchive" alt="...">
           <div class="card-body">
             <h5 class="card-title"><?php the_title(); ?></h5>
             <div class="d-flex justify-content-between align-items-center">
             </div>
           </div>
         </div>
       </div>
    <?php } ?>
     </div><!--row-->
   </div>
 </div>



<?php get_footer(); ?>
