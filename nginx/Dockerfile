FROM debian:jessie

RUN apt-get update && apt-get install -y nginx

ADD nginx.conf /etc/nginx/
ADD facebook_app.conf /etc/nginx/sites-available/

RUN ln -s /etc/nginx/sites-available/facebook_app.conf /etc/nginx/sites-enabled/facebook_app
RUN rm /etc/nginx/sites-enabled/default

RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN usermod -u 1000 www-data

CMD ["nginx"]

EXPOSE 81
EXPOSE 80
EXPOSE 443
