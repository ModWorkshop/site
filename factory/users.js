export default $axios => ({
    async getOne(id) {
        if (!id) {
            return null;
        }
        return await $axios.get(`/users/${id}`).then(res => res.data);
    }
});