MAKEFLAGS += --silent

include ./docker/.env
export ENV_FILE = ./docker/.env
export UID=$(shell id -u)
export GID=$(shell id -g)
export USER_NAME=$(shell id -un)
export DOCKER_COMPOSE = docker-compose -f ./docker/docker-compose.yml

HOST_IS_SET=$(shell grep $(DOMAIN) /etc/hosts)

.PHONY: *
SHELL=/bin/bash -o pipefail

COLOR="\033[32m%-25s\033[0m %s\n"

.PHONY: help
help: ## Show this help
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {printf "  "${COLOR}, $$1, $$2}' ${MAKEFILE_LIST}

.PHONY: build_development
build_development: ## Builds for development containers
	${DOCKER_COMPOSE} build
	@${MAKE} set_host

.PHONY: start_development
start_development: ## Start development containers
	@${MAKE} stop_development
	@${MAKE} docker_compose_up_development
	@${MAKE} set_host
	@${MAKE} info

.PHONY: stop_development
stop_development: ## Stop development containers
	${DOCKER_COMPOSE} down -v --remove-orphans

.PHONY: docker_compose_up_development
docker_compose_up_development:
	${DOCKER_COMPOSE} up -d --remove-orphans

.PHONY: build_development_no-cache
build_development_no-cache:
	${DOCKER_COMPOSE} build --no-cache

.PHONY: shell sh
sh: shell
shell: ## Start shell into backend container
	@printf ${COLOR} 'Login to backend container';
	docker exec -ti ${APP_CONTAINER_NAME} bash

.PHONY: logs
logs: ## Show logs
	docker logs -f ${APP_CONTAINER_NAME}

.PHONY: info
info: ## Show info
	echo '****************************************'
	echo '****************************************'
	echo Site available here: http://${DOMAIN}
	echo '****************************************'
	echo '****************************************'

.PHONY: shell_root
shell_root:
	docker exec -ti --user root ${APP_CONTAINER_NAME} bash

.PHONY: set_host
set_host: ## Set link in /etc/hosts
ifneq ($(shell grep $(DOMAIN) /etc/hosts), )
	@echo "/etc/hosts already updated"
else
	sudo sh -c "echo \"\n127.0.0.1  ${DOMAIN}\" >> /etc/hosts"
	@echo "/etc/hosts updated"
endif

# Global
.DEFAULT_GOAL := help
