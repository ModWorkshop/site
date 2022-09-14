<template>
    <div>
        <a-list v-model="reports" url="reports" query :params="{ game_id: $route.params.gameId }">
            <template #item="{ item }">
                <flex column class="list-button">
                    <flex>
                        <flex :class="{'items-center': true, archived: item.archived}">
                            <a-tag class="capitalize">{{item.reportable_type}}</a-tag>
                            <strong v-if="item.name" :class="{ archived: item.archived }">Name: {{getName(item)}}</strong>
                        </flex>
                        <flex class="ml-auto">
                            <a-button v-if="item.archived" icon="trash" @click="deleteReport(item)">{{$t('delete')}}</a-button>
                            <a-button style="opacity: 1;" @click="toggleArchiveReport(item)">{{$t(item.archived ? 'unarchive' : 'archive')}}</a-button>
                        </flex>
                    </flex>
                    <div :class="{ archived: item.archived }">
                        <flex class="items-center">
                            Reported by <a-user :user="item.user" avatar-size="xs"/> <time-ago :time="item.created_at"/>
                        </flex>
                        Reason: "{{item.reason}}"
                    </div>
                </flex>
            </template>
        </a-list>
    </div>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { Paginator } from '~~/types/paginator';

const reports = ref<Paginator>({ data: [], meta: null });
function getName(report) {
    if ((report.name || report.reportable.name) && (report.name != report.reportable.name)) {
        return `"${report.name}" (Changed to "${report.reportable.name}")`;
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