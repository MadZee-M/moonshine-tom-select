.PHONY: assets

assets:
	yarn build
	cd ../.. && php artisan vendor:publish --tag=moonshine-tom-select-assets --force