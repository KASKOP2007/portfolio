window.$ = window.jQuery = require('jquery');
import 'slick-slider';
import '@fancyapps/fancybox';

// Media Query Breakpoints
const mq = {
    "sm":   576,
    "md":   768,
    "lg":   992,
    "xl":   1200,
    "xxl":  1400
};

/**
* Header Toggle
*/
$(document).on('click', '.js-header-toggle', function(e) {
    e.preventDefault();
    $(this).toggleClass('is-active');
    $('html').toggleClass('lock');
});

/**
* Menu Replacement
*/
const replaceMenu = function () {
    if ($(window).width() < mq.xl) {
        $('.js-panel-inner').append($('.js-header-menu'));
    } else {
        $('.js-header-right').prepend($('.js-header-menu'));
        $('.panel').removeClass('panel-active');
        $('.js-header-toggle').removeClass('is-active');
        $('html').removeClass('lock');
    }
};
replaceMenu();
$(window).on('resize', replaceMenu);

// Check of het element met class .text bestaat
if (document.querySelector('.text')) {
    var typed = new Typed(".text", {
        strings: ["Frontend Developer", "Backend Developer", "Web Developer"],
        typeSpeed: 100,
        backSpeed: 100,
        backDelay: 1000,
        loop: true
    });
}


$(".js-images-slider").slick({
    centerMode: true,
    centerPadding: '60px',
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    responsive: [
        {
            breakpoint: mq.lg,
            settings: {
                slidesToShow: 2
            }
        },
        {
            breakpoint: mq.md,
            settings: {
                slidesToShow: 1
            }
        }
    ]
});

