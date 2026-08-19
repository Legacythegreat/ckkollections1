Creating the Master Admin (manual)

1) Import the admins table schema

Use your hosting control panel or run via `mysql` client:

```bash
mysql -h <DB_HOST> -u <DB_USER> -p < <path/to>/sql/admin_schema.sql
```

2) Generate a password hash (on the server where PHP is available)

Replace `YourStrongPassword` with the admin password you want, then run:

```bash
php -r "echo password_hash('YourStrongPassword', PASSWORD_DEFAULT);"
```

This prints a bcrypt/hash string like `$2y$...` — copy it.

3) Insert the master admin record (use the hash from step 2)

Connect to MySQL and run (replace values):

```sql
USE `alcy_42591217_ckkollection`;
INSERT INTO admins (email, password_hash, is_master) VALUES ('admin@yourdomain.com', '$2y$...PASTE_HASH_HERE...', 1);
```

4) Verify: visit `https://your-site/admin` and log in with the email and the password you chose.

Notes
- The `is_master` flag (1) marks a master admin who can create/delete other admin users via the admin UI.
- Do NOT paste plaintext passwords into SQL — always insert the hashed value generated in step 2.
- If you prefer, you can run the `createAdmin()` helper in PHP to add users programmatically instead of inserting SQL directly.
