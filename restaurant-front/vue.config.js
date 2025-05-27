const webpack = require('webpack');

module.exports = {
  // Plugins configuration
  configureWebpack: {
    plugins: [
      new webpack.DefinePlugin({
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(true),
      }),
    ],
    module: {
      rules: [
        // TypeScript rule
        {
          test: /\.ts$/,
          use: 'ts-loader',
          exclude: /node_modules/,
        },
        // Vue loader rule
        {
          test: /\.vue$/,
          loader: 'vue-loader',
        },
        // Babel rule for modern JavaScript
        {
          test: /\.js$/,
          use: 'babel-loader',
          exclude: /node_modules/,
        },
      ],
    },
  },

  // DevServer configuration to set up API proxy to backend
  devServer: {
    proxy: 'http://localhost:8000', // API backend proxy
  },

  // Modify Webpack config using chainWebpack for further customization
  chainWebpack: (config) => {
    config.module
      .rule('vue')
      .test(/\.vue$/)
      .use('vue-loader')
      .loader('vue-loader')
      .end();
  },

  // Transpile specific dependencies that may need special handling
  transpileDependencies: [
    'vuetify', // Add any other dependencies you want to transpile here
    /^(?!.*node_modules\/.*(vue|vue-router|vuex)).*/ // Add other packages as needed
  ],

  // Additional settings, if necessary for optimizing Vue 3 hydration issues
};
