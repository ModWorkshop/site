<template>
    <simple-resource-form v-model="userCase" url="user-cases" :create-url="createUrl">
        <a-input v-model="userCase.active" type="checkbox" :label="$t('active')"/>
        <a-duration v-model="userCase.expire_date" :label="$t('duration')"/>
        <a-input v-model="userCase.reason" type="textarea" :label="$t('reason')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Game, UserCase } from '~~/types/models';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const createUrl = computed(() => getGameResourceUrl('user-cases', props.game));

const { data: userCase } = await useEditResource<UserCase>('case', 'user-cases');
</script>