<?php if(!is_home() && !is_front_page()): ?>
<section class="layout-breadcrumbs py-1 py-xl-2">
    <div class="container">
        <div class="layout-breadcrumbs__link">
            <?php growww_breadcrumb(); ?>
        </div>
    </div>
</section>
<?php endif; ?>