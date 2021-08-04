export default function({$axios, app}) {
    function check() {
        if (!app.$cookies.get('XSRF-TOKEN')) {
            console.log("Looks like the token has expired...");
            $axios.get('/sanctum/csrf-cookie', {headers: {'X-Requested-With': 'XMLHttpRequest'}});
        }
    }
    setInterval(check, 300000); // Check every 5 minutes
    check();
}