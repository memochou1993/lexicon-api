Lexicon Server
===

## Installation

Download project.

```
git clone --recursive git@github.com:memochou1993/lexicon-server
```

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

Create `.env` file.

```
echo VUE_APP_API_URL="http://localhost:8001/api" >> .env
echo "VUE_APP_API_DEMO_TOKEN=2|..." >> .env
```

Build static files.

```
yarn build
```
