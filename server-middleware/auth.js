export default function(req, res, next) {
    req.headers.referer = 'localhost:3000';
    next();
}