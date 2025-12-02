# Frontend Integration Guide - Multi-User Authentication

## Overview
The backend now requires authentication for all API endpoints (except login). This guide explains how to integrate authentication in your React frontend.

## Authentication Flow

### 1. Login Request
Send a POST request to `/api/login` with user credentials:

```javascript
const login = async (email, password) => {
  try {
    const response = await axios.post('http://your-api-url/api/login', {
      email: email,
      password: password
    });
    
    // Store the token
    const token = response.data.token;
    localStorage.setItem('auth_token', token);
    
    // Set default authorization header for all future requests
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    
    return response.data;
  } catch (error) {
    console.error('Login failed:', error);
    throw error;
  }
};
```

### 2. Setting Up Axios Interceptors
Add this to your main App.js or a separate axios configuration file:

```javascript
import axios from 'axios';

// Set base URL
axios.defaults.baseURL = 'http://your-api-url/api';

// Add request interceptor to include token
axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add response interceptor to handle auth errors
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid - redirect to login
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

### 3. Protected Routes in React
Use a ProtectedRoute component to guard authenticated pages:

```javascript
import { Navigate } from 'react-router-dom';

const ProtectedRoute = ({ children }) => {
  const token = localStorage.getItem('auth_token');
  
  if (!token) {
    return <Navigate to="/login" replace />;
  }
  
  return children;
};

// Usage in your routes
<Route 
  path="/dashboard" 
  element={
    <ProtectedRoute>
      <Dashboard />
    </ProtectedRoute>
  } 
/>
```

### 4. Logout Function

```javascript
const logout = () => {
  // Remove token from storage
  localStorage.removeItem('auth_token');
  
  // Remove authorization header
  delete axios.defaults.headers.common['Authorization'];
  
  // Redirect to login
  window.location.href = '/login';
};
```

## API Endpoints

All endpoints now require authentication except:
- `POST /api/login` - User login
- `POST /api/auth/google/callback` - Google OAuth

### Protected Endpoints

#### Daily Tasks
- `GET /api/tasks` - Get all tasks for authenticated user
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get specific task
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task
- `PUT /api/tasks/{id}/complete` - Toggle task completion
- `GET /api/tasks/export?range=1d` - Export tasks (1d, 1m, 2m, 3m)

#### Categories
- `GET /api/categories` - Get all categories for authenticated user
- `POST /api/categories` - Create a new category
- `DELETE /api/categories/{id}` - Delete category

#### Transactions
- `GET /api/transactions` - Get all transactions for authenticated user
- `POST /api/transactions` - Create a new transaction
- `GET /api/transactions/{id}` - Get specific transaction
- `PUT /api/transactions/{id}` - Update transaction
- `DELETE /api/transactions/{id}` - Delete transaction

#### Plans
- `GET /api/plans` - Get all plans for authenticated user
- `POST /api/plans` - Create a new plan
- `GET /api/plans/{id}` - Get specific plan
- `PUT /api/plans/{id}` - Update plan
- `DELETE /api/plans/{id}` - Delete plan
- `PATCH /api/plans/{id}/complete` - Mark plan as completed

#### Interview Questions
- `GET /api/interview-questions` - Get all questions for authenticated user
- `POST /api/interview-questions` - Create a new question
- `GET /api/interview-questions/{id}` - Get specific question
- `PUT /api/interview-questions/{id}` - Update question
- `DELETE /api/interview-questions/{id}` - Delete question
- `GET /api/interview-questions/export?range=1d` - Export questions

#### Tools
- `GET /api/tools` - Get all tools for authenticated user
- `POST /api/tools` - Create a new tool
- `GET /api/tools/{id}` - Get specific tool
- `PUT /api/tools/{id}` - Update tool
- `DELETE /api/tools/{id}` - Delete tool
- `GET /api/tools/export?range=1d` - Export tools

## Example API Calls

### Fetching Tasks
```javascript
const fetchTasks = async () => {
  try {
    const response = await axios.get('/tasks');
    return response.data;
  } catch (error) {
    console.error('Error fetching tasks:', error);
    throw error;
  }
};
```

### Creating a Category
```javascript
const createCategory = async (name, icon, color) => {
  try {
    const response = await axios.post('/categories', {
      name: name,
      icon: icon,
      color: color
    });
    return response.data;
  } catch (error) {
    console.error('Error creating category:', error);
    throw error;
  }
};
```

### Creating a Transaction
```javascript
const createTransaction = async (categoryId, amount, note) => {
  try {
    const response = await axios.post('/transactions', {
      category_id: categoryId,
      amount: amount,
      note: note
    });
    return response.data;
  } catch (error) {
    console.error('Error creating transaction:', error);
    throw error;
  }
};
```

## Error Handling

Handle these common error responses:

```javascript
const handleApiError = (error) => {
  if (error.response) {
    switch (error.response.status) {
      case 401:
        // Unauthorized - redirect to login
        console.error('Authentication required');
        logout();
        break;
      case 403:
        // Forbidden - trying to access another user's data
        console.error('Access denied');
        alert('You do not have permission to access this resource');
        break;
      case 404:
        // Not found
        console.error('Resource not found');
        break;
      case 422:
        // Validation error
        console.error('Validation error:', error.response.data.errors);
        break;
      default:
        console.error('An error occurred:', error.response.data.message);
    }
  } else {
    console.error('Network error:', error.message);
  }
};
```

## Environment Variables

Create a `.env` file in your React app:

```env
REACT_APP_API_URL=http://localhost:8000/api
```

Use it in your code:
```javascript
axios.defaults.baseURL = process.env.REACT_APP_API_URL;
```

## Testing Authentication

1. **Login with valid credentials**
   - Should receive a token
   - Token should be stored in localStorage
   - Subsequent requests should include the token

2. **Access protected routes**
   - Should work with valid token
   - Should redirect to login without token

3. **Token expiration**
   - Should redirect to login when token expires
   - Should show appropriate error message

4. **Logout**
   - Should clear token from storage
   - Should redirect to login page
   - Should not be able to access protected routes

## Important Notes

1. **Never send user_id from frontend** - The backend automatically assigns the authenticated user's ID to all created records.

2. **Data isolation is automatic** - Each user can only see and modify their own data. The backend enforces this.

3. **Token security** - Store tokens in localStorage or sessionStorage, never in cookies without proper security measures.

4. **CORS** - Ensure your backend has CORS properly configured to accept requests from your frontend domain.

5. **HTTPS in production** - Always use HTTPS in production to protect authentication tokens.

## Complete Example: Login Component

```javascript
import React, { useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

const Login = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleLogin = async (e) => {
    e.preventDefault();
    setError('');

    try {
      const response = await axios.post('/login', {
        email: email,
        password: password
      });

      // Store token
      localStorage.setItem('auth_token', response.data.token);
      
      // Set authorization header
      axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
      
      // Redirect to dashboard
      navigate('/dashboard');
    } catch (error) {
      setError(error.response?.data?.message || 'Login failed');
    }
  };

  return (
    <div className="login-container">
      <h2>Login</h2>
      <form onSubmit={handleLogin}>
        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
        />
        <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
        />
        {error && <div className="error">{error}</div>}
        <button type="submit">Login</button>
      </form>
    </div>
  );
};

export default Login;
```

## Support

For any issues or questions, refer to:
- Backend API documentation: `/MULTI_USER_IMPLEMENTATION.md`
- Laravel Sanctum docs: https://laravel.com/docs/sanctum
- Axios docs: https://axios-http.com/docs/intro
