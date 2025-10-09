# Git SSH Setup Documentation

**Date:** 2025-10-09
**Repositories:**
- GitHub: https://github.com/ramesh10779/claudeproject.git
- GitLab: https://gitlab.com/ramesh10779-group/ramesh10779-project.git

---

## Overview

This document describes the SSH key setup process for authenticating with GitHub and GitLab to push code changes from the local development environment.

---

## SSH Key Generation

### Step 1: Generate SSH Key Pair

```bash
ssh-keygen -t ed25519 -C "ramesh10779@github" -f ~/.ssh/id_ed25519 -N ""
```

**Parameters:**
- `-t ed25519`: Use Ed25519 algorithm (modern, secure, fast)
- `-C "ramesh10779@github"`: Comment for identification
- `-f ~/.ssh/id_ed25519`: Output file location
- `-N ""`: No passphrase (for automation)

**Output:**
```
Generating public/private ed25519 key pair.
Your identification has been saved in /Users/rameshgnanasekaran/.ssh/id_ed25519
Your public key has been saved in /Users/rameshgnanasekaran/.ssh/id_ed25519.pub
The key fingerprint is:
SHA256:l/1y9l3uQg3wVw71kQKwdHPVGh+uKwqpphBYMlMyW0U ramesh10779@github
```

### Step 2: Display Public Key

```bash
cat ~/.ssh/id_ed25519.pub
```

**Generated Public Key:**
```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAICEXl8/OhRH7KZJmpzUt1flQkKnA1B7tqlDN6/i7gV1l ramesh10779@github
```

---

## GitHub Configuration

### Step 3: Add SSH Key to GitHub Account

**Method 1: Via Web Interface**

1. Navigate to: https://github.com/settings/ssh/new
2. Fill in the form:
   - **Title:** "MacBook AITechHub"
   - **Key type:** "Authentication Key"
   - **Key:** Paste the public key from above
3. Click "Add SSH key"
4. Confirm with GitHub password if prompted

**Method 2: Via Settings Menu**

1. Go to GitHub Settings: https://github.com/settings/keys
2. Click "New SSH key"
3. Enter title and paste key
4. Click "Add SSH key"

---

## Git Repository Configuration

### Step 4: Update Git Remote URL to Use SSH

```bash
git remote set-url origin git@github.com:ramesh10779/claudeproject.git
```

**Verify Remote URL:**
```bash
git remote -v
```

**Expected Output:**
```
origin  git@github.com:ramesh10779/claudeproject.git (fetch)
origin  git@github.com:ramesh10779/claudeproject.git (push)
```

---

## GitLab Configuration

### Step 4: Add SSH Key to GitLab Account

**Method 1: Via Direct Link**

1. Navigate to: https://gitlab.com/-/profile/keys
2. Fill in the form:
   - **Title:** "MacBook AITechHub"
   - **Key:** Paste the public key from above
   - **Expiration date:** Leave blank (no expiration)
3. Click "Add key"

**Method 2: Via Settings Menu**

1. Go to GitLab: https://gitlab.com
2. Click your avatar → Settings → SSH Keys
3. Enter title and paste key
4. Click "Add key"

---

## Testing SSH Connection

### Step 5: Test SSH Connection to GitHub

```bash
ssh -T git@github.com
```

**Expected Output:**
```
Hi ramesh10779! You've successfully authenticated, but GitHub does not provide shell access.
```

### Step 6: Test SSH Connection to GitLab

```bash
ssh -T git@gitlab.com
```

**Expected Output:**
```
Welcome to GitLab, @ramesh10779!
```

---

## File Locations

| File | Location | Purpose |
|------|----------|---------|
| Private Key | `~/.ssh/id_ed25519` | Secret key (never share) |
| Public Key | `~/.ssh/id_ed25519.pub` | Public key (add to GitHub/GitLab) |
| SSH Config | `~/.ssh/config` | Optional SSH configuration |

---

## Security Best Practices

### Private Key Security

✅ **DO:**
- Keep private key (`id_ed25519`) secure and never share
- Set proper permissions: `chmod 600 ~/.ssh/id_ed25519`
- Use passphrase for additional security (if not automating)
- Rotate keys periodically (annually recommended)

❌ **DON'T:**
- Share private key with anyone
- Commit private key to repository
- Use same key across multiple critical services
- Leave private key on shared computers

### Public Key Management

- Public key can be safely shared
- Can be added to multiple services (GitHub, GitLab, servers)
- Should include identifying comment for tracking

---

## SSH Key Permissions (Best Practices)

```bash
# Set correct permissions for SSH directory and keys
chmod 700 ~/.ssh
chmod 600 ~/.ssh/id_ed25519
chmod 644 ~/.ssh/id_ed25519.pub
chmod 644 ~/.ssh/known_hosts
```

---

## Optional: SSH Config File

Create `~/.ssh/config` for easier GitHub access:

```bash
cat > ~/.ssh/config << 'EOF'
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519
    IdentitiesOnly yes
EOF

chmod 600 ~/.ssh/config
```

**Benefits:**
- Automatic key selection
- Cleaner SSH commands
- Multiple key management

---

## Troubleshooting

### Issue: Permission Denied (publickey)

```bash
ssh -T git@github.com
# Permission denied (publickey)
```

**Solutions:**
1. Verify key is added to GitHub: https://github.com/settings/keys
2. Check SSH agent is running: `ssh-add -l`
3. Add key to agent: `ssh-add ~/.ssh/id_ed25519`
4. Verify remote URL uses SSH: `git remote -v`

### Issue: Host Key Verification Failed

```bash
ssh -T git@github.com
# Host key verification failed
```

**Solution:**
```bash
ssh-keyscan -t ed25519 github.com >> ~/.ssh/known_hosts
```

### Issue: Multiple SSH Keys

If you have multiple keys, create SSH config:

```bash
# ~/.ssh/config
Host github-work
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519_work

Host github-personal
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519_personal
```

Then use: `git clone git@github-work:user/repo.git`

---

## Git Operations with SSH

### Clone Repository
```bash
git clone git@github.com:ramesh10779/claudeproject.git
```

### Push Changes
```bash
git push origin main
```

### Pull Changes
```bash
git pull origin main
```

### Fetch Updates
```bash
git fetch origin
```

---

## Key Rotation Procedure

When rotating SSH keys (recommended annually):

1. **Generate new key:**
   ```bash
   ssh-keygen -t ed25519 -C "ramesh10779@github-2026" -f ~/.ssh/id_ed25519_new
   ```

2. **Add new key to GitHub**
3. **Test new key:**
   ```bash
   ssh -T -i ~/.ssh/id_ed25519_new git@github.com
   ```

4. **Update Git config:**
   ```bash
   git config core.sshCommand "ssh -i ~/.ssh/id_ed25519_new"
   ```

5. **Remove old key from GitHub**
6. **Backup and delete old key:**
   ```bash
   mv ~/.ssh/id_ed25519 ~/.ssh/id_ed25519.old.backup
   mv ~/.ssh/id_ed25519_new ~/.ssh/id_ed25519
   ```

---

## Integration with CI/CD

For automated deployments (GitLab CI, GitHub Actions):

1. **Generate deploy key (read-only):**
   ```bash
   ssh-keygen -t ed25519 -C "deploy-key" -f deploy_key -N ""
   ```

2. **Add to repository deploy keys:**
   - GitHub: Settings → Deploy keys → Add deploy key
   - Paste public key
   - Check "Allow write access" if needed

3. **Use in CI/CD:**
   ```yaml
   before_script:
     - eval $(ssh-agent -s)
     - echo "$SSH_PRIVATE_KEY" | ssh-add -
   ```

---

## Reference Links

- **GitHub SSH Documentation:** https://docs.github.com/en/authentication/connecting-to-github-with-ssh
- **GitHub SSH Keys Settings:** https://github.com/settings/keys
- **SSH Key Generation Guide:** https://docs.github.com/en/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent

---

## Summary

✅ **Completed Steps:**
1. Generated Ed25519 SSH key pair
2. Added public key to GitHub account
3. Configured Git repository to use SSH
4. Tested connection successfully

**SSH Key Fingerprint:** `SHA256:l/1y9l3uQg3wVw71kQKwdHPVGh+uKwqpphBYMlMyW0U`

**Repository URL:** `git@github.com:ramesh10779/claudeproject.git`

**Status:** ✅ Ready for secure Git operations

---

**Last Updated:** 2025-10-09
**Maintained By:** AITechHub Store DevOps Team
