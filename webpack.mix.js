const { mix } = require('laravel-mix');

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

mix
    .js('resources/assets/js/login.js', 'public/js')
    .js('resources/assets/js/main.js', 'public/js')
    .js('resources/assets/js/vue.js', 'public/js')

    // .copy('node_modules/jquery-contextmenu/dist/jquery.contextMenu.css', 'public/css')
    // .copy('node_modules/jquery-contextmenu/dist/font', 'public/css/font')

    .sass('resources/assets/sass/login.scss', 'public/css')
    .sass('resources/assets/sass/main.scss', 'public/css')

    .sass('resources/assets/sass/bootstrap.scss', 'public/css')
    .version();

// mix.webpackConfig({
//     module: {
//         rules: [
//             {
//                 test: /datatables\.net.*/,
//                 loader: 'imports-loader?define=>false'
//             }
//         ]
//     }
// });
