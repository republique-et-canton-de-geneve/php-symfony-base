const Encore = require("@symfony/webpack-encore");
const { CKEditorTranslationsPlugin } = require("@ckeditor/ckeditor5-dev-translations");
const { styles } = require("@ckeditor/ckeditor5-dev-utils");


// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
  // directory where compiled assets will be stored
  .setOutputPath("public/build/")
  // public path used by the web server to access the output path
  .setPublicPath("build")
  // only needed for CDN's or subdirectory deploy
  //.setManifestKeyPrefix('build/')

  /*
   * ENTRY CONFIG
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
   */
  .addEntry("app", "./assets/app.js")

  .addEntry("ckeditor", "./assets/ckeditor.js")

  .addStyleEntry("error", "./assets/error.scss")

  // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
  .splitEntryChunks()

  // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
  .enableStimulusBridge("./assets/controllers.json")

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .enableSingleRuntimeChunk()

  /*
   * FEATURE CONFIG
   *
   * Enable & configure other features below. For a full
   * list of features, see:
   * https://symfony.com/doc/current/frontend.html#adding-more-features
   */
  .cleanupOutputBeforeBuild((options) => {
    options.keep = /build/;
  })

  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())

  // configure Babel
  .configureBabel((config) => {
    config.plugins.push("@babel/plugin-proposal-class-properties");
  })

  // enables and configure @babel/preset-env polyfills
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = "usage";
    config.corejs = "3.23";
  })

  // enables Sass/SCSS support
  .enableSassLoader((options) => {
    // If set to true, Sass won’t print warnings that are caused by dependencies (like bootstrap):
    // https://sass-lang.com/documentation/js-api/interfaces/options/#quietDeps
    options.sassOptions.quietDeps = true;
    options.sassOptions.silenceDeprecations = ["import"];
  })

  // uncomment if you use TypeScript
  .enableTypeScriptLoader()

  // uncomment if you use React
  //.enableReactPreset()

  // uncomment to get integrity="..." attributes on your script & link tags
  // requires WebpackEncoreBundle 1.4 or higher
  //.enableIntegrityHashes(Encore.isProduction())

  // uncomment if you're having problems with a jQuery plugin
  //.autoProvidejQuery()
  .addPlugin(new CKEditorTranslationsPlugin({
    // See https://ckeditor.com/docs/ckeditor5/latest/features/ui-language.html
    language: "fr",
    addMainLanguageTranslationsToAllAssets: true
  }))

  //    Use raw-loader for CKEditor&nbsp;5 SVG files.
  .addRule({
    test: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
    loader: "raw-loader"
  })

  // Configure other image loaders to exclude CKEditor&nbsp;5 SVG files.
  .configureLoaderRule("images", loader => {
    loader.exclude = /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/;
  })

  // Configure PostCSS loader.
  .addLoader({
    test: /ckeditor5-[^/\\]+[/\\]theme[/\\].+\.css$/,
    loader: "postcss-loader",
    options: {
      postcssOptions: styles.getPostCssConfig({
        themeImporter: {
          themePath: require.resolve("@ckeditor/ckeditor5-theme-lark")
        },
        minify: true
      })
    }
  })

;

module.exports = Encore.getWebpackConfig();
