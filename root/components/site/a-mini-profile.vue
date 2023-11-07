<template>
    <div>
        <Suspense>
            <flex style="width: 350px;">
                <content-block alt-background padding="5" grow>
                    <span class="text-xl">
                        <a-user :user="user" avatar-size="lg" :mini-profile="false" show-online-state>
                            <template #details>
                                <span v-if="user.unique_name" class="user-at text-base">@{{user.unique_name}} / ID {{user.id}}</span>
                                <span v-if="!userInvisible" class="text-base">{{user.custom_title}}</span>
                            </template>
                        </a-user>
                    </span>
                    <role-selector :user="user"/>
                </content-block>
            </flex>
        </Suspense>
    </div>
</template>

<script setup lang="ts">
import type { User } from '~~/types/models';

const props = defineProps<{
    user: User
}>();
const userInvisible = computed(() => props.user.invisible);
</script>