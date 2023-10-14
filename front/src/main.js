import Vue from 'vue'
import App from './App.vue'
import router from './router';
import Vuex from 'vuex';
import store from './store';
import Vuesax from 'vuesax'
import 'vuesax/dist/vuesax.css'
import 'remixicon/fonts/remixicon.css'
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import { Tabs, TabPane, Input, Button, Badge,Avatar } from 'element-ui';
import instance from './api/request';
import { timeFormat } from './api/time';
Vue.config.productionTip = false
Vue.use(Tabs)
Vue.use(TabPane)
Vue.use(Input)
Vue.use(Button)
Vue.use(Badge)
Vue.use(Avatar)
Vue.use(Toast, {
  transition: "Vue-Toastification__fade",
  maxToasts: 20,
  newestOnTop: true
});
Vue.use(Vuex)
Vue.use(Vuesax)
Vue.directive('time',{
  bind:function(el,binding){
    el.innerHTML = timeFormat(binding.value.time, binding.value.mode)
    el.__timeout__=setInterval(function(){
      el.innerHTML = timeFormat(binding.value.time, binding.value.mode)
    },60000)
  },
  update:function(el,binding){
    el.innerHTML = timeFormat(binding.value.time, binding.value.mode)
    clearInterval(el.__timeout__);
    delete el.__timeout__
    el.__timeout__ = setInterval(function () {
      el.innerHTML = timeFormat(binding.value.time, binding.value.mode)
    }, 60000)
  },
  unbind:function(el,binding){
    clearInterval(el.__timeout__);
    delete el.__timeout__
  }
})
Vue.prototype.$EventBus = new Vue()
Vue.prototype.$instance = instance
router.beforeEach((to, from, next) => {
  if (to.meta.requireLogin) {
    next()
  }
  else{
    next()
  }
})
new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')

