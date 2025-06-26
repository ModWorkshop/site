<template>
    <div v-if="button" @click="showReportModal">
        <slot>
            <m-button color="danger"><i-mdi-flag/> {{$t('report')}}</m-button>
        </slot>
    </div>
    <Teleport to="body">
        <m-form-modal v-model="vm" :title="$t('report')" :desc="$t('report_desc', [$t(`resource_${resourceName}`)])" @submit="report">
            <m-input v-model="reason" :label="$t('reason')" type="textarea" rows="6"/>
        </m-form-modal>
    </Teleport>
</template>

<script setup lang="ts">
import { useStore } from '~/store';

const props = withDefaults(defineProps<{
    url: string,
    resourceName: string,
    showModal?: boolean,
    button?: boolean,
}>(), {
    showModal: false,
    button: true
});

const emit = defineEmits(['update:showModal']);

const vm = useVModel(props, 'showModal', emit, { passive: true });

const reason = ref('');

const { t } = useI18n();
const { showToast } = useToaster();
const { user } = useStore();
const router = useRouter();

function showReportModal() {
    if (!user) {
        router.push('/login');
        return;
    }
    vm.value = true;
}
async function report(onError) {
    try {
        await postRequest(props.url, { reason: reason.value });
        reason.value = '';
        vm.value = false;
        
        showToast({
            desc: t('report_sent'),
            color: 'success'
        });
    } catch (error) {
        onError(error);
    }
}
</script>