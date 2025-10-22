<?php
    if(empty($args['link'])) return;
    
    $link  = $args['link'];
    $url = $link['url'];
    $text  = $link['title'];
    $target =  !empty($link['target']) ? $link['target'] : '_self';
    $color = !empty($args['color']) ? '-' . $args['color'] : '-primary';
    $class = !empty($args['class']) ? ' ' . $args['class'] : '';
    $fancybox = preg_match('/youtu\.be/i', $url) || preg_match('/youtube\.com\/watch/i', $url) || preg_match('/vimeo\.com/i', $url);
?>
<a href="<?php echo $url; ?>" class="btn btn<?php echo $color . $class; ?>"<?php echo $fancybox ? ' data-fancybox' : ''; ?> target="<?php echo esc_attr( $target ); ?>">
    <?php echo $text; ?>
    <i class="fa-solid fa-chevron-right"></i>
</a>