<script setup lang="ts">
import type { Game } from "~/types/models";

const { game } = defineProps<{
    game: Game
}>();

navigateTo('/mod/new?game=' + game.id);
</script>