<template>
    <simple-resource-form v-model="userCase" url="user-cases" :create-url="createUrl" :merge-params="{ can_appeal: userCase.ban?.can_appeal }">
        <a-input v-model="userCase.pardoned" type="checkbox" :label="$t('pardoned')"/>
        <a-input v-if="userCase.pardoned" v-model="userCase.pardon_reason" type="textarea" :label="$t('pardon_reason')" required/>
        <a-duration v-else v-model="userCase.expire_date" label="Duration"/>
        <a-input v-model="userCase.reason" type="textarea" :label="$t('reason')"/>
        <a-input v-if="userCase.ban" v-model="userCase.ban.can_appeal" type="checkbox" :label="$t('can_appeal')"/>
        <template #danger-zone>
            <span v-if="userCase.ban">
                This case is a ban. Deleting will also cancel the ban.
            </span>
        </template>
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