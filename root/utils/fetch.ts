import axios, { AxiosRequestConfig } from 'axios';
import qs from 'qs';

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

export async function postRequest<T>(url: string, body?: object|null, config?: AxiosRequestConfig) {
    const { public: runtimeConfig } = useRuntimeConfig();

    const { data } = await axios.request<T>({
        url,
        data: body,
        withCredentials: true,
        baseURL: runtimeConfig.apiUrl,
        ...config
    });

    return data;
}

export async function patchRequest<T>(url: string, body?: object|null, config?: AxiosRequestConfig) {
    return postRequest<T>(url, body, {
        method: 'PATCH',
        ...config
    });
}

export async function deleteRequest<T>(url: string, body?: object|null, config?: AxiosRequestConfig) {
    return postRequest<T>(url, body, {
        method: 'DELETE',
        ...config
    });
}
