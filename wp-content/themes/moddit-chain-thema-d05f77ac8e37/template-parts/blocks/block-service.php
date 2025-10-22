<div class="block-service d-flex flex-column h-100  rounded-4 p-lg-5 p-md-3 p-2">
    <span class=" pb-3"><?php echo get_sub_field('icon'); ?></span>
    <h2 class="py-3"><?php echo get_sub_field('title');?></h2>
    <p class="d-block mb-md-4 mb-3"><?php echo get_sub_field('content') ?></p>
    <a class="btn btn-primary rounded-pill mt-auto " href="<?php echo get_sub_field('button')['url']?>" target="<?php echo get_sub_field('button')['target'] ?>"><?php echo get_sub_field('button')['title'] ?> </a>
</div>