<template>
    <div id="layout">
        <div>
           <vs-switch v-model="themeButton" dark>
                <template #circle>
                    <i v-if="!themeButton" class="ri-sun-fill"></i>
                    <i v-else class="ri-moon-fill"></i>
                </template>
            </vs-switch>
        </div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
    name: 'chat',
    data() {
        return {
            themeButton:false
        }
    },
    computed: {
        ...mapGetters([
            'theme'
        ])
    },
    watch: {
        themeButton: {
            handler(val, old) {
                if(val){
                    this.$store.commit('setTheme','dark')
                }else{
                    this.$store.commit('setTheme', '')
                }
            },
            immediate: false,
            deep: true
        }
    },
    mounted(){
        if(this.theme=='dark'){
            this.themeButton=true
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
    background-color: var(--vs-theme-bg2);
    border: 1px solid black;
}

::-webkit-scrollbar {
    width: 10px;
    background-color: transparent !important;
}

::-webkit-scrollbar-thumb {
    background-color: #F0F3F4;
    border-radius: 5px;
    height: 50%;
}

::-webkit-scrollbar-button {
    display: none !important;
}


</style>