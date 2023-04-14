<template>
    <NuxtLink v-if="link" :to="link">
        <a-img :src="image" class="donation-button" is-asset/>
    </NuxtLink>
</template>

<script setup lang="ts">
const props = defineProps({
    link: String
});

//TODO: improve these regexes because they're probably shit
const bmc = /(?:https:\/\/)?(?:www\.)?buymeacoffee\.com\/(\w+)/;
const kofi = /(?:https:\/\/)?(?:www\.)?ko-fi\.com\/(\w+)/;
const paypalme  = /(?:https:\/\/)?(?:www\.)?paypal\.me\/(\w+)/;
const paypal = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;

const link = computed(() => {
    const l = props.link;
    if (l) {
        if (kofi.test(l)) {
            return `https://ko-fi.com/${l.match(kofi)![1]}`;
        } else if (bmc.test(l)) {
            return `https://buymecoffee.com/${l.match(bmc)![1]}`;
        } else if (paypalme.test(l)) {
            return `https://paypal.me/${l.match(paypalme)![1]}`;
        } else if (paypal.test(l)) {
            return `https://www.paypal.com/donate/?business=${l}`;
        }
    }
});

const image = computed(() => {
    const l = props.link;
    if (l) {
        if (kofi.test(l)) {
            return `kofi.png`;
        } else if (bmc.test(l)) {
            return `buymecoffee.png`;
        } else if (paypalme.test(l) || paypal.test(l)) {
            return `paypal.png`;
        }
    }
});
</script>

<style>
.donation-button {
    width: 150px;
}
</style>