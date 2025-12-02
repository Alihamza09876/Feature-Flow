# Multi-User Quick Reference Card

## 🔐 Authentication Pattern

### Controller Methods
```php
// ✅ CORRECT - Filter by authenticated user
public function index()
{
    return Model::where('user_id', auth()->id())->get();
}

// ❌ WRONG - Returns all users' data
public function index()
{
    return Model::all();
}
```

### Creating Records
```php
// ✅ CORRECT - Auto-assign user_id
public function store(Request $request)
{
    $data = $request->validated();
    $data['user_id'] = auth()->id();
    return Model::create($data);
}

// ❌ WRONG - Allows client to set user_id
public function store(Request $request)
{
    return Model::create($request->all());
}
```

### Updating/Deleting Records
```php
// ✅ CORRECT - Validate ownership
public function update(Request $request, $id)
{
    $model = Model::where('user_id', auth()->id())->findOrFail($id);
    $model->update($request->validated());
    return $model;
}

// ❌ WRONG - No ownership check
public function update(Request $request, $id)
{
    $model = Model::findOrFail($id);
    $model->update($request->all());
    return $model;
}
```

## 📋 Model Checklist

Every user-owned model must have:

```php
class YourModel extends Model
{
    // 1. Include user_id in fillable
    protected $fillable = [
        'user_id',  // ✅ Required
        // ... other fields
    ];

    // 2. Add user relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

## 🛣️ Route Protection

```php
// ✅ CORRECT - Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});

// ❌ WRONG - Unprotected routes
Route::apiResource('tasks', TaskController::class);
```

## 🔍 Common Queries

```php
// Get all records for current user
$records = Model::where('user_id', auth()->id())->get();

// Get specific record with ownership check
$record = Model::where('user_id', auth()->id())->findOrFail($id);

// Count user's records
$count = Model::where('user_id', auth()->id())->count();

// Get with relationships
$records = Model::where('user_id', auth()->id())
    ->with('relationship')
    ->get();
```

## ⚠️ Common Mistakes

### ❌ DON'T
```php
// Don't use Model::all()
$all = Model::all();

// Don't trust client-provided user_id
Model::create($request->all());

// Don't skip ownership validation
$model = Model::find($id);
$model->delete();

// Don't forget auth middleware
Route::get('/tasks', [TaskController::class, 'index']);
```

### ✅ DO
```php
// Filter by authenticated user
$userRecords = Model::where('user_id', auth()->id())->get();

// Set user_id server-side
$data['user_id'] = auth()->id();
Model::create($data);

// Validate ownership
$model = Model::where('user_id', auth()->id())->findOrFail($id);
$model->delete();

// Protect routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
});
```

## 🧪 Testing Checklist

- [ ] User A cannot see User B's data
- [ ] User A cannot modify User B's data
- [ ] User A cannot delete User B's data
- [ ] Unauthenticated requests return 401
- [ ] Unauthorized access returns 403
- [ ] user_id is set automatically on creation
- [ ] Export functions filter by user

## 🚀 Frontend Integration

```javascript
// Set token after login
localStorage.setItem('auth_token', token);
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Handle 401 errors
if (error.response?.status === 401) {
    // Redirect to login
    window.location.href = '/login';
}

// Never send user_id from frontend
// ❌ WRONG
axios.post('/api/tasks', { user_id: 1, title: 'Task' });

// ✅ CORRECT
axios.post('/api/tasks', { title: 'Task' });
```

## 📊 Database Schema

All user-owned tables must have:
```sql
user_id BIGINT UNSIGNED NOT NULL,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
```

## 🔑 Key Principles

1. **Never trust the client** - Always set user_id server-side
2. **Always filter queries** - Use `where('user_id', auth()->id())`
3. **Validate ownership** - Check user_id before update/delete
4. **Protect routes** - Use `auth:sanctum` middleware
5. **Return proper errors** - 401 for auth, 403 for ownership

## 📞 Quick Commands

```bash
# Run tests
php test-multi-user.php

# Check routes
php artisan route:list --path=api

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## 📚 Documentation

- Backend: `MULTI_USER_IMPLEMENTATION.md`
- Frontend: `FRONTEND_INTEGRATION_GUIDE.md`
- Summary: `IMPLEMENTATION_SUMMARY.md`

---

**Remember:** When in doubt, always filter by `auth()->id()` and validate ownership!
