let mix = require('laravel-mix');

// .version() writes a per-build content hash into public/mix-manifest.json;
// blade templates resolve the versioned URL via the mix() helper. A stub
// manifest is committed so the site still renders before a build has run.
mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .version();
