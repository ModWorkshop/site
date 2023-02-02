<template>
    <flex column>
        <div>{{ $t('api_access_desc') }}</div>
        <a-input type="checkbox" :label="true">
            <template #label>
                <span>
                    <i18n-t keypath="api_access_checkbox">
                        <template #here>
                            <NuxtLink to="/docs/api-tos">{{$t('here')}}</NuxtLink>
                        </template>
                    </i18n-t>
                </span>
            </template>
        </a-input>
        <a-button class="mx-auto">{{$t('generate_api_key')}}</a-button>
        <a-input v-if="user.api_key" v-model="user.api_key" readonly :label="$t('your_api_key')"/>
    </flex>
</template>

<script setup lang="ts">
import { UserForm } from '~~/types/models';

const props = defineProps<{
    user: UserForm
}>();

props.user.api_key = 'xd';
</script>