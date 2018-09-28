all: proxy-upstream proxy-docker proxy-frontend

proxy-frontend:
	echo "building frontend (nginx) image " ;\
	cd src/nginx ;\
	docker image build --tag dpsocialauth/frontend .

proxy-docker:
	echo "building proxy (bitly) image  " ;\
	cd src/oauth2-proxy ;\
	docker image build --no-cache --tag dpsocialauth/proxy .

proxy-upstream:
	echo "building upstream " ;\
	cd src/upstream ;\
	docker image build --tag dpsocialauth/upstream:php .

.PHONY: proxy-frontend proxy-docker all
