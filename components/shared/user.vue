<template>
    <a :href="`/user/${user.id}`">{{user.name}}</a>
</template>
<script>
export default {
    props: {
        user : Object,
    }
}
</script>