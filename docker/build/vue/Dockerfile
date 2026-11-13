# Build stage
FROM node:current-alpine as build

WORKDIR /var/www/html

COPY package*.json ./

#RUN npm install
RUN npm ci --include=dev

RUN npm install -g cross-env

COPY . .

RUN npm run build

CMD ["npm", "run", "start"]
