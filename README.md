# laravel-multi-tenancy-demo

Allows the creation of e-commerce sites (tenants) each with its own subdomain, users and authentication session.

> See the [`without-packages`](https://github.com/iagobruno/laravel-multi-tenancy-demo/tree/without-packages) branch for a manual implementation with a single database.

# Getting started

Clone this repo and run commands in the order below:

```
composer install
yarn install
cp .env.example .env # And edit the values
php artisan key:generate
```

Then start Docker containers using Sail:

```
sail up -d
```

Run the migrations

```
sail artisan migrate --seed
```

### Front-end assets

Open another terminal tab and run the command below to compile front-end assets:

```
yarn run dev
```

Now you can access the project at http://localhost in your browser.
