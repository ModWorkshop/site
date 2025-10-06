import clone from 'rfdc/default';

// This is only added due to VueUse's one breaking on Nuxt 3 + Adding casting support because typescript is shit sometimes

// Trying to make this work with typescript is just... No
let queue = {};

export default function (name, defaultValue, cast, statefully) {
	const router = useRouter();
	const route = useRoute();
	const isLoading = useState('loading');

	if (Array.isArray(defaultValue)) {
		cast = 'array';
	}

	cast ??= typeof defaultValue;

	let current;
	if (statefully) {
		current = useState(name, () => route.query[name]);
	} else {
		current = ref(route.query[name]);
	}

	// Listen for changes, but avoid doing so when when the composable did it
	watch(() => route.query[name], v => {
		if (!queue[name]) {
			current.value = v;
		}
	});

	let lastTImeout;

	function replaceRoute() {
		if (isLoading.value) { // Prevents breaking page, remove when https://github.com/nuxt/nuxt/issues/13350 is resolved
			if (lastTImeout) {
				clearTimeout(lastTImeout);
			}
			lastTImeout = setTimeout(replaceRoute, 50);
		} else {
			router.replace({ query: { ...route.query, ...queue } });
			nextTick(() => queue = {});
		}
	}

	return computed({
		get() {
			const data = current.value;
			if (data === null || data === undefined) {
				return (cast === 'array' ? clone(defaultValue) : defaultValue) ?? null;
			}

			if (cast === 'number') {
				return parseInt(data);
			} else if (cast === 'boolean') {
				return data ? 1 : 0;
			} else if (typeof cast === 'function') {
				return cast(data);
			} else if (cast === 'array') {
				if (typeof data === 'string') {
					return data.split(',').map(value => parseInt(value));
				} else {
					return clone(defaultValue);
				}
			}

			return data;
		},
		set(v) {
			if (cast === 'array') {
				if (v.length) {
					v = v.join(',');
				} else {
					v = undefined;
				}
			}

			queue[name] = (v === defaultValue || v === null) ? undefined : v;
			current.value = queue[name];

			nextTick(replaceRoute);
		}
	});
}
