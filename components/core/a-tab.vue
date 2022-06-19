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

<script setup>
const { name } = defineProps({
    name: String,
    title: String
});

const tabState = inject('tabState');
const type = inject('type');

const show = computed(() => name == tabState.current);
const shouldExist = computed(() => type == 'route' ? show : true);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>