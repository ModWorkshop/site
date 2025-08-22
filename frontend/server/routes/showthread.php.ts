// Redirects old mydownloads.php links

export default defineEventHandler(async event => {
	const query = getQuery(event);

	if (query.tid) {
		const tid = parseInt(query.tid as string);
		return sendRedirect(event, `thread/${tid}`);
	}

	return setResponseStatus(event, 404);
});
