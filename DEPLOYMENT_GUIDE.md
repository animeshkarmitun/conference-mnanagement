# Shared Hosting Deployment Guide

## For Shared Hosting (No npm support)

### Files to Upload

1. **Upload the entire Laravel project** to your `public_html` folder
2. **Make sure the `public/css/app.css` file is included** - this contains all your Tailwind CSS styles

### File Structure on Server

Your server should have this structure:
```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── css/
│   │   └── app.css          ← This is your static CSS file
│   ├── index.php
│   └── .htaccess
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
└── composer.json
```

### Important Notes

1. **The `public/css/app.css` file contains all your Tailwind CSS styles** - this is what your application will use instead of Vite
2. **Alpine.js is loaded via CDN** - no need for local JavaScript files
3. **Make sure your `.env` file is configured for production**:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://staginginstinctfusionx.xyz
   ```

### If You Need to Update Styles

If you need to update your CSS styles in the future:

1. **On your local machine**, run:
   ```bash
   npx tailwindcss -i ./resources/css/app.css -o ./public/css/app.css --minify
   ```

2. **Upload the updated `public/css/app.css` file** to your server

### Troubleshooting

- **If styles aren't loading**: Check that `public/css/app.css` exists and is accessible
- **If the site looks broken**: Make sure all files were uploaded correctly
- **If you get 404 errors**: Verify your `.htaccess` file is in the `public_html` directory

### Benefits of This Approach

- ✅ Works on shared hosting (no npm required)
- ✅ Fast loading (static CSS file)
- ✅ No build process needed on server
- ✅ Easy to update (just replace the CSS file)
- ✅ Compatible with all hosting providers 