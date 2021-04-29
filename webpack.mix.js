const mix = require('laravel-mix');
const SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

 mix.webpackConfig({
    plugins: [
        new SVGSpritemapPlugin('resources/svgs/*.svg', {
            output: {
                filename: 'img/icons/sprite.svg'
            }
        })
    ]
});

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .css('resources/css/app.css', 'public/css/web.css')
    .css('resources/css/custom-switch.css', 'public/css/custom-switch.css');
