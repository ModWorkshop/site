<template>
    <div>
        <div>dasd</div>
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