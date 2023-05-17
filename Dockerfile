#### Stage Caddy ####
FROM caddy:builder-alpine AS caddy-builder
RUN xcaddy build

COPY ./Caddyfile /etc/caddy/Caddyfile
########

#### Stage 1 ####
FROM node:18-alpine as builder

# Copy and set directory
COPY ./root /var/www/html
WORKDIR /var/www/html 

# Install deps and build the site
RUN yarn && yarn build
########

#### Stage 2 ####
FROM node:18-alpine as runner

COPY --from=builder  /var/www/html/.nuxt  /var/www/html/.nuxt
COPY --from=builder  /var/www/html/.output  /var/www/html/.output
COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy

WORKDIR /var/www/html 

# All ready now
EXPOSE 3000
CMD ["/bin/sh", "-c", "node .output/server/index.mjs & caddy run --config /etc/caddy/Caddyfile --adapter caddyfile "
