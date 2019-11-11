const path = require('path');
const { argv } = require('yargs');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const { default: ImageminPlugin } = require('imagemin-webpack-plugin');
const imageminMozjpeg = require('imagemin-mozjpeg');
const WebpackBar = require('webpackbar');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');

const isProduction = !!((argv.env && argv.env.production) || argv.p);
const rootPath = process.cwd();

if (undefined === process.env.NODE_ENV) {
  process.env.NODE_ENV = isProduction ? 'production' : 'development';
}

const config = {
  paths: {
    root: rootPath,
    assets: path.join(rootPath, 'assets'),
    dist: path.join(rootPath, 'build'),
  },
  enabled: {
    sourceMaps: !isProduction,
    cacheBusting: isProduction,
    optimization : isProduction,
  },
  cacheBusting: '[name]_[hash]',
};

const filename = config.enabled.cacheBusting ? config.cacheBusting : '[name]';
const userConfig = require(`${config.paths.assets}/config.json`);

module.exports = {
  context: config.paths.assets,
  entry : userConfig.entry,
  devtool: config.enabled.sourceMaps ? '#source-map' : undefined,
  mode : isProduction ? 'production' : 'development',
  output: {
    filename: `scripts/${filename}.js`,
    path: config.paths.dist,
    publicPath: path.join(userConfig.publicPath, path.basename(config.paths.dist)),
  },
  stats: {
    children: false,
  },
  resolve: {
    modules: [
      config.paths.assets,
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
        include: config.paths.assets,
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
        include: config.paths.assets,
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
              sourceComments: true,
            },
          },
        ],
      },
      {
        test: /\.(png|svg|jpe?g|gif|woff|woff2|eot|ttf|otf)$/,
        include: config.paths.assets,
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
              outputPath: 'vendor/',
              name: `${filename}.[ext]`,
            },
          },
        ],
      },
    ],
  },
  plugins: [
    // Remove all files inside output.path director
    new CleanWebpackPlugin(),
    // Extract CSS into separate files
    new MiniCssExtractPlugin({
      filename: `styles/${filename}.css`,
    }),
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
    // Generate a JSON file that matches the original filename with the hashed version.
    new WebpackAssetsManifest({
      output: 'assets.json',
      space: 2,
      writeToDisk: false,
      assets: {},
      replacer: (key, value) => {
        if (typeof value === 'string') {
          return value;
        }
        const manifest = value;
        // Prepend scripts/ or styles/ to manifest keys
        Object.keys(manifest).forEach((src) => {
          const sourcePath = path.basename(path.dirname(src));
          const targetPath = path.basename(path.dirname(manifest[src]));
          if (sourcePath === targetPath) {
            return;
          }
          manifest[`${targetPath}/${src}`] = manifest[src];
          delete manifest[src];
        });
        return manifest;
      },
    }),
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
