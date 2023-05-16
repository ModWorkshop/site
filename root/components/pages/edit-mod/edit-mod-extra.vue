<template>
    <md-editor v-model="mod.license" :label="$t('license')" rows="8">
        <template #desc>
            <a href="https://choosealicense.com/" target="_blank">{{$t('cant_choose_license')}}?</a>
        </template>
    </md-editor>

    <a-select v-if="isModerator" v-model="mod.game_id" :label="$t('game')" :options="games?.data"/>

    <a-input v-model="mod.short_desc" :label="$t('short_desc')" type="textarea" rows="2" maxlength="150" :desc="$t('short_desc_desc')"/>

    <a-input v-model="mod.comments_disabled" type="checkbox" :label="$t('disable_comments')"/>

    <a-alert v-if="canDelete" class="w-full" color="danger" :title="$t('danger_zone')">
        <div>
            <a-button color="danger" @click="deleteMod">{{$t('delete')}}</a-button>
        </div>
    </a-alert>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game, Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const yesNoModal = useYesNoModal();
const router = useRouter();
const { hasPermission, user } = useStore();
const { t } = useI18n();

const superUpdate = inject<boolean>('canSuperUpdate');

const isModerator = computed(() => hasPermission('manage-mods', props.mod.game));
const member = computed(() => user ? props.mod.members.find(member => member.id == user.id) : null);
const canDelete = computed(() => superUpdate || member.value?.level === 'maintainer');

watch(() => props.mod.game_id, () => props.mod.category_id = undefined);

const { data: games } = await useFetchMany<Game>('games', { immediate: isModerator.value });

function deleteMod() {
    yesNoModal({
        desc: t('delete_mod_desc'),
        async yes() {
            await deleteRequest(`mods/${props.mod.id}`);
            router.push('/');
        }
    });
}
</script>