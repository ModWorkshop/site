<template>
    <simple-resource-form v-model="userCase" url="user-cases" :game="game">
        <m-input v-model="userCase.active" type="checkbox" :label="$t('active')"/>
        <m-duration v-model="userCase.expire_date" :label="$t('duration')"/>
        <m-input v-model="userCase.reason" type="textarea" :label="$t('reason')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import type { Game, UserCase } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const { data: userCase } = await useEditResource<UserCase>('case', 'user-cases');
</script>