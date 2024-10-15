<template>
    <m-flex class="list-button">
        <m-flex column :class="{archived: report.archived}">
            <m-tag class="capitalize mr-auto">{{report.reportable_type}}</m-tag>
            <m-flex class="items-center">{{$t('reported_by')}}: <a-user :user="report.user" avatar-size="xs"/> <m-time-ago :time="report.created_at"/></m-flex>
            <m-flex class="items-center">{{$t('reported_user')}}: <a-user :user="report.reported_user ?? reportedUser" avatar-size="xs"/></m-flex>
            <span>{{ $t('reason') }}:</span>
            <blockquote>{{report.reason}}</blockquote>
            <span>{{ contentTitle }}: <span v-if="content && currentContent != content">{{$t('edited')}}</span></span>
            <details>{{ content }}</details>
        </m-flex>
        <m-flex column class="ml-auto my-auto" gap="2">
            <m-flex class="ml-auto">
                <m-button v-if="report.reportable?.user_id" :to="`/admin/${casesUrl}?user=${report.reportable.user_id}`">{{$t('warn_owner')}}</m-button>
                <mod-suspend v-if="report.reportable_type == 'mod' && report.reportable" :mod="report.reportable as Mod"/>
                <m-button v-if="reportLink" :to="reportLink">{{$t('go_to_content')}}</m-button>
            </m-flex>
            <m-flex class="ml-auto">
                <m-button v-if="report.archived" color="danger" @click="deleteReport"><i-mdi-delete/> {{$t('delete')}}</m-button>
                <m-button style="opacity: 1;" @click="toggleArchiveReport">{{$t(report.archived ? 'unarchive' : 'archive')}}</m-button>
            </m-flex>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { useI18n } from 'vue-i18n';
import type { Comment, Game, Mod, Report, User } from '~~/types/models';
import { useStore } from '~~/store/index';

const { game, report, reports } = defineProps<{
    game?: Game,
    report: Report,
    reports: Report[]
}>();

const store = useStore();

const { t } = useI18n();
const casesUrl = computed(() => getGameResourceUrl('cases', game));
const contentTitle = computed(() => {
    if (report.reportable_type == 'mod' || report.reportable_type == 'user') {
        return t('name');
    } else {
        return t('content');
    }
});

const currentContent = computed(() => {
    if (!report.reportable) {
        return;
    }
    if (report.reportable_type == 'mod' || report.reportable_type == 'user') {
        return (report.reportable as Mod|User).name;
    } else {
        return (report.reportable as Comment).content;
    } 
});

const content = computed(() => {
    if (report.reportable_type == 'mod' || report.reportable_type == 'user') {
        return report.name;
    } else {
        return report.data.content;
    } 
});

const reportedUser = computed(() => {
    if (!report.reportable) {
        return;
    }

    if (report.reportable_type == 'user') {
        return report.reportable as User;
    } else {
        return (report.reportable as { id: number, user: User }).user;
    }
});

const reportLink = computed(() => report.reportable ? getObjectLink(report.reportable_type, report.reportable) : null);

async function toggleArchiveReport() {
    const archived = !report.archived;
    await patchRequest(`reports/${report.id}`, { archived });
    report.archived = !report.archived;
    if (game) {
        game.report_count = Math.max(0, game.report_count! + (archived ? -1 : 1));
    }
    store.reportCount = Math.max(0, store.reportCount! + (archived ? -1 : 1));
}

async function deleteReport() {
    await deleteRequest(`reports/${report.id}`);
    remove(reports, report);
}
</script>