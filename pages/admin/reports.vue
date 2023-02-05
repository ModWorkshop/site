<template>
    <div>
        <a-list v-model="reports" :url="url" query :params="{ all }">
            <template #item="{ item }">
                <flex class="list-button">
                    <flex column :class="{archived: item.archived}">
                        <a-tag class="capitalize mr-auto">{{item.reportable_type}}</a-tag>
                        <strong v-if="item.name" :class="{ archived: item.archived }">{{$t('name')}}: {{getName(item)}}</strong>
                        <flex class="items-center">{{$t('reported_by')}}: <a-user :user="item.user" avatar-size="xs"/> <time-ago :time="item.created_at"/></flex>
                        <div>{{`${$t('reason')}: "${item.reason}"`}}</div>
                    </flex>
                    <flex class="ml-auto my-auto">
                        <a-button v-if="item.archived" icon="mdi:trash" @click="deleteReport(item)">{{$t('delete')}}</a-button>
                        <a-button style="opacity: 1;" @click="toggleArchiveReport(item)">{{$t(item.archived ? 'unarchive' : 'archive')}}</a-button>
                    </flex>
                </flex>
            </template>
        </a-list>
    </div>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { Game } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const { t } = useI18n();

const url = computed(() => getGameResourceUrl('reports', props.game));
const all = useRouteQuery('all', false, 'boolean');

const reports = ref<Paginator>({ data: [], meta: null });
function getName(report) {
    if ((report.name || report.reportable.name) && (report.name != report.reportable.name)) {
        return t('report_name_changed', { name: report.name, changed: report.reportable.name });
    } else {
        return report.name;
    }
}

async function toggleArchiveReport(report) {
    await usePatch(`reports/${report.id}`, { archived: !report.archived });
    report.archived = !report.archived;
}

async function deleteReport(report) {
    await useDelete(`reports/${report.id}`);
    remove(reports.value.data, report);

}
</script>

<style>
.archived {
    opacity: 0.25;
}
</style>