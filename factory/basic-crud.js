export default (path, $axios) => ({
    async getOne(id) {
        if (!id) {
            return null;
        }
        return await $axios.get(`/${path}/${id}`).then(res => res.data);
    },

    async get() {
        return await $axios.get(`/${path}`).then(res => res.data);
    },

    async create(data) {
        return await $axios.post(`/${path}/`, data).then(res => res.data);
    },

    async update(id, data) {
        return await $axios.patch(`/${path}/${id}`, data).then(res => res.data);
    },

    async delete(id) {
        return await $axios.delete(`/${path}/${id}`).then(res => res.data);
    }
});