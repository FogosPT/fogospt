# syntax=docker/dockerfile:experimental

FROM node:10-alpine AS node-builder

WORKDIR /src

ARG npm_config_unsafe_perm=true
# hack for node to build in linux

RUN --mount=type=cache,id=apk,sharing=locked,target=/var/cache/apk ln -vs /var/cache/apk /etc/apk/cache && \
    apk add --update \
    git \
    python \
    make

## ideally here we copy only the files required for npm to build
COPY fogospt/ /src/

RUN --mount=type=cache,id=npm,sharing=locked,target=/root/.npm \
    npm install ## --production
## package.json deps need to be fixed to split between dev and prod deps

# RUN --mount=type=cache,id=npm,sharing=locked,target=/root/.npm \
#     npx run webpack


# FROM node:10-alpine AS node-exec

# EXPOSE 3000
# CMD node build/server.js
# WORKDIR /var/www
# ENV NODE_ENV=production

# RUN chown node /var/www

# USER node

# COPY --from=node-builder --chown=node /src/node_modules /var/www/node_modules
# # COPY --chown=node fogospt/package.json /var/www/package.json
# COPY --chown=node fogospt/public /var/www/
# ## ideally here we copy only the files required for node to serve

FROM alpine AS node-storage

RUN adduser -DHSu 100 nginx -s /sbin/nologin
WORKDIR /var/www

COPY --from=node-builder --chown=nginx:0 /src/node_modules /var/www/node_modules
# COPY --chown=nginx:0 fogospt/package.json /var/www/package.json
## ideally here we copy only the files required for nginx to serve
# COPY --chown=nginx:0 fogospt/public /var/www/
