# POS PC System - Deployment Guide

## GitHub Repository
Your project is now available at: https://github.com/Hongly21/pos_pc2

## Deploying to Render

### Prerequisites
1. A Render account (https://render.com)
2. Your GitHub repository connected to Render

### Step-by-Step Deployment

#### 1. Connect GitHub Repository
- Go to https://dashboard.render.com
- Click "New +" → "Web Service"
- Select "Connect a Repository"
- Authorize and select `Hongly21/pos_pc2`
- Click "Connect"

#### 2. Configure the Service
Use these settings for your web service:

| Field | Value |
|-------|-------|
| Name | `pos-pc-app` |
| Environment | `PHP` |
| Build Command | `./build.sh` |
| Start Command | `php artisan serve --host=0.0.0.0 --port=$PORT` |
| Plan | `Free` (or Starter if needed) |

#### 3. Add Environment Variables
Set these in the Render dashboard:

```
APP_NAME=POS_PC
APP_DEBUG=false
APP_ENV=production
APP_KEY=<generate using: php artisan key:generate>
LOG_CHANNEL=stack
DB_CONNECTION=mysql (or postgresql)
DB_HOST=<your-database-host>
DB_PORT=3306
DB_DATABASE=pos_pc
DB_USERNAME=<your-username>
DB_PASSWORD=<your-password>
REDIS_HOST=<your-redis-host>
REDIS_PORT=6379
```

#### 4. Optional: Add Database
If using Render's PostgreSQL:
- Go to "Databases" in Render dashboard
- Click "New Database"
- Name: `pos-pc-db`
- Set environment variables to connect to it

#### 5. Deploy
- Click "Create Web Service"
- Render will automatically deploy from your main branch
- Monitor the deployment in the Logs tab

### Environment Variables Needed

**Critical (Must Set):**
- `APP_KEY` - Generate with: `php artisan key:generate`
- `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

**Recommended:**
- `APP_DEBUG=false` (for production)
- `APP_ENV=production`
- `LOG_CHANNEL=stack` (or syslog)
- `REDIS_HOST` and `REDIS_PORT` (if using Redis)

### Post-Deployment

After successful deployment:

1. **Run Migrations**
   - Use Render's "Shell" tab to run:
   ```bash
   php artisan migrate --force
   ```

2. **Generate App Key** (if not set)
   ```bash
   php artisan key:generate
   ```

3. **Clear Cache**
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

### Database Setup

If not using Render's PostgreSQL, you can use:
- **MySQL**: Configure with external MySQL provider
- **PostgreSQL**: Use Render's native PostgreSQL service
- **SQLite**: Update `.env` with: `DB_CONNECTION=sqlite` (less recommended for production)

### Troubleshooting

**Build fails?**
- Check `build.sh` permissions: `chmod +x build.sh`
- View build logs in Render dashboard
- Ensure all dependencies in `composer.json` and `package.json` are compatible

**Application not running?**
- Check environment variables are set correctly
- Run migrations: `php artisan migrate --force`
- Check application logs in Render dashboard

**Static assets not loading?**
- Run: `php artisan storage:link`
- Ensure `public/storage` exists
- Check Vite build: `npm run build`

### Auto-Deploy from GitHub

Render will automatically redeploy whenever you push to the main branch. To control this:
1. Push code to GitHub
2. Render watches main branch and triggers builds automatically
3. Monitor progress in the Render dashboard

### Database Migrations

Your Laravel migrations will run during build (see `build.sh`). Ensure:
- All migrations in `database/migrations/` are correct
- Database credentials are set before build
- Use `--force` flag for production: `php artisan migrate --force`

---

**Need Help?**
- Render Docs: https://render.com/docs
- Laravel Deployment: https://laravel.com/docs/deployment
- GitHub: https://github.com/Hongly21/pos_pc2
