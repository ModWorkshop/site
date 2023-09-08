<template>
    <flex class="tabs" gap="3">
        <flex v-if="side" class="items-center hidden max-lg:block" @click="menuOpen = !menuOpen">
            <a-link-button class="collapse-button">
                <i-mdi-menu/>
            </a-link-button>
            <span v-if="currentTab" class="text-2xl">{{currentTab.title}}</span>
        </flex>
        <flex :class="[menuOpen && 'menu-open', 'flex-grow']" :column="!props.side" :gap="gap ?? (side ? 8 : 2)">
            <div v-if="menuOpen" class="menu-closer" @click.prevent="menuOpen = false"/>
            <Transition name="left-slide">
                <flex v-show="!side || menuOpen" wrap class="nav-menu" :style="{flex: side ? 1 : undefined}" :column="side" role="tablist">
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
            <div ref="tabContentHolder" :class="{'nav-menu-content': true, [`px-${padding}`]: padding !== 0}" :style="{flex: 4}">
                <slot/>
            </div>
        </flex>
    </flex>
</template>

<script setup lang="ts">
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
const currentTab = computed(() => tabs.value.find(tab => tab.name === tabState.current));

function refreshTabs() {
    tabs.value = getCurrentTabs();
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
    current: props.query ? route.query.tab as string : undefined, 
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
    menuOpen.value = false;
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

.nav-menu-content {
    width: 100%;
}
</style>