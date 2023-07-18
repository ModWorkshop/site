

export default defineNuxtPlugin((nuxtApp) => {
    const launched = ref(false);
    nuxtApp.hook('app:created', () => {
        if (process.client) {
            Element.prototype._addEventListener = Element.prototype.addEventListener;
            Element.prototype._removeEventListener = Element.prototype.removeEventListener;
            Element.prototype.addEventListener = function(type,listener,useCapture=false) {
                this._addEventListener(type,listener,useCapture);
                if(!this.eventListenerList) this.eventListenerList = {};
                if(!this.eventListenerList[type]) this.eventListenerList[type] = [];
                this.eventListenerList[type].push( {type, listener, useCapture} );
            };
            Element.prototype.removeEventListener = function(type,listener,useCapture=false) {
                this._removeEventListener(type,listener,useCapture);
                if(!this.eventListenerList) this.eventListenerList = {};
                if(!this.eventListenerList[type]) this.eventListenerList[type] = [];
                for(let i=0; i<this.eventListenerList[type].length; i++){
                    if( this.eventListenerList[type][i].listener===listener && this.eventListenerList[type][i].useCapture===useCapture){
                        this.eventListenerList[type].splice(i, 1);
                        break;
                    }
                }
                if(this.eventListenerList[type].length==0) delete this.eventListenerList[type];
            };
        }
    });
    nuxtApp.hook('page:finish', () => {
        console.log('Page finished loading. Attempting to launch or reinstate ads.');

        if (document.eventListenerList) {
            const click = document.eventListenerList.click?.[0];
            if (click && click.useCapture) {
                document.removeEventListener('click', click.listener, click.useCapture);
            }
        }

        if (process.client && (window.egAps && typeof(window.egAps.reinstate) === "function")) {
            if (launched.value) { // Ads were not launched yet
                console.log(`Reinstate ads [URL: ${window.location.href}]`);
                window.egAps.reinstate();
            } else { // Launch and set the ref so next time we only reinstate them.
                console.log(`Launch ads`);
                window.egAps.launchAds();
                launched.value = true;
            }
        }
    });
})
