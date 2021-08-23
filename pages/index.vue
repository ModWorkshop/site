<template>
    <div class="content-block-large">
        <mod-list :mods="mods"/>
    </div>
</template>
<script>
export default {
    data() {
        return {
            mods: []
        }
    },
    async asyncData({$axios}) {
        const mods = await $axios.get('/mods').then(res => res.data);
        return { mods };
    },
}
</script>