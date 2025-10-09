# SSH Setup Quick Reference

**Your SSH Public Key:**
```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAICEXl8/OhRH7KZJmpzUt1flQkKnA1B7tqlDN6/i7gV1l ramesh10779@github
```

---

## ✅ GitHub - DONE
- Status: **Configured and Working**
- Test: `ssh -T git@github.com`
- Repository: https://github.com/ramesh10779/claudeproject.git

---

## 🔧 GitLab - TODO

### Add SSH Key to GitLab

1. **Go to:** https://gitlab.com/-/profile/keys

2. **Paste this key:**
   ```
   ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAICEXl8/OhRH7KZJmpzUt1flQkKnA1B7tqlDN6/i7gV1l ramesh10779@github
   ```

3. **Title:** MacBook AITechHub

4. **Click:** Add key

5. **Test connection:**
   ```bash
   ssh -T git@gitlab.com
   ```
   Expected: "Welcome to GitLab, @ramesh10779!"

6. **Then you can push to GitLab:**
   ```bash
   git remote add gitlab git@gitlab.com:ramesh10779-group/ramesh10779-project.git
   git push gitlab main
   ```

---

## Quick Commands

```bash
# View your public key
cat ~/.ssh/id_ed25519.pub

# Test GitHub connection
ssh -T git@github.com

# Test GitLab connection
ssh -T git@gitlab.com

# Push to GitHub
git push origin main

# Push to GitLab
git push gitlab main

# Push to both
git push origin main && git push gitlab main
```

---

**Status:**
- ✅ GitHub SSH: Working
- ⏳ GitLab SSH: Pending (add key at link above)
- ✅ Code: Pushed to GitHub
- 📋 Next: Add SSH key to GitLab
