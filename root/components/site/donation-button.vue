<template>
    <iframe 
        v-if="link && type == 'github'"
        :src="`https://github.com/sponsors/${link.match(github)![1]}/button`"
        height="32"
        width="114"
        style="border: 0; border-radius: 6px;"
    />
    <NuxtLink v-else-if="link" :to="link">
        <m-img :src="image" class="donation-button" is-asset/>
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
const paypalBtn = /(?:https:\/\/)?(?:www\.)?paypal(?:\.me|\.com)\/donate\/\?hosted_button_id=(\w+)/;
const paypal = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
const github = /(?:https:\/\/)?(?:www\.)?github\.com\/sponsors\/(\w+)/;

const type = computed(() => {
    const l = props.link;
    if (l) {
        if (kofi.test(l)) {
            return 'kofi';
        } else if (bmc.test(l)) {
            return 'bmc';
        } else if (paypalme.test(l)) {
            return 'paypalme';
        } else if (paypal.test(l)) {
            return 'paypal';
        } else if (paypalBtn.test(l)) {
            return 'paypalBtn';
        } else if (github.test(l)) {
            return 'github';
        }
    }
});

const link = computed(() => {
    const l = props.link;

    if (!l) {
        return null;
    }

    switch(type.value) {
        case 'kofi':
            return `https://ko-fi.com/${l.match(kofi)![1]}`;
        case 'bmc':
            return `https://buymeacoffee.com/${l.match(bmc)![1]}`;
        case 'paypalme':
            return `https://paypal.me/${l.match(paypalme)![1]}`;
        case 'paypal':
            return `https://www.paypal.com/donate/?business=${l}`;
        case 'paypalBtn':
            return `https://www.paypal.com/donate/?hosted_button_id=${l.match(paypalBtn)![1]}`;
        case 'github':
            return `https://github.com/sponsors/${l.match(github)![1]}`;
    }
});

const image = computed(() => {
    const l = props.link;
    if (l) {
        if (kofi.test(l)) {
            return `kofi.png`;
        } else if (bmc.test(l)) {
            return `buymeacoffee.png`;
        } else if (paypalme.test(l) || paypal.test(l) || paypalBtn.test(l)) {
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