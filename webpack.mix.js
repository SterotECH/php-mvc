const mix = require('laravel-mix');

mix.disableSuccessNotifications();
mix.setPublicPath('public/');

mix.js('resources/js/bootstrap.js', 'js').version();
mix.js('resources/js/main.js', 'js').version();
mix.js('resources/js/app.js', 'js').version();
mix
    .postCss('resources/css/main.css', 'css', [
        require('postcss-import'),
        require('tailwindcss'),
    ])
    .version();
