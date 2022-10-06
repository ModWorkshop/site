import { Category } from "~~/types/models";

export default defineEventHandler(async event => {
    const { public: config } = useRuntimeConfig();

    try {
        const data = await $fetch<Category>(`categories/${event.context.params.id}`, {
            baseURL: config.apiUrl
        });

        return sendRedirect(event, `/g/${data.game.short_name}?category=${data.id}`);
    } catch (error) {
        //Do nothing
    }
});