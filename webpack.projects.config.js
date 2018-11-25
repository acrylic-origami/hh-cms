const path = require('path');
const webpack = require('webpack');

const config = {
  entry: './js-src/projects.js',
  externals: {
    'MathJax': 'MathJax',
    'highlight': 'hljs'
  },
  mode: 'development',
  output: {
    path: path.resolve(__dirname, 'public/js'),
    filename: 'index.projects.js'
  },
  // devtool: 'cheap-eval-source-map',
  module: {
    rules: [
      {test: /\.(js|jsx)$/, use: 'babel-loader', exclude: /node_modules/},
      {
        test: /node_modules\/vanilla-jsx\/lib\/.*\.(js|jsx)$/,
        use: 'babel-loader'
      },
      {
        test: /\.scss$/,
        use: [{
            loader: "style-loader" // creates style nodes from JS strings
        }, {
            loader: "css-loader" // translates CSS into CommonJS
        }, {
            loader: "sass-loader" // compiles Sass to CSS
        }]
    }]
  },
  node: {
    fs: 'empty'
  },
  // plugins: [
  //   new webpack.optimize.UglifyJsPlugin()
  // ],
  resolve: {
    alias: {
      // From mapbox-gl-js README. Required for non-browserify bundlers (e.g. webpack):
      'mapbox-gl$': path.resolve('./node_modules/mapbox-gl/dist/mapbox-gl.js')
    }
  }
};

module.exports = config;