<template>
    <div>
        <slot name="title"/>
        <iframe 
            v-if="link && type == 'github'"
            :src="`https://github.com/sponsors/${link.match(donationSites.github)![1]}/button`"
            height="32"
            width="114"
            style="border: 0; border-radius: 6px;"
        />
        <NuxtLink v-else-if="link && image" :to="link">
            <m-img :src="image" class="donation-button" is-asset/>
        </NuxtLink>
        <m-button v-else-if="type" :to="link">{{$t('donate_to_user')}}</m-button>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    link: String
});

const type = computed(() => linkToDonationType(props.link));

const link = computed(() => {
    const l = props.link;

    if (!l) {
        return null;
    }

    switch(type.value) {
        case 'kofi':
            return `https://ko-fi.com/${l.match(donationSites.kofi)![1]}`;
        case 'bmc':
            return `https://buymeacoffee.com/${l.match(donationSites.bmc)![1]}`;
        case 'paypalme':
            return `https://paypal.me/${l.match(donationSites.paypalme)![1]}`;
        case 'paypal':
            return `https://www.paypal.com/donate/?business=${l}`;
        case 'paypalBtn':
            return `https://www.paypal.com/donate/?hosted_button_id=${l.match(donationSites.paypalBtn)![1]}`;
        case 'github':
            return `https://github.com/sponsors/${l.match(donationSites.github)![1]}`;
        case 'boosty':
            return `https://boosty.to/${l.match(donationSites.boosty)![1]}`;
    }
});

const image = computed(() => {
    const l = props.link;
    if (l) {
        if (donationSites.kofi.test(l)) {
            return `kofi.png`;
        } else if (donationSites.bmc.test(l)) {
            return `buymeacoffee.png`;
        } else if (donationSites.paypalme.test(l) || donationSites.paypal.test(l) || donationSites.paypalBtn.test(l)) {
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