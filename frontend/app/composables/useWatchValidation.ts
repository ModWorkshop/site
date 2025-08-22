/**
 * Watches watchVal and triggers native validation errors, setting them to a ref.
 */
export default function (watchVal: Ref, element: Ref<HTMLTextAreaElement | HTMLInputElement | undefined>) {
	const error = ref();

	let timeoutId: NodeJS.Timeout;

	watch(watchVal, () => {
		if (element.value) {
			if (timeoutId) {
				clearTimeout(timeoutId);
			}
			timeoutId = setTimeout(() => {
				if (element.value) {
					error.value = element.value.validationMessage;
				}
			}, 500);
		}
	});

	return error;
}
