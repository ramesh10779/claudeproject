# Hostinger Credentials Setup for aitechhub.store

## GitLab CI/CD Variables to Add

Go to: **GitLab Project → Settings → CI/CD → Variables**

### Required Variables

#### 1. HOSTINGER_FTP_HOST
```
Value: aitechhub.store
OR
Value: ftp.aitechhub.store
```
- ✅ Check "Protect variable"
- ✅ Check "Mask variable"

#### 2. HOSTINGER_FTP_USER
```
Value: u631122123.aitechhub.store
```
This is your exact FTP username from Hostinger panel.

**Where to find:** Hostinger Panel → Files → FTP Accounts

- ✅ Check "Protect variable"
- ✅ Check "Mask variable"

#### 3. HOSTINGER_FTP_PASS
```
Value: [Your FTP password]
```
**Where to find:** Hostinger Panel → Files → FTP Accounts
- If you don't know it, click "Change Password" to set a new one

- ✅ Check "Protect variable"
- ✅ Check "Mask variable"

### Optional Variables (For SSH Access - More Secure)

If your Hostinger plan has SSH enabled:

#### 4. HOSTINGER_SSH_HOST (Optional)
```
Value: ssh.aitechhub.store
OR
Value: aitechhub.store
```

#### 5. HOSTINGER_SSH_USER (Optional)
```
Value: [Your SSH username]
```

#### 6. HOSTINGER_SSH_KEY (Optional)
```
Value: [Your private SSH key in base64 format]
```

To generate base64 SSH key:
```bash
cat ~/.ssh/id_rsa | base64
```

## Deployment Path

Files will be deployed to:
```
/public_html/
```

Web root (where your domain points):
```
/public_html/public/
```

## After Adding Variables

Once all three required variables are added:

1. **Commit and push to GitLab:**
   ```bash
   git add .
   git commit -m "Configure deployment"
   git push origin main
   ```

2. **Check deployment status:**
   - Go to: GitLab Project → CI/CD → Pipelines
   - Watch the deployment progress

3. **Access your site:**
   - https://aitechhub.store

## Testing FTP Connection Locally

Before deploying, you can test your FTP credentials:

```bash
# Using command line FTP
ftp 72.60.238.18
# Enter username: u631122123.aitechhub.store
# Enter password when prompted
# Type: ls
# Type: quit

# Or using LFTP (better)
lftp -u u631122123.aitechhub.store,your_password 72.60.238.18
```

## Next Steps

1. ✅ Add the 3 required GitLab variables
2. ⏳ Create MySQL database in Hostinger
3. ⏳ Upload .env file with database credentials
4. ⏳ Run first deployment
5. ⏳ Test the live site

## Support

If you have issues:
1. Check Hostinger Panel → Files → FTP Accounts for credentials
2. Contact Hostinger support chat (usually instant)
3. Check GitLab CI/CD logs for deployment errors
