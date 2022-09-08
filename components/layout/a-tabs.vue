<template>
    <flex :column="!side" :gap="gap ?? (side ? 8 : 2)">
        <flex class="tab-list" role="tablist" :column="side">
            <!--ARIA compliant, I hope xd -->
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
            <slot name="buttons"/>
        </flex>
        <slot name="pre-panels"/>
        <div :class="{'tab-panels': true, [`px-${padding}`]: padding !== 0, 'flex-grow': side}">
            <slot/>
        </div>
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


function getCurrentTabs() {
    return slots.default().map(tab => {
        if (tab.props) {
            return { name: tab.props.name, title: tab.props.title };
        }
    }).filter(tab => typeof tab == 'object');
}

const tabs = ref(getCurrentTabs());

onUpdated(() => {
    tabs.value = getCurrentTabs();
});

const tabState = reactive({
    current: props.query ? route.query.tab : null, 
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
}

if (props.query) {
    watch(queryTab, val => {
        setCurrentTab(val, true);
    });
}
</script>