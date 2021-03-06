const { argv } = require('yargs');
const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const { default: ImageminPlugin } = require('imagemin-webpack-plugin');
const imageminMozjpeg = require('imagemin-mozjpeg');
const WebpackBar = require('webpackbar');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');

const rootPath = process.cwd();
const isProduction = !!((argv.env && argv.env.production) || argv.p);
const userConfig = require('./config.json');

const config = { ...userConfig, ...{
  paths: {
    root: rootPath,
    src: path.join(rootPath, 'assets'),
    dist: path.join(rootPath, 'dist'),
  },
  enabled: {
    sourceMaps: !isProduction,
    optimization : isProduction,
  },
}};

const filename = '[name]';

if (undefined === process.env.NODE_ENV) {
  process.env.NODE_ENV = isProduction ? 'production' : 'development';
}

module.exports = {
  context: config.paths.src,
  entry : config.entry,
  devtool: config.enabled.sourceMaps ? '#source-map' : undefined,
  mode : isProduction ? 'production' : 'development',
  output: {
    filename: `scripts/${filename}.js`,
    path: config.paths.dist,
    publicPath: path.join(config.publicPath, path.basename(config.paths.dist)),
  },
  stats: {
    children: false,
  },
  resolve: {
    modules: [
      config.paths.src,
      'node_modules',
    ],
    enforceExtension: false,
  },
  externals: {
    jquery: 'jQuery',
  },
  module:
  {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
      {
        test: /\.css$/,
        include: config.paths.src,
        use: [
          { loader : MiniCssExtractPlugin.loader },
          { loader: 'css-loader', options: { sourceMap: config.enabled.sourceMaps } },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: config.enabled.sourceMaps,
              plugins: function() {
                return [ require('autoprefixer') ];
              },
            },
          },
        ],
      },
      {
        test: /\.scss$/,
        include: config.paths.src,
        use: [
          { loader : MiniCssExtractPlugin.loader },
          { loader: 'css-loader', options: { sourceMap: config.enabled.sourceMaps } },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: config.enabled.sourceMaps,
              plugins: function() {
                return [ require('autoprefixer') ];
              },
            },
          },
          { loader: 'resolve-url-loader', options: { sourceMap: config.enabled.sourceMaps } },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: config.enabled.sourceMaps,
            },
          },
        ],
      },
      {
        test: /\.(png|svg|jpe?g|gif|woff|woff2|eot|ttf|otf)$/,
        include: config.paths.src,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: `[path]${filename}.[ext]`,
              limit: 4096,
            },
          },
        ],
      },
      {
        test: /\.(png|svg|jpe?g|gif|woff|woff2|eot|ttf|otf)$/,
        include: /node_modules/,
        use: [
          {
            loader: 'file-loader',
            options: {
              limit: 4096,
              outputPath: '/vendor/',
              name: `${filename}.[ext]`,
            },
          },
        ],
      },
    ],
  },
  plugins: [
    // Remove all files inside output.path director
    new CleanWebpackPlugin({
      // Prevent unchanged files from removal, while copy-webpack-plugin only emit files when they are changed
      cleanStaleWebpackAssets: false,
    }),
    // Extract CSS into separate files
    new MiniCssExtractPlugin({
      filename: `styles/${filename}.css`,
    }),
    // Find js files from chunks of css only entries and remove the js file from the compilation.
    new FixStyleOnlyEntriesPlugin(),
    // Automatically load modules
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
      Popper: 'popper.js/dist/umd/popper.js',
    }),
    // Copy
    new CopyWebpackPlugin([
      'images/**/*',
      'fonts/**/*',
    ]),
    // Elegant ProgressBar and Profiler
    new WebpackBar(),
  ],
  optimization: {
    minimize: config.enabled.optimization,
    minimizer : [
      new OptimizeCssAssetsPlugin({
        cssProcessorPluginOptions: {
          preset: ['default', { discardComments: { removeAll: true } }],
        },
      }),
      new ImageminPlugin({
        test: /\.(png|svg|je?pg|gif)$/,
        optipng: { optimizationLevel: 2 },
        gifsicle: { optimizationLevel: 3 },
        pngquant: { quality: '65-90', speed: 4 },
        svgo: {
          plugins: [
            { removeUnknownsAndDefaults: false },
            { cleanupIDs: false },
            { removeViewBox: false },
          ],
        },
        plugins: [imageminMozjpeg({ quality: 75 })],
      }),
      new UglifyJsPlugin({
        uglifyOptions: {
          warnings: true,
          output: { comments: false },
          compress: { drop_console: true },
        },
      }),
    ],
  },
};
