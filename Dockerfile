FROM node:23.3.0 as build
RUN mkdir -p /var/www/html/
RUN mkdir -p /home/qsl
WORKDIR /home/qsl
COPY package.json /home/qsl/package.json
COPY . /home/qsl
RUN npm install -g @angular/cli
RUN npm install
RUN npm run build

FROM nginx:alpine
COPY --from=build /home/qsl/dist/qsl-manager-web/browser/ /usr/share/nginx/html/
COPY nginx/nginx.conf /etc/nginx/

