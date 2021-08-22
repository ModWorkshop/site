export default $axios => ({
    async getOne(id) {
        if (!id) {
            return null;
        }
        return await $axios.get(`/mods/${id}`).then(res => res.data);
    },

    async get() {
        return await $axios.get(`/mods`).then(res => res.data);
    },

    async create(data) {
        return await $axios.post('/mods/', data).then(res => res.data);
    },

    async update(id, data) {
        return await $axios.patch(`/mods/${id}`, data).then(res => res.data);
    },

    async delete(id) {
        return await $axios.delete(`/mods/${id}`).then(res => res.data);
    }
});