
proxy-frontend:
	echo "building frontend (nginx) image " ;\
	cd src/nginx ;\
	docker image build --tag dpsocialauth/frontend .

proxy-docker:
	echo "building proxy (bitly) image  " ;\
	cd src/oauth2-proxy ;\
	docker image build --tag dpsocialauth/proxy .

.PHONY: oauth2proxy-docker
