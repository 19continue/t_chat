<template>
  <div class="layout">
    <div :class="classList" id="container">
      <div class="form-container sign-up">
        <div class="form">
          <h1>创建一个账号</h1>
          <div class="social-icons">
            <a href="" class="icon">
              <i class="fa-brands fa-google-plus-g"></i>
            </a>
            <a href="" class="icon">
              <i class="fa-brands fa-facebook-f"></i>
            </a>
            <a href="" class="icon">
              <i class="fa-brands fa-github"></i>
            </a>
            <a href="" class="icon">
              <i class="fa-brands fa-linkedin-in"></i>
            </a>
          </div>
          <vs-input color="#60B2FD" v-model="registerForm.phone" label-placeholder="手机号" block>
          </vs-input>
          <vs-input color="#60B2FD" v-model="registerForm.nickName" label-placeholder="呢称" style="margin-top: 1.1rem;" block>
          </vs-input>
          <vs-input color="#60B2FD" v-model="registerForm.password" type="password" :progress="registerForm.passProgress" label-placeholder="密码" :visiblePassword="registerForm.passVisible"
            icon-after
            @click-icon="registerForm.passVisible = !registerForm.passVisible" style="margin-top: 1.1rem;" block>
          <template #icon>
            <i v-if="!registerForm.passVisible"  class='ri-eye-off-fill'></i>
            <i v-else class="ri-eye-fill"></i>
          </template>
          </vs-input>
          <vs-button color="#60B2FD" style="padding:3px 35px;margin-top: 2rem;" @click="register">注 册</vs-button>
        </div>
      </div>

      <div class="form-container sign-in">
        <div class="form">
          <h1>Tchat</h1>
          <div class="social-icons">
            <a href="" class="icon">
              <i class="fa-brands fa-google-plus-g"></i>
            </a>
            <a href="" class="icon">
              <i class="fa-brands fa-facebook-f"></i>
            </a>
            <a href="" class="icon">
              <i class="fa-brands fa-github"></i>
            </a>
            <a href="" class="icon">
              <i class="fa-brands fa-linkedin-in"></i>
            </a>
          </div>
          <vs-input color="#60B2FD" v-model="loginForm.account" placeholder="账号" block>
            <template #icon>
              <i class="ri-user-fill"></i>
            </template>
          </vs-input>
          <vs-input color="#60B2FD" v-model="loginForm.password" placeholder="密码" style="margin-top: 0.8rem;" block>
            <template #icon>
              <i class="ri-lock-2-fill"></i>
            </template>
          </vs-input>
          <a href="">忘记密码?</a>
          <vs-button color="#60B2FD" style="padding:3px 35px;" @click="login">登 录</vs-button>
        </div>
      </div>

      <div class="toggle-container">
        <div class="toggle">
          <div class="toggle-panel toggle-left">
            <h1>你好!</h1>
            <p>欢迎来到Tchat</p>
            <button class="hidden" id="login" @click="sign_up">创建新的账号</button>
          </div>

          <div class="toggle-panel toggle-right">
            <h1>尊敬的用户</h1>
            <p>欢迎您加入Tchat!</p>
            <button class="hidden" id="register" @click="sign_in">去登录</button>
          </div>
        </div>
      </div>
    </div>
    <vs-dialog width="550px" not-center v-model="showDialog">
          <template #header>
            <h4 class="not-margin">
              注册成功！
            </h4>
          </template>


          <div class="con-content">
            <p>
              您的账号为 "{{ newAccount }}" 是否该账号登录?
            </p>
          </div>

          <template #footer>
            <div class="con-footer">
              <vs-button @click="toLogin()" dark transparent>
                直接登录
              </vs-button>
              <vs-button @click="showDialog = false" transparent>
                稍后再说
              </vs-button>
            </div>
          </template>
        </vs-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      classList: 'container active',
      loginForm:{
        account: '',
        password: '',
        passVisible: false
      },
      registerForm:{
        phone:'',
        nickName:'',
        password:'',
        passProgress: 0,
        passVisible: false
      },
      showDialog:false,
      newAccount:''
    }
  },
  watch: {
    registerForm: {
      handler(val, old) {
        if(this.registerForm.password.length<=20){
          this.registerForm.passProgress = this.registerForm.password.length * 5
        }else{
          this.registerForm.passProgress =100
        }
      },
      immediate: false,
      deep: true
    }
  },
  methods: {
    sign_up() {
      this.classList = 'container'
    },
    sign_in() {
      this.classList = 'container active'
    },
    login(){
      let params=new FormData();
      params.append('account',this.loginForm.account)
      params.append('password', this.loginForm.password)
      this.$instance.simple('login/byAccount',params).then(res=>{
        if(res.code==200){
          this.$store.commit('setIsLogin', true)
          this.$store.commit('setAccessToken', res.data.access_token)
          this.$store.commit('setRefreshToken', res.data.refresh_token)
          this.$store.commit('setUserInfo', res.data.user)
          this.$store.commit('setAvatar', res.data.user.avatar)
          this.$toast.success(res.msg, {
            position: "top-center",
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: false,
            draggable: true,
            draggablePercent: 1,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            closeButton: "button",
            icon: false,
            rtl: false
          })
          this.getData();
          this.$router.push('/home/chat');
        }else{
          this.$toast.error(res.msg, {
            position: "top-center",
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: false,
            draggable: true,
            draggablePercent: 1,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            closeButton: "button",
            icon: false,
            rtl: false
          })
        }
      })
    },
    toLogin(){
      let params = new FormData();
      params.append('account', this.newAccount)
      params.append('password', this.registerForm.password)
      this.$instance.simple('login/byAccount', params).then(res => {
        if (res.code == 200) {
          this.$store.commit('setIsLogin', true)
          this.$store.commit('setAccessToken', res.data.access_token)
          this.$store.commit('setRefreshToken', res.data.refresh_token)
          this.$store.commit('setUserInfo', res.data.user)
          this.$store.commit('setAvatar', res.data.user.avatar)
          this.$toast.success(res.msg, {
            position: "top-center",
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: false,
            draggable: true,
            draggablePercent: 1,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            closeButton: "button",
            icon: false,
            rtl: false
          })
          this.getData();
          this.$router.push('/home/chat');
        } else {
          this.$toast.error(res.msg, {
            position: "top-center",
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: false,
            draggable: true,
            draggablePercent: 1,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            closeButton: "button",
            icon: false,
            rtl: false
          })
        }
      })
    },
    register(){
      let params=new FormData
      params.append('phone',this.registerForm.phone)
      params.append('nick_name', this.registerForm.nickName)
      params.append('password', this.registerForm.password)
      this.$instance.simple('register/byPhone',params).then(res=>{
        if(res.code==200){
          this.newAccount=res.data
          this.showDialog=true
        }
      })
    },
    getData(){
      this.$instance.get('user/getAllFriend').then(res => {
        this.$store.commit('setFriends', res.data)
      })
      this.$instance.get('user/getAllGroup').then(res => {
        this.$store.commit('setGroups', res.data)
      })
      this.$instance.get('user/getAllRequest').then(res => {
        if (res.code == 200) {
          this.$store.commit('setRequests', res.data);
        }
      })
      this.$instance.get('user/getMessages').then(res=>{
        if(res.code==200){
          this.$store.commit('setMessages', res.data);
        }
      })
      this.$instance.get('user/getNews').then(res => {
        if (res.code == 200) {
          this.$store.commit('setNews', res.data);
        }
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.layout {
  background-color: #c9d6ff;
  background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  min-height: 100vh;
}

.container {
  background-color: #fff;
  border-radius: 30px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, .35);
  position: relative;
  overflow: hidden;
  width: 768px;
  max-width: 100%;
  min-width: 580px;
  min-height: 480px;
}

.container p {
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0.3px;
  margin: 20px 0;
}

.container span {
  font-size: 12px;
}

.container a {
  color: #333;
  font-size: 13px;
  text-decoration: none;
  margin: 15px 0 10px;
}

.container button {
  background-color: #78BAFD;
  color: #fff;
  font-size: 12px;
  padding: 10px 45px;
  border: 1px solid transparent;
  border-radius: 8px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-top: 10px;
  cursor: pointer;
}

.container button.hidden {
  background-color: transparent;
  border-color: #fff;
}


.container .form {
  background-color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  padding: 0 40px;
  height: 100%;
}

.container input {
  background-color: #eee;
  border: none;
  margin: 8px 0;
  padding: 10px 15px;
  font-size: 13px;
  border-radius: 8px;
  width: 100%;
  outline: none;
}

.form-container {
  position: absolute;
  top: 0;
  height: 100%;
  transition: all .6s ease-in-out;
}

.sign-up {
  left: 0;
  width: 50%;
  z-index: 2;
}

.container.active .sign-up {
  transform: translateX(100%);
  opacity: 0;
}

.sign-in {
  left: 0;
  width: 50%;
  opacity: 0;
  z-index: 1;
}

.container.active .sign-in {
  transform: translateX(100%);
  opacity: 1;
  z-index: 5;
  animation: move .8s;
}

@keyframes move {

  0%,
  49.99% {
    opacity: 0;
    z-index: 1;
  }

  50%,
  100% {
    opacity: 1;
    z-index: 5;
  }
}

.social-icons {
  margin: 20px 0;
}

.social-icons a {
  border: 1px solid #ccc;
  border-radius: 20%;
  display: inline-flex;
  justify-content: center;
  align-items: center;
  margin: 0 3px;
  width: 40px;
  height: 40px;
}

.toggle-container {
  position: absolute;
  top: 0;
  left: 50%;
  width: 50%;
  height: 100%;
  overflow: hidden;
  transition: all .6s ease-in-out;
  border-radius: 50px 0 0 30px;
  z-index: 1000;
}

.container.active .toggle-container {
  transform: translateX(-100%);
  border-radius: 0 50px 30px 0;
}

.toggle {
  background-color: #4facfe;
  height: 100%;
  background: linear-gradient(to right, #4facfe 0%, #A7CBFD 100%);
  color: #fff;
  position: relative;
  left: -100%;
  height: 100%;
  width: 200%;
  transform: translateX(0);
  transition: all .6s ease-in-out;
}

.container.active .toggle {
  transform: translateX(50%);
}

.toggle-panel {
  position: absolute;
  width: 50%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  text-align: center;
  top: 0;
  transform: translateX(0);
  transition: all .6s ease-in-out;
}

.toggle-left {
  transform: translateX(-200%);
}

.container.active .toggle-left {
  transform: translateX(0);
}

.toggle-right {
  right: 0;
  transform: translateX(0);
}

.container.active .toggle-right {
  transform: translateX(200%);
}
.con-footer{
    display:flex;
    align-items:center;
    justify-content:flex-end;
}
</style>