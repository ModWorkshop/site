export default function({$axios}, inject) {
    const ftch = {
        async get(url, config) {
            return $axios.get(url, config).then(res => res.data);
        },

        async patch(url, config) {
            return $axios.patch(url, config).then(res => res.data);
        },

        async post(url, config) {
            return $axios.post(url, config).then(res => res.data);
        },

        async delete(url, config) {
            return $axios.delete(url, config).then(res => res.data);
        },
    };

    inject('ftch', ftch);
}