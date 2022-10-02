<template>
    <div class="layout">
        <the-header>Header</the-header>
        <main>
            <flex id="toaster" column gap="2">
				<TransitionGroup name="toasts">
					<a-toast v-for="toast of toasts" :key="toast.key" :title="toast.title" :desc="toast.desc" :color="toast.color"/>
				</TransitionGroup>
			</flex>
            <slot/>
        </main>
        <the-footer>Footer</the-footer>
    </div>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

const store = useStore();

watch(() => props.game, () => store.currentGame = props.game, { immediate: true });

const toasts = useState('toasts', () => []);
</script>

<style>
.layout {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr auto;
    grid-template-areas: 
        'header'
        'main'
        'footer';
}

main {
    grid-area: main;
    display: flex;
    flex-direction: column;
    padding: 8px;
    align-items: center;
}
</style>