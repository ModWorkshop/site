<template>
    <m-flex gap="2" column>
        <m-input v-model="user.extra!.auto_subscribe_to_mod" type="checkbox" :label="$t('auto_subscribe_to_mod')"/>
        <m-input v-model="user.extra!.auto_subscribe_to_thread" type="checkbox" :label="$t('auto_subscribe_to_thread')"/>
    </m-flex>
</template>

<script setup lang="ts">
import type { User } from '~/types/models';

const { user } = defineProps<{
    user: User
}>();
</script>