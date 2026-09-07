# Deploying Money Manage to Render.com with Docker 🚀

This guide explains how to deploy your Laravel 12 + Vue 3 application to [Render.com](https://render.com) using the Docker setup.

---

## 📁 Docker Structure Overview

```
money-manage/
├── Dockerfile               # Multi-stage production Dockerfile (Node Vite -> PHP 8.2 + Nginx)
├── .dockerignore            # Excludes node_modules, vendor, tests, etc.
├── render.yaml              # Render Blueprint specification for 1-click / Blueprint deploy
├── docker-compose.yml       # For testing Docker setup locally
└── docker/
    ├── nginx/
    │   ├── default.conf     # Virtual host configuration with Render $PORT support
    │   └── nginx.conf       # Optimized Nginx core config
    ├── php/
    │   ├── php.ini          # PHP production settings
    │   ├── opcache.ini      # PHP OPcache optimization
    │   └── www.conf         # PHP-FPM pool config with clear_env=no
    ├── supervisor/
    │   └── supervisord.conf # Process manager for Nginx & PHP-FPM
    └── entrypoint.sh        # Startup script (migrations, cache, storage link)
```

---

## 🛠️ Method 1: Deploy as a Web Service (Recommended)

### Step 1: Push code to GitHub / GitLab
Commit and push your files including the new `Dockerfile` and `docker/` folder:
```bash
git add .
git commit -m "feat: add docker configuration for Render deployment"
git push origin main
```

### Step 2: Create a Web Service on Render
1. Log in to [Render Dashboard](https://dashboard.render.com).
2. Click **New +** → **Web Service**.
3. Connect your Git repository (`money-manage`).
4. Set the following details:
   - **Name**: `money-manage` (or your preferred name)
   - **Region**: Choose the closest region (e.g., Singapore, Frankfurt, Oregon)
   - **Branch**: `main` (or your deployment branch)
   - **Language / Runtime**: `Docker`
   - **Dockerfile Path**: `./Dockerfile`
   - **Instance Type**: `Free` or `Starter` ($7/mo recommended for production)

### Step 3: Configure Environment Variables
Under the **Environment Variables** section, add the following:

| Key | Value | Description |
|---|---|---|
| `APP_NAME` | `Money Manage` | Application name |
| `APP_ENV` | `production` | Set environment to production |
| `APP_KEY` | *(Generate using `php artisan key:generate --show`)* | **Required** Laravel 32-char key |
| `APP_DEBUG` | `false` | Disable debug mode in production |
| `APP_URL` | `https://money-manage.onrender.com` | Your Render service URL |
| `LOG_CHANNEL` | `stderr` | Route logs to Render console |
| `LOG_LEVEL` | `info` | Log level |
| `DB_CONNECTION` | `mysql` *(or `pgsql`)* | Database driver |
| `DB_HOST` | `your-db-host` | Database host |
| `DB_PORT` | `3306` *(or `5432` for pgsql)* | Database port |
| `DB_DATABASE` | `your_database_name` | Database name |
| `DB_USERNAME` | `your_db_username` | Database user |
| `DB_PASSWORD` | `your_db_password` | Database password |
| `SESSION_DRIVER` | `database` | Store sessions in DB |
| `CACHE_STORE` | `database` | Cache store |
| `QUEUE_CONNECTION` | `database` | Queue driver |
| `RUN_MIGRATIONS` | `true` | Runs `php artisan migrate --force` on startup |

### Step 4: Health Check (Optional)
In **Advanced Settings**:
- **Health Check Path**: `/up` (Laravel 12 built-in health check endpoint)

### Step 5: Deploy
Click **Create Web Service**. Render will automatically build the multi-stage Docker container and start your service!

---

## 🗄️ Database Options for Render

1. **External MySQL (Recommended for existing MySQL setup)**:
   - Free/Managed cloud MySQL providers:
     - [Aiven](https://aiven.io) (Free tier MySQL)
     - [TiDB Cloud](https://tidbcloud.com) (Free tier Serverless MySQL)
     - [Railway](https://railway.app) (MySQL addon)
     - [PlanetScale](https://planetscale.com)
   - Fill in the `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in Render's environment variables.

2. **Render PostgreSQL (Native on Render)**:
   - Click **New +** → **PostgreSQL** in Render.
   - Set `DB_CONNECTION=pgsql` in your Web Service environment variables and copy the credentials from the PostgreSQL database dashboard.

---

## ⚡ Running Seeders or Artisan Commands

To run database seeders or other artisan commands:
1. Go to your Web Service in the Render Dashboard.
2. Click the **Shell** tab (opens a live SSH-like terminal inside the running container).
3. Run:
   ```bash
   php artisan db:seed
   ```
   or any other command like:
   ```bash
   php artisan route:list
   php artisan tinker
   ```

---

## 🧪 Testing Docker Locally

To test the container before deploying:
```bash
docker compose up --build
```
Access the application at `http://localhost:8080`.
