<template>
    <m-flex column gap="3">
        <m-flex>
            <m-input v-model="query" :label="$t('search')"/>
            <m-button class="mt-auto" style="margin-bottom: 2px;" :to="`${adminUrl}/new`">{{ $t('new') }}</m-button>
        </m-flex>
        <m-list v-model:page="page" query :loading="loading" :items="roles">
            <template #items>
                <TransitionGroup name="list">
                    <admin-role v-for="[, role] of Object.entries(rolesSorted)"
                        :key="role.id" 
                        :role="role"
                        :game="game"
                        :hovering="hoveringDrag == role"
                        :dragged-item="draggedItem"

                        @drop="onDrop()"
                        @dragenter.prevent="showDropHint(role)"
                        @dragover.prevent="showDropHint(role)"

                        @dragstart="startDrag(role)"
                        @dragend="stopDrag"
                    />
                </TransitionGroup>
            </template>
        </m-list>
    </m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Game, Role } from '~/types/models';

const props = defineProps<{
    game?: Game
}>();

useNeedsPermission('manage-roles', props.game);

const route = useRoute();
const gameId = route.params.game;

const { user, hasPermission } = useStore();

const page = ref(1);
const query = useRouteQuery('query');
const url = computed(() => gameId ? `games/${gameId}/roles` : 'roles');
const adminUrl = computed(() => getAdminUrl('roles', props.game));

const { data: roles, loading } = await useWatchedFetchMany<Role>(url.value, { page, query });

onMounted(() => {
    if (roles.value) {
        calculateHighestOrder();
    }
});

const rolesNoMember = computed(() => (gameId ? roles.value?.data : roles.value?.data.filter(role => role.id !== 1)) ?? []);
const rolesSorted = computed(() => {
    const newRoles = rolesNoMember.value;
    if (!gameId) {
        const member = roles.value?.data.find(role => role.id == 1);
        if (member) {
            newRoles.unshift(member);
        }
    }

    return newRoles;
});

const draggedItem = ref<Role>();
const hoveringDrag = ref<Role>();

const highestRoleOrder = computed(() => props.game ? props.game.user_data!.highest_role_order : user!.highest_role_order);
const userRoleIds = computed(() => props.game ? props.game.user_data!.role_ids : user!.role_ids);

function showDropHint(belowRole: Role) {
    if ((props.game && hasPermission('manage-roles')) || (highestRoleOrder.value && highestRoleOrder.value > ((belowRole.order || 1001) - 2))) {
        hoveringDrag.value = belowRole;
        document.body.style.cursor = 'move';
    } else {
        hoveringDrag.value = undefined;
        document.body.style.cursor = 'not-allowed';
    }
}

function calculateHighestOrder() {
    // Make sure that the user has the correct highest order
    let highestOrder: number|undefined;
    for (const role of roles.value!.data) {
        if (!role.is_vanity && userRoleIds.value?.find(id => id == role.id) && (!highestOrder || highestOrder < role.order)) {
            highestOrder = role.order;
        }
    }

    if (props.game) {
        props.game.user_data!.highest_role_order = highestOrder;
    } else {
        user!.highest_role_order = highestOrder;
    }
}

async function onDrop() {
    if (draggedItem.value && hoveringDrag.value) {
        const dragged = draggedItem.value;
        const hovering = hoveringDrag.value;
        const newRoles = rolesNoMember.value.filter(role => role.id !== dragged.id);

        let nextOrder = 1000;
        for (const role of newRoles) {
            if (role.id == hovering.id) {
                dragged.order = nextOrder-5;
            }
            role.order = nextOrder;
            nextOrder -= 10;
        }

        newRoles.unshift(dragged);

        roles.value!.data = newRoles.sort((a,b) => b.order - a.order);
        calculateHighestOrder();

        document.body.style.cursor = '';

        await patchRequest(`${url.value}/${draggedItem.value.id}`, { order: dragged.order });
    }
}

function startDrag(item) {
    draggedItem.value = item;
}

function stopDrag() {
    draggedItem.value = undefined;
    hoveringDrag.value = undefined;
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