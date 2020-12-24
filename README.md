Lexicon Server
===

## Installation

Start server.

```
docker-compose up -d
```

Run migrations and demo command.

```
docker-compose exec app bash
php artisan migrate
php artisan lexicon:demo
```

Copy `API Token` and `Personal Access Token`.

```
API Token: 1|...
Personal Access Token: 2|...
```

Enter `resources/js` folder.

```
cd resources/js
```

Create `.env.production.local` file.

```
echo VUE_APP_API_URL="http://localhost:8001/api" >> .env.production.local
echo "VUE_APP_API_DEMO_TOKEN=2|..." >> .env.production.local
```

Build static files.

```
yarn build
```
