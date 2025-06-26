import { useStore } from "~/store";

interface NitroAdConfig {
    refreshLimit?: number;
    refreshTime?: number;
    renderVisibleOnly?: boolean;
    refreshVisibleOnly?: boolean;
    mediaQuery?: string;
    sizes: [string, string][];
    report?: {
        enabled?: boolean;
        icon?: boolean;
        wording?: string;
        position?: string;
    }
}

export default function(name: string, cfg: NitroAdConfig) {
    const { user } = useStore();

    onMounted(() => {
        if (user?.has_supporter_perks) {
            return;
        }

        if (import.meta.client) {
            window['nitroAds'].createAd(name, {
                "refreshLimit": 0,
                "refreshTime": 30,
                "renderVisibleOnly": false,
                "refreshVisibleOnly": true,
                ...cfg,
                "report": {
                    "enabled": true,
                    "icon": true,
                    "wording": "Report Ad",
                    "position": "bottom-right-side",
                    ...(cfg.report ?? {})
                },
            });
        }
    });
}