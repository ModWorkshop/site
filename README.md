<p align="center"><a href="https://modworkshop.net" target="_blank"><img src="https://modworkshop.net/assets/mws_logo_white.svg" width="200"></a></p>

# ModWorkshop Frontend

## Developing
Developing the backend, alongside the frontend, requires the use of [Docker](https://www.docker.com/).
Visit https://github.com/ModWorkshop/mws-docker-setup for more information.
While it is possible to install all dependencies by yourself, it is not that recommended as Docker can install all instantly.

## Docker Build and Run

You can access the server on http://localhost:3000 for development.<br>
**NOTE: The container will not run correctly without the backend container. You should use [docker-setup](https://github.com/ModWorkshop/mws-docker-setup) instead for ease of development.**

### Manually Building and Running Dockerfile

Run this command:

```bash
docker build . -f Dockerfile-dev -t mws-frontend-dev # Build Development Image
docker run -d mws-frontend-dev --name mws-frontend-dev -p 3000:3000 --env-file root/.env # Run Image as Container (name: mws-frontend-dev)
```

### Production Environment

Run this command:

```bash
docker build . -f Dockerfile-prod -t mws-frontend-prod # Build Production Image
docker run -d mws-frontend-prod --name mws-frontend-prod -p 3000:3000 --env-file root/.env # Run Image as Container (name: mws-frontend-prod)
```

## Docker-less installation
There isn't too much special things to do for the front-end.
We use Yarn mainly so just get NodeJS + Yarn, copy .env.example to .env and fill it.

Make sure to install the dependencies
```bash
yarn
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

## Style Guide
Keep consistent style, function names are camelCase. 4 Tab Spaces (**Not 2**).


We recommend to look at the [documentation](https://nuxt.com).
