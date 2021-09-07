export default function({$axios, app}) {
    // Set XSRF token for SSR
    const token = app.$cookies.get('XSRF-TOKEN');
    $axios.setHeader('X-XSRF-TOKEN', token);

    if (process.server) {
        return;
    }

    function check() {
        if (!app.$cookies.get('XSRF-TOKEN')) {
            console.log("Looks like the token has expired...");
            $axios.get('/sanctum/csrf-cookie', {headers: {'X-Requested-With': 'XMLHttpRequest'}});
        }
    }
    setInterval(check, 300000); // Check every 5 minutes
    check();
}