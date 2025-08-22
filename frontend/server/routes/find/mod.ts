// Redirects old mydownloads.php links

export default defineEventHandler(async event => {
	const query = getQuery(event);

	const game = query.gid ? parseInt(query.gid as string) : '';
	const q = query.q ? encodeURIComponent(query.q as string) : '';

	return sendRedirect(event, `/search/mods?query=${q}&game=${game}`);
});
