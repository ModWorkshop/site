<p align="center"><a href="https://modworkshop.net" target="_blank"><img src="https://modworkshop.net/assets/mws_logo_white.svg" width="200"></a></p>

# ModWorkshop Frontend

## Developing
Developing the backend, alongside the frontend, requires the use of [Docker](https://www.docker.com/).
Visit https://github.com/ModWorkshop/mws-docker-setup for more information.
While it is possible to install all dependencies by yourself, it is not that recommended as Docker can install all instantly.

## Docker-less installation
There isn't too much special things to do for the front-end.
We use Yarn mainly so just get NodeJS + Yarn, copy .env.example to .env and fill it.

Make sure to install the dependencies
```bash
yarn install
```

## Development
Start the development server on http://localhost:3000

```bash
yarn dev
```

## Production

Build the application for production:

```bash
yarn build
```

# Style Guide
Keep consistent style, function names are camelCase. 4 Tab Spaces (**Not 2**).


We recommend to look at the [documentation](https://nuxt.com).
