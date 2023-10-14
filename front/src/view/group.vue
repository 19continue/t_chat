<template>
    <div id="layout">
        <div class="group">
            <div class="head">
                <el-tabs class="tabs" v-model="activeName" @tab-click=";">
                    <el-tab-pane label="好友" name="1"></el-tab-pane>
                    <el-tab-pane label="群聊" name="2"></el-tab-pane>
                </el-tabs>
                <vs-tooltip top style="margin-top: 6px;margin-left: 30px;">
                    <vs-button color="#60B2FD"
                        style="height: 30px;width: 30px;border-top-right-radius: 0px;border-bottom-right-radius: 0px;"
                        @click="showRight = 0, activeId = 0">
                        <i class="ri-user-add-line"></i>
                    </vs-button>
                    <template #tooltip>
                        添加 好友/群聊
                    </template>
                </vs-tooltip>
                <vs-tooltip top style="margin-top: 6px;margin-left: -8px;">
                    <vs-button color="#60B2FD"
                        style="height: 30px;width: 30px;border-top-left-radius: 0px;border-bottom-left-radius: 0px;"
                        @click="showRight = 1, activeId = 0">
                        <i class="ri-chat-new-line"></i>
                    </vs-button>
                    <template #tooltip>
                        创建群聊
                    </template>
                </vs-tooltip>
            </div>
            <div class="people" v-if="activeName == 1">
                <template v-for="item in friends">
                    <div :class="activeId == item.id ? 'item active' : 'item'" @click="open(item)">
                        <vs-avatar class="avatar">
                            <img :src="item.avatar" alt="">
                        </vs-avatar>
                        <div
                            style="display: flex;flex-direction: column;justify-content: center;width: 190px;margin-left: 10px;">
                            <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                {{ item.note_name ? item.note_name : item.nick_name }}
                            </div>
                            <div
                                style="display: flex;flex-direction: row;align-items: center;color: var(--vs-theme-color2);font-size: 0.9rem;width: 90%;">
                                <div style="white-space: nowrap;">
                                    {{ online.includes(item.id) ? '[在线]' : '[离线]' }}
                                </div>
                                <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                    {{ item.signature }}
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="people" v-else>
                <template v-for="item in groups">
                    <div :class="activeId == item.id ? 'item active' : 'item'" @click="openGroup(item)">
                        <vs-avatar class="avatar">
                            <img :src="item.avatar" alt="">
                        </vs-avatar>
                        <div
                            style="display: flex;flex-direction: column;justify-content: center;width: 190px;margin-left: 10px;">
                            <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                {{ item.note_name ? item.note_name : item.name }}
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="right">
            <div class="container">
                <addFriend v-if="showRight == 0"></addFriend>
                <div v-else-if="showRight == 1">
                    <h1 style="text-align: center;padding-top: 10px;">创建群聊</h1>
                    <vs-input style="margin-left: 33%;margin-top: 90px;" label="群名称" v-model="newGroup.name"
                        placeholder="群名称" />
                    <vs-input style="margin-left: 33%;margin-top: 40px;" label="群公告" v-model="newGroup.remark"
                        placeholder="群名称" />
                    <vs-button color="#5EB1FE" style="width: 100px;margin: 0 auto;margin-top: 50px;" @click="createGroup">
                        创建
                    </vs-button>
                </div>
                <div v-else-if="showRight == 2">
                    <div>
                        <div style="display: flex;flex-direction: row;margin-left: 10%;padding-top: 5%;">
                            <vs-avatar class="avatar" size="100" style="border-radius: 10px;">
                                <img :src="current.avatar" alt="">
                            </vs-avatar>
                            <div style="height: 100px;display: flex;flex-direction: column;margin-left: 20px;">
                                <div
                                    style="height: 70px;line-height:70px;font-size: 1.5rem;font-weight: bold;color: var(--vs-theme-color);">
                                    {{ current.nick_name }}
                                </div>
                                <div style="height: 30px;font-size: 0.8rem;color: var(--vs-theme-color2);">
                                    {{ '个性签名: ' + (current.signature ? current.signature : '无') }}
                                </div>
                            </div>
                        </div>
                        <hr style="width: 80%;margin-left: 10%;margin-top: 5%;">
                        <div style="width: 80%;margin-left: 10%;margin-top: 5%;">
                            <div class="info-item">
                                {{ '账号: ' + current.account }}
                            </div>
                            <div class="info-item">
                                {{ '等级: ' + (current.level) }}
                            </div>
                            <div class="info-item">
                                {{ '电话: ' + (current.phone? current.phone:'无') }}
                            </div>
                            <div class="info-item">
                                {{ '邮箱: ' + (current.email? current.email:'无') }}
                            </div>
                            <div class="info-item">
                                {{ '性别: ' + getSex(current.sex) }}
                            </div>
                            <div class="info-item">
                                {{ '年龄: ' + (current.email ? current.email : '未知') }}
                            </div>
                            <div class="info-item">
                                {{ '地址: ' + (current.location ? current.location : '未知') }}
                            </div>
                            <div style="display: flex;flex-direction: row;font-size: 0.9rem;color: var(--vs-theme-color2);">
                                <div style="margin-top: 3px;">备注:</div>
                                <el-input style="width: 50%;margin-left: 5px;" size="mini" placeholder="" v-model="changeNoteName1" :disabled="editNoteName1">
                                </el-input>
                                <i v-if="editNoteName1" style="font-size: 1.1rem;margin-left: 10px;margin-top: 3px;" class="ri-edit-2-line hover" @click="editNoteName1=false"></i>
                                <div v-else>
                                    <el-button style="background-color: #60B2FD;margin-left: 10px;" type="primary" size="mini"
                                    @click="changeUserNoteName">保存</el-button>
                                    <el-button style="margin-left: 10px;" size="mini" 
                                        @click="editNoteName1 = true">取消</el-button>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex;flex-direction: row;margin-top: 40px;">
                            <el-button style="margin-left: 11%;" type="danger" size="mini" 
                                    @click=";">移除好友</el-button>
                            <el-button style="background-color: #60B2FD;margin-left: 45%;" type="primary" size="mini" 
                                @click="chatWith()">发消息</el-button>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div>
                            <div style="display: flex;flex-direction: row;margin-left: 10%;padding-top: 5%;">
                                <vs-avatar class="avatar" size="100" style="border-radius: 10px;">
                                    <img :src="current.avatar" alt="">
                                </vs-avatar>
                                <div style="height: 100px;display: flex;flex-direction: column;margin-left: 20px;">
                                    <div
                                        style="height: 70px;line-height:70px;font-size: 1.5rem;font-weight: bold;color: var(--vs-theme-color);">
                                        {{ current.name }}
                                    </div>
                                    <div style="height: 30px;font-size: 0.8rem;color: var(--vs-theme-color2);">
                                        {{ '个性签名: ' + (current.signature ? current.signature : '无') }}
                                    </div>
                                </div>
                            </div>
                            <hr style="width: 80%;margin-left: 10%;margin-top: 5%;">
                            <div style="width: 80%;margin-left: 10%;margin-top: 5%;">
                                <div class="info-item">
                                    {{ '群号: ' + current.number }}
                                </div>
                                <div class="info-item">
                                    {{ '公告: ' + (current.remark ? current.remark : '无') }}
                                </div>
                                <div style="display: flex;flex-direction: row;font-size: 0.9rem;color: var(--vs-theme-color2);">
                                    <div style="margin-top: 3px;">备注:</div>
                                    <el-input style="width: 50%;margin-left: 5px;" size="mini" placeholder="" v-model="changeNoteName2" :disabled="editNoteName2">
                                    </el-input>
                                    <i v-if="editNoteName2" style="font-size: 1.1rem;margin-left: 10px;margin-top: 3px;" class="ri-edit-2-line hover" @click="editNoteName2 = false"></i>
                                    <div v-else>
                                        <el-button style="background-color: #60B2FD;margin-left: 10px;" type="primary" size="mini"
                                        @click="changeUserNoteName">保存</el-button>
                                        <el-button style="margin-left: 10px;" size="mini" 
                                            @click="editNoteName2 = true">取消</el-button>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex;flex-direction: row;margin-top: 180px;">
                                <el-button style="margin-left: 11%;" type="danger" size="mini" 
                                        @click=";">退出群聊</el-button>
                                <el-button style="background-color: #60B2FD;margin-left: 45%;" type="primary" size="mini" 
                                    @click="chatWith()">发消息</el-button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import addFriend from '@/components/addFriend.vue';
import { mapGetters } from 'vuex';
export default {
    name: 'chat',
    components: {
        addFriend
    },
    computed: {
        ...mapGetters([
            'userInfo',
            'friends',
            'groups',
            'news',
            'online'
        ])
    },
    data() {
        return {
            activeName: '1',
            showRight: 0,
            activeId: 0,
            current: {},
            newGroup: {
                name: '',
                remark: ''
            },
            changeNoteName1:'',
            editNoteName1:true,
            changeNoteName2: '',
            editNoteName2: true,
        }
    },
    methods: {
        open(data) {
            this.current = data
            this.activeId = data.id
            this.changeNoteName1=(this.current.note_name? this.current.note_name:'无')
            this.showRight = 2
        },
        openGroup(data) {
            this.current = data
            this.activeId = data.id
            this.changeNoteName2 = (this.current.note_name ? this.current.note_name : '无')
            this.showRight = 3
        },
        chatWith(isGroup = false) {
            let arr = this.news
            let current = JSON.parse(JSON.stringify(this.current))
            let isHave = false
            for (let i = 0; i < arr.length; i++) {
                if (arr[i].id == this.current.id) {
                    isHave = true
                    break
                }
            }
            current.isGroup = isGroup
            if (!isHave) {
                if (current.create_time) {
                    current.create_time = null
                }
                arr.unshift(current)
                this.$store.commit('setNews', arr)
            }
            setTimeout(() => {
                this.$EventBus.$emit("chatWithNow", current)
            }, 100)
            this.$router.push('/home/chat')
        },
        createGroup() {
            if (this.newGroup.name && this.newGroup.remark) {
                let params = new FormData
                params.append('name', this.newGroup.name)
                params.append('remark', this.newGroup.remark)
                this.$instance.post('user/createGroup', params).then(res => {
                    if (res.code == 200) {
                        let groups = this.groups
                        groups.push(res.data)
                        this.$store.commit('setGroups', groups)
                        this.newGroup.name = ''
                        this.newGroup.remark = ''
                        this.$toast.success(res.msg, {
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
            } else {
                this.$toast.info('请将信息输入完整', {
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
        changeUserNoteName(){

        },
        getSex(sex){
            if(sex==0){
                return'男'
            }
            else if(sex==1){
                return '女'
            }
            return '不明'
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
    cursor: pointer;
}
.info-item{
    font-size: 0.9rem;
    color: var(--vs-theme-color2);
    margin-bottom: 10px;
}
.group {
    width: 310px;
    height: 95%;
    margin-top: 2%;

    .head {
        width: 310px;
        display: flex;
        flex-direction: row;

        .tabs {
            width: 96px;
            margin-left: 11%;
        }
    }

    .people {
        height: 90%;
        padding-left: 7%;
        overflow: auto;

        &:first-child {
            margin-top: 20px;
        }

        .item {
            background-color: var(--vs-theme-layout3);
            width: 250px;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 7px;
            border-radius: 8px;
            margin-bottom: 10px;

            &:hover {
                cursor: pointer;
            }
        }

        .active {
            background-color: #5EB1FE;
            color: #fff;
        }
    }
}

.right {
    width: 100%;
    height: 100%;
    background-color: var(--vs-theme-layout);

    .container {
        width: 80%;
        height: 80%;
        margin-top: 10%;
        margin-left: 10%;
        border-radius: 10px;
        background: var(--vs-theme-bg2);
    }
}
</style>