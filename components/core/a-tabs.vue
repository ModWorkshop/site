<template>
    <flex :column="!side" :gap="side ? 8 : 1">
        <flex class="tab-list mb-2" role="tablist" :column="side">
            <!--ARIA compliant, I hope xd-->
            <a
                v-for="tab of tabs"
                role="tab"
                ref="tabLinks" 
                :id="`${tab.name}-tab-link`"
                :class="{'tab-link': true, 'tab-link-side': side, 'selected': tabState.current == tab.name}"
                :tabindex="tabState.current == tab.name ? 0 : -1"
                :aria-selected="tabState.current == tab.name"
                :aria-controls="`${tab.name}-tab-panel`"
                :key="tab.name" 
                :href="type == 'route' ? tab.name : (type == 'hash' ? `#${tab.name}` : '#')"
                @click.prevent="() => setCurrentTab(tab.name)"
                @keydown.left="() => arrowKeysMove(true)"
                @keydown.right="() => arrowKeysMove(false)">
                {{tab.title}}
            </a>
            <slot name="buttons"/>
        </flex>
        <div :class="{'tab-panels': true, [`px-${padding}`]: padding !== 0, 'flex-grow': side}">
            <slot v-if="type != 'route'"/>
            <slot v-else name="content"/>
        </div>
    </flex>
</template>

<script setup>
const props = defineProps({
    side: Boolean,
    route: String,
    type: {
        default: 'hash',
        type: String
    },
    padding: {
        default: 2,
        type: [String, Number]
    }
});

const slots = useSlots();
const tabLinks = ref();

let listening = false;
onBeforeUpdate(() => {
    tabLinks.value = [];

    if (!listening && process.client) {
        listening = true;
        window.addEventListener('hashchange', () => {
            if (props.type == 'hash') {
                setCurrentTab(window.location.hash.replace('#', ''));
            }
        });
    }
});

const tabs = ref([]);
const tabState = ref({current: '', focus: 0});
provide('tabState', tabState.value);
provide('type', props.type);

computeTabs();

function arrowKeysMove(left) {
    let focus = tabState.value.focus;

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

    tabState.value.focus = focus;
    tabLinks.value[focus].$el.focus();
}

function setCurrentTab(name) {
    tabState.value.current = name;
    tabState.value.focus = tabs.value.findIndex(tab => tab.name === name);
}

function computeTabs() {
    const def = slots.default();
    if (def) {
        let currentTab; 
    
        if (props.type == 'route') {
            currentTab = props.route;
        } else if (props.type == 'hash') {
            currentTab = process.client && window.location.hash.replace('#', '');
        }

        tabs.value = def.reduce((prev, curr) => {
            if (curr.props) {
                const tab = curr.props;
                if (tabState.value.current === '') {
                    tabState.value.current = currentTab || tab.name;
                }
                prev.push(tab);
            }

            return prev;
        }, []);
    }
}
</script>

<style>
    .tab-link {
        border-radius: 4px;
        padding: 0.5rem 2rem;
        color: var(--secondary-text-color);
    }

    .tab-link:hover {
        cursor: pointer;
    }

    .tab-link.selected {
        background-color: var(--tab-selected-color);
        color: var(--primary-color);
    }

    .tab-link-side {
        padding: 0.5rem 4rem 0.5rem 0.75rem;
        min-width: 200px;
    }
</style>