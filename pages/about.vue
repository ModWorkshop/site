<template>
    <div class="bg-dark2 p-3 text-center">
        <a-img src="assets/mws_logo_white.svg" class="m-auto" width="128" height="128"/>
        <h4 class="text-primary m-auto">{{$t('about_mws')}}</h4>
        <br>
        <Markdown :text="$t('about_desc')"/>
    </div>
</template>

<script setup>
</script>

<style>
.bioimage {
    width: 120px;
    height: 120px;
    margin-bottom: 10px;
}
</style>