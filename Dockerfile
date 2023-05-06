FROM node:18-alpine

EXPOSE 3000

ENV NODE_OPTIONS=--max-old-space-size=8192

COPY ./root /var/www/html

WORKDIR /var/www/html 

RUN yarn && yarn build

CMD node .output/server/index.mjs
# CMD yarn && yarn dev