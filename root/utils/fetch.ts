import axios, { AxiosRequestConfig } from 'axios';
import qs from 'qs';

interface AxiosRequestConfigPlus extends AxiosRequestConfig {
    onResponse?: (response: any) => void;
}

export function buildQueryParams(params) {
    return qs.stringify(params, { 
        arrayFormat: 'brackets',
        addQueryPrefix: true,
        encoder: function (str, defaultEncoder, charset, type) {
            if (type == 'value' && typeof str == 'boolean') {
                return str ? 1 : 0;
            }
            return str;
        },
    });
}

export async function postRequest<T>(url: string, body?: object|null, config?: AxiosRequestConfigPlus = {}) {
    const { public: runtimeConfig } = useRuntimeConfig();
    const token = useCookie('XSRF-TOKEN', { readonly: true });

    config.headers ??= {};
    config.headers['X-XSRF-TOKEN'] = token.value;
    
    const response = await axios.request<T>({
        method: 'POST',
        url,
        data: body,
        withCredentials: true,
        baseURL: runtimeConfig.apiUrl,
        ...config
    });

    if (config?.onResponse) {
        config.onResponse(response);
    }

    return response.data;
}

export async function patchRequest<T>(url: string, body?: object|null, config?: AxiosRequestConfigPlus) {
    return postRequest<T>(url, body, {
        method: 'PATCH',
        ...config
    });
}

export async function deleteRequest<T>(url: string, body?: object|null, config?: AxiosRequestConfigPlus) {
    return postRequest<T>(url, body, {
        method: 'DELETE',
        ...config
    });
}
