<script setup lang="ts">
import { useStore } from '~~/store';

const router = useRouter();
const { user } = useStore();

definePageMeta({
    middleware: 'users-only'
});

await router.replace(`user/${user.unique_name}`);
</script>