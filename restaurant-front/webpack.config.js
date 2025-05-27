const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
  transpileDependencies: [
    'vuetify',  // Transpile vuetify (if you are using it)
  ],
  chainWebpack: (config) => {
    // Default vue-loader should already be set up by Vue CLI, so we can remove this manual config.
    config.module
      .rule('vue')
      .test(/\.vue$/)
      .use('vue-loader')
      .loader('vue-loader')
      .end()
  }
})
