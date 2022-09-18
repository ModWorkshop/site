<template>
    <md-editor v-model="mod.license" :label="$t('license')" rows="8">
        <template #desc>
            <a href="https://choosealicense.com/" target="_blank">Can't choose?</a>
        </template>
    </md-editor>

    <a-input v-model="mod.short_desc" label="Short Description" type="textarea" rows="2" maxlength="150" desc="Maximum of 150 letters. Will be shown in places like Discord, and when hovering mods"/>

    <a-input v-model="mod.comments_disabled" type="checkbox" :label="$t('disable_comments')"/>

    <a-alert class="w-full" color="danger" :title="$t('danger_zone')">
        <div>
            <a-button color="danger" @click="deleteMod">{{$t('delete')}}</a-button>
        </div>
    </a-alert>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const { init: openModal } = useModal();
const router = useRouter();

function deleteMod() {
    openModal({
        message: 'Are you sure you want to delete the mod?',
        async onOk() {
            await useDelete(`mods/${props.mod.id}`);
            router.push('/');
        }
    });
}
</script>