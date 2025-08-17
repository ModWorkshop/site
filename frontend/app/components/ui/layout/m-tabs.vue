<template>
    <m-flex class="tabs" gap="3">
        <m-flex v-if="side" class="items-center hidden max-lg:block" @click="menuOpen = !menuOpen">
            <m-link class="collapse-button">
                <i-mdi-menu/>
            </m-link>
            <span v-if="currentTab" class="text-2xl">{{currentTab.title}}</span>
        </m-flex>
        <m-flex :class="[menuOpen && 'menu-open', 'flex-grow', 'h-full']" :column="!side" :gap="gap">
            <div v-if="menuOpen" class="menu-closer" @click.prevent="menuOpen = false"/>
            <Transition name="left-slide">
                <m-flex 
                    v-show="!side || menuOpen"
                    :wrap="!scrollOnOverflow"
                    :class="{'nav-menu': true, 'overflow-x-auto': scrollOnOverflow}"
                    :style="{flex: side ? 1 : undefined}"
                    :column="side" role="tablist"
                >
                    <m-flex :wrap="!scrollOnOverflow" grow :column="side" :class="{'flex-shrink-0': scrollOnOverflow, [`p-${padding}`]: padding !== 0}">
                        <m-tab-link 
                            v-for="tab of tabs"
                            ref="tabLinks"
                            :key="tab.name"
                            :tab-name="tab.name" 
                            :tab-title="tab.title" 
                            @click.prevent="() => setCurrentTab(tab.name)"
                            @keydown.left="() => arrowKeysMove(true)"
                            @keydown.right="() => arrowKeysMove(false)"
                        />
                    </m-flex>
                    <slot name="buttons"/>
                </m-flex>
            </Transition>
            <slot name="pre-panels"/>
            <div ref="tabContentHolder" :class="{'nav-menu-content': true, 'nav-menu-bg': background, [`p-${padding}`]: padding !== 0}" :style="{flex: 4}">
                <slot/>
            </div>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
const route = useRoute();
const queryTab = useRouteQuery('tab');

const { padding = 2, side, query, lazy, background = false, gap = 0 } = defineProps<{
    side?: boolean,
    query?: boolean,
    gap?: string|number,
    lazy?: boolean,
    scrollOnOverflow?: boolean,
    padding?: string|number,
    background?: boolean
}>();

const slots = useSlots();
const tabLinks = ref();
const tabContentHolder = ref();
const menuOpen = ref(false);

function getCurrentTabs() {
    if (!slots.default) {
        return [];
    }

    return slots.default().map(tab => {
        if (tab.props) {
            return { name: tab.props.name, title: tab.props.title };
        }
    }).filter(tab => typeof tab == 'object') as { name: string, title: string }[];
}

const tabs = ref(getCurrentTabs());
const currentTab = computed(() => tabs.value.find(tab => tab.name === tabState.current) || tabs.value[0]);

function refreshTabs() {
    tabs.value = getCurrentTabs();

    // Check if our current tab exists, otherwise fallback to the first.
    if (tabs.value.length > 0 && tabs.value[0]) {
        if (!tabState.current || tabs.value.reduce((prev, curr) => prev && curr.name != tabState.current, true)) {
            tabState.current = tabs.value[0].name;
        }
    }
}

onUpdated(refreshTabs);

// https://austingil.com/watching-changes-vue-js-component-slot-content/ to ensure that it is updated.
let observer: MutationObserver;
onMounted(() => {
    observer = new MutationObserver(refreshTabs);
    observer.observe(tabContentHolder.value, {
        childList: true,
        subtree: true
    });
});

// Get rid of the observer when the component is unmounted.
onBeforeUnmount(() => observer?.disconnect());

const tabState: { focus: number, current?: string } = reactive({
    current: query ? route.query.tab as string : undefined, 
    focus: 0
});

provide('tabState', tabState);
provide('side', side);
provide('lazy', lazy);
refreshTabs();

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
    if (query && !skipSetQuery) {
        //We only want to set the query
        queryTab.value = name;
    }

    tabState.current = name;
    tabState.focus = tabs.value.findIndex(tab => tab.name === name);
    menuOpen.value = false;
}

if (query) {
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

<style>

.nav-menu-content {
    width: 100%;
    overflow-x: hidden;
}

.nav-menu-bg {
    border-radius: var(--content-border-radius);
    background-color: var(--content-bg-color);
    padding: 1.5rem;
}
</style>