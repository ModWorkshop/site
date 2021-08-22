<template>
    <nuxt-link :to="`/user/${user.id}`">{{user.name}}</nuxt-link>
</template>
<script>
export default {
    props: {
        user : Object,
    }
}
</script>