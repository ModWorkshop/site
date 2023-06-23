import { Category } from "~~/types/models";

export default defineEventHandler(async event => {
    const { public: config } = useRuntimeConfig();

    if (event.context.params) {
        try {
            const data = await $fetch<Category>(`categories/${event.context.params.id}`, {
                baseURL: config.apiUrl
            });
    
            if (data.game) {
                return sendRedirect(event, `/g/${data.game.short_name}/mods?category=${data.id}`);
            }
        } catch (error) {
            //Do nothing
        }
    }
});