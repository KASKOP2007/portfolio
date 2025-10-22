<div class="block-project d-flex flex-column align-items-center h-100">
    <div class="block-project__img">
        <img class=" img-abs-center rounded-3" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" />
    </div>
    <div class="block-project__content d-flex flex-column align-items-center justify-content-center px-3">
        <span class="display-5"><?php echo get_the_title() ?></span>
        <p class=""><?php echo get_the_content() ?> </p>
        <a class="block-project__icon stretched-link" href="<?php echo get_permalink() ?>"><i class="fa-solid fa-up-right-from-square"></i></a>
    </div>
    
</div>


