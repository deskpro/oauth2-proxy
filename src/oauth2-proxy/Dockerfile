FROM alpine:3.5

MAINTAINER Régis Belson <me@regisbelson.fr>

RUN addgroup -S oauth2_proxy && adduser -S oauth2_proxy oauth2_proxy

RUN apk add --no-cache bash ca-certificates su-exec

ENV GOLANG_VERSION 1.8.1
ENV OAUTH2_PROXY_VERSION_MINOR 2.2
ENV OAUTH2_PROXY_VERSION_PATCH 2.2.0

RUN set -ex \
    \
	&& apk add --no-cache --virtual .fetch-deps curl \
	&& curl -L "https://github.com/bitly/oauth2_proxy/releases/download/v$OAUTH2_PROXY_VERSION_MINOR/oauth2_proxy-$OAUTH2_PROXY_VERSION_PATCH.linux-amd64.go$GOLANG_VERSION.tar.gz" \
	| tar -xz --strip-components=1 -C /usr/local/bin  \
  	&& chmod +x /usr/local/bin/oauth2_proxy \
  	&& apk del .fetch-deps

COPY docker-entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 4180

ENTRYPOINT ["/entrypoint.sh"]

CMD ["oauth2_proxy", "--help"]
