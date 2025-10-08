# GitLab CI/CD Variables - Quick Setup Guide

## Your Exact FTP Details

```
FTP IP:       72.60.238.18
FTP Hostname: ftp.aitechhub.store
FTP Username: u631122123.aitechhub.store
Upload Path:  /public_html
```

## Step-by-Step: Add GitLab Variables

### 1. Navigate to GitLab Settings

1. Go to your GitLab project
2. Click **Settings** (left sidebar)
3. Click **CI/CD**
4. Find **Variables** section
5. Click **Expand**

### 2. Add Variable #1: FTP Host

Click **Add variable** and enter:

```
Key:   HOSTINGER_FTP_HOST
Value: 72.60.238.18
```

**Settings:**
- ✅ Check **Protect variable**
- ✅ Check **Mask variable**
- ❌ Leave **Expand variable reference** unchecked

Click **Add variable**

### 3. Add Variable #2: FTP Username

Click **Add variable** and enter:

```
Key:   HOSTINGER_FTP_USER
Value: u631122123.aitechhub.store
```

**Settings:**
- ✅ Check **Protect variable**
- ✅ Check **Mask variable**
- ❌ Leave **Expand variable reference** unchecked

Click **Add variable**

### 3. Add Variable #3: FTP Password

Click **Add variable** and enter:

```
Key:   HOSTINGER_FTP_PASS
Value: [Your FTP password from Hostinger panel]
```

**Where to find password:**
- Hostinger Panel → Files → FTP Accounts
- Look for username `u631122123.aitechhub.store`
- Click "Show password" or "Change password"

**Settings:**
- ✅ Check **Protect variable**
- ✅ Check **Mask variable**
- ❌ Leave **Expand variable reference** unchecked

Click **Add variable**

## ✅ Verification

After adding all 3 variables, you should see:

```
HOSTINGER_FTP_HOST     Protected, Masked
HOSTINGER_FTP_USER     Protected, Masked
HOSTINGER_FTP_PASS     Protected, Masked
```

## Next: Deploy!

Once variables are added:

```bash
git add .
git commit -m "Configure Hostinger deployment"
git push origin main
```

**Watch deployment:**
- GitLab Project → CI/CD → Pipelines
- You'll see: `build:customer` → `deploy:hostinger`
- Takes ~5-10 minutes

## Visual Guide

### What the GitLab Variables Page Should Look Like:

```
┌─────────────────────────────────────────────────────────────┐
│ Variables                                                   │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ [Add variable]                                              │
│                                                             │
│ Key                      | Value     | Options              │
│ ───────────────────────────────────────────────────────     │
│ HOSTINGER_FTP_HOST       | ********* | Protected, Masked    │
│ HOSTINGER_FTP_USER       | ********* | Protected, Masked    │
│ HOSTINGER_FTP_PASS       | ********* | Protected, Masked    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

## Troubleshooting

### "Cannot find GitLab project"
- Make sure you're logged into GitLab
- URL format: `https://gitlab.com/your-username/aitechhub-store`

### "Cannot add variable"
- You need **Maintainer** or **Owner** role
- Check project permissions

### "How do I find my FTP password?"
1. Login to Hostinger Panel: https://hpanel.hostinger.com
2. Go to **Files** → **FTP Accounts**
3. Find username: `u631122123.aitechhub.store`
4. Click **Change Password** to view or reset

### "Variables added but deployment fails"
1. Check CI/CD logs: GitLab → CI/CD → Pipelines → Click failed job
2. Verify FTP credentials by testing locally:
   ```bash
   ftp 72.60.238.18
   # Username: u631122123.aitechhub.store
   # Password: [your password]
   ```

## Security Notes

- ✅ **Protected variables** only available on protected branches (main/master)
- ✅ **Masked variables** won't show in CI/CD logs
- ✅ Never commit passwords to Git
- ✅ Never share FTP credentials publicly

## After Deployment

Once deployment succeeds:

1. **Set Document Root** in Hostinger:
   - Domains → aitechhub.store → Manage
   - Document Root: `/public_html/public`

2. **Create Database**:
   - Databases → MySQL Databases → Create
   - Name: `u631122123_aitechhub`

3. **Upload .env file**:
   - Via File Manager or FTP
   - Path: `/public_html/.env`

4. **Run migrations via SSH**:
   ```bash
   ssh u631122123.aitechhub.store@aitechhub.store
   cd /public_html
   php artisan migrate --force
   ```

5. **Visit site**: https://aitechhub.store

## Support

- **GitLab CI/CD Docs**: https://docs.gitlab.com/ee/ci/variables/
- **Hostinger Support**: https://www.hostinger.com/support
- **Project Docs**: See HOSTINGER_QUICK_START.md
