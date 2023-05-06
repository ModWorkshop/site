<template>
    <div>
        <component :is="roleCanBeEdited ? NuxtLink : 'span'"
            class="list-button flex gap-2 items-center"
            :to="`${adminUrl}/${role.id}`" 
            :draggable="(role.id != 1 || game) && roleCanBeEdited"
            @dragstart="$emit('dragstart')"
            @dragend="$emit('dragend')"
        >
            <span v-if="!roleCanBeEdited">🔒</span>
            <a-icon v-else-if="role.id != 1 || game" icon="grip-lines"/>
            <flex column>
                <span class="my-auto">
                    <a-tag :color="role.color">{{role.name}}</a-tag>
                </span>
                <small v-if="role.id == 1 && !game">{{$t('members_role_desc')}}</small>
            </flex>
            <span v-if="role.is_vanity" class="ml-auto">✨</span>
        </component>
        <div :class="{ hovering, dropzone: true }"/>
    </div>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import { Role, Game } from '../../../types/models';

const props = defineProps<{
    role: Role,
    game?: Game,
    hovering?: boolean
}>();

defineEmits(['dragstart', 'dragend']);

const NuxtLink = resolveComponent('NuxtLink');
const { user, hasPermission } = useStore();
const adminUrl = computed(() => getAdminUrl('roles', props.game));
const highestRoleOrder = computed(() => props.game ? props.game.user_data!.highest_role_order : user!.highest_role_order);

const roleCanBeEdited = computed(() => {
    const game = props.game;
    const role = props.role;

    //A user that can manage roles globally, can essentially edit any game role.
    if (hasPermission('manage-roles', game)) {
        return true;
    }

    if (game) {
        return highestRoleOrder.value && (highestRoleOrder.value > role.order);
    } else {
        return (role.id !== 1 || hasPermission('admin')) && (highestRoleOrder.value && highestRoleOrder.value > role.order);
    }
});
</script>