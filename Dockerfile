#### Stage Caddy ####
# FROM caddy:builder-alpine AS caddy-builder
# RUN xcaddy build
########

#### Stage 1 ####
FROM nginx:alpine as builder
# https://github.com/hoosin/alpine-nginx-nodejs/blob/master/Dockerfile
# Install nvm with node and npm
RUN apk add --update nodejs npm yarn

# Copy and set directory
COPY ./root /var/www/html
WORKDIR /var/www/html 

# Install deps and build the site
RUN yarn && yarn build
########

#### Stage 2 ####
FROM builder as prod

# Copy caddy stuff
# COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
# COPY ./Caddyfile /etc/caddy/Caddyfile
COPY ./nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/html
COPY --from=builder  /var/www/html/.output  ./.output

EXPOSE 80

RUN nginx -t

# All ready now
CMD ["/bin/sh", "-c", "nginx -g 'daemon off';", "node .output/server/index.mjs;"]

#### Stage 2 DEV ####
FROM builder as dev

WORKDIR /var/www/html
CMD yarn && yarn dev

EXPOSE 3000