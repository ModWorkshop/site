/**
 * A composable that handles validation errors when they happen while the tab is not being rendered
 * The composable simply moves the tab to the correct tab and reports the validation message so the user sees it
 *
 * Note: the input *must* be in an m-tab and m-form for this to work
 *
 * @returns function to call on @invalid event on input/textarea/etc
 */
export default function () {
	const queryTab = useRouteQuery('tab');
	const tabName = inject<string | undefined>('tab-name', undefined);
	const form = inject<Ref<HTMLFormElement | undefined>>('form', ref());

	let ignoreOnce = false;
	return function () {
		if (queryTab.value !== tabName) {
			queryTab.value = tabName;

			if (!ignoreOnce) {
				setTimeout(() => {
					ignoreOnce = true;
					if (!form?.value?.checkValidity()) {
						form?.value?.reportValidity();
					}
					ignoreOnce = false;
				}, 100);
			}
		}
	};
}
