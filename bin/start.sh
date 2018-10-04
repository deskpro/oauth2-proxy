#!/usr/bin/env bash

if [ -f src/docker-compose/docker-compose.dev.yml ]; then
    echo running with dev configuration
    (cd src/docker-compose && COMPOSE_PROJECT_NAME=oauth2-proxy docker-compose -f docker-compose.yml -f docker-compose.dev.yml up )
else
    echo running with prod configuration
    (cd src/docker-compose && COMPOSE_PROJECT_NAME=oauth2-proxy docker-compose -f docker-compose.yml -f docker-compose.prod.yml up )
fi

