FROM nginx:1.27.0-alpine

ARG DOMAIN
ARG SRC_PATH
ARG PUBLIC_PATH

COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY nginx/nginx.conf /etc/nginx/nginx.conf

RUN sed -i -r "s|%PUBLIC_PATH%|$PUBLIC_PATH|g" /etc/nginx/conf.d/default.conf
RUN sed -i -r "s|%DOMAIN%|$DOMAIN|g" /etc/nginx/conf.d/default.conf

WORKDIR $SRC_PATH
