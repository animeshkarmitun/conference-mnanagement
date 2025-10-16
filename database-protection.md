# Database Protection Guidelines

## ⚠️ IMPORTANT: Preventing Database Clearing

### What Happened
Your database was automatically cleared because:
- **PHPUnit tests were run** with `RefreshDatabase` trait
- This trait **completely wipes the database** for each test
- The trait was applied to **all Feature tests** globally

### Prevention Measures

#### 1. **Never Run Tests in Production**
```bash
# ❌ DON'T DO THIS in production
php artisan test

# ✅ Use specific test files instead
php artisan test tests/Unit/RoomAllocationValidationTest.php
```

#### 2. **Use Test-Specific Database**
```bash
# Set up a separate test database
DB_CONNECTION=mysql_test
DB_DATABASE=unb_cgs_test
```

#### 3. **Check Environment Before Running Tests**
```bash
# Always check your environment first
php artisan env
```

#### 4. **Use Database Transactions Instead**
For tests that need database isolation, use `DatabaseTransactions` instead of `RefreshDatabase`:

```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MyTest extends TestCase
{
    use DatabaseTransactions; // Rolls back instead of clearing
}
```

### Recovery Commands

#### Restore Database from Seeders
```bash
php artisan db:seed
```

#### Check Database Status
```bash
php artisan tinker --execute="
echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
echo 'Participants: ' . \App\Models\Participant::count() . PHP_EOL;
echo 'Hotels: ' . \App\Models\Hotel::count() . PHP_EOL;
"
```

### Best Practices

1. **Always backup before testing**
2. **Use separate test database**
3. **Run tests in isolated environment**
4. **Never run `php artisan test` in production**
5. **Use specific test files instead of global test runs**

### Emergency Recovery

If database is cleared again:
1. Run `php artisan db:seed` to restore data
2. Check logs for what caused the clearing
3. Implement prevention measures
4. Consider setting up automated backups
