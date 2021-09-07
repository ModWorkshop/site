export default function(req, res, next) {
    req.headers.referer = '127.0.0.1:3000';
    next();
}