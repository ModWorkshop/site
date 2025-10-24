<p align="center"><a href="https://modworkshop.net" target="_blank"><img src="https://modworkshop.net/assets/mws_logo_white.svg" width="200"></a></p>

# ModWorkshop Monorepo

[![Crowdin](https://badges.crowdin.net/modworkshop/localized.svg)](https://crowdin.com/project/modworkshop)

This repository holds all of the code for modworkshop + docker compose + env files.

## Installing

### Requirements
Pretty much just Docker or Docker Desktop whatever works for you.
On Windows you'll need WSL2.

### Gettings things running:
1. Clone the repo.
2. Copy .env.example, naming it .env and fill any necessary information in both frontend and backend.
3. Run docker compose up -d.

### Notes
1. On Windows, you **should** place the folders in the distro of your choice, otherwise the performance is unbearable.
2. Default email: admin@modworkshop.net and password: admin.
3. The defaults are meant to be convenient for development they are not to be used in production. Make sure to setup with more care for production.
4. Due to the complexity of getting PHP setup (libraries + config), we currently don't have a dockerless guide.

## Security
If you discover a security vulnerability, please submit a [private vulnerability report](https://github.com/ModWorkshop/site/security/advisories/new).
Alternatively, you can email me directly at luffy@modworkshop.net.
