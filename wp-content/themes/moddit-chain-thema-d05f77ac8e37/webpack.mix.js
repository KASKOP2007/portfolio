let mix = require('laravel-mix');
let path = require('path');

require('laravel-mix-purgecss');

mix.webpackConfig(webpack => {
   return {
       plugins: [
           new webpack.ProvidePlugin({
               $: 'jquery',
               jQuery: 'jquery',
               'window.jQuery': 'jquery',
           })
       ]
   };
});

mix
    .extract()
    .js('assets/js/app.js', 'js')
    .sass('assets/scss/app.scss', 'css')
    .options({
        processCssUrls: false
    })
    .copy('assets/images/ajax-loader.gif', 'dist/images/ajax-loader.gif')
    // .copyDirectory('assets/images', 'dist/images')
    // .copyDirectory('assets/fonts', 'dist/fonts')
    .setPublicPath('dist')
    // .purgeCss({
    //    // enabled: true,
    //    content: [path.join(__dirname, './**/*.php')],
    //    css: [path.join(__dirname, './dist/css/app.css')],
    //    safelist: [
    //       /^btn-/,
    //       /^href/,
    //       /^is-/,
    //       /^fancybox-/,
    //       /^gform_/,
    //       /^ginput_/,
    //       /^lock/,
    //       /^block/,
    //       /^wpadminbar/,
    //       /^slick-/,
    //    ]
    // });