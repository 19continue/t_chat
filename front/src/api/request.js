import router from "@/router"
import store from "@/store"
import axios from "axios"

let isRefreshing=false
let requests=[]
const baseURL = "http://localhost:8787"
const refreshTokenUrl='/login/refresh'
const http=axios.create()
let instance={}
http.defaults.timeout = 30000
http.defaults.baseURL = baseURL
http.interceptors.request.use(
    config => {
        const token = store.getters.accessToken
        if (token) {
            config.headers.Authorization = 'Bearer ' + token
        }
        return config
    },
    error => {
        return Promise.error(error);
    }
);
http.interceptors.response.use(
    response => {
        if (response.status == 200) {
            console.log(isRefreshing)
            if(response.data.code==300){
                let config=response.config
                if (!isRefreshing){
                    isRefreshing = true
                    const refreshToken = store.getters.refreshToken
                    toRefreshToken(refreshToken).then(res=>{
                        if(res.code==200){
                            isRefreshing = false
                            store.commit('setAccessToken', res.data.access_token)
                            store.commit('setRefreshToken', res.data.refresh_token)
                            requests.forEach(rq => rq(res.data.access_token))
                            requests=[]
                            config.headers['Authorization'] = 'Bearer ' + res.data.access_token
                            return axios(config)
                        }
                        else if (res.code == 301) {
                            store.commit('setAccessToken', '')
                            store.commit('setRefreshToken', '')
                            store.commit('setUserInfo',{})
                            router.replace({
                                path: '/',
                                query: {
                                    redirect: router.currentRoute.fullPath
                                }
                            })
                        }
                        else{

                        }
                    })
                }
                else{
                    return new Promise((resolve)=>{
                        requests.push((token)=>{
                            config.headers['Authorization'] = 'Bearer ' + token
                            resolve(axios(config))
                        })
                    })
                }
            } 
            return Promise.resolve(response);
        }
        else {
            return Promise.reject(response);
        }
    },
    error => {
        if (error.response.status) {
            switch (error.response.status) {
                case 401:
                    router.replace({
                        path: '/',
                        query: {
                            redirect: router.currentRoute.fullPath
                        }
                    })
                    break;
                case 404:
                    break;
            }
            return Promise.reject(error.response);
        }
    }
);

instance.baseURL=baseURL

instance.get=function get(url, params = {}) {
    return new Promise((resolve, reject) => {
        http.get(url, { parames: params })
            .then(response => {
                resolve(response.data);
            })
            .catch(err => {
                reject(err);
            })
    })
}

instance.post=function post(url, data = {}) {
    return new Promise((resolve, reject) => {
        http.post(url, data)
            .then(response => {
                resolve(response.data);
            })
            .catch(err => {
                reject(err);
            })
    })
}
instance.simple = function simple(url, data = {},method='post') {
    return new Promise((resolve, reject) => {
        axios({
            method: method,
            data: data,
            url: url,
            baseURL: baseURL
        })
        .then(response => {
            resolve(response.data);
        })
        .catch(err => {
            reject(err);
        })
    })
}
instance.upload = function simple(url, data = {}, method = 'post') {
    return new Promise((resolve, reject) => {
        http({
            method: method,
            data: data,
            url: url,
            baseURL: baseURL,
            headers:{
                'Content-Type':'multipart/form-data'
            }
        })
            .then(response => {
                resolve(response.data);
            })
            .catch(err => {
                reject(err);
            })
    })
}
function toRefreshToken(token) {
    return new Promise((resolve, reject) => {
        axios({
            method:'post',
            data:{},
            url:refreshTokenUrl,
            baseURL:baseURL,
            headers:{
                'Authorization':'Bearer ' + token
            }
        })
        .then(response => {
            resolve(response.data);
        })
        .catch(err => {
            reject(err);
        })
    })
}

export default instance
