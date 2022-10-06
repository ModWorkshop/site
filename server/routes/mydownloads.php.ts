//Redirects old mydownloads.php links

export default defineEventHandler(async event => {
    const query = getQuery(event);

    if (query.did) {
        const did = parseInt(query.did as string);
        if (Number.isInteger(did)) {
            switch(query.action) {
            case 'view_down':
                return sendRedirect(event, `mod/${did}`);
            case 'edit_down':
                return sendRedirect(event, `mod/${did}/edit`);
            }
        }
    }

    if (query.action === 'categories') {
        return sendRedirect(event, 'games');
    } else if (query.action === 'mysubmissions') {
        if (!query.uid) {
            return sendRedirect(event, 'me');
        } else {
            const uid = parseInt(query.uid as string);
            if (Number.isInteger(uid)) {
                return sendRedirect(event, `user/${uid}`);
            }
        }
    }

    return sendRedirect(event, '');
});