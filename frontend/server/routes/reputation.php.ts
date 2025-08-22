// Redirects old mydownloads.php links

export default defineEventHandler(async event => {
	const query = getQuery(event);

	if (query.uid) {
		const uid = parseInt(query.uid as string);
		return sendRedirect(event, `user/${uid}`);
	}

	return setResponseStatus(event, 404);
});
