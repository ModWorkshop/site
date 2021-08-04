<template>
    <el-dropdown-item v-bind="$props" :type="type">
        <nuxt-link v-if="to" :to="to"><slot/></nuxt-link>
        <a v-else-if="click" @click="click"><slot/></a>
    </el-dropdown-item>
</template>
<script>
export default {
    props: {
        command: String | Number | Object,
        to: String,
        href: String,
        disabled: Boolean,
        type: String,
        icon: String
    },
    methods: {
        click() {
            this.$emit('click');
        }
    }
}
</script>
<style>
    .dropdown-splitter {
        height: 1px;
        margin: 6px;
        background: var(--secondary-text-color);
        opacity: 0.25;
    }
    
    .el-dropdown-menu__item {
        padding: 0;
    }
    .el-dropdown-menu__item a {
        padding: 0 20px;
        display: block;
        color: var(--text-color);
    }
</style>