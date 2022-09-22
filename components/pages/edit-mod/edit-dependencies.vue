<template>
    <flex>
        <a-button @click="openDepModal">{{$t('add_mod')}}</a-button>
        <a-button @click="openOffsiteDepModal">{{$t('add_offsite_mod')}}</a-button>
    </flex>
    <a-table>
        <template #head>
            <th>Name</th>
            <th>URL</th>
            <th>Optional</th>
            <th>Actions</th>
        </template>
        <tr v-for="dep in dependable.dependencies" :key="dep.id">
            <td v-if="dep.mod">{{dep.mod.name}}</td>
            <td v-else>{{dep.name}}</td>
            <td v-if="dep.mod"><NuxtLink :to="`/mod/${dep.mod.id}`">{{$t('mod_page')}}</NuxtLink></td>
            <td v-else><NuxtLink :to="dep.url">{{dep.url}}</NuxtLink></td>
            <td>{{dep.optional ? '✔' : '❌'}}</td>
            <td class="flex gap-1">
                <a-button icon="cog" @click="editDep(dep)">{{$t('edit')}}</a-button>
                <a-button color="danger" icon="trash" @click="deleteDep(dep)">{{$t('remove')}}</a-button>
            </td>
        </tr>
    </a-table>
    <a-modal-form v-model="showAddModModal" :title="$t('add_offsite_mod')" @submit="addDependency">
        <template v-if="currentDep.offsite">
            <a-input v-model="currentDep.name" :label="$t('name')"/>
            <a-input v-model="currentDep.url" type="url" :label="$t('url')"/>
        </template>
        <a-async-select v-else v-model="currentDep.mod_id" url="mods" label="Mod"/>
        <a-input v-model="currentDep.optional" type="checkbox" :label="$t('optional')"/>
    </a-modal-form>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { Dependency } from '~~/types/models';

const props = defineProps<{
    dependable: { 
        id: number,
        dependencies?: Dependency[] 
    },
    url: string,
}>();

const showAddModModal = ref(false);

const dep = {
    mod_id: null,
    optional: false,
    offsite: false
};

const offsiteDep = {
    name: '',
    url: '',
    offsite: true,
    optional: false
};

const currentDep = ref(null);

function openOffsiteDepModal() {
    currentDep.value = {...offsiteDep};
    showAddModModal.value = true;
}

function openDepModal() {
    currentDep.value = {...dep};
    showAddModModal.value = true;
}

function editDep(dep) {
    currentDep.value = {...dep};
    showAddModModal.value = true;
}

async function addDependency(onError) {
    try {
        if (currentDep.value.id) {
            const dep = await usePatch<Dependency>(`${props.url}/${props.dependable.id}/dependencies/${currentDep.value.id}`, currentDep.value);
            for (const d of props.dependable.dependencies) {
                if (d.id === currentDep.value.id) {
                    Object.assign(d, dep);
                }
            }
        } else {
            const dep = await usePost<Dependency>(`${props.url}/${props.dependable.id}/dependencies`, currentDep.value);
            props.dependable.dependencies.push(dep);
        }
        showAddModModal.value = false;
    } catch (error) {
        onError(error);
    }
}

async function deleteDep(dep) {
    try {
        await useDelete(`${props.url}/${props.dependable.id}/dependencies/${currentDep.id}`);
        remove(props.dependable.dependencies, dep);
        showAddModModal.value = false;
    } catch (error) {
        //TODO
    }
}
</script>