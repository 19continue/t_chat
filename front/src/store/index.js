import Vuex from 'vuex'
import Vue from 'vue'
Vue.use(Vuex)

const actions = {

}

const mutations = {
    setIsLogin: (state, isLogin) => {
        state.isLogin = isLogin;
        window.sessionStorage.setItem('isLogin', JSON.stringify(isLogin));
    },
    setConn: (state, conn) => {
        state.conn = conn;
    },
    setTheme: (state, theme) => {
        state.theme = theme;
        window.sessionStorage.setItem('theme', JSON.stringify(theme));
    },
    setUserInfo: (state, userInfo) => {
        state.userInfo = userInfo;
        window.sessionStorage.setItem('userInfo', JSON.stringify(userInfo));
    },
    setAvatar: (state, avatar) => {
        state.avatar = avatar;
        window.sessionStorage.setItem('avatar', JSON.stringify(avatar));
    },
    setAccessToken: (state, accessToken) => {
        state.accessToken = accessToken;
        window.sessionStorage.setItem('accessToken', JSON.stringify(accessToken));
    },
    setRefreshToken: (state, refreshToken) => {
        state.refreshToken = refreshToken;
        window.sessionStorage.setItem('refreshToken', JSON.stringify(refreshToken));
    },
    setCallId: (state, callId) => {
        state.callId = callId;
        window.sessionStorage.setItem('callId', JSON.stringify(callId));
    },
    setFriends: (state, friends) => {
        state.friends = friends;
        window.sessionStorage.setItem('friends', JSON.stringify(friends));
    },
    setGroups: (state, groups) => {
        state.groups = groups;
        window.sessionStorage.setItem('groups', JSON.stringify(groups));
    },
    setNews: (state, news) => {
        state.news = news;
        window.sessionStorage.setItem('news', JSON.stringify(news));
    },
    setOnline: (state, online) => {
        state.online = online;
        window.sessionStorage.setItem('online', JSON.stringify(online));
    },
    setMessages: (state, messages) => {
        state.messages = messages;
        window.sessionStorage.setItem('messages', JSON.stringify(messages));
    },
    setRequests: (state, requests) => {
        state.requests = requests;
        window.sessionStorage.setItem('requests', JSON.stringify(requests));
    }
}
const state = {
    isLogin: false,
    conn:false,
    theme: '',
    userInfo: null,
    avatar: '',
    accessToken: null,
    refreshToken: null,
    callId: null,
    friends: [],
    groups: [],
    news: [],
    online:[],
    messages:[],
    requests:[],
}
const getters = {
    isLogin: state => {
        let isLogin = state.isLogin;
        if (!isLogin) {
            isLogin = JSON.parse(window.sessionStorage.getItem('isLogin') || null);
        }
        return isLogin;
    },
    conn: state => {
        return state.conn;
    },
    theme: state => {
        let theme = state.theme;
        if (!theme) {
            theme = JSON.parse(window.sessionStorage.getItem('theme') || null);
        }
        return theme;
    },
    userInfo: state => {
        let userInfo = state.userInfo;
        if (!userInfo) {
            userInfo = JSON.parse(window.sessionStorage.getItem('userInfo') || null);
        }
        return userInfo;
    },
    avatar: state => {
        let avatar = state.avatar;
        if (!avatar) {
            avatar = JSON.parse(window.sessionStorage.getItem('avatar') || null);
        }
        return avatar;
    },
    accessToken: state => {
        let accessToken = state.accessToken;
        if (!accessToken) {
            accessToken = JSON.parse(window.sessionStorage.getItem('accessToken') || null);
        }
        return accessToken;
    },
    refreshToken: state => {
        let refreshToken = state.refreshToken;
        if (!refreshToken) {
            refreshToken = JSON.parse(window.sessionStorage.getItem('refreshToken') || null);
        }
        return refreshToken;
    },
    callId: state => {
        let callId = state.callId;
        if (!callId) {
            callId = JSON.parse(window.sessionStorage.getItem('callId') || null);
        }
        return callId;
    },
    friends: state => {
        let friends = state.friends;
        if (friends.length == 0) {
            friends = JSON.parse(window.sessionStorage.getItem('friends')) || [];
        }
        return friends;
    },
    groups: state => {
        let groups = state.groups;
        if (groups.length == 0) {
            groups = JSON.parse(window.sessionStorage.getItem('groups')) || [];
        }
        return groups;
    },
    news: state => {
        let news = state.news;
        if (news.length==0) {
            news = JSON.parse(window.sessionStorage.getItem('news')) || [];
        }
        return news;
    },
    online: state => {
        let online = state.online;
        if (online.length == 0) {
            online = JSON.parse(window.sessionStorage.getItem('online')) || [];
        }
        return online;
    },
    messages: state => {
        let messages = state.messages;
        if (messages.length == 0) {
            messages = JSON.parse(window.sessionStorage.getItem('messages')) || [];
        }
        return messages;
    },
    requests: state => {
        let requests = state.requests;
        if (requests.length == 0) {
            requests = JSON.parse(window.sessionStorage.getItem('requests')) || [];
        }
        return requests;
    }
}
export default new Vuex.Store({
    actions,
    mutations,
    state,
    getters
})