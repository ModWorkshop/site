<template>
    <simple-resource-form v-model="ban" url="bans" :game="game">
        <a-duration v-model="ban.expire_date" :label="$t('duration')"/>
        <a-input v-model="ban.reason" type="textarea" :label="$t('reason')"/>
        <a-input v-model="ban.can_appeal" type="checkbox" :label="$t('can_appeal')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Game, Ban } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const { data: ban } = await useEditResource<Ban>('ban', 'bans');
</script>