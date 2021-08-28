<template>
    <flex :column="!list" gap="2">
        <flex class="tab-list" role="tablist" :column="list">
            <!--ARIA compliant, I hope xd-->
            <a 
                v-for="tab of tabs"
                role="tab"
                ref="tabLinks"
                :id="`${tab.name}-tab-link`"
                :tabindex="tabState.current == tab.name ? 0 : -1"
                :aria-selected="tabState.current == tab.name"
                :aria-controls="`${tab.name}-tab-panel`"
                :key="tab.name" 
                :href="hash && `#${tab.name}`"
                @click="() => setCurrentTab(tab.name)"
                @keydown.left="() => arrowKeysMove(true)"
                @keydown.right="() => arrowKeysMove(false)"
                :class="{'tab-link': true, 'selected': tabState.current == tab.name}">
                {{tab.title}}
            </a>
            <slot name="buttons"/>
        </flex>
        <div :class="{'tab-panels': true, [`p-${padding}`]: padding !== 0, 'flex-grow': list}">
            <slot/>
        </div>
    </flex>
</template>

<script>
    import { provide, ref } from '@nuxtjs/composition-api';

    export default {
        props: {
            list: Boolean,
            hash: {
                default: true,
                type: Boolean
            },
            padding: {
                default: 2,
                type: [String, Number]
            }
        },
        setup() {
            const tabs = ref([]);
            const tabState = ref({current: '', focus: 0});
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
            arrowKeysMove(left) {
                const tabs = this.$refs.tabLinks;
                let focus = this.tabState.focus;

                if (left) {
                    focus--;
                } else {
                    focus++;
                }

                if (focus < 0) {
                    focus = tabs.length - 1;
                } else if (focus >= tabs.length) {
                    focus = 0;
                }

                this.tabState.focus = focus;
                tabs[focus].focus();
            },
            setCurrentTab(name) {
                const tabState = this.tabState;
                tabState.current = name;
                tabState.focus = this.tabs.findIndex(tab => tab.name === name);
            },
            computeTabs() {
                this.tabs = this.$slots.default.reduce((prev, curr) => {
                    if (curr.tag) {
                        const tab = curr.componentOptions.propsData;
                        if (this.tabState.current === '') {
                            this.tabState.current = process.client && !this.hash && window.location.hash.replace('#', '') || tab.name;
                        }
                        prev.push(tab);
                    }

                    return prev;
                }, []);
            }
        }
    }
</script>

<style scoped>
    .tab-link {
        border-radius: 4px;
        padding: 0.5rem 2rem;
        color: var(--secondary-text-color);
    }

    .tab-link.selected {
        background-color: var(--tab-selected-color);
        color: var(--primary-color);
    }
</style>