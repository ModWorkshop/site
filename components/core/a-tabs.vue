<template>
    <flex :column="!side" :gap="side ? 8 : 1">
        <flex class="tab-list mb-2" role="tablist" :column="side">
            <!--ARIA compliant, I hope xd-->
            <a-tab-link 
                v-for="tab of tabs"
                :tab-name="tab.name"
                :tab-title="tab.title"
                ref="tabLinks" 
                :key="tab.name" 
                :href="type == 'route' ? tabName : null"
                @click="() => setCurrentTab(tab.name)"
                @keydown.left="() => arrowKeysMove(true)"
                @keydown.right="() => arrowKeysMove(false)"
            />
            <slot name="buttons"/>
        </flex>
        <div :class="{'tab-panels': true, [`px-${padding}`]: padding !== 0, 'flex-grow': side}">
            <slot v-if="type != 'route'"/>
            <slot v-else name="content"/>
        </div>
    </flex>
</template>

<script setup>
const route = useRoute();
const router = useRouter();

const props = defineProps({
    side: Boolean,
    route: String,
    type: {
        default: 'query',
        type: String
    },
    padding: {
        default: 2,
        type: [String, Number]
    }
});

const slots = useSlots();
const tabLinks = ref();

const tabs = ref(slots.default().map(tab => {
    if (tab.props) {
        return { name: tab.props.name, title: tab.props.title };
    }
}).filter(tab => typeof tab == 'object'));

const tabState = reactive({
    current: props.type == 'route' ? props.route : route.query.tab, 
    focus: 0
});

// Check if our current tab exists, otherwise fallback to the first.
if (tabs.value.length > 0 && (!tabState.current || tabs.value.reduce((prev, curr) => prev && curr.name != tabState.current))) {
    tabState.current = tabs.value[0].name;
}

provide('tabState', tabState);
provide('type', props.type);
provide('side', props.side);

function arrowKeysMove(left) {
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

function setCurrentTab(name) {
    if (props.type == 'query') {
        //We only want to set the query
        router.push({ query: { ...route.query, tab: name } });
    }

    tabState.current = name;
    tabState.focus = tabs.value.findIndex(tab => tab.name === name);
}
</script>