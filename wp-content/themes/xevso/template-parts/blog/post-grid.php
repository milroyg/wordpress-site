<div class="row all-grid-posts-wrapper all-posts-wrapper masonrys">
<?php
if ( have_posts() ) :
    if ( is_home() && ! is_front_page() ) :
        ?>
        <header>
            <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
        </header>
        <?php
    endif;
    /* Start the Loop */
    while ( have_posts() ) :
        the_post();
        /*
            * Include the Post-Type-specific template for the content.
            * If you want to override this in a child theme, then include a file
            * called content-___.php (where ___ is the Post Type name) and that will be used instead.
            */
        get_template_part( 'template-parts/blog/grid-post-item' );
    endwhile;
else :
    get_template_part( 'template-parts/content', 'none' );
endif;
?>
</div>
<?php $xevso_pagination = xevso_options('xevso_show_pagination', true );
    if($xevso_pagination == true ){
        xevso_pagination();
    };?>