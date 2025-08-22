<template>
	<span :class="{ tag: true, 'tag-small': small, capsule }">
		<slot/>
	</span>
</template>
<script setup lang="ts">
import Color from 'colorjs.io';
const { small, color, capsule = false } = defineProps<{
	small?: boolean;
	color?: string;
	capsule?: boolean;
}>();

const col = computed(() => {
	if (color) {
		try {
			return new Color(color);
		} catch {
			return new Color('#006ce0');
		}
	}
	return new Color('#006ce0');
});

const h = computed(() => col.value.hsl.h || '0');
const s = computed(() => col.value.hsl.s);
const l = computed(() => col.value.hsl.l);
</script>
<style scoped>
.tag {
	display: inline-flex;
	color: #000;
	text-shadow: none;
	padding: 0.5rem;
	gap: 4px;
	align-items: center;
	justify-content: center;
	font-size: 75%;
	font-weight: bold;
	background: hsla(var(--color-h), calc(var(--color-s) * 1%), calc(var(--color-l) * 1%), 0.2);
	color: hsl(var(--color-h), calc(var(--color-s) * 1%), var(--tag-lightness));
	border-radius: 16px;
	border: hsla(var(--color-h), calc(var(--color-s) * 1%), var(--tag-lightness), 0.2) 1px solid;

	--color-h: v-bind(h);
	--color-s: v-bind(s);
	--color-l: v-bind(l);
}

.tag {
	--tag-lightness: 75%;
}

html.light .tag {
	--tag-lightness: 25%;
}

@media (prefers-color-scheme: light) {
	.tag {
		--tag-lightness: 25%;
	}
}

.tag-small {
	padding: 0.35rem;
	line-height: 1;
}

.capsule {
	border-radius: 1rem;
}
</style>
