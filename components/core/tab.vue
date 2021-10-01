<template>
    <div 
        role="tabpanel"
        :aria-hidden="!show" 
        :aria-labelledby="`${name}-tab-link`"
        :id="`${name}-tab-panel`"
        tabindex="0"
        v-show="show"
        v-if="shouldExist"
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
            },
            shouldExist() {
                return this.type == 'route' ? this.show : true;
            }
        },
        inject: ['tabState', 'type']
    };
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>