<template>
    <a-user v-if="type == 'user' && typeof object == 'object'" :user="(object as User)" :avatar="false"/>
    <template v-else>
        <NuxtLink v-if="link" :to="link">{{name}}</NuxtLink>
        <span v-else>{{name}}</span><span v-if="typeHint"> ({{typeHint}})</span>
    </template>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { User } from '~~/types/models';

const props = defineProps({
    type: {
        type: String,
        default: 'err',
    },
    object: [Object, String]
});

const { t } = useI18n();

const name = computed(() => {
    if (!props.object) {
        return null;
    }

    if (typeof props.object == 'string') {
        return props.object;
    }

    return props.object.name || t(props.type).toLowerCase();
});

const typeHint = computed(() => {
    const n = name.value;

    if (typeof props.object == 'string') {
        return null;
    }
    
    const type = t(props.type).toLowerCase();
    if (n !== type) {
        return type;
    }
});

const link = computed(() => typeof props.object == 'object' ? getObjectLink(props.type, props.object) : null);
</script>