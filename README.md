# Vue Demo - Laravel + Vue 3 E-commerce Application

A modern e-commerce application built with Laravel 12 and Vue 3, featuring user authentication, product management, shopping cart, wishlist, and order processing.

## Tech Stack

### Backend
- **Laravel 12** - PHP Framework
- **PHP 8.2+**
- **MySQL** - Database
- **Sanctum** - API Authentication
- **Stripe** - Payment Processing
- **DomPDF** - PDF Generation

### Frontend
- **Vue 3.5** - Progressive JavaScript Framework
- **Vue Router 5** - Client-side Routing
- **Pinia 2** - State Management
- **Vite 7** - Build Tool
- **Tailwind CSS 4** - Utility-first CSS Framework
- **Axios** - HTTP Client

### Development Tools
- **ESLint** - Code Linting
- **Prettier** - Code Formatting

## Features

- **User Authentication**: Login, registration, profile management
- **Role-based Access**: Admin and user roles
- **Product Management**: CRUD operations for products
- **Category Management**: Organize products by categories
- **Shopping Cart**: Add, remove, and update cart items
- **Wishlist**: Save favorite products
- **Order Processing**: Checkout and order history
- **User Management**: Admin can manage users
- **Data Export**: Export products to PDF/CSV
- **Responsive Design**: Mobile-friendly interface

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- npm or yarn
- MySQL

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd vue-demo
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file and set your database credentials:
   ```env
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

## Development

### Start Development Server
```bash
# Start Laravel server
php artisan serve

# Start Vite dev server (in another terminal)
npm run dev
```

### Available Scripts

```bash
npm run dev          # Start Vite dev server
npm run build        # Build for production
npm run lint         # Run ESLint
npm run format       # Format code with Prettier
```

## Project Structure

```
resources/js/
├── components/      # Vue components
├── stores/          # Pinia stores (auth, cart, wishlist)
├── utils/           # Utility functions
├── router.js        # Vue Router configuration
└── app.js           # Application entry point
```

## Key Improvements

This project has been upgraded with modern development practices:

- **Security**: Removed hardcoded IP, uses environment variables
- **Code Quality**: ESLint and Prettier for consistent code style
- **Modern Vue**: Composition API with script setup syntax
- **State Management**: Pinia for cart, auth, and wishlist state
- **Direct Imports**: Components import utilities directly for clarity

## Environment Variables

Required environment variables in `.env`:

```env
VITE_APP_URL=http://localhost:8000
VITE_DEV_HOST=localhost  # Optional: for network access
```

## Usage Examples

### Using Pinia Stores
```javascript
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const authStore = useAuthStore()
const cartStore = useCartStore()

// Auth
await authStore.login({ email, password })
await authStore.fetchUser()

// Cart
await cartStore.addToCart(productId)
await cartStore.fetchCart()
```

### Using Utilities in Components
```javascript
import { formatDate } from '../utils/formatDate'
import { getImageUrl } from '../utils/ImageUrl'
import { showToast } from '../utils/ui-toasts'

export default {
  methods: {
    formatDate,
    getImageUrl,
    showToast
  }
}
```

## License

This project is open-sourced software licensed under the MIT license.
