<template>
    <flex column gap="3">
        <h2>{{$t('upgrade_user')}}</h2>
        <a-user-select v-model="user" :label="$t('user')"/>
        <a-duration v-model="banDuration" :label="$t('duration')"/>
        <a-button class="mr-auto" :disabled="!user" @click="upgrade">{{$t('upgrade')}}</a-button>

        <h2>{{$t('supporters')}}</h2>
        <a-list url="supporters" query @fetched="(items: Paginator<Supporter>) => supporters = items">
            <template #item="{ item }">
                <flex class="list-button">
                    <a-user :user="item.user"/>
                    <a-button class="ml-auto" icon="trash" @click="removeSupporter(item)">{{$t('stop')}}</a-button>
                </flex>
            </template>
        </a-list>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { Supporter } from '~~/types/models';
import { Paginator } from '~~/types/paginator';

useNeedsPermission('manage-users');

const user = ref(null);
const banDuration = ref(null);
const supporters = ref<Paginator<Supporter>>();

const yesNoModal = useYesNoModal();
const { showToast } = useToaster();

async function upgrade() {
    try {
        const supporter = await usePost<Supporter>('supporters', {
            user_id: user.value,
            duration: banDuration.value
        });
        supporters.value.data.unshift(supporter);
    } catch (error) {
        showToast({
            title: 'Error',
            color: 'danger',
            desc: 'Could not add supporter. Perhaps they already have membership?'
        });
    }
}

function removeSupporter(supporter: Supporter) {
    yesNoModal({
        title: 'Stop supporter status of user?',
        desc: 'You should do this only if the user got refunded or asked for it!',
        async yes() {
            await useDelete(`supporters/${supporter.id}`);
            remove(supporters.value.data, supporter);
        }
    });
}
</script>