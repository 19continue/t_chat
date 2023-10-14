<template>
    <div id="layout">
        <div class="left">
            <div class="menu">
                <div :class="'button' + (active == 1 ? ' active' : '')" @click="active = 3 - active">
                    好友申请
                </div>
                <div :class="'button' + (active == 2 ? ' active' : '')" style="margin-top: 20px;"
                    @click="active = 3 - active">
                    群组申请
                </div>
            </div>
        </div>
        <div class="right">
            <div v-if="active == 1" class="content">
                <div v-for="item, index in requests" :key="index">
                    <div v-if="item.pattern == 0">
                        <div class="item" v-if="item.to_id == userInfo.id">
                            <vs-avatar style="margin-left: 10px;" circle>
                                <img :src="item.from_avatar" alt="">
                            </vs-avatar>
                            <div
                                style="margin-left: 20px;width: 250px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                {{ '(' + item.from_name + ')申请成为好友' }}
                            </div>
                            <div v-if="item.status == 0" style="margin-left: 70px;">
                                <el-button style="background-color: #60B2FD;" size="mini" round
                                    @click="userButton(item, index, 1)">同意</el-button>
                                <el-button type="danger" style="margin-left: 10px;" size="mini" round
                                    @click="userButton(item, index, 2)">拒绝</el-button>
                                <el-button style="margin-left: 10px;" size="mini" round
                                    @click="userButton(item, index, 3)">忽略</el-button>
                            </div>
                            <div v-else-if="item.status == 1" style="margin-left: 190px;">
                                已同意
                            </div>
                            <div v-else-if="item.status == 2" style="margin-left: 190px;">
                                已拒绝
                            </div>
                            <div v-else style="margin-left: 190px;">
                                已忽略
                            </div>
                        </div>
                        <div v-else class="item">
                            <vs-avatar style="margin-left: 10px;" circle>
                                <img :src="item.to_avatar" alt="">
                            </vs-avatar>
                            <div
                                style="margin-left: 20px;width: 250px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                {{ '已向(' + item.to_name + ')发送好友请求' }}
                            </div>
                            <div v-if="item.status == 1" style="margin-left: 190px;">
                                已同意
                            </div>
                            <div v-else-if="item.status == 2" style="margin-left: 190px;">
                                已拒绝
                            </div>
                            <div v-else style="margin-left: 190px;">
                                请求中
                            </div>
                        </div>
                    </div>

                </div>
                <div v-if="requests.length == 0"
                    style="width: 100%;height: 100%;display: flex;flex-direction: row;justify-content: center;align-items: center;font-size: 20rem;">
                    <i class="ri-ghost-line" style="opacity: 0.2;"></i>
                </div>
            </div>
            <div v-else class="content">
                <div v-for="item, index in requests" :key="index">
                    <div v-if="item.pattern == 1">
                        <div class="item" v-if="item.from_id != userInfo.id">
                            <vs-avatar style="margin-left: 10px;" circle>
                                <img :src="item.from_avatar" alt="">
                            </vs-avatar>
                            <div
                                style="margin-left: 20px;width: 250px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                {{ '(' + item.from_name + ')申请入群('+ item.group_name+')' }}
                            </div>
                            <div v-if="item.status == 0" style="margin-left: 110px;">
                                <el-button style="background-color: #60B2FD;" size="mini" round
                                    @click="userButton(item, index, 1)">同意</el-button>
                                <el-button type="danger" style="margin-left: 10px;" size="mini" round
                                    @click="userButton(item, index, 2)">拒绝</el-button>
                            </div>
                            <div v-else-if="item.status == 1" style="margin-left: 190px;">
                                已同意
                            </div>
                            <div v-else-if="item.status == 2" style="margin-left: 190px;">
                                已拒绝
                            </div>
                            <div v-else style="margin-left: 190px;">
                                已忽略
                            </div>
                        </div>
                        <div v-else class="item">
                            <vs-avatar style="margin-left: 10px;" circle>
                                <img :src="item.group_avatar" alt="">
                            </vs-avatar>
                            <div
                                style="margin-left: 20px;width: 250px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                {{ '已向(' + item.group_name + ')发送入群申请' }}
                            </div>
                            <div v-if="item.status == 1" style="margin-left: 190px;">
                                已同意
                            </div>
                            <div v-else-if="item.status == 2" style="margin-left: 190px;">
                                已拒绝
                            </div>
                            <div v-else style="margin-left: 190px;">
                                请求中
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="requests.length == 0"
                    style="display: flex;width: 100%;height: 100%;flex-direction: row;justify-content: center;align-items: center;font-size: 20rem;">
                    <i class="ri-ghost-line" style="opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
    name: 'request',
    computed: {
        ...mapGetters([
            'conn',
            'userInfo',
            'friends',
            'groups',
            'requests'
        ])
    },
    data() {
        return {
            active: 1
        }
    },
    created() {
        this.$EventBus.$off('getFriendRequest')
        this.$EventBus.$on("getFriendRequest", (msg) => {

        })
    },
    beforeDestory() {
        this.$EventBus.$off('getFriendRequest')
    },
    methods: {
        userButton(item, index, status) {
            let data = item
            data.status = status
            data.code=300
            if (item.pattern == 0) {
                if (this.conn) {
                    this.$EventBus.$emit('sendMessage', data)
                    let requests = this.requests
                    requests[index].status = status
                    this.$store.commit('setRequests', requests)
                    if (status == 1) {
                        let params = new FormData
                        params.append('id', item.from_id)
                        this.$instance.post('user/getById', params).then(res => {
                            if (res.code == 200) {
                                let friends = this.friends
                                friends.push(res.data)
                                this.$store.commit('setFriends', friends)
                                let isOnline = {
                                    code: 105,
                                    id: item.from_id
                                }
                                this.$EventBus.$emit('sendMessage', isOnline)
                            }
                        })
                    }
                }
            } else {
                if (this.conn) {
                    this.$EventBus.$emit('sendMessage', data)
                    let requests = this.requests
                    requests[index].status = status
                    this.$store.commit('setRequests', requests)
                    if (status == 1) {
                        let params = new FormData
                        params.append('id', item.from_id)
                        this.$instance.post('user/getGroupById', params).then(res => {
                            if (res.code == 200) {
                                let groups = this.groups
                                groups.push(res.data)
                                this.$store.commit('setGroups', groups)
                            }
                        })
                    }
                }
            }
        }
    }
}
</script>

<style lang="scss" scoped>
#layout {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: row;

    .left {
        width: 17%;
        height: 90%;
        display: flex;
        flex-direction: column;

        margin-left: 2%;

        .menu {
            height: 500px;
            display: flex;
            flex-direction: column;
            margin-top: 44%;

            .button {
                width: 100px;
                height: 40px;
                background-color: var(--vs-theme-layout3);
                border-radius: 8px;
                line-height: 40px;
                text-align: center;
                color: var(--vs-theme-color);

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
        width: 60%;
        height: 80%;
        margin-top: 7%;
        border-radius: 10px;
        background-color: var(--vs-theme-bg2);
        padding: 15px;

        .content {
            width: 96%;
            height: 96%;
            margin-left: 2%;
            margin-top: 2%;
            overflow: auto;

            .item {
                background-color: var(--vs-theme-layout2);
                border-radius: 5px;
                margin-bottom: 10px;
                padding: 5px;
                display: flex;
                flex-direction: row;
                align-items: center;
                border: 1px solid rgba($color: #6B7884, $alpha: 0.1);
            }
        }
    }
}</style>