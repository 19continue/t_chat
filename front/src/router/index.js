import Vue from "vue";
import VueRouter from "vue-router";
import Router from "vue-router";

Vue.use(Router);
const originalPush = VueRouter.prototype.push;
VueRouter.prototype.push = function push(location) {
    return originalPush.call(this, location).catch(err => err);
}

export default new Router({
    routes: [
        {
            path: '/',
            component: resolve => require(['../pages/login.vue'], resolve)
        },
        {
            path: '/home',
            component: resolve => require(['../pages/home.vue'], resolve),
            meta: {
                requireLogin: true,
            },
            children: [
                {
                    path: 'my',
                    component: resolve => require(['../view/my.vue'], resolve),
                    meta: {
                        requireLogin: true,
                    }
                },

                {
                    path: 'chat',
                    component: resolve => require(['../view/chat.vue'], resolve),
                    meta: {
                        requireLogin: true,
                    }
                },
                {
                    path: 'group',
                    component: resolve => require(['../view/group.vue'], resolve),
                    meta: {
                        requireLogin: true,
                    }
                },
                {
                    path: 'request',
                    component: resolve => require(['../view/request.vue'], resolve),
                    meta: {
                        requireLogin: true,
                    }
                },
                {
                    path: 'setup',
                    component: resolve => require(['../view/setup.vue'], resolve),
                    meta: {
                        requireLogin: true,
                    }
                }
            ]
        }
    ]
})