export default $axios => ({
    async getOne(id) {
        if (!id) {
            return null;
        }
        return await $axios.get(`/mods/${id}`).then(res => res.data);
    }
});