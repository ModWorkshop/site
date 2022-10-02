<template>
    <md-editor v-model="mod.license" :label="$t('license')" rows="8">
        <template #desc>
            <a href="https://choosealicense.com/" target="_blank">Can't choose?</a>
        </template>
    </md-editor>

    <a-select v-if="isModerator" v-model="mod.game_id" label="Game" placeholder="Select a game" :options="games.data"/>

    <a-input v-model="mod.short_desc" label="Short Description" type="textarea" rows="2" maxlength="150" desc="Maximum of 150 letters. Will be shown in places like Discord, and when hovering mods"/>

    <a-input v-model="mod.comments_disabled" type="checkbox" :label="$t('disable_comments')"/>

    <a-alert class="w-full" color="danger" :title="$t('danger_zone')">
        <div>
            <a-button color="danger" @click="deleteMod">{{$t('delete')}}</a-button>
        </div>
    </a-alert>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Game, Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const yesNoModal = useYesNoModal();
const router = useRouter();
const { hasPermission } = useStore();

const isModerator = computed(() => hasPermission('manage-mods', props.mod.game));

const { data: games } = await useFetchMany<Game>('games', { immediate: isModerator.value });

function deleteMod() {
    yesNoModal({
        desc: 'Are you sure you want to delete the mod?',
        async yes() {
            await useDelete(`mods/${props.mod.id}`);
            router.push('/');
        }
    });
}
</script>