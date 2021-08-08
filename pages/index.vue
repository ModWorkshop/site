<template>
    <div>
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
    async fetch() {
        this.mods = await this.$axios.get('/mods').then(res => res.data);
        console.log(this.mods);
    },
}
</script>
<style>
    
</style>