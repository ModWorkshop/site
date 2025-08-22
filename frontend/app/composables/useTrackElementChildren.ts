export default function (elementRef: Ref<HTMLElement>) {
	const count = ref(0);
	let observer: MutationObserver | undefined;
	onMounted(() => {
		if (elementRef.value) {
			count.value = elementRef.value.children.length;

			observer = new MutationObserver(() => count.value = elementRef.value.children.length);

			observer.observe(elementRef.value, { childList: true });
		}
	});

	onBeforeUnmount(() => {
		if (observer) {
			observer.disconnect();
			observer = undefined;
		}
	});

	return count;
}
