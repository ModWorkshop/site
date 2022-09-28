<template>
    <flex column>
        <a-input v-model="user.name" label="Username"/>
        <a-input v-model="user.unique_name" label="Unique Name" desc="A unique name for your profile and to allow people to mention you."/>
        <a-input v-model="user.donation_url" label="Donation Link" desc="Supports PayPal, Ko-Fi, and Buy Me a Coffee. Shows in your profile and mod pages."/>
        <a-input v-if="user.email || isMe" v-model="user.email" label="Email" :disabled="!isMe"/>
        <template v-if="isMe">
            <h3>Change Password</h3>
            <flex>
                <a-input v-model="user.password" label="New Password" type="password"/>
                <a-input v-model="user.confirm_password" label="Confirm Password" type="password"/>
            </flex>
        </template>
        <a-alert class="w-full" color="danger" :title="$t('danger_zone')">
            <div>
                <a-button color="danger" @click="doDelete">{{$t('delete')}}</a-button>
            </div>
        </a-alert>
    </flex>
</template>

<script setup lang="ts">
import { Ref } from 'vue';
import { useStore } from '~~/store';
import { UserForm } from '~~/types/models';

const props = defineProps<{
    user: UserForm
}>();

const store = useStore();
const yesNoModal = useYesNoModal();
const isMe = inject<Ref<boolean>>('isMe');

async function doDelete() {
    yesNoModal({
        title: 'Are you sure?',
        desc: 'This action is irreversible!',
        async yes() {
            await useDelete(`users/${props.user.id}`);
            await store.logout();
        }
    });
}
</script>