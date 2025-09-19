import axios from 'axios';
import type { AxiosRequestConfig, AxiosResponse } from 'axios';
import qs from 'qs';

interface AxiosRequestConfigPlus extends AxiosRequestConfig {
	onResponse?: (response: AxiosResponse) => void;
}

export function buildQueryParams(params, addQueryPrefix = true) {
	return qs.stringify(params, {
		arrayFormat: 'indices',
		addQueryPrefix,
		encoder: function (str, defaultEncoder, charset, type) {
			if (type === 'value' && typeof str === 'boolean') {
				return str ? 1 : 0;
			}
			return str;
		}
	});
}

export async function postRequest<T>(url: string, body?: object | null, config: AxiosRequestConfigPlus = {}) {
	const { public: runtimeConfig, innerApiUrl } = useRuntimeConfig();
	const token = useCookie('XSRF-TOKEN', { readonly: true });

	config.headers ??= {};
	config.headers['X-XSRF-TOKEN'] = token.value;

	const response = await axios.request<T>({
		method: 'POST',
		url,
		data: body,
		withCredentials: true,
		baseURL: import.meta.client ? runtimeConfig.apiUrl : innerApiUrl,
		...config
	});

	if (config?.onResponse) {
		config.onResponse(response);
	}

	return response.data;
}

export async function getRequest<T>(url: string, body?: object | null, config?: AxiosRequestConfigPlus) {
	return postRequest<T>(url + buildQueryParams(body), body, {
		method: 'GET',
		...config
	});
}

export async function patchRequest<T>(url: string, body?: object | null, config?: AxiosRequestConfigPlus) {
	return postRequest<T>(url, body, {
		method: 'PATCH',
		...config
	});
}

export async function deleteRequest<T>(url: string, body?: object | null, config?: AxiosRequestConfigPlus) {
	return postRequest<T>(url, body, {
		method: 'DELETE',
		...config
	});
}
