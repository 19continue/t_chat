<template>
    <div id="layout">
        <div class="message">
            <div class="title">
                消息列表
            </div>
            <div class="people">
                <template v-for="item, index in news">
                    <div :class="'item' + (chatWithNow && chatWithNow.id == item.id ? ' active' : '')"
                        @click="chatWithThis(item)">
                        <vs-avatar circle>
                            <img :src="item.avatar" alt="">
                        </vs-avatar>
                        <div class="text">
                            <div class="top">
                                <div class="top-left" v-if="item.strange">
                                    {{ '[陌生]' + (item.note_name ? item.note_name : item.nick_name) }}
                                </div>
                                <div class="top-left" v-else>
                                    {{ item.note_name ? item.note_name : (item.nick_name || item.name) }}
                                </div>
                                <div class="top-right" v-time="{ time: item.create_time, mode: true }"></div>
                            </div>
                            <div class="bottom">
                                <div class="bottom-left">
                                    {{ item.content }}
                                </div>
                                <div class="bottom-right">
                                    <el-badge v-if="item.count" :value="item.count" :max="99">
                                    </el-badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="chat" v-if="chatWithNow">
            <div class="chat-box" v-if="chatWithNow.isGroup">
                <div class="header">
                    <div class="container">
                        <vs-avatar class="avatar">
                            <img :src="chatWithNow.avatar" alt="">
                        </vs-avatar>
                        <div style="margin-left: 15px;color: var(--vs-theme-color);">
                            {{ chatWithNow.note_name ? chatWithNow.note_name : chatWithNow.name }}
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="show-box" id="message">
                        <div class="messages">
                            <div v-for="item, index in currentMessages">
                                <div v-if="showTime(index)" v-time="{ time: item.create_time, mode: false }"
                                    style="width: 100%;text-align: center;padding-top: 10px;font-size: 0.8rem;color: var(--vs-theme-color2);">
                                </div>
                                <div v-if="userInfo.id == item.from_id">
                                    <div v-if="item.type == 0"
                                        style="display: flex;flex-direction: row-reverse;margin-top: 10px;margin-bottom: 10px;width: 100%;">
                                        <vs-avatar class="avatar" style="margin-top: 0.8rem;">
                                            <img :src="userInfo.avatar">
                                        </vs-avatar>
                                        <div style="display: flex;flex-direction: column;width: 100%;">
                                            <div style="display: flex;flex-direction: row-reverse;">
                                                <div
                                                    style="color: var(--vs-theme-color2);font-size: 0.8rem;margin-right: 15px;max-width: 300px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                                    {{ userInfo.nick_name }}
                                                </div>
                                            </div>
                                            <div style="display: flex;flex-direction: row-reverse;">
                                                <div
                                                    style="display: flexbox;height: 0;width: 0;border-left: 5px solid #5EB1FE;border-top:5px solid transparent;border-right:5px solid transparent;border-bottom:5px solid transparent;margin-top: 12px;">
                                                </div>
                                                <div
                                                    style="max-width: 70%;min-width: 10px;min-height: 10px;padding: 10px;border-radius: 8px;background-color: #5EB1FE;">
                                                    {{ item.content }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div v-if="item.type == 0"
                                        style="display: flex;flex-direction: row;margin-top: 10px;margin-bottom: 10px;width: 100%">
                                        <vs-avatar class="avatar" style="margin-top: 0.8rem;">
                                            <img :src="item.avatar">
                                        </vs-avatar>
                                        <div style="display: flex;flex-direction: column;width: 100%">
                                            <div
                                                style="color: var(--vs-theme-color2);font-size: 0.8rem;margin-left: 15px;max-width: 300px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                                {{ item.nick_name }}</div>
                                            <div style="display: flex;flex-direction: row;">
                                                <div
                                                    style="display: flexbox;height: 0;width: 0;border-right: 5px solid var(--vs-theme-layout2);border-top:5px solid transparent;border-left:5px solid transparent;border-bottom:5px solid transparent;margin-top: 12px;">
                                                </div>
                                                <div
                                                    style="max-width: 70%;min-width: 10px;min-height: 10px;padding: 10px;border-radius: 8px;background-color: var(--vs-theme-layout2);">
                                                    {{ item.content }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="menu-box">
                            <i class="ri-emotion-line"></i>
                            <i class="ri-folder-2-line"></i>
                            <i class="ri-folder-image-line"></i>
                            <i class="ri-phone-line"></i>
                            <i class="ri-vidicon-line"></i>
                        </div>
                        <textarea class="input" v-model="content"></textarea>
                        <div class="button-box">
                            <vs-button class="button" animation-type="scale" @click="send(1)">
                                <i class="ri-send-plane-fill"></i>
                                <template #animate>
                                    发送
                                </template>
                            </vs-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chat-box" v-else>
                <div class="header">
                    <div class="container">
                        <vs-avatar class="avatar" badge
                            :badge-color="online.includes(chatWithNow.id) ? 'success' : 'danger'">
                            <img :src="chatWithNow.avatar" alt="">
                        </vs-avatar>
                        <div style="margin-left: 15px;color: var(--vs-theme-color);">
                            {{ chatWithNow.note_name ? chatWithNow.note_name : chatWithNow.nick_name }}
                        </div>
                        <div style="color: var(--vs-theme-color2);font-size: 0.9rem;">
                            {{ online.includes(chatWithNow.id) ? '[在线]' : '[离线]' }}
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="show-box" id="message">
                        <div class="messages">
                            <div v-for="item, index in currentMessages">
                                <div v-if="showTime(index)" v-time="{ time: item.create_time, mode: false }"
                                    style="width: 100%;text-align: center;padding-top: 10px;font-size: 0.8rem;color: var(--vs-theme-color2);">
                                </div>
                                <div v-if="userInfo.id == item.from_id">
                                    <div v-if="item.type == 0"
                                        style="display: flex;flex-direction: row-reverse;margin-top: 10px;margin-bottom: 10px;">
                                        <vs-avatar class="avatar">
                                            <img :src="userInfo.avatar">
                                        </vs-avatar>
                                        <div
                                            style="display: flexbox;height: 0;width: 0;border-left: 5px solid #5EB1FE;border-top:5px solid transparent;border-right:5px solid transparent;border-bottom:5px solid transparent;margin-top: 12px;">
                                        </div>
                                        <div
                                            style="max-width: 70%;min-width: 10px;min-height: 10px;padding: 10px;border-radius: 8px;background-color: #5EB1FE;">
                                            {{ item.content }}
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div v-if="item.type == 0"
                                        style="display: flex;flex-direction: row;margin-top: 10px;margin-bottom: 10px;">
                                        <vs-avatar class="avatar">
                                            <img :src="chatWithNow.avatar">
                                        </vs-avatar>
                                        <div
                                            style="display: flexbox;height: 0;width: 0;border-right: 5px solid var(--vs-theme-layout2);border-top:5px solid transparent;border-left:5px solid transparent;border-bottom:5px solid transparent;margin-top: 12px;">
                                        </div>
                                        <div
                                            style="max-width: 70%;min-width: 10px;min-height: 10px;padding: 10px;border-radius: 8px;background-color: var(--vs-theme-layout2);">
                                            {{ item.content }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="menu-box">
                            <i class="ri-emotion-line"></i>
                            <i class="ri-folder-2-line"></i>
                            <i class="ri-folder-image-line"></i>
                            <i class="ri-phone-line"></i>
                            <i class="ri-vidicon-line" @click="openNotificationCall"></i>
                        </div>
                        <textarea class="input" v-model="content"></textarea>
                        <div class="button-box">
                            <vs-button class="button" animation-type="scale" @click="send(0)">
                                <i class="ri-send-plane-fill"></i>
                                <template #animate>
                                    发送
                                </template>
                            </vs-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="chat" v-else
            style="display: flex;flex-direction: row;justify-content: center;align-items: center;font-size: 20rem;">
            <i class="ri-ghost-line" style="opacity: 0.2;"></i>
        </div>
        <div :style="'position: absolute;border-radius:10px;background:#333333;left:' + call_left + 'px;top:' + call_top + 'px;width:'+call_width+'px;height:'+call_height+'px;z-index:999996;'" v-if="callId != null">
            <i class="ri-drag-move-2-fill hover" 
            :style="'position: fixed;color:#fff;left:'+(call_left+call_width/2-16)+'px;top:'+(call_top+call_height*3/100)+'px;z-index:9999999;'"
                draggable="true"
                @dragstart="dragstart($event)"
                @dragend="dragend($event)"></i>
            <call :callId="callId" :height="call_height" :width="call_width" @close="closeCall"></call>
        </div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import call from '@/components/call.vue';
export default {
    name: 'chat',
    components:{
        call
    },
    data() {
        return {
            content: '',
            chatWithNow: null,
            currentMessages: [],
            groupMembers: [],
            chatIndex: 0,
            callId:null,
            call_left:0,
            call_top:0,
            call_width:350,
            call_height:250,
            move_x:0,
            move_y:0
        }
    },
    computed: {
        ...mapGetters([
            'conn',
            'userInfo',
            'friends',
            'groups',
            'news',
            'online',
            'messages'
        ])
    },
    created() {
        this.$EventBus.$off('getUserMessage')
        this.$EventBus.$on("getUserMessage", (val) => {
            if (val.pattern == 0) {
                let news = this.news
                let index = -1
                let messages = this.messages
                messages.push(val)
                this.$store.commit('setMessages', messages)
                for (let i = 0; i < news.length; i++) {
                    if (news[i].id == val.from_id) {
                        index = i
                        break
                    }
                }
                if (index == -1) {
                    let isHave = -1
                    let obj = {}
                    for (let i = 0; i < this.friends.length; i++) {
                        if (this.friends[i].id == val.from_id) {
                            isHave = i
                            break
                        }
                    }
                    if (isHave != -1) {
                        obj = this.friends[isHave]
                        obj.content = val.content
                        obj.create_time = val.create_time
                        obj.count = 1
                        obj.isGroup = false
                        news.unshift(obj)
                        this.$store.commit('setNews', news)
                    }
                    else {
                        let params = new FormData()
                        params.append('id', val.from_id)
                        this.$instance.post('user/getById', params).then(res => {
                            console.log(res)
                            if (res.code == 200) {
                                obj = res.data
                                obj.content = val.content
                                obj.create_time = val.create_time
                                obj.count = 1
                                obj.isGroup = false
                                obj.strange = true
                                news.unshift(obj)
                                this.$store.commit('setNews', news)
                            }
                        })
                    }
                    let sendData = {
                        code: 130,
                        data: obj
                    }
                    this.$EventBus.$emit("sendMessage", sendData)
                }
                else {
                    if (this.chatWithNow && this.chatWithNow.id == val.from_id) {
                        this.currentMessages.push(val)
                        setTimeout(() => {
                            document.getElementById('message').scrollTop = document.getElementById('message').scrollHeight
                        }, 100);
                    } else {
                        if (news[index].count) {
                            news[index].count = news[index].count + 1
                        } else {
                            news[index].count = 1
                        }
                    }
                    news[index].content = val.content
                    news[index].create_time = val.create_time
                    this.$store.commit('setNews', news)
                    let sendData = {
                        code: 130,
                        data: news[index]
                    }
                    this.$EventBus.$emit("sendMessage", sendData)
                }
            }
            else if (val.pattern == 1) {
                let news = this.news
                let index = -1
                for (let i = 0; i < news.length; i++) {
                    if (news[i].id == val.to_id) {
                        index = i
                        break
                    }
                }
                if (index == -1) {
                    let isHave = -1
                    let obj = {}
                    for (let i = 0; i < this.groups.length; i++) {
                        if (this.groups[i].id == val.to_id) {
                            isHave = i
                            break
                        }
                    }
                    if (isHave != -1) {
                        obj = this.groups[isHave]
                        obj.content = val.content
                        if (val.from_id != this.userInfo.id) {
                            obj.content = val.nick_name + ': ' + val.content
                        } else {
                            obj.content = val.content
                        }
                        obj.create_time = val.create_time
                        obj.count = 1
                        obj.isGroup = true
                        news.unshift(obj)
                        this.$store.commit('setNews', news)
                        let sendData = {
                            code: 130,
                            data: obj
                        }
                        this.$EventBus.$emit("sendMessage", sendData)
                    }
                }
                else {
                    if (this.chatWithNow && this.chatWithNow.id == val.to_id) {
                        this.currentMessages.push(val)
                        setTimeout(() => {
                            document.getElementById('message').scrollTop = document.getElementById('message').scrollHeight
                        }, 100);
                    } else {
                        if (news[index].count) {
                            news[index].count = news[index].count + 1
                        } else {
                            news[index].count = 1
                        }
                    }
                    if(val.from_id!=this.userInfo.id){
                        news[index].content=val.nick_name+': '+ val.content
                    }else{
                        news[index].content = val.content
                    }
                    news[index].create_time = val.create_time
                    this.$store.commit('setNews', news)
                    let sendData = {
                        code: 130,
                        data: news[index]
                    }
                    this.$EventBus.$emit("sendMessage", sendData)
                }
            }
        })
        this.$EventBus.$off('chatWithNow')
        this.$EventBus.$on("chatWithNow", (val) => {
            this.chatWithThis(val)
        })
        this.$EventBus.$off('getUserCall')
        this.$EventBus.$on("getUserCall", (val) => {
            this.callId = {
                type: 'reciever',
                avatar: val.avatar,
                from_id: this.userInfo.id,
                to_id: val.from_id,
                peerId:val.peerId
            }
        })
        this.$EventBus.$off('userRefuseCall')
        this.$EventBus.$on("userRefuseCall", (val) => {
            this.callId = null
            if(val.status==2){
                this.$toast.error('对方已拒绝', {
                    position: "top-center",
                    timeout: 2000,
                    closeOnClick: true,
                    pauseOnFocusLoss: true,
                    pauseOnHover: false,
                    draggable: true,
                    draggablePercent: 1,
                    showCloseButtonOnHover: false,
                    hideProgressBar: true,
                    closeButton: "button",
                    icon: false,
                    rtl: false
                })
            }
            else if(val.status == 3){
                this.$toast.error('对方不在线', {
                    position: "top-center",
                    timeout: 2000,
                    closeOnClick: true,
                    pauseOnFocusLoss: true,
                    pauseOnHover: false,
                    draggable: true,
                    draggablePercent: 1,
                    showCloseButtonOnHover: false,
                    hideProgressBar: true,
                    closeButton: "button",
                    icon: false,
                    rtl: false
                })
            }
            else if(val.status == 4){
                this.$toast.info('已取消', {
                    position: "top-center",
                    timeout: 2000,
                    closeOnClick: true,
                    pauseOnFocusLoss: true,
                    pauseOnHover: false,
                    draggable: true,
                    draggablePercent: 1,
                    showCloseButtonOnHover: false,
                    hideProgressBar: true,
                    closeButton: "button",
                    icon: false,
                    rtl: false
                })
            }
            else if(val.status == 5 || val.status == 6){
                this.$toast.info('对方挂断', {
                    position: "top-center",
                    timeout: 2000,
                    closeOnClick: true,
                    pauseOnFocusLoss: true,
                    pauseOnHover: false,
                    draggable: true,
                    draggablePercent: 1,
                    showCloseButtonOnHover: false,
                    hideProgressBar: true,
                    closeButton: "button",
                    icon: false,
                    rtl: false
                })
            }
        })
        
    },
    methods: {
        chatWithThis(item) {
            this.chatWithNow = item
            if (item.isGroup) {
                let params = new FormData
                params.append('id', item.id)
                this.$instance.post('user/getGroupMembers', params).then(res => {
                    if (res.code == 200) {
                        this.groupMembers = res.data
                    }
                    else {
                        this.groupMembers = []
                    }
                }).catch(e => {
                    this.groupMembers = []
                })
                this.$instance.post('user/getGroupMessages', params).then(res => {
                    if (res.code == 200) {
                        this.currentMessages = res.data
                    }
                    else {
                        this.currentMessages = []
                    }
                    this.dealMessage()
                }).catch(e => {
                    this.currentMessages = []
                    this.dealMessage()
                })
            } else {
                let arr = []
                for (let i = 0; i < this.messages.length; i++) {
                    if (this.messages[i].from_id == this.userInfo.id && this.messages[i].to_id == item.id || this.messages[i].from_id == item.id && this.messages[i].to_id == this.userInfo.id) {
                        arr.push(this.messages[i])
                    }
                }
                this.currentMessages = arr
                this.dealMessage()
            }

        },
        dealMessage() {
            this.currentMessages.sort(function (a, b) {
                return b.create_time > a.create_time ? -1 : 1
            })
            let news = this.news
            let index = -1
            for (let i = 0; i < news.length; i++) {
                if (news[i].id == this.chatWithNow.id) {
                    index = i
                    break
                }
            }
            if (index != -1) {
                news[index].count = null
                this.$store.commit('setNews', news)
                let sendData = {
                    code: 130,
                    data: news[index]
                }
                this.$EventBus.$emit("sendMessage", sendData)
            }
            setTimeout(() => {
                document.getElementById('message').scrollTop = document.getElementById('message').scrollHeight
            }, 100);
            if (this.currentMessages.length > 0 && this.currentMessages[this.currentMessages.length - 1].id) {
                console.log(this.currentMessages[this.currentMessages.length - 1])
                setTimeout(() => {
                    let data = {
                        code: 120,
                        id: this.userInfo.id,
                        point: this.currentMessages[this.currentMessages.length - 1].id
                    }
                    this.$EventBus.$emit("sendMessage", data)
                }, 100);
            }
        },
        send(pattern) {
            let data = null
            data = {
                code: 200,
                type: 0,
                pattern: pattern,
                from_id: this.userInfo.id,
                to_id: this.chatWithNow.id,
                content: this.content
            }
            if (pattern != 0) {
                data.avatar = this.userInfo.avatar
                data.nick_name = this.userInfo.nick_name
            }
            let date = new Date();
            let time = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.toLocaleTimeString('chinese', { hour12: false })
            let message = {
                type: 0,
                pattern: pattern,
                from_id: this.userInfo.id,
                to_id: this.chatWithNow.id,
                content: this.content,
                create_time: time,
                status: false
            }
            let messages = this.messages
            if (this.conn) {
                this.$EventBus.$emit("sendMessage", data)
                let news = this.news
                let index = -1
                for (let i = 0; i < news.length; i++) {
                    if (news[i].id == this.chatWithNow.id) {
                        index = i
                        break
                    }
                }
                if (index != -1) {
                    news[index].content = message.content
                    news[index].create_time = message.create_time
                    this.$store.commit('setNews', news)
                    let sendData = {
                        code: 130,
                        data: news[index]
                    }
                    this.$EventBus.$emit("sendMessage", sendData)
                }
            } else {
                message.status = true
            }
            this.currentMessages.push(message)
            messages.push(message)
            this.$store.commit('setMessages', messages)
            this.content = ''
            setTimeout(() => {
                document.getElementById('message').scrollTop = document.getElementById('message').scrollHeight
            }, 100);
        },
        showTime(index) {
            if (index == 0) {
                return true
            } else {
                let nowTime = Date.parse(new Date(this.currentMessages[index].create_time))
                let lastTime = Date.parse(new Date(this.currentMessages[index - 1].create_time))
                if (nowTime - lastTime >= 300000) {
                    return true
                } else {
                    return false
                }
            }
        },
        openNotificationCall() {
            if(this.callId==null){
                this.callId = {
                    type: 'caller',
                    avatar: this.chatWithNow.avatar,
                    from_id: this.userInfo.id,
                    to_id: this.chatWithNow.id
                }
            }else{
                this.$toast.error('只能开启一个视频通话', {
                    position: "top-center",
                    timeout: 2000,
                    closeOnClick: true,
                    pauseOnFocusLoss: true,
                    pauseOnHover: false,
                    draggable: true,
                    draggablePercent: 1,
                    showCloseButtonOnHover: false,
                    hideProgressBar: true,
                    closeButton: "button",
                    icon: false,
                    rtl: false
                })
            }
        },
        closeCall(){
            if(this.callId){
                this.callId = null
            }
        },
        dragstart(e){
            this.move_x=e.clientX
            this.move_y=e.clientY
        },
        dragend(e){
            let x= e.clientX -this.move_x
            let y = e.clientY - this.move_y
            this.call_left+=x
            this.call_top+=y
        }
    }
}
</script>

<style lang="scss" scoped>
#layout {
    display: flex;
    flex-direction: row;
    width: 100%;
    height: 100%;
}

.hover:hover{
    cursor:grab;
}

.title {
    color: var(--vs-theme-color);
    font-weight: bold;
    opacity: 0.8;
    width: 115px;
    text-align: center;
    margin-bottom: 10px;
}

.message {
    margin-top: 5%;
    width: 420px;

    .people {
        padding-left: 20px;
        height: 86%;
        overflow: auto;

        .item {
            width: 250px;
            display: flex;
            flex-direction: row;
            background-color: var(--vs-theme-layout3);
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 8px;

            .text {
                display: flex;
                flex-direction: column;
                justify-content: center;
                width: 195px;
                margin-left: 10px;

                .top {
                    width: 100%;
                    display: flex;
                    flex-direction: row;
                    align-items: center;

                    .top-left {
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        color: var(--vs-theme-color);
                        font-size: 1.1rem;
                        min-height: 1.1rem;
                    }

                    .top-right {
                        margin-left: auto;
                        color: var(--vs-theme-color2);
                        font-size: 0.9rem;
                    }
                }

                .bottom {
                    width: 100%;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    min-height: 0.9rem;

                    .bottom-left {
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        color: var(--vs-theme-color2);
                        font-size: 0.9rem;
                    }

                    .bottom-right {
                        margin-left: auto;
                        font-size: 0.1rem;
                    }
                }

            }

            &:hover {
                cursor: pointer;
            }
        }

        .active {
            background-color: #5EB1FE;

            .text {
                .top {
                    .top-left {
                        color: #fff;
                    }

                    .top-right {
                        color: #F4F4F5;
                    }
                }

                .bottom {
                    .bottom-left {
                        color: #F4F4F5;
                    }
                }

            }
        }
    }
}

.chat {
    width: 100%;
    height: 100%;

    .chat-box {
        width: 100%;
        height: 100%;

        .header {
            width: 100%;
            height: 12%;
            overflow: hidden;

            .container {
                display: flex;
                flex-direction: row;
                align-items: center;
                margin-top: 2%;
                margin-left: 2%;
            }
        }

        .content {
            width: 95%;
            height: 85%;
            margin-left: 2%;
            border-radius: 10px;
            background-color: var(--vs-theme-bg2);
            display: flex;
            flex-direction: column;

            .show-box {
                width: 100%;
                height: 75%;
                overflow: auto;

                .messages {
                    width: 95%;
                    margin-left: 2.5%;
                }
            }

            .input-box {
                width: 95%;
                height: 25%;
                margin-left: 2.5%;
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
                border: 1px solid rgba($color: #6B7884, $alpha: 0.1);
                background-color: var(--vs-theme-layout2);
                margin-bottom: 10px;

                .menu-box {
                    width: 100%;
                    height: 20%;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    font-size: 1.2rem;

                    &:first-child {
                        margin-left: 1%;
                    }

                    i {
                        margin-right: 2%;
                    }

                    i:hover {
                        cursor: pointer;
                    }
                }

                .input {
                    width: 96%;
                    height: 50%;
                    margin-left: 2%;
                    border: none;
                    outline: none;
                    background: none;
                    resize: none;
                }

                .button-box {
                    width: 100%;
                    height: 15%;
                    margin-top: 0.5%;
                    display: flex;
                    flex-direction: row-reverse;
                    align-items: center;

                    .button {
                        margin-right: 2%;
                        width: 100px;
                        background-color: #5EB1FE;
                    }

                }
            }
        }
    }
}
</style>