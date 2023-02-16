<template>
    <div>
        <flex>
            <a-input v-model="query" :label="$t('search')"/>
            <a-button class="mt-auto" :to="`/admin/${url}/new`">{{ $t('new') }}</a-button>
        </flex>
        <a-items v-model:page="page" :loading="loading" :items="roles">
            <template #items="{ items }">
                <TransitionGroup name="list">
                    <div v-for="[, item] of Object.entries(roles.data)" :key="item.id" @drop="onDrop()" @dragenter.prevent="showDropHint(item)" @dragover.prevent="showDropHint(item)">
                        <component :is="roleCanBeEdited(item) ? NuxtLink : 'span'"
                            class="list-button flex gap-2 items-center"
                            :to="`/admin/${url}/${item.id}`" 
                            :draggable="item.id != 1 && roleCanBeEdited(item)"
                            @dragstart="startDrag(item)"
                            @dragend="stopDrag"
                        >
                            <span v-if="!roleCanBeEdited(item)">🔒</span>
                            <a-icon v-else-if="item.id != 1" icon="grip-lines"/>
                            <flex column>
                                <span class="my-auto">
                                    <a-tag :color="item.color">{{item.name}}</a-tag>
                                </span>
                                <small v-if="item.id == 1">{{$t('members_role_desc')}}</small>
                            </flex>
                            <span v-if="item.is_vanity" class="ml-auto">✨</span>
                        </component>
                        <div :class="{ hovering: hoveringDrag == item, dropzone: true }"/>
                    </div>
                </TransitionGroup>
            </template>
        </a-items>
    </div>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Game, Role } from '~~/types/models';

const props = defineProps<{
    game?: Game
}>();

useNeedsPermission('manage-roles');
const NuxtLink = resolveComponent('NuxtLink');

const route = useRoute();
const gameId = route.params.game;

const { user, hasPermission } = useStore();

const page = useRouteQuery('page', 1);
const query = useRouteQuery('query');
const url = computed(() => gameId ? `games/${gameId}/roles` : 'roles');

const { data: roles, loading } = await useWatchedFetchMany<Role>(url.value, { page, query });

const rolesNoMember = computed(() => gameId ? roles.value.data : roles.value.data.filter(role => role.id !== 1));

const draggedItem = ref(null);
const hoveringDrag = ref(null);

function roleCanBeEdited(role: Role) {
    if (gameId) {
        return props.game.user_data.highest_role_order > role.order;
    } else {
        return (role.id !== 1 || hasPermission('admin')) && user.highest_role_order > role.order;
    }
}

function showDropHint(belowRole: Role) {
    if (user.highest_role_order > ((belowRole.order || 1001) - 2)) {
        hoveringDrag.value = belowRole;
    }
}

async function onDrop() {
    if (draggedItem.value && hoveringDrag.value) {
        draggedItem.value.order = (hoveringDrag.value.order ?? 1002) - 1;

        await usePatch(`${url.value}/${draggedItem.value.id}`, { order: draggedItem.value.order });

        const newRoles = rolesNoMember.value.sort((a,b) => b.order - a.order);

        let nextOrder = 1000;
        for (const role of newRoles) {
            role.order = nextOrder;
            nextOrder -= 2;
        }

        if (!gameId) {
            newRoles.unshift(roles.value.data[0]);
        }
        roles.value.data = newRoles;

    }
}

function startDrag(item) {
    draggedItem.value = item;
}

function stopDrag() {
    draggedItem.value = null;
    hoveringDrag.value = null;
}
</script>

<style>
.dropzone {
    transition: height ease 0.2s;
    height: 0;
}

.hovering {
    margin-top: 4px;
    transition: height ease 0.2s;
    background: var(--dropdown-hover-bg);
    height: 32px !important;
}

.list-move, /* apply transition to moving elements */
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* ensure leaving items are taken out of layout flow so that moving
   animations can be calculated correctly. */
.list-leave-active {
  position: absolute;
}
</style>