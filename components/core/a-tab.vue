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

<script setup>
const { name } = defineProps({
    name: String,
    title: String
});

const tabState = inject('tabState');
const show = computed(() => name == tabState.current);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>