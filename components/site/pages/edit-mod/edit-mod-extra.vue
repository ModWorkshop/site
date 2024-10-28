<template>
    <md-editor v-model="mod.license" :label="$t('license')" rows="8">
        <template #desc>
            <NuxtLink href="https://choosealicense.com/">{{$t('cant_choose_license')}}</NuxtLink>
        </template>
    </md-editor>

    <m-input v-model="mod.short_desc" :label="$t('short_desc')" type="textarea" rows="2" maxlength="150" :desc="$t('short_desc_desc')"/>
    <m-input v-model="mod.donation" :label="$t('donation')"/>
    <m-input v-model="mod.comments_disabled" type="checkbox" :label="$t('disable_comments')"/>

    <m-alert v-if="mod.id && canDelete" class="w-full" color="danger" :title="$t('danger_zone')">
        <div>
            <m-button color="danger" @click="deleteMod">{{$t('delete')}}</m-button>
        </div>
    </m-alert>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import type { Mod } from '~~/types/models';

const mod = defineModel<Mod>({ required: true });

const yesNoModal = useYesNoModal();
const router = useRouter();
const { user } = useStore();
const { t } = useI18n();

const superUpdate = inject<boolean>('canSuperUpdate');

const member = computed(() => user ? mod.value.members.find(member => member.id == user.id) : null);
const canDelete = computed(() => superUpdate || member.value?.level === 'maintainer');

watch(() => mod.value.game_id, () => mod.value.category_id = undefined);

function deleteMod() {
    yesNoModal({
        desc: t('delete_mod_desc'),
        async yes() {
            await deleteRequest(`mods/${mod.value.id}`);
            router.push('/');
        }
    });
}
</script>