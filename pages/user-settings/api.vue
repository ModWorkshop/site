<template>
    <m-flex column>
        <div>{{ $t('api_access_desc') }}</div>
        <m-input type="checkbox" :label="true">
            <template #label>
                <span>
                    <i18n-t keypath="api_access_checkbox" scope="global">
                        <template #here>
                            <NuxtLink to="/document/api-tos">{{$t('here')}}</NuxtLink>
                        </template>
                    </i18n-t>
                </span>
            </template>
        </m-input>
        <m-button class="mx-auto">{{$t('generate_api_key')}}</m-button>
        <m-input v-if="user.api_key" v-model="user.api_key" readonly :label="$t('your_api_key')"/>
    </m-flex>
</template>

<script setup lang="ts">
import type { UserForm } from '~~/types/models';

const props = defineProps<{
    user: UserForm
}>();

props.user.api_key = 'xd';
</script>