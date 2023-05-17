FROM caddy:builder-alpine AS caddy-builder
RUN xcaddy build

COPY ./Caddyfile /etc/caddy/Caddyfile

#Multi stage build

# Stage 1
FROM node:18-alpine as builder

# Copy and set directory
COPY ./root /var/www/html
WORKDIR /var/www/html 

# Install deps and build the site
RUN yarn && yarn build

# Delete unused files/folders. Nuxt makes us .output and .nuxt that handle everything for us!
RUN  find -maxdepth 1 ! -name ".output" ! -name ".nuxt" -exec rm -rv {} \;

# Stage 2

FROM node:18-alpine as runner
WORKDIR /var/www/html 

COPY --from=builder ./ ./
COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy

# All ready now
EXPOSE 3000
CMD ["/bin/sh", "-c", "node .output/server/index.mjs & caddy run --config /etc/caddy/Caddyfile --adapter caddyfile "
