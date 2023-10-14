<template>
    <div id="box">
        <div style="width: 96px;margin: 0 auto;">
            <el-tabs v-model="active" @tab-click=";">
                <el-tab-pane label="找人" name="1"></el-tab-pane>
                <el-tab-pane label="找群" name="2"></el-tab-pane>
            </el-tabs>
        </div>
        <div v-if="active=='1'">
            <div class="head">
                    <vs-input v-model="userKey" placeholder="输入账号搜索" />
                    <vs-button  color="#60B2FD" @click="search">
                        搜索
                    </vs-button>
            </div>
            <div class="result">
                <div v-for="item,index in userResult" :key="index">
                    <div class="item">
                        <vs-avatar style="margin-left: 20px;" circle>
                            <img :src="item.avatar" alt="">
                        </vs-avatar>
                        <div style="margin-left: 20px;width: 200px;">
                            {{ item.nick_name }}
                        </div>
                        <el-button style="background-color: #60B2FD;margin-left: 150px;" type="primary" size="mini" round @click="addFriend(item)">添加</el-button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="head">
                        <vs-input v-model="groupKey" placeholder="输入群号搜索" />
                        <vs-button  color="#60B2FD" @click="searchGroup">
                            搜索
                        </vs-button>
                </div>
                <div class="result">
                    <div v-for="item, index in groupResult" :key="index">
                        <div class="item">
                            <vs-avatar style="margin-left: 20px;" circle>
                                <img :src="item.avatar" alt="">
                            </vs-avatar>
                            <div style="margin-left: 20px;width: 200px;">
                                {{ item.name }}
                            </div>
                            <el-button style="background-color: #60B2FD;margin-left: 150px;" type="primary" size="mini" round @click="addGroup(item)">加入</el-button>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
    name: 'addFriend',
    props:{

    },
    data(){
        return{
            userKey:'',
            groupKey:'',
            active:'1',
            userResult:[],
            groupResult:[]
        }
    },
    computed: {
        ...mapGetters([
            'userInfo'
        ])
    },
    mounted(){

    },
    methods:{
        search(){
            let params=new FormData
            params.append('account',this.userKey)
            this.$instance.post('user/getByAccount',params).then(res=>{
                if(res.code==200){
                    this.userResult=res.data
                }
            })
        },
        searchGroup(){
            let params = new FormData
            params.append('number', this.groupKey)
            this.$instance.post('user/getByNumber', params).then(res => {
                if (res.code == 200) {
                    this.groupResult = res.data
                }
            })
        },
        addFriend(item){
            this.$EventBus.$emit('addFriend',item)
        },
        addGroup(item){
            this.$EventBus.$emit('addGroup', item)
        }
    }
}
</script>

<style lang="scss" scoped>
#box{
    .head{
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
    }
    .result{
        width: 80%;
        margin-left: 10%;
        height: 420px;
        overflow: auto;
        .item{
            
            background-color: var(--vs-theme-layout2);
            border-radius: 5px;
            margin-top: 10px;
            padding: 5px;
            display: flex;
            flex-direction: row;
            align-items: center;
            border: 1px solid rgba($color: #6B7884, $alpha: 0.1);
        }
    }
}
</style>
