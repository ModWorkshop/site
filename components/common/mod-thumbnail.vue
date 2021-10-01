<template>
    <picture class="card-img-top overflow-hidden">
        <source v-if="mod.thumbnail" :srcset="mod.thumbnail" :type="mod.thumbnail_type == 'webp' && 'image/webp'">
        <source srcset="http://localhost:8000/storage/assets/nopreview.png" type="image/png">
        <img src="http://localhost:8000/storage/assets/nopreview.png" :class="{'ratio-image': true, nsfw: mod.is_nsfw && !mod.uses_default_thumbnail}" alt="thumbnail">
    </picture>
</template>
<script>
export default {
    props: {
        mod: Object
    }
};
</script>