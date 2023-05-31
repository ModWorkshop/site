#### Stage Caddy ####
FROM caddy:builder-alpine AS caddy-builder
RUN xcaddy build
########

#### Stage 1 ####
FROM node:18.16.0-alpine as builder

# Copy and set directory
COPY ./root /var/www/html
WORKDIR /var/www/html 

# Install deps and build the site
RUN yarn && yarn build
########

#### Stage 2 ####
FROM node:18.16.0-alpine as runner

# Copy caddy stuff
COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
COPY ./Caddyfile /etc/caddy/Caddyfile

WORKDIR /var/www/html

COPY --from=builder  /var/www/html/.output  ./.output

# All ready now
EXPOSE 3000
CMD ["/bin/sh", "-c", "node .output/server/index.mjs & caddy run --config /etc/caddy/Caddyfile --adapter caddyfile"]

#### Stage 2 DEV ####
FROM node:18.16.0-alpine as dev

WORKDIR /var/www/html
CMD yarn && yarn dev
EXPOSE 3000