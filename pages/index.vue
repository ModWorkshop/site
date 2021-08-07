<template>
    <div>
        OK
    </div>
</template>
<script>
export default {
    async fetch() {
        const user = await this.$axios.get('/user').then(res => res.data);
        console.log(user);
    },
}
</script>
<style>
    
</style>