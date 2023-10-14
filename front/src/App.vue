<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: 'App',
  computed: {
    ...mapGetters([
      'theme'
    ])
  },
  watch: {
    theme: {
      handler(val, old) {
        window.document.documentElement.setAttribute('vs-theme', val)
      },
      immediate: false,
      deep: true
    }
  },
  mounted(){
    window.document.documentElement.setAttribute('vs-theme', this.theme)
  }
}
</script>

<style>
:root {
  --vs-theme-color: #2c3e50;
  --vs-theme-color2: #909399;
  --vs-theme-bg: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
  --vs-theme-layout: #F1F3F4;
  --vs-theme-layout2:#F4F7F8;
  --vs-theme-layout3:#fff;
  --vs-theme-bg2: #fff;
  --scroll-bg:#DCDFE6;
}

:root[vs-theme=dark] {
  --vs-theme-color: #fff;
  --vs-theme-color2: #909399;
  --vs-theme-bg: #3F3F3F;
  --vs-theme-layout: #18191C;
  --vs-theme-layout2:#363B4A;
  --vs-theme-layout3:#363B4A;
  --vs-theme-bg2: #1E2023;
  --scroll-bg:#2B2B2B;
}

* {
  padding: 0;
  margin: 0;
}

#app {
  color: var(--vs-theme-color);
}

::-webkit-scrollbar {
  width: 10px;
  background-color: transparent !important;
}

::-webkit-scrollbar-thumb {
  background-color: var(--scroll-bg);
  border-radius: 5px;
  height: 50%;
}

::-webkit-scrollbar-button {
  display: none !important;
}

/* [vs-theme=dark]{
    --vs-theme-color:#fff;
    color: var(--vs-theme-color);
  } */</style>
