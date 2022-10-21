<template>
    <flex class="tabs" gap="3">
        <flex v-if="side" class="items-center hidden max-lg:block" @click="menuClosed = !menuClosed">
            <a-link-button class="collapse-button" icon="bars"/>
            <span v-if="currentTab" class="text-2xl">{{currentTab.title}}</span>
        </flex>
        <flex :class="[!menuClosed && 'menu-open']" :column="!props.side" :gap="gap ?? (side ? 8 : 2)">
            <div v-if="!menuClosed" class="menu-closer" @click.prevent="menuClosed = true"/>
            <Transition name="left-slide">
                <flex v-show="!side || !menuClosed" wrap class="nav-menu" :column="side" role="tablist">
                    <flex wrap grow :column="side">
                        <a-tab-link 
                            v-for="tab of tabs"
                            ref="tabLinks"
                            :key="tab.name"
                            :tab-name="tab.name" 
                            :tab-title="tab.title" 
                            @click.prevent="() => setCurrentTab(tab.name)"
                            @keydown.left="() => arrowKeysMove(true)"
                            @keydown.right="() => arrowKeysMove(false)"
                        />
                    </flex>
                    <slot name="buttons"/>
                </flex>
            </Transition>
            <slot name="pre-panels"/>
            <div :class="{'nav-menu-content': true, [`px-${padding}`]: padding !== 0, 'flex-grow': !isSm && side}">
                <slot/>
            </div>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';

const route = useRoute();
const queryTab = useRouteQuery('tab');

const props = defineProps({
    side: Boolean,
    query: Boolean,
    gap: [String, Number],
    lazy: Boolean,
    padding: {
        default: 2,
        type: [String, Number]
    }
});

const slots = useSlots();
const tabLinks = ref();

const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smallerOrEqual('sm');

const menuClosed = ref(true);

const column = computed(() => !props.side || isSm.value);


function getCurrentTabs() {
    return slots.default().map(tab => {
        if (tab.props) {
            return { name: tab.props.name, title: tab.props.title };
        }
    }).filter(tab => typeof tab == 'object');
}

const tabs = ref(getCurrentTabs());
const currentTab = computed(() => tabs.value.find(tab => tab.name === tabState.current));

onUpdated(() => {
    tabs.value = getCurrentTabs();
});

const tabState: { focus: number, current: string } = reactive({
    current: props.query ? route.query.tab as string : null, 
    focus: 0
});

// Check if our current tab exists, otherwise fallback to the first.
if (tabs.value.length > 0) {
    if (!tabState.current || tabs.value.reduce((prev, curr) => prev && curr.name != tabState.current, true)) {
        tabState.current = tabs.value[0].name;
    }
}

provide('tabState', tabState);
provide('side', props.side);
provide('lazy', props.lazy);

function arrowKeysMove(left: boolean) {
    let focus = tabState.focus;

    if (left) {
        focus--;
    } else {
        focus++;
    }

    if (focus < 0) {
        focus = tabLinks.value.length - 1;
    } else if (focus >= tabLinks.value.length) {
        focus = 0;
    }

    tabState.focus = focus;
    tabLinks.value[focus].$el.focus();
}

function setCurrentTab(name: string, skipSetQuery = false) {
    if (props.query && !skipSetQuery) {
        //We only want to set the query
        queryTab.value = name;
    }

    tabState.current = name;
    tabState.focus = tabs.value.findIndex(tab => tab.name === name);
    menuClosed.value = true;
}

if (props.query) {
    watch(queryTab, val => {
        setCurrentTab(val, true);
    });
}
</script>

<style scoped>
.tabs {
    flex-direction: column;
    position: relative;
}
</style>