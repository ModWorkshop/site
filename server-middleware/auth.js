export default function(req, res, next) {
    req.headers.referer = 'http://localhost:3000';
    next();
}