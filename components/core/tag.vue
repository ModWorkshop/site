<template>
    <span class="tag" :style="{backgroundColor: color}"><slot/></span>
</template>
<script>
export default {
    props: {
        color: String
    }
};
</script>
<style scoped>
    .tag {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        background: var(--primary-color);
        border-radius: var(--border-radius);
    }
</style>