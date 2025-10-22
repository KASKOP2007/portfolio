<?php

use Growww\Resizer;
use Growww\GithubLibs;

/**
 * zoals get_field, maar met Markdown
 * @param  string  $name   naam/id ACF veld
 * @param  int     $id     Post id, of null voor de globale post
 * @param  boolean $format ACF parameter: waarde formatten?
 * @return string          Bedoeld voor tekst velden
 */
function growww_get_field($name, $id = null, $format = true)
{
    $ret = \get_field($name, $id, $format);
    return growww_transform($ret);
}

/**
 * zoals get_sub_field, maar met Markdown
 * @param  string  $name   naam/id ACF veld
 * @param  boolean $format ACF parameter: waarde formatten?
 * @return string          Bedoeld voor tekst velden
 */
function growww_get_sub_field($name, $format = true)
{
    $ret = \get_sub_field($name, $format);
    return growww_transform($ret);
}

/**
 * parse de Markdown in een string naar HTML
 * @param  string $text de string met Markdown syntax
 * @return string       output HTML
 */
function growww_transform($text)
{
    $class = GithubLibs::get_instance()->require('markdown');
    $ret = $class::defaultTransform($text);
    if ($ret) {
        if (substr($ret, 0, 3) === '<p>') $ret = substr($ret, 3, -5);
    }
    return $ret;
}

/**
 * get een resized image
 * @param  string/id  $idOrUrl Attachment id/URL
 * @param  int        $width   De gewenste breedte
 * @param  int        $height  Optioneel de gewenste hoogte
 * @param  boolean    $crop    Afbeelding bijsnijden?
 * @param  boolean    $single  true voor URL returnen, anders wordt array [URL,width,height] returned
 * @param  boolean    $upscale Mag de afbeelding evt vergroot worden?
 * @return string     Afbeelding URL / [URL,width,height] (zie $single)
 */
function growww_image($id_or_url, $width, $height = null, $crop = null, $single = true, $upscale = false)
{

    //Return with no url/id
    if (empty($id_or_url)) return false;
    $resizer = new Resizer($id_or_url);

    try {
        $resizer->process($id_or_url, $width, $height, $crop, $single, $upscale);
    } catch (Exception $e) {
        return $e;
    }

    return $resizer->process($id_or_url, $width, $height, $crop, $single, $upscale);
}

/**
 * Debug a var
 * @param  array    $array with data to debug
 */
function growww_debug_data($data)
{
    ini_set("highlight.comment", "#969896; font-style: italic");
    ini_set("highlight.default", "#FFFFFF");
    ini_set("highlight.html", "#D16568");
    ini_set("highlight.keyword", "#7FA3BC; font-weight: bold");
    ini_set("highlight.string", "#F2C47E");
    $output = highlight_string("<?php\n\n" . var_export($data, true), true);
    echo "<div style=\"background-color: #1C1E21; padding: 1rem\">{$output}</div>";
    die();
}

/**
 * Dump a var
 * @param  array    $array with data to dump
 */
function growww_dump_data($data)
{
    ini_set("highlight.comment", "#969896; font-style: italic");
    ini_set("highlight.default", "#FFFFFF");
    ini_set("highlight.html", "#D16568");
    ini_set("highlight.keyword", "#7FA3BC; font-weight: bold");
    ini_set("highlight.string", "#F2C47E");
    $output = highlight_string("<?php\n\n" . var_export($data, true), true);
    echo "<div style=\"background-color: #1C1E21; padding: 1rem\">{$output}</div>";
}

/**
 * Get the favicon in all img size transformations
 */
function growww_favicon()
{
    $icon144 = get_field('favicon', 'options', false);
    if ($icon144 && ($icon144 = wp_get_attachment_url($icon144))) {
        //Get the sizes and print the favicon
        $ti16 = growww_image($icon144, 16, 16, false);
        $ti32 = growww_image($icon144, 32, 32, false);
        $ti57 = growww_image($icon144, 57, 57, false);
        $ti72 = growww_image($icon144, 72, 72, false);
        $ti114 = growww_image($icon144, 114, 114, false);
        $ti144 = growww_image($icon144, 144, 144, false);
        $ti180 = growww_image($icon144, 180, 180, false);
?>
        <link rel="apple-touch-icon-precomposed" href="<?php echo $ti57; ?>" />
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $ti72; ?>" />
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $ti114; ?>" />
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $ti144; ?>" />
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $ti180; ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $ti32; ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $ti16; ?>">
        <link rel="shortcut icon" type="image/png" href="<?php echo $ti32; ?>">
    <?php
    }
}

/**
 * Get a list of socials
 */
function growww_social($wrapping_html = '<li>%s</li>', $use_ul = true, $extra_ul_class = 'social-media')
{
    //Print ul>li with all socials from extra settings

    if ($use_ul) echo '<ul class="' . $extra_ul_class . '">';
    ?>

    <?php if (get_field('facebook', 'options')): ?>
        <?php echo sprintf($wrapping_html, '<a href="' . get_field('facebook', 'options') . '" target="_blank"><i class="fab fa-facebook-f"></i><span>Facebook</span></a>'); ?>
    <?php endif; ?>
    <?php if (get_field('twitter', 'options')): ?>
        <?php echo sprintf($wrapping_html, '<a href="' . get_field('twitter', 'options') . '" target="_blank"><i class="fa-brands fa-x-twitter"></i><span>X</span></a>'); ?>
    <?php endif; ?>
    <?php if (get_field('instagram', 'options')): ?>
        <?php echo sprintf($wrapping_html, '<a href="' . get_field('instagram', 'options') . '" target="_blank"><i class="fab fa-instagram"></i><span>Instagram</span></a>'); ?>
    <?php endif; ?>
    <?php if (get_field('pinterest', 'options')): ?>
        <?php echo sprintf($wrapping_html, '<a href="' . get_field('pinterest', 'options') . '" target="_blank"><i class="fab fa-pinterest-p"></i><span>Pinterest</span></a>'); ?>
    <?php endif; ?>
    <?php if (get_field('linkedin', 'options')): ?>
        <?php echo sprintf($wrapping_html, '<a href="' . get_field('linkedin', 'options') . '" target="_blank"><i class="fab fa-linkedin-in"></i><span>LinkedIn</span></a>'); ?>
    <?php endif; ?>
    <?php if (get_field('youtube', 'options')): ?>
        <?php echo sprintf($wrapping_html, '<a href="' . get_field('youtube', 'options') . '" target="_blank"><i class="fab fa-youtube"></i><span>Youtube</span></a>'); ?>
    <?php endif; ?>
    <?php if (get_field('whatsapp', 'options')): ?>
        <?php echo sprintf($wrapping_html, '<a href="https://wa.me/' . get_field('whatsapp', 'options') . '" target="_blank"><i class="fab fa-whatsapp"></i><span>Whatsapp</span></a>'); ?>
    <?php endif; ?>
    <?php if (get_field('tiktok', 'options')): ?>
        <?php echo sprintf($wrapping_html, '<a href="' . get_field('tiktok', 'options') . '" target="_blank"><i class="fab fa-tiktok"></i><span>TikTok</span></a>'); ?>
    <?php endif; ?>

<?php if ($use_ul) echo '</ul>';
}

function growww_branding()
{
?>
    <a class="growww-logo" href="https://growww.nl" target="_blank">
        <svg class="svg-logo" viewBox="0 0 140 20" style="
            height: 12px;
            width: 86px;
        ">
            <g id="Laag_1-2">
                <path id="Path_358" data-name="Path 358" d="M76.629,19.778a2.4,2.4,0,0,1-2.284-1.719l-.025-.074L68.979,3.975a4.092,4.092,0,0,1-.319-1.338A2.167,2.167,0,0,1,69.225,1.2,2.49,2.49,0,0,1,71.091.352h.1c.8,0,1.891.233,2.726,2.223l3.168,7.576,2.689-7.883V2.219A2.44,2.44,0,0,1,82.143.34h.135a2.535,2.535,0,0,1,2.48,1.977v.049l2.787,7.969,3.242-8.2A2.7,2.7,0,0,1,93.317.34a2.577,2.577,0,0,1,.909.16,2.744,2.744,0,0,1,.921.565,2.305,2.305,0,0,1,.7,1.744,4.493,4.493,0,0,1-.332,1.314l-5,13.556c-.7,1.879-1.744,2.1-2.48,2.1h0a2.458,2.458,0,0,1-2.444-1.916v-.049L82.314,8.26l-3.156,9.48c-.6,1.805-1.621,2.038-2.431,2.038h-.111Z" transform="translate(15.65 0.077)"></path>
                <path id="Path_359" data-name="Path 359" d="M99.929,19.83a2.4,2.4,0,0,1-2.284-1.719l-.025-.074L92.292,4.027a4.117,4.117,0,0,1-.332-1.338,2.167,2.167,0,0,1,.565-1.437A2.49,2.49,0,0,1,94.391.4h.1c.8,0,1.891.233,2.726,2.223l3.168,7.564,2.689-7.883V2.259A2.44,2.44,0,0,1,105.443.38h.135a2.525,2.525,0,0,1,2.48,1.977v.049l2.787,7.969,3.242-8.2A2.7,2.7,0,0,1,116.617.38a2.577,2.577,0,0,1,.909.16,2.744,2.744,0,0,1,.921.565,2.305,2.305,0,0,1,.7,1.744,4.493,4.493,0,0,1-.332,1.314l-5,13.556c-.688,1.879-1.744,2.1-2.48,2.1h0a2.458,2.458,0,0,1-2.444-1.916v-.049L105.614,8.3l-3.156,9.48c-.6,1.805-1.621,2.038-2.431,2.038h-.111Z" transform="translate(20.96 0.087)"></path>
                <path id="Path_360" data-name="Path 360" d="M18.874,11.739v2.137a4.391,4.391,0,0,1-.749,2.468,8.012,8.012,0,0,1-1.94,1.866A13.71,13.71,0,0,1,14.367,19.3a.368.368,0,0,0-.1.049c-.221.1-.454.2-.688.282a.662.662,0,0,0-.123.037c-.221.086-.454.147-.675.221a.648.648,0,0,0-.147.037l-.663.147a1.356,1.356,0,0,0-.2.037,5.232,5.232,0,0,1-.614.074c-.074,0-.147.025-.233.025-.209.025-.43.025-.663.037h-.172c-.295,0-.577-.012-.86-.037H9.173c-.258-.025-.528-.049-.786-.1A.182.182,0,0,1,8.3,20.1c-.258-.049-.5-.1-.761-.16a.175.175,0,0,1-.074-.025c-.258-.074-.5-.147-.749-.233a.094.094,0,0,1-.061-.025,7.982,7.982,0,0,1-.749-.307.074.074,0,0,1-.049-.012,11.606,11.606,0,0,1-1.51-.86c-.246-.172-.467-.344-.688-.528a10.287,10.287,0,0,1-2.689-3.5,10.136,10.136,0,0,1,.553-9.688,9.628,9.628,0,0,1,4.1-3.7A10.146,10.146,0,0,1,10.143,0a10.011,10.011,0,0,1,5.4,1.559l.049.025.049.025A2.986,2.986,0,0,1,17.3,4.1a2.43,2.43,0,0,1-.749,1.744,2.5,2.5,0,0,1-1.744.761A6.5,6.5,0,0,1,12.7,5.8a5.806,5.806,0,0,0-2.64-.7A4.649,4.649,0,0,0,6.57,6.618a4.836,4.836,0,0,0-1.388,3.5,4.9,4.9,0,0,0,1.449,3.549,4.824,4.824,0,0,0,2.935,1.424h.012c.135,0,.258.012.393.025H10.2a2.577,2.577,0,0,0,.442-.037h.012a3.229,3.229,0,0,0,.688-.123,5.294,5.294,0,0,0,1.523-.675,6.973,6.973,0,0,0,.737-.614v-.012H11.985a2.563,2.563,0,0,1-2.579-2.542,2.5,2.5,0,0,1,.761-1.793,2.585,2.585,0,0,1,1.805-.737H15.73a3.129,3.129,0,0,1,3.131,3.131Z"></path>
                <path id="Path_361" data-name="Path 361" d="M19.041,19.8a2.2,2.2,0,0,1-1.78-.884,3.919,3.919,0,0,1-.675-2.345L16.45,3.594C16.426,1.457,17.42.524,19.778.5l4.4-.049a6.06,6.06,0,0,1,3.512,1.056A6.525,6.525,0,0,1,30.117,4.33a6.909,6.909,0,0,1,.6,2.665,7.017,7.017,0,0,1-1.94,4.74l-.5.528,2.051,3.217a3.227,3.227,0,0,1,.553,1.756,2.305,2.305,0,0,1-.7,1.744,2.463,2.463,0,0,1-1.744.724h-.2A2.187,2.187,0,0,1,26.31,18.6L21.6,12.263l.061,4.949a2.535,2.535,0,0,1-.688,1.842,2.321,2.321,0,0,1-1.658.749h-.258Z" transform="translate(3.75 0.103)"></path>
                <path id="Path_363" data-name="Path 363" d="M48.195,5.636a10.078,10.078,0,0,0-3.721-4.1A9.639,9.639,0,0,0,39.243,0a10.439,10.439,0,0,0-5.6,1.584,9.617,9.617,0,0,0-3.733,4.273,9.433,9.433,0,0,0-.921,4.089,10.732,10.732,0,0,0,1.068,4.727,10.1,10.1,0,0,0,8.988,5.6H39.1A10.153,10.153,0,0,0,48.207,5.636Zm-5.919,8.227a1.475,1.475,0,0,1-2.087,0l-1.167-1.154-1.167,1.154a1.728,1.728,0,0,1-2.444-2.444l1.154-1.167L35.387,9.087a1.475,1.475,0,0,1,0-2.087h0a1.471,1.471,0,0,1,.9-.43l5.292-.491h.307a.5.5,0,0,1,.233.049.381.381,0,0,1,.209.086.547.547,0,0,1,.147.074A.038.038,0,0,1,42.5,6.3a.715.715,0,0,1,.184.135l.135.135s.1.123.135.184a.124.124,0,0,1,.012.037,1.294,1.294,0,0,1,.086.135c.025.074.049.135.074.209a1.3,1.3,0,0,1,.049.233,1.284,1.284,0,0,1,0,.307l-.5,5.292a1.408,1.408,0,0,1-.43.9Z" transform="translate(6.608)"></path>
                <path id="Path_364" data-name="Path 364" d="M53.329,19.778a2.4,2.4,0,0,1-2.284-1.719l-.025-.074L45.679,3.975a4.02,4.02,0,0,1-.319-1.338A2.167,2.167,0,0,1,45.925,1.2,2.49,2.49,0,0,1,47.791.352h.1c.786,0,1.891.233,2.726,2.223l3.156,7.576L56.46,2.268V2.219A2.44,2.44,0,0,1,58.83.34h.135a2.535,2.535,0,0,1,2.48,1.977v.049l2.787,7.969,3.242-8.2A2.7,2.7,0,0,1,70,.34a2.577,2.577,0,0,1,.909.16,2.744,2.744,0,0,1,.921.565,2.305,2.305,0,0,1,.7,1.744A4.493,4.493,0,0,1,72.2,4.122l-5,13.556c-.688,1.879-1.744,2.1-2.48,2.1h0a2.458,2.458,0,0,1-2.444-1.916v-.049L59,8.26l-3.156,9.48c-.6,1.805-1.621,2.038-2.431,2.038H53.3Z" transform="translate(10.339 0.077)"></path>
                <path id="Path_365" data-name="Path 365" d="M76.629,19.778a2.4,2.4,0,0,1-2.284-1.719l-.025-.074L68.979,3.975a4.092,4.092,0,0,1-.319-1.338A2.167,2.167,0,0,1,69.225,1.2,2.49,2.49,0,0,1,71.091.352h.1c.8,0,1.891.233,2.726,2.223l3.168,7.576,2.689-7.883V2.219A2.44,2.44,0,0,1,82.143.34h.135a2.535,2.535,0,0,1,2.48,1.977v.049l2.787,7.969,3.242-8.2A2.7,2.7,0,0,1,93.317.34a2.577,2.577,0,0,1,.909.16,2.744,2.744,0,0,1,.921.565,2.305,2.305,0,0,1,.7,1.744,4.493,4.493,0,0,1-.332,1.314l-5,13.556c-.7,1.879-1.744,2.1-2.48,2.1h0a2.458,2.458,0,0,1-2.444-1.916v-.049L82.314,8.26l-3.156,9.48c-.6,1.805-1.621,2.038-2.431,2.038h-.111Z" transform="translate(15.649 0.077)"></path>
                <path id="Path_366" data-name="Path 366" d="M99.929,19.83a2.4,2.4,0,0,1-2.284-1.719l-.025-.074L92.292,4.027a4.117,4.117,0,0,1-.332-1.338,2.167,2.167,0,0,1,.565-1.437A2.49,2.49,0,0,1,94.391.4h.1c.8,0,1.891.233,2.726,2.223l3.168,7.564,2.689-7.883V2.259A2.44,2.44,0,0,1,105.443.38h.135a2.525,2.525,0,0,1,2.48,1.977v.049l2.787,7.969,3.242-8.2A2.7,2.7,0,0,1,116.617.38a2.577,2.577,0,0,1,.909.16,2.744,2.744,0,0,1,.921.565,2.305,2.305,0,0,1,.7,1.744,4.493,4.493,0,0,1-.332,1.314l-5,13.556c-.688,1.879-1.744,2.1-2.48,2.1h0a2.458,2.458,0,0,1-2.444-1.916v-.049L105.614,8.3l-3.156,9.48c-.6,1.805-1.621,2.038-2.431,2.038h-.111Z" transform="translate(20.96 0.087)"></path>
                <path id="Path_367" data-name="Path 367" d="M18.874,11.739v2.137a4.391,4.391,0,0,1-.749,2.468,8.012,8.012,0,0,1-1.94,1.866A13.71,13.71,0,0,1,14.367,19.3a.368.368,0,0,0-.1.049c-.221.1-.454.2-.688.282a.662.662,0,0,0-.123.037c-.221.086-.454.147-.675.221a.648.648,0,0,0-.147.037l-.663.147a1.356,1.356,0,0,0-.2.037,5.232,5.232,0,0,1-.614.074c-.074,0-.147.025-.233.025-.209.025-.43.025-.663.037h-.172c-.295,0-.577-.012-.86-.037H9.173c-.258-.025-.528-.049-.786-.1A.182.182,0,0,1,8.3,20.1c-.258-.049-.5-.1-.761-.16a.175.175,0,0,1-.074-.025c-.258-.074-.5-.147-.749-.233a.094.094,0,0,1-.061-.025,7.982,7.982,0,0,1-.749-.307.074.074,0,0,1-.049-.012,11.606,11.606,0,0,1-1.51-.86c-.246-.172-.467-.344-.688-.528a10.287,10.287,0,0,1-2.689-3.5,10.136,10.136,0,0,1,.553-9.688,9.628,9.628,0,0,1,4.1-3.7A10.146,10.146,0,0,1,10.143,0a10.011,10.011,0,0,1,5.4,1.559l.049.025.049.025A2.986,2.986,0,0,1,17.3,4.1a2.43,2.43,0,0,1-.749,1.744,2.5,2.5,0,0,1-1.744.761A6.5,6.5,0,0,1,12.7,5.8a5.806,5.806,0,0,0-2.64-.7A4.649,4.649,0,0,0,6.57,6.618a4.836,4.836,0,0,0-1.388,3.5,4.9,4.9,0,0,0,1.449,3.549,4.824,4.824,0,0,0,2.935,1.424h.012c.135,0,.258.012.393.025H10.2a2.577,2.577,0,0,0,.442-.037h.012a3.229,3.229,0,0,0,.688-.123,5.294,5.294,0,0,0,1.523-.675,6.973,6.973,0,0,0,.737-.614v-.012H11.985a2.563,2.563,0,0,1-2.579-2.542,2.5,2.5,0,0,1,.761-1.793,2.585,2.585,0,0,1,1.805-.737H15.73a3.129,3.129,0,0,1,3.131,3.131Z"></path>
                <path id="Path_368" data-name="Path 368" d="M19.041,19.8a2.2,2.2,0,0,1-1.78-.884,3.919,3.919,0,0,1-.675-2.345L16.45,3.594C16.426,1.457,17.42.524,19.778.5l4.4-.049a6.06,6.06,0,0,1,3.512,1.056A6.525,6.525,0,0,1,30.117,4.33a6.909,6.909,0,0,1,.6,2.665,7.017,7.017,0,0,1-1.94,4.74l-.5.528,2.051,3.217a3.227,3.227,0,0,1,.553,1.756,2.305,2.305,0,0,1-.7,1.744,2.463,2.463,0,0,1-1.744.724h-.2A2.187,2.187,0,0,1,26.31,18.6L21.6,12.263l.061,4.949a2.535,2.535,0,0,1-.688,1.842,2.321,2.321,0,0,1-1.658.749h-.258Z" transform="translate(3.75 0.103)"></path>
            </g>
        </svg>
    </a>

<?php add_action('growwwBranding', 'growwwBranding');
}

function growww_footer()
{
    echo get_field('custom_Footer_code', 'option');
    add_action('growwwFooter', 'growwwFooter');
}

function growww_custom_header_code()
{
    echo get_field('custom_header_code', 'option');
}

function growww_custom_footer_code()
{
    echo get_field('custom_footer_code', 'option');
}

function growww_google_code()
{
    echo get_field('google_analytics', 'option');
}
