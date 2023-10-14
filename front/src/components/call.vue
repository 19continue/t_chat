<template>
    <div>
        <audio style="display: none;" ref="localAudio"></audio>
        <div v-if="callId.type == 'caller'" :style="'position: relative;height:' + height + 'px;width:' + width + 'px;'">
            <el-avatar v-if="!connect" shape="square" :size="width / 5" fit="cover" :src="callId.avatar"
                :style="'position: absolute;width: 50px;height: 50px;left:' + (width / 20) + 'px;top:' + (height / 20) + 'px;z-index: 999999;'"></el-avatar>
            <video :style="'width:' + localVideoWedth + 'px;height:' + localVideoHeight + 'px;position: absolute;left:'+ localVideoLeft +'px;top:'+ localVideoTop+'px;z-index: 999997;'"
                ref="localCameraVideo"></video>
            <video v-show="connect"
                :style="'width:' + width + 'px;height:' + height + 'px;position: absolute;z-index: 999996;'"
                ref="remoteCameraVideo"></video>
            <vs-button color="danger" @click="cancel"
                :style="'position: absolute;left:' + (width / 2 - 30) + 'px;top:' + (height * 3 / 4) + 'px;font-size: 1rem;z-index: 999998;'">
                <i class="ri-phone-fill" style="transform: rotate(-45deg);"></i>
            </vs-button>
        </div>
        <div v-if="callId.type == 'reciever'" :style="'position: relative;height:' + height + 'px;width:' + width + 'px;'">
            <el-avatar v-if="!connect" shape="square" :size="width / 5" fit="cover" :src="callId.avatar"
                :style="'position: absolute;width: 50px;height: 50px;left:' + (width * 2 / 5) + 'px;top:' + (height / 5) + 'px;z-index: 999999;'"></el-avatar>
            <video v-show="connect"
                :style="'width:' + (width / 4) + 'px;height:' + (height / 4) + 'px;position: absolute;left:' + (width * 1 / 50) + 'px;top:+' + (height * 1 / 40) + 'px;z-index: 999997;'"
                ref="localCameraVideo"></video>
            <video v-show="connect"
                :style="'width:' + width + 'px;height:' + height + 'px;position: absolute;z-index: 999996;'"
                ref="remoteCameraVideo"></video>
            <vs-button id="btn1" color="danger" v-if="connect"  @click="hangUp"
                :style="'position: absolute;left:' + (width / 2 - 30) + 'px;top:' + (height * 3 / 4) + 'px;font-size: 1rem;z-index: 999998;'">
                 <i class="ri-phone-fill" style="transform: rotate(-45deg);"></i>
            </vs-button>
            <vs-button id="btn2" v-if="!connect" color="danger" @click="refuse"
                    :style="'position: absolute;left:' + (width / 4 ) + 'px;top:' + (height * 3 / 4) + 'px;font-size: 1rem;z-index: 999998;'">
                    <i class="ri-phone-fill" style="transform: rotate(-45deg);"></i>
            </vs-button>
            <vs-button v-if="!connect" color="success" @click="accept"
                :style="'position: absolute;left:' + (width * 4 / 7) + 'px;top:' + (height * 3 / 4) + 'px;font-size: 1rem;z-index: 999998;'">
                <i class="ri-phone-fill"></i> 
            </vs-button>
        </div>
    </div>
</template>

<script>
import Peer from 'peerjs';
import { mapGetters } from 'vuex';
export default {
    name: 'call',
    props: {
        callId: {
            type: Object,
            default: null
        },
        width: {
            type: Number,
            default: 0
        },
        height: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            localCameraVideo: null,
            remoteCameraVideo: null,
            localAudio: null,
            userMediaStream: null,
            connect: false,
            peer: null,
            peerId: '',
            localVideoHeight: this.height,
            localVideoWedth:this.width,
            localVideoLeft:0,
            localVideoTop:0
        }
    },
    computed: {
        ...mapGetters([
            'userInfo',
        ])
    },
    mounted() {
        this.localCameraVideo = this.$refs.localCameraVideo
        this.remoteCameraVideo = this.$refs.remoteCameraVideo
        console.log(this.localCameraVideo)
        this.localAudio = this.$refs.localAudio
        if (this.callId == null) {
            return
        } else if (this.callId.type == 'caller') {
            this.getUserMedia({ audio: true, video: true }).then(userMedia => {
                this.userMediaStream = userMedia
                this.localCameraVideo.srcObject = userMedia
                this.localCameraVideo.play()
                this.localAudio.src = 'http://127.0.0.1:8787/mp3/call.mp3'
                this.localAudio.loop = true
                this.localAudio.play()
            })
            this.createPeer()
        } else if (this.callId.type == 'reciever') {
            this.peer = new Peer()
            this.peer.on('open', peerId => {
                console.log('peer', peerId)
                this.peerId = peerId
                this.peer.on('close', () => {
                    let data = {
                        code: 400,
                        type: 7,
                        pattern: 0,
                        from_id: this.callId.from_id,
                        to_id: this.callId.to_id,
                        peerId: peerId,
                        status: 2
                    }
                    this.$EventBus.$emit("sendMessage", data)
                })
            })
            this.localAudio.src = 'http://127.0.0.1:8787/mp3/call.mp3'
            this.localAudio.loop = true
            this.localAudio.play()
        }
    },
    destroyed() {
        if(this.localCameraVideo.srcObject!=null){
            this.userMediaStream.getVideoTracks().forEach(track => {
                track.stop()
            });
            this.localCameraVideo.srcObject = null
        }
    },
    methods: {
        getUserMedia(constrains) {
            if (window.navigator.mediaDevices.getUserMedia) {
                return window.navigator.mediaDevices.getUserMedia(constrains)
            } else if (window.navigator.webkitGetUserMedia) {
                return window.navigator.webkitGetUserMedia(constrains)
            } else if (window.navigator.mozGetUserMedia) {
                return window.navigator.mozGetUserMedia(constrains)
            } else if (window.navigator.getUserMedia) {
                return window.navigator.getUserMedia(constrains)
            }
        },
        createPeer() {
            this.peer = new Peer()
            this.peer.on('open', peerId => {
                console.log('peer', peerId)
                this.peerId = peerId
                let data = {
                    code: 400,
                    type: 6,
                    pattern: 0,
                    from_id: this.callId.from_id,
                    to_id: this.callId.to_id,
                    peerId: peerId,
                    status: 0,
                    avatar: this.userInfo.avatar
                }
                this.peer.on('close', () => {
                    let data = {
                        code: 400,
                        type: 6,
                        pattern: 0,
                        from_id: this.callId.from_id,
                        to_id: this.callId.to_id,
                        peerId: peerId,
                        status: 5
                    }
                    this.$EventBus.$emit("sendMessage", data)
                })
                this.peer.on('call',conn=>{
                    this.audioStop()
                    conn.answer(this.userMediaStream)
                    conn.on('stream',remoteMedia=>{
                        if (!this.connect) {
                            this.localVideoWedth = this.width / 4
                            this.localVideoHeight = this.height / 4
                            this.localVideoLeft=this.width/50
                            this.localVideoTop=this.height/40 
                            this.remoteCameraVideo.srcObject=remoteMedia
                            this.remoteCameraVideo.play()
                            this.connect = true
                        }
                    })
                })
                this.$EventBus.$emit("sendMessage", data)
            })
        },
        accept(){
            this.getUserMedia({ audio: true, video: true }).then(userMedia => {
                this.audioStop()
                this.userMediaStream = userMedia
                this.localCameraVideo.srcObject = userMedia
                this.localCameraVideo.play()
                const call=this.peer.call(this.callId.peerId, userMedia)
                call.on('stream',remoteMedia=>{
                    if(!this.connect){
                        this.remoteCameraVideo.srcObject = remoteMedia
                        this.remoteCameraVideo.play()
                        this.connect = true
                    }
                })
            })
        },
        refuse(){
            let data = {
                code: 400,
                type: 7,
                pattern: 0,
                from_id: this.callId.from_id,
                to_id: this.callId.to_id,
                status: 2
            }
             this.$EventBus.$emit("sendMessage", data)
            this.$emit('close')
        },
        hangUp(){
            let data = {
                code: 400,
                type: 7,
                pattern: 0,
                from_id: this.callId.from_id,
                to_id: this.callId.to_id,
                status: 6
            }
            this.$EventBus.$emit("sendMessage", data)
            this.userMediaStream.getVideoTracks().forEach(track => {
                track.stop()
            });
            this.localCameraVideo.srcObject = null
            this.$emit('close')
        },
        cancel(){
            let data=null
            if(!this.connect){
                data = {
                    code: 400,
                    type: 6,
                    pattern: 0,
                    from_id: this.callId.from_id,
                    to_id: this.callId.to_id,
                    status: 4
                }
            }
            else{
                data = {
                    code: 400,
                    type: 6,
                    pattern: 0,
                    from_id: this.callId.from_id,
                    to_id: this.callId.to_id,
                    status: 5
                }
            }
            this.$EventBus.$emit("sendMessage", data)
            this.userMediaStream.getVideoTracks().forEach(track => {
                track.stop()
            });
            this.localCameraVideo.srcObject = null
            this.$emit('close')
        },
        audioStop(){
            this.localAudio.pause()
            this.localAudio.src = null
        }
    }
}
</script>

<style lang="scss" scoped></style>
