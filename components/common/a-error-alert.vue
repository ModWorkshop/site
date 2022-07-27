<template>
    <va-alert v-if="errorStr || error && description" class="w-full whitespace-pre" color="danger">
        <h4>{{$t('error')}}</h4>{{errorStr || description}}
    </va-alert>
</template>

<script setup lang="ts">
const props = defineProps({
    error: Object,
    errorStr: String
});

const description = computed(() => {
    const data = props.error.data;
    let i = 1;
    if (data) {
        if (data.errors) {
            let errStr = '';
            for (const err of Object.values(data.errors)) {
                for (const str of (err as string[])) {
                    if (errStr) {
                        errStr += '\n' + i + '. ' + str;
                    } else {
                        errStr = i + '. ' + str;
                    }
                    i++;
                }
            }
            return errStr;
        } else if (data.message) {
            return data.message;
        }
    }
});
</script>