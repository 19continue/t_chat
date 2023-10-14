<template>
    <div id="layout">
        <div id="container">
            <div class="left">
                <vs-avatar class="avatar" pointer size="120">
                    <img :src="avatar" alt="">
                </vs-avatar>
                <input ref="upload" type="file" @change="getFile($event)" style="display: none;">
                <vs-button @click="upload()" color="#6B7884" animation-type="scale" style="width: 100px;margin-top: 10px;">
                    <i class="ri-camera-line"></i>
                    <template #animate>
                        修改头像
                    </template>
                </vs-button>
                <vs-button :class="active == 0 ? 'active' : 'no-active'" animation-type="scale"
                    style="width: 100px;margin-top: 10px;" @click="active = 0">
                    <i class="ri-edit-box-line"></i>
                    <template #animate>
                        编辑资料
                    </template>
                </vs-button>
                <vs-button :class="active == 1 ? 'active' : 'no-active'" animation-type="scale"
                    style="width: 100px;margin-top: 10px;" @click="active = 1">
                    <i class="ri-notification-3-line"></i>
                    <template #animate>
                        消息设置
                    </template>
                </vs-button>
                <vs-button :class="active == 2 ? 'active' : 'no-active'" animation-type="scale"
                    style="width: 100px;margin-top: 10px;" @click="active = 2">
                    <i class="ri-lock-password-line"></i>
                    <template #animate>
                        密码设置
                    </template>
                </vs-button>
            </div>
            <div class="right" v-if="active == 0">
                <div class="info-item" style="margin-top: 40px;">
                    <div style="margin-top: 5px;">昵称:</div>
                    <el-input style="width: 60%;margin-left: 5px;" size="small" placeholder="" v-model="editInfo.nick_name"
                        :disabled="edit">
                    </el-input>
                </div>
                <div class="info-item">
                    <div style="margin-top: 5px;">号码:</div>
                    <el-input style="width: 60%;margin-left: 5px;" size="small" placeholder="" v-model="editInfo.phone"
                        :disabled="edit">
                    </el-input>
                </div>
                <div class="info-item">
                    <div style="margin-top: 5px;">邮箱:</div>
                    <el-input style="width: 60%;margin-left: 5px;" size="small" placeholder="" v-model="editInfo.email"
                        :disabled="edit">
                    </el-input>
                </div>

                <div class="info-item">
                    <div style="margin-top: 5px;">性别:</div>
                    <vs-select style="width: 60%;margin-left: 5px;" v-model="editInfo.sex" :disabled="edit">
                        <vs-option label="男" value="0">
                            男
                        </vs-option>
                        <vs-option label="女" value="1">
                            女
                        </vs-option>
                        <vs-option label="不明" value="2">
                            不明
                        </vs-option>
                    </vs-select>
                </div>
                <div class="info-item">
                    <div style="margin-top: 5px;">个性签名:</div>
                    <el-input style="width: 60%;margin-left: 5px;" size="small" placeholder="" v-model="editInfo.signature"
                        :disabled="edit">
                    </el-input>
                </div>
                <div class="info-item">
                    <div style="margin-top: 5px;">地区:</div>
                    <el-input style="width: 60%;margin-left: 5px;" size="small" placeholder="" v-model="editInfo.location"
                        :disabled="edit">
                    </el-input>
                </div>
                <div style="display: flex;flex-direction: row;margin-top: 50px;">
                    <el-button v-show="edit" style="margin-left: 25%;"  size="mini" @click="edit=false">编辑</el-button>
                    <el-button v-show="!edit" style="margin-left: 25%;"  size="mini" @click="cancelEdit">取消</el-button>
                    <el-button v-show="!edit" style="background-color: #60B2FD;margin-left: 10%;" type="primary" size="mini"
                        @click="saveUserInfo">保存</el-button>
                </div>
            </div>
            <div class="right" v-if="active == 2">
                <div class="info-item" style="margin-top: 40px;">
                    <div style="margin-top: 5px;">账号:{{ editInfo.account }}</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
    name: 'chat',
    data() {
        return {
            uploadFile: null,
            active: 0,
            edit: true,
            editInfo: {}
        }
    },
    mounted() {
        this.editInfo = JSON.parse(JSON.stringify(this.userInfo))
    },
    computed: {
        ...mapGetters([
            'userInfo',
            'avatar'
        ])
    },
    methods: {
        getFile(event) {
            if (event.target.files[0]) {
                console.log(event)
                if (event.target.files[0].type == 'image/jpeg' || event.target.files[0].type == 'image/png') {
                    let params = new FormData()
                    params.append('file', event.target.files[0])
                    this.$instance.upload('user/upload', params).then(res => {
                        if (res.code == 200) {
                            let url = res.data + '?' + Date.now()
                            let userInfo = this.userInfo
                            userInfo.avatar = url
                            this.$store.commit('setAvatar', url)
                            this.$store.commit('setUserInfo', userInfo)
                        }
                    })
                }
                else {
                    this.$toast.error('头像只支持JPEG和PNG格式', {
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
            }
        },
        upload() {
            this.$refs.upload.dispatchEvent(new MouseEvent('click'))
        },
        cancelEdit(){
            this.editInfo = JSON.parse(JSON.stringify(this.userInfo))
            this.edit=true
        },
        saveUserInfo(){
            let params = new FormData()
            for(let key in this.editInfo){
                params.append(key, this.editInfo[key])
            }
            this.$instance.upload('user/updateInfo', params).then(res => {
                if (res.code == 200) {
                    let userInfo = JSON.parse(JSON.stringify(this.editInfo)) 
                    this.$store.commit('setUserInfo', userInfo)
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
                    this.edit = true
                }else{
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
                    this.cancelEdit()
                }
            })
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

.no-active {
    background-color: #D1DEFF;
}

.active {
    background-color: #5EB1FE;
}

.info-item {
    display: flex;
    flex-direction: row;
    font-size: 0.9rem;
    color: var(--vs-theme-color);
    margin-left: 20%;
    margin-top: 20px;
}

#container {
    width: 80%;
    height: 80%;
    position: relative;
    left: 10%;
    top: 10%;
    border-radius: 10px;
    background-color: var(--vs-theme-bg2);
    display: flex;
    flex-direction: row;

    .left {
        width: 30%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-right: 1px solid rgba($color: #6B7884, $alpha: 0.2);

        .avatar {
            margin-top: 10%;
        }
    }

    .right {
        width: 70%;
    }
}
</style>