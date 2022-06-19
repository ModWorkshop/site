<template>
    <div>
        <span @click="onClick">
            <slot/>
        </span>
        <flex column gap="1" class="dropdown" v-show="show" v-click-outside="onClickOutside">
            <slot name="items"/>
        </flex>
    </div>
</template>
<script setup>
    let show = ref(false);
    let hasClickedButton = false;

    function onClick() {
        show.value = !show.value;
        hasClickedButton = true;
    }

    function onClickOutside() {
        if (!hasClickedButton) {
            show.value = false;
        }
        hasClickedButton = false;
    }
</script>

<style>
    .dropdown {
        margin-top: 0.25rem;
        margin-left: -0.25rem;
        width: 200px;
        position: fixed;
        background-color: var(--dropdown-bg);
        padding: 0.25rem;
    }
</style>