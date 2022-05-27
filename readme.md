## MODX Slim PHP Boilerplate API

This API example is intened as a starting point for your own MODX integration. The idea is to use MODX as a headless CMS, or create an easy method for having your site talk dynamically to MODX without additional parsing time. 

### Installation

Clone (or fork) this repository. You can place it wherever, but for the following examples I will assume it is in a folder named "api" in the base directory of your MODX site. 

Next, copy the `/config/local.sample.php` file and rename it `/config/local.php`. You can adjust any paths or variables as needed. 

Next adjust your nginx config to handle requests to the `/api/index.php` file.
E.g. 
```
location /api {
rewrite ^/(.*)/$ /$1 permanent;
try_files $uri $uri/ /api/index.php?$args;
}
```

That's it! You can now access the API by hitting your site at /api/!

### Adjusting Routes
The route handling is managed in the `config/routes.php` file. You can see the initial examples there. You will set your listener path, allowed methods and Controller. 

### Controllers
The Controllers are what handle the response. This project has a few set up for you in `/src/Controllers/`. You can create your own here based off the ones provide.

### Extending
If you need additional services added. You can adjust the `config/dependencies.php` file. This will inject external dependencies that can be used in your controllers.

## Have Fun!
This has the opportunity to be a pretty robust tool, and we've created this boilerplate as a clean slate example of how to get started. 
