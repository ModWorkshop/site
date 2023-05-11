FROM caddy:builder-alpine AS caddy-builder
RUN xcaddy build

FROM node:18-alpine

EXPOSE 3000

ENV NODE_OPTIONS=--max-old-space-size=8192

# Configure caddy
COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
COPY ./Caddyfile /etc/caddy/Caddyfile

COPY ./root /var/www/html

WORKDIR /var/www/html 

RUN yarn && yarn build

CMD ["/bin/sh", "-c", "node .output/server/index.mjs & caddy run --config /etc/caddy/Caddyfile --adapter caddyfile "]

# CMD yarn && yarn dev