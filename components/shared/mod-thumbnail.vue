<template>
    <picture class="card-img-top overflow-hidden">
        <source :data-srcset="mod.thumbnail" :type="mod.thumbnail_type == 'webp' && 'image/webp'">
        <source data-srcset="/mws/assets/images/nopreview.png" type="image/png">
        <img src="http://localhost:8001/storage/assets/nopreview.png" :class="{lazy: true, 'ratio-image': true, nsfw: mod.is_nsfw && !mod.uses_default_thumbnail}" alt="thumbnail">
    </picture>
</template>
<script>
export default {
    props: {
        mod: Object
    }
}
</script>