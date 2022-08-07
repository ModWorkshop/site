<template>
    <a-alert v-if="errorStr || description" class="w-full whitespace-pre" color="danger">
        <h4>{{$t('error')}}</h4>{{errorStr || description}}
    </a-alert>
</template>

<script setup lang="ts">
const props = defineProps({
    error: Object,
    errorStr: String
});

const description = computed(() => {
    let i = 1;
    console.log(props.error.data);
    
    if (props.error.data && props.error.data.message) {
        const data = props.error.data;

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
    } else {
        return props.error.message;
    }
});
</script>