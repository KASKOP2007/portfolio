<?php if(get_field('show')): ?>
    <section class="my-lg-12 my-md-7 my-5">
        <div class="container">
            <?php if(get_field('title')): ?>
                <h2 class="display-2 d-block mb-1"><?php echo get_field('title'); ?></h2>
            <?php endif; ?>
            <div class="row g-lg-3 g-2">
                <div class="col-lg-6">
                    <div class="content-container">
                        <?php echo get_field('wysiwyg'); ?>
                    </div>
                    <?php growww_add_btns(get_field('btns')['btns'], 'mt-lg-3 mt-2'); ?>
                </div>
                <div class="col-lg-6">
                    <div class="content-container">
                        <?php echo get_field('wysiwyg_2'); ?>
                    </div>
                    <?php growww_add_btns(get_field('btns_2')['btns'], 'mt-lg-3 mt-2'); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>