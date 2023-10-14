<template>
  <div class="layout">
    <div class="container">
      <vs-sidebar reduce relative v-model="active" open color="#5EB1FE">
        <template #logo>
          <vs-avatar class="avatar" badge :badge-color="conn ? 'success' : 'danger'" @click="toMy">
            <img :src="avatar" alt="">
          </vs-avatar>
        </template>
        <vs-sidebar-item id="/home/chat" to="/home/chat">
          <template #icon>
            <vs-tooltip top>
              <i class="ri-message-3-fill"></i>
              <template #tooltip>
                消息
              </template>
            </vs-tooltip>
          </template>
        </vs-sidebar-item>
        <vs-sidebar-item id="/home/group" to="/home/group">
          <template #icon>
            <vs-tooltip top>
              <i class="ri-group-fill"></i>
              <template #tooltip>
                联系人
              </template>
            </vs-tooltip>
          </template>
        </vs-sidebar-item>
        <vs-sidebar-item id="/home/request" to="/home/request">
          <template #icon>
            <el-badge :is-dot="newFriend" class="item">
              <vs-tooltip top>
                <i class="ri-chat-new-fill"></i>
                <template #tooltip>
                  新朋友
                </template>
              </vs-tooltip>
            </el-badge>
          </template>
        </vs-sidebar-item>
        <template #footer>
            <vs-tooltip right shadow not-hover v-model="activeTooltip" style="margin-left: 10px;font-size: 1.2rem;">
              <i class="ri-settings-3-fill" @click="activeTooltip=!activeTooltip"></i>
              <template #tooltip>
                <div>
                  <vs-switch v-model="themeButton" dark>
                    <template #circle>
                      <i v-if="!themeButton" class="ri-sun-fill"></i>
                      <i v-else class="ri-moon-fill"></i>
                    </template>
                  </vs-switch>
                </div>
              </template>
            </vs-tooltip>
        </template>
      </vs-sidebar>
      <div class="main">
        <keep-alive>
          <router-view></router-view>
        </keep-alive>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  data() {
    return {
      connect: false,
      socketUrl: 'ws://127.0.0.1:7272',
      timeout: 20000,
      client: null,
      heartBit: null,
      active: '/home/chat',
      activeTooltip: false,
      themeButton: false,
      newFriend:false
    }
  },
  computed: {
    ...mapGetters([
      'isLogin',
      'conn',
      'userInfo',
      'avatar',
      'accessToken',
      'theme',
      'online',
      'friends',
      'groups',
      'requests'
    ])
  },
  watch: {
    $route: {
      handler(val, old) {
        this.active=this.$route.path
        if(this.$route.path=='/home/request'&& this.newFriend){
          this.newFriend = false
        }
      },
      immediate: false,
      deep: true
    },
    themeButton: {
      handler(val, old) {
        if (val) {
          this.$store.commit('setTheme', 'dark')
        } else {
          this.$store.commit('setTheme', '')
        }
      },
      immediate: false,
      deep: true
    }
  },
  mounted() {
    this.active = this.$route.path
    if (this.theme == 'dark') {
      this.themeButton = true
    }
    this.clientStart()
    this.$EventBus.$off('addFriend')
    this.$EventBus.$on("addFriend", (val) => {
      if (!this.conn) {
        return false
      }
      this.addFriend(val)
    })
    this.$EventBus.$off('addGroup')
    this.$EventBus.$on("addGroup", (val) => {
      if (!this.conn) {
        return false
      }
      this.addGroup(val)
    })
    this.$EventBus.$off('sendMessage')
    this.$EventBus.$on("sendMessage", (val) => {
      if (!this.conn) {
        return false
      }
      this.client.send(JSON.stringify(val))
    })
  },
  beforeDestroy() {
    this.$EventBus.$off("addFriend")
    this.$EventBus.$off("sendMessage")
  },
  methods: {
    clientStart() {
      if (!this.conn && this.isLogin) {
        this.client = new WebSocket(`ws://127.0.0.1:7272?token=${this.accessToken}`)
        this.client.onopen = (e) => {
          this.$store.commit('setConn', true)
          console.log('connect', this.conn)
        }
        this.client.onmessage = (e) => {
          let msg = JSON.parse(e.data)
          console.log(msg)
          if(msg.code == 100){
            this.$store.commit('setOnline', msg.data)
          }
          else if (msg.code == 105) {
            let online=this.online
            online.push(msg.id)
            this.$store.commit('setOnline', online)
          }
          else if(msg.code==110){
            this.$toast.info(msg.msg, {
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
          else if(msg.code == 200){
            this.$EventBus.$emit("getUserMessage", msg)
          }
          else if (msg.code == 300) {
            let requests = this.requests
            let index = -1
            for (let i = 0; i < requests.length; i++) {
              if (requests[i].pattern == 0) {
                if (requests[i].from_id == msg.from_id && requests[i].to_id == msg.to_id) {
                  index = i
                }
              } else {
                if (requests[i].from_id == msg.from_id && requests[i].to_id == msg.to_id && requests[i].group_id == msg.group_id) {
                  index = i
                }
              }
            }
            if (index != -1) {
              requests[index] = msg
            } else {
              requests.unshift(msg)
            }
            this.$store.commit('setRequests', requests)
            this.newFriend = true
            if(msg.pattern==0&&msg.status==1){
              let params = new FormData
              params.append('id', msg.to_id)
              this.$instance.post('user/getById', params).then(res => {
                if (res.code == 200) {
                  let friends = this.friends
                  let online=this.online
                  friends.push(res.data)
                  online.push(msg.to_id)
                  this.$store.commit('setFriends', friends)
                  this.$store.commit('setOnline', online)
                }
              })
            }
            else if (msg.pattern == 1 && msg.status == 1) {
              let params = new FormData
              params.append('id', msg.to_id)
              this.$instance.post('user/getGroupById', params).then(res => {
                if (res.code == 200) {
                  let groups = this.groups
                  groups.push(res.data)
                  this.$store.commit('setGroups', groups)
                }
              })
            }
          } else if (msg.code == 400) {
            if(msg.status==0){
              this.$EventBus.$emit("getUserCall", msg)
            }
            else if(msg.status == 3 ||msg.status == 2 || msg.status==4||msg.status==5 || msg.status == 6){
              this.$EventBus.$emit("userRefuseCall", msg)
            }
          }
        }
        this.client.onclose = (e) => {
          console.log(e)
          clearInterval(this.heartBit)
          this.$store.commit('setConn', false)
        }
        this.client.onerror = (e) => {
          this.$store.commit('setConn', false)
          console.log(e)
        }
        this.heart()
      }
    },
    heart() {
      clearInterval(this.heartBit)
      this.heartBit = setInterval(() => {
        try {
          const datas = { code: 0 }
          this.client.send(JSON.stringify(datas))
        } catch (err) {

        }
      }, this.timeout)
    },
    toMy() {
      this.$router.push('/home/my') 
    },
    addFriend(item) {
      const data = {
        code: 300,
        pattern: 0,
        from_id: this.userInfo.id,
        to_id: item.id,
        from_avatar:this.userInfo.avatar,
        to_avatar: item.avatar,
        from_name: this.userInfo.nick_name,
        to_name: item.nick_name,
        status: 0
      }
      let requests=this.requests
      requests.unshift(data)
      this.$store.commit('setRequests', requests)
      this.client.send(JSON.stringify(data))
    },
    addGroup(item) {
      const data = {
        code: 300,
        pattern: 1,
        from_id: this.userInfo.id,
        to_id: item.id,
        group_id: item.id,
        from_avatar: this.userInfo.avatar,
        group_avatar: item.avatar,
        from_name: this.userInfo.nick_name,
        group_name: item.name,
        status: 0
      }
      let requests = this.requests
      requests.unshift(data)
      this.$store.commit('setRequests', requests)
      this.client.send(JSON.stringify(data))
    }
  }
}
</script>
<style lang="scss" scoped>
.layout {
  background: var(--vs-theme-bg);
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  min-height: 100vh;
}

.container {
  width: 1100px;
  height: 700px;
  border-radius: 10px;
  margin: 0 auto;
  display: flex;
  flex-direction: row;
  background-color: var(--vs-theme-layout);
  padding: 5px;
  overflow: hidden;
}

.avatar:hover {
  cursor: pointer;
}

.main {
  width: 100%;
  height: 100%;
  margin-left: 5px;
}
</style>