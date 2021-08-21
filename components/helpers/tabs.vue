<template>
    <div>
        <flex :gap="2">
            <a v-for="tab of tabs" :key="tab.name" :href="`#${tab.name}`" @click="() => setCurrentTab(tab)">{{tab.title}}</a>
        </flex>
        <slot/>
    </div>
</template>

<script>
    import { provide, ref, useContext } from '@nuxtjs/composition-api';

    export default {
        setup() {
            const { route } = useContext();
            const tabs = ref([]);
            const tabState = ref({current: ''});
            provide('tabState', tabState.value);

            return { tabs, tabState };
        },
        created() {
            this.computeTabs();
        },
        beforeUpdate() {
            this.computeTabs();
        },
        methods: {
            setCurrentTab(tab) {
                this.tabState.current = tab.name;
            },
            computeTabs() {
                this.tabs = this.$slots.default.reduce((prev, curr) => {
                    if (curr.tag) {
                        const tab = curr.componentOptions.propsData;
                        if (this.tabState.current === '') {
                            this.tabState.current = process.client && window.location.hash.replace('#', '') || tab.name;
                        }
                        prev.push(tab);
                    }
                    return prev;
                }, []);
            }
        }
    }
</script>