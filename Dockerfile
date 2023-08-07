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
FROM node:18.16.0-alpine as prod

# Copy caddy stuff
COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
COPY ./Caddyfile /etc/caddy/Caddyfile

WORKDIR /var/www/html

COPY --from=builder  /var/www/html/.output  ./.output
COPY --from=builder  /var/www/html/ecosystem.config.js ./ecosystem.config.js

# All ready now
EXPOSE 3000

ENV NITRO_PRESET=node_cluster
RUN yarn global add pm2

CMD ["/bin/sh", "-c", "pm2 start /var/www/html/ecosystem.config.js & caddy run --config /etc/caddy/Caddyfile --adapter caddyfile"]

#### Stage 2 DEV ####
FROM node:18.16.0-alpine as dev

ARG GIT_VERSION_HASH
ENV NUXT_VERSION_HASH $GIT_VERSION_HASH

WORKDIR /var/www/html
CMD yarn && yarn dev
EXPOSE 3000
