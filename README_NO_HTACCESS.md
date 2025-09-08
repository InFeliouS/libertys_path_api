# Liberty's Path — XAMPP build (no .htaccess)

This build removes `.htaccess` and uses query-string routing so mod_rewrite is not required.

## How to run
1. Extract to `C:\xampp\htdocs\libertys_path_api`
2. (Optional) `composer install`
3. Configure `.env` for DB, or edit `src/config/db.php`
4. Import SQL from `/sql/`
5. Open:
   - `http://localhost/libertys_path_api/public/index.php?r=login`
   - or `http://localhost/libertys_path_api/index.php?r=login` (root shim provided)

## Notes
- Public assets: `/public/assets/*`
- API endpoints (unchanged): `/public/api/v1/*.php` (use relative `api/v1/...` calls in JS)
- Links and form actions converted to `index.php?r=...`
