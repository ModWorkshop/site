<template>
    <img :src="src" :class="{'avatar': true, 'avatar-large': large, 'avatar-largest': largest}">
</template>
<script>
export default {
    props: {
        src: String,
        large: Boolean,
        largest: Boolean
    }
}
</script>
<style scoped>
    .avatar {
        object-fit: cover;
        width: 38px;
        height: 38px;
        border-radius: 5%;
    }

    .avatar-large {
        width: 150px;
        height: 150px;
    }
    
    .avatar-largest {
        width: 200px;
        height: 200px;
    }
</style>