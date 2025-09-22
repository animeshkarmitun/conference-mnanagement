# 🧹 Manual Cache Clear - When cPanel Doesn't Work

## The Problem
You can't clear Laravel cache from cPanel, so the storage error persists.

## 🚀 Solution: Use PHP Scripts

### Step 1: Clear Cache
1. Upload `clear-cache.php` to your server root directory
2. Visit: `https://staginginstinctfusionx.xyz/clear-cache.php`
3. This will clear all Laravel caches automatically

### Step 2: Fix Storage Directories
1. Upload `server-fix.php` to your server root directory
2. Visit: `https://staginginstinctfusionx.xyz/server-fix.php`
3. This will create all missing storage directories

### Step 3: Test Your Site
1. Clear browser cache (Ctrl+F5)
2. Visit: `https://staginginstinctfusionx.xyz`
3. The error should be gone!

## 🔧 Manual File Deletion (if scripts don't work)

### Via cPanel File Manager:

1. **Delete these cache files** (if they exist):
   ```
   bootstrap/cache/config.php
   bootstrap/cache/packages.php
   bootstrap/cache/services.php
   bootstrap/cache/routes-v7.php
   bootstrap/cache/routes.php
   ```

2. **Clear storage cache**:
   - Go to `storage/framework/cache/`
   - Delete all files inside (except .gitkeep)

3. **Clear old sessions**:
   - Go to `storage/framework/sessions/`
   - Delete all files inside (except .gitkeep)

## 🔄 Alternative: Database Sessions

If file permissions still don't work:

### 1. Edit your .env file:
```env
SESSION_DRIVER=database
```

### 2. Create sessions table:
```sql
CREATE TABLE sessions (
    id varchar(255) NOT NULL,
    user_id bigint(20) unsigned NULL,
    ip_address varchar(45) NULL,
    user_agent text NULL,
    payload longtext NOT NULL,
    last_activity int(11) NOT NULL,
    PRIMARY KEY (id),
    KEY sessions_user_id_index (user_id),
    KEY sessions_last_activity_index (last_activity)
);
```

## 🧪 Quick Test

After applying any fix:
1. **Hard refresh** your browser (Ctrl+Shift+R)
2. **Visit your site**
3. **Try to register or login**
4. **Check if the error is gone**

## 📞 Still Having Issues?

If nothing works:
1. **Contact hosting support** - ask about Laravel file permissions
2. **Check if mod_rewrite is enabled**
3. **Verify PHP version is 8.1+**
4. **Consider switching to a Laravel-friendly host**

---

**The PHP scripts are the easiest solution - just upload and run them!** 🚀


