<template>
    <flex class="list-button">
        <flex column :class="{archived: report.archived}">
            <a-tag class="capitalize mr-auto">{{report.reportable_type}}</a-tag>
            <flex class="items-center">{{$t('reported_by')}}: <a-user :user="report.user" avatar-size="xs"/> <time-ago :time="report.created_at"/></flex>
            <flex class="items-center">{{$t('reported_user')}}: <a-user :user="reportedUser" avatar-size="xs"/></flex>
            <span>{{ $t('reason') }}:</span>
            <blockquote>{{report.reason}}</blockquote>
            <span>{{ contentTitle }}: <span v-if="content && currentContent != content">{{$t('edited')}}</span></span>
            <details>{{ content }}</details>
        </flex>
        <flex column class="ml-auto my-auto" gap="2">
            <flex class="ml-auto">
                <a-button v-if="report.reportable" :to="`/admin/${casesUrl}?user=${report.reportable.user_id}`">{{$t('warn_owner')}}</a-button>
                <mod-suspend v-if="report.reportable_type == 'mod' && report.reportable" :mod="report.reportable"/>
                <a-button @click="goToContent(report)">{{$t('go_to_content')}}</a-button>
            </flex>
            <flex class="ml-auto">
                <a-button v-if="report.archived" color="danger" icon="mdi:trash" @click="deleteReport(report)">{{$t('delete')}}</a-button>
                <a-button style="opacity: 1;" @click="toggleArchiveReport(report)">{{$t(report.archived ? 'unarchive' : 'archive')}}</a-button>
            </flex>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { Game, Report } from '~~/types/models';
import { useStore } from '../../../store/index';

const props = defineProps<{
    game?: Game,
    report: Report,
    reports: Report[]
}>();

const store = useStore();

const { t } = useI18n();
const router = useRouter();
const casesUrl = computed(() => getGameResourceUrl('cases', props.game));
const contentTitle = computed(() => {
    if (props.report.reportable_type == 'mod' || props.report.reportable_type == 'user') {
        return t('name');
    } else {
        return t('content');
    }
});

const currentContent = computed(() => {
    const report = props.report;
    if (!report.reportable) {
        return;
    }
    if (report.reportable_type == 'mod' || props.report.reportable_type == 'user') {
        return report.reportable.name;
    } else {
        return report.reportable.content;
    } 
});

const content = computed(() => {
    const report = props.report;

    if (report.reportable_type == 'mod' || report.reportable_type == 'user') {
        return report.name;
    } else {
        return report.data.content;
    } 
});

const reportedUser = computed(() => {
    const report = props.report;
    
    if (!report.reportable) {
        return;
    }

    if (report.reportable_type == 'user') {
        return report.reportable;
    } else {
        return report.reportable.user;
    }
});

async function toggleArchiveReport(report) {
    const archived = !report.archived;
    await patchRequest(`reports/${report.id}`, { archived });
    report.archived = !report.archived;
    if (props.game) {
        props.game.report_count = Math.max(0, props.game.report_count! + (archived ? -1 : 1));
    }
    store.reportCount = Math.max(0, store.reportCount! + (archived ? -1 : 1));
}

async function deleteReport(report) {
    await deleteRequest(`reports/${report.id}`);
    remove(props.reports, report);
}

async function goToContent(obj: Report) {
    if (obj.reportable_type == 'comment') {
        router.push(await getCommentLink(obj.reportable));
    } else {
        const link = getObjectLink(obj.reportable_type, obj.reportable);
        if (link) {
            router.push(link);
        }
    }
}
</script>