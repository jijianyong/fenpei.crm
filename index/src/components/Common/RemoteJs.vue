<template>
  <remote-js :src="this.src" @load-js-finish="this.callback"></remote-js>
</template>

<script>
export default {
  components: {
    'remote-js': {
      render(createElement) {
        var self = this
        return createElement('script', {
          attrs: { type: 'text/javascript', src: this.src },
          on: {
            load: function() {
              // console.log('load js')
              self.$emit('load-js-finish')
            }
          }
        })
      },
      props: {
        src: { type: String, required: true }
      }
    }
  },
  props: {
    src: { type: String, required: true }, // 需要加载的外部url
    callback: Function// 外部js加载完成回调
  }
}
</script>
