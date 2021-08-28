<template>
    <div 
        role="tabpanel"
        :aria-hidden="!show" 
        :aria-labelledby="`${name}-tab-link`"
        :id="`${name}-tab-panel`"
        tabindex="0"
        v-show="show" 
        class="tab-panel">
        <slot/>
    </div>
</template>

<script>
    export default {
        props: {
            name: String,
            title: String,
        },
        computed: {
            show() {
                return this.name == this.tabState.current;
            }
        },
        inject: ['tabState']
    }
</script>

<style scoped>

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>