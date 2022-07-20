<template>
    <div 
        v-show="show"
        :id="`${name}-tab-panel`" 
        role="tabpanel"
        :aria-hidden="!show"
        :aria-labelledby="`${name}-tab-link`"
        tabindex="0"
        class="tab-panel"
	>
        <slot/>
    </div>
</template>

<script setup>
const props = defineProps({
    name: String,
    title: String
});

const tabState = inject('tabState');
const show = computed(() => props.name == tabState.current);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>