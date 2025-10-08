# Hostinger FTP Credentials Setup Guide

## Finding Your FTP Credentials in Hostinger

### Step 1: Login to Hostinger
1. Go to https://hpanel.hostinger.com
2. Login with your Hostinger account

### Step 2: Access FTP Accounts
1. From the dashboard, select your hosting plan
2. Go to **Files → FTP Accounts**
3. You'll see a table with FTP account details

### Step 3: FTP Credentials Format

You'll find these details:

**HOSTINGER_FTP_HOST:**
- Usually looks like: `ftp.aitechhub.store` or `123.456.789.012` (IP address)
- **NOT** the browser URL like `ftp://aitechhub.store/files/...`

**HOSTINGER_FTP_USER:**
- Usually looks like: `u123456789` or `username@aitechhub.store`
- May be your Hostinger username or auto-generated

**HOSTINGER_FTP_PASS:**
- The password you set when creating the FTP account
- If you don't remember it, you can reset it in the FTP Accounts page

**FTP PORT:**
- Standard FTP: Port `21`
- We'll use this in the deployment script

### Step 4: Deployment Directory

The path where files should be uploaded:
```
/public_html/aitechhub.store/
```

**Important:** The `/public` subdirectory inside should be your web root:
```
/public_html/aitechhub.store/public/  ← This is where domain points
```

## Visual Guide

### What You're Looking For in Hostinger Panel:

```
┌─────────────────────────────────────────────────────┐
│ FTP Accounts                                        │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Server:   ftp.aitechhub.store                      │ ← HOSTINGER_FTP_HOST
│ Username: u123456789                                │ ← HOSTINGER_FTP_USER
│ Password: ****************  [Change Password]      │ ← HOSTINGER_FTP_PASS
│ Port:     21                                        │
│                                                     │
└─────────────────────────────────────────────────────┘
```

## Common Mistakes to Avoid

❌ **WRONG:** `ftp://aitechhub.store/files/public_html/laravel/`
- This is a browser file manager URL, not FTP credentials

✅ **CORRECT:**
- **Host:** `ftp.aitechhub.store` (or IP like `154.56.78.90`)
- **User:** `u123456789` (or similar)
- **Pass:** Your FTP password
- **Port:** `21`
- **Remote Path:** `/public_html/aitechhub.store/`

## If You Can't Find FTP Accounts

### Option A: Create New FTP Account
1. In Hostinger Panel → **Files → FTP Accounts**
2. Click **Create FTP Account**
3. Set username and password
4. Note down the credentials shown

### Option B: Use SSH/SFTP Instead
If your Hostinger plan supports SSH (most do):
1. Go to **Advanced → SSH Access**
2. Enable SSH
3. Use those credentials for SFTP (more secure than FTP)

**For SFTP, you'd use:**
- **Host:** Same as FTP host
- **Port:** `22` (instead of 21)
- **Username:** SSH username
- **Password:** SSH password

## Testing Your FTP Credentials

### Using FileZilla (Recommended)
1. Download FileZilla (free FTP client)
2. Enter your credentials:
   - Host: `ftp.aitechhub.store`
   - Username: `u123456789`
   - Password: Your password
   - Port: `21`
3. Click **Quickconnect**
4. You should see `/public_html/` directory

### Using Command Line (Mac/Linux)
```bash
ftp ftp.aitechhub.store
# Enter username when prompted
# Enter password when prompted
# Type: pwd
# You should see your home directory
# Type: quit
```

## Next Steps After Finding Credentials

1. Add them as GitLab CI/CD secrets (as shown in previous guide)
2. Update `.gitlab-ci-hostinger.yml` if needed
3. Push to GitLab to trigger deployment

## Need Help?

If you're having trouble finding these, you can:
1. Contact Hostinger support chat (usually very quick)
2. Share a screenshot of your Hostinger Files section (hide sensitive info)
3. Check your Hostinger welcome email - often contains FTP details
