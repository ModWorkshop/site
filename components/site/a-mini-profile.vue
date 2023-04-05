<template>
    <div>
        <Suspense>
            <flex style="width: 350px;">
                <content-block alt-background padding="5" grow>
                    <span class="text-xl">
                        <a-user :user="user" avatar-size="lg" :mini-profile="false"/>
                    </span>
                    <role-selector :user="user"/>
                </content-block>
            </flex>
        </Suspense>
    </div>
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

defineProps<{
    user: User
}>();
</script>