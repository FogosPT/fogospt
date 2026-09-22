# Puppeteer's official image ships Chromium plus a global puppeteer install
# at /home/pptruser/node_modules. Placing our tiny Fastify wrapper under
# /home/pptruser/app lets `require('puppeteer')` resolve via Node's normal
# upward lookup — no need to duplicate the ~300 MB puppeteer/chromium deps.
FROM ghcr.io/puppeteer/puppeteer:latest

WORKDIR /home/pptruser/app

# Copy the manifest first so Docker can cache the npm install layer across
# server.js edits.
COPY --chown=pptruser:pptruser package.json ./
RUN npm install --omit=dev --no-audit --no-fund

COPY --chown=pptruser:pptruser server.js ./

CMD ["node", "server.js"]
