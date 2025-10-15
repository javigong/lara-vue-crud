# Laravel Vue CRUD Operations Example

A complete CRUD (Create, Read, Update, Delete) application built with Laravel, Vue.js, and Inertia.js. This starter kit demonstrates modern web development patterns and is perfect for developers transitioning from other frameworks.

## 🚀 Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue.js 3 with TypeScript
- **UI Framework**: Tailwind CSS with Reka UI components
- **Full-Stack Bridge**: Inertia.js
- **Authentication**: Laravel Fortify
- **Route Generation**: Laravel Wayfinder
- **Build Tool**: Vite

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (included) or MySQL/PostgreSQL

## 🛠️ Installation

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd lara-vue-crud
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database setup**

    ```bash
    php artisan migrate
    ```

5. **Build assets**

    ```bash
    npm run build
    ```

6. **Start development server**
    ```bash
    npm run dev
    # Or use the comprehensive dev script
    composer run dev
    ```

Visit `http://localhost:8000` to see the application.

## 🏗️ Project Architecture

### Laravel MVC Architecture with Vue.js & Inertia.js

This application follows Laravel's MVC (Model-View-Controller) pattern enhanced with Vue.js and Inertia.js for a modern full-stack experience.

#### Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND (Vue.js)                        │
├─────────────────────────────────────────────────────────────┤
│  User (Alex)  →  URL (/products)  →  Vue Components        │
│                     ↓                                      │
│  Vue 3 + TypeScript + Tailwind CSS + Shadcn/ui            │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      │ Inertia.js Bridge
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                    BACKEND (Laravel)                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Routes  →  Controller  →  Model  →  Database              │
│    ↓           ↓           ↓          ↓                    │
│  URL         Business    Eloquent   SQLite/MySQL           │
│  Mapping     Logic       ORM        Storage                │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

#### Request Flow Diagram

```
┌─────────┐    ┌─────────┐    ┌─────────────┐    ┌─────────────┐
│  User   │───▶│  Route  │───▶│ Controller  │───▶│   Model     │
│ (Alex)  │    │         │    │             │    │             │
└─────────┘    └─────────┘    └─────────────┘    └─────────────┘
     ▲              │                │                │
     │              │                │                ▼
     │              │                │          ┌─────────────┐
     │              │                │          │  Database   │
     │              │                │          │             │
     │              │                │          └─────────────┘
     │              │                │                │
     │              │                │                ▼
     │              │                │          ┌─────────────┐
     │              │                │          │   Model     │
     │              │                │          │ (Response)  │
     │              │                │          └─────────────┘
     │              │                │                │
     │              │                │                ▼
     │              │                │          ┌─────────────┐
     │              │                │          │ Controller  │
     │              │                │          │ (Response)  │
     │              │                │          └─────────────┘
     │              │                │                │
     │              │                │                ▼
     │              │                │          ┌─────────────┐
     │              │                │          │   Inertia   │
     │              │                │          │   Bridge    │
     │              │                │          └─────────────┘
     │              │                │                │
     │              │                │                ▼
     │              │                │          ┌─────────────┐
     │              │                │          │ Vue Component│
     │              │                │          │ (Updated)   │
     │              │                │          └─────────────┘
     │              │                │                │
     └──────────────┴────────────────┴────────────────┘
```

#### Request Flow

```
1. User Request → 2. Route → 3. Controller → 4. Model → 5. Database
                                                         ↓
6. Database → 7. Model → 8. Controller → 9. Inertia → 10. Vue Component → 11. User
```

#### Component Breakdown

**Frontend (Vue.js)**

- **User Interface**: Vue.js components with TypeScript
- **Styling**: Tailwind CSS + Shadcn/ui components
- **State Management**: Inertia.js handles server state
- **Routing**: Client-side navigation with Inertia

**Backend (Laravel)**

- **Routes**: Define URL endpoints and middleware
- **Controllers**: Handle business logic and orchestration
- **Models**: Eloquent ORM for database interactions
- **Database**: Data persistence layer

**Bridge (Inertia.js)**

- **Server-Client Communication**: Seamless data passing
- **Page Transitions**: SPA-like experience
- **Form Handling**: Reactive form submissions
- **State Synchronization**: Real-time UI updates

### File Structure Implementation

```
├── app/
│   ├── Http/Controllers/     # Controllers (C)
│   │   └── ProductController.php
│   └── Models/              # Models (M)
│       └── Product.php
├── resources/
│   └── js/pages/products/   # Views (V) - Vue Components
│       ├── Index.vue        # List/Read
│       ├── Create.vue       # Create
│       └── Edit.vue         # Update
└── routes/
    └── web.php              # Routes
```

#### Real-World Example: Product Creation Flow

Let's trace a complete request through the MVC architecture:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         PRODUCT CREATION FLOW                              │
└─────────────────────────────────────────────────────────────────────────────┘

1. User clicks "Create Product" button in Vue component
   ↓
2. Form submission triggers Inertia POST request
   ↓
3. Laravel Route receives request: POST /products
   ↓
4. ProductController@store method handles request
   ↓
5. Controller validates data and calls Product model
   ↓
6. Model creates new record in database
   ↓
7. Controller redirects with success message
   ↓
8. Inertia passes data to Vue component
   ↓
9. Vue component updates UI with new product
```

**Code Flow Example:**

```php
// 1. Route (web.php)
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// 2. Controller (ProductController.php)
public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ]);

    Product::create($data);  // 3. Model interaction
    return redirect()->route('products.index')->with('message', 'Product added successfully');
}

// 4. Model (Product.php)
class Product extends Model
{
    protected $fillable = ['name', 'price', 'description'];
}
```

```vue
<!-- 5. Vue Component (Create.vue) -->
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    price: '',
    description: '',
});

const handleSubmit = () => {
    form.post(route('products.store')); // 6. Inertia request
};
</script>

<template>
    <form @submit.prevent="handleSubmit">
        <!-- Form fields -->
    </form>
</template>
```

## 📚 CRUD Operations Explained

### 1. **Create** - Adding New Products

**Backend (Controller)**:

```php
public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ]);

    Product::create($data);
    return redirect()->route('products.index')->with('message', 'Product added successfully');
}
```

**Frontend (Vue Component)**:

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    price: '',
    description: '',
});

const handleSubmit = () => {
    form.post(productsStore.url());
};
</script>
```

**Key Concepts**:

- **Form Validation**: Laravel handles server-side validation
- **Inertia Forms**: `useForm()` provides reactive form state
- **Route Model Binding**: Automatic parameter resolution

### 2. **Read** - Displaying Products

**Backend (Controller)**:

```php
public function index()
{
    $products = Product::latest()->get();
    return Inertia::render('products/Index', compact('products'));
}
```

**Frontend (Vue Component)**:

```vue
<template>
    <TableRow v-for="product in products" :key="product.id">
        <TableCell>{{ product.name }}</TableCell>
        <TableCell>{{ product.price }}</TableCell>
        <TableCell>{{ product.description }}</TableCell>
    </TableRow>
</template>
```

**Key Concepts**:

- **Inertia Rendering**: Server data passed directly to Vue components
- **Reactive Data**: Vue automatically updates when data changes
- **TypeScript Support**: Full type safety with generated types

### 3. **Update** - Editing Products

**Backend (Controller)**:

```php
public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ]);

    $product->update($request->all());
    return redirect()->route('products.index')->with('message', 'Product updated successfully');
}
```

**Frontend (Vue Component)**:

```vue
<script setup>
const form = useForm({
    name: product.name,
    price: product.price,
    description: product.description,
});

const handleSubmit = () => {
    form.put(productsUpdate.url(product.id));
};
</script>
```

**Key Concepts**:

- **Route Model Binding**: `Product $product` automatically resolves the model
- **Form Pre-population**: Data loaded from server into form
- **PUT Method**: RESTful update operation

### 4. **Delete** - Removing Products

**Backend (Controller)**:

```php
public function destroy(Product $product)
{
    $product->delete();
    return redirect()->route('products.index')->with('message', 'Product deleted successfully');
}
```

**Frontend (Vue Component)**:

```vue
<script setup>
import { router } from '@inertiajs/vue3';

const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(productsDelete.url(id));
    }
};
</script>
```

**Key Concepts**:

- **Confirmation Dialog**: User-friendly delete confirmation
- **Router Methods**: Inertia router handles HTTP methods
- **Automatic Redirect**: Server redirects after successful deletion

## 🗄️ Database Schema

**Products Table**:

```sql
CREATE TABLE products (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Model Definition**:

```php
class Product extends Model
{
    protected $fillable = ['name', 'price', 'description'];
}
```

## 🛣️ Routing System

**Laravel Routes**:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});
```

**Auto-Generated Frontend Routes**:
Laravel Wayfinder automatically generates TypeScript route definitions for frontend use.

## 🔐 Authentication

This application includes Laravel Fortify for authentication:

- **Registration**: User signup with email verification
- **Login**: Email/password authentication
- **Password Reset**: Email-based password recovery
- **Two-Factor Authentication**: Optional 2FA support

## 🎨 UI Components

Built with modern component libraries:

- **Reka UI**: Headless UI components
- **Tailwind CSS**: Utility-first CSS framework
- **Lucide Icons**: Beautiful SVG icons
- **Responsive Design**: Mobile-first approach

## 🚀 Development Workflow

### Running the Application

```bash
# Comprehensive development environment
composer run dev
# Runs: PHP server, Queue worker, Logs, and Vite dev server
```

### Building for Production

```bash
npm run build
php artisan optimize
```

### Testing

This project includes comprehensive test coverage using **Pest PHP**, a modern testing framework built on top of PHPUnit.

#### Running Tests

```bash
# Run all tests
composer run test

# Run specific test files
php artisan test tests/Feature/ProductTest.php

# Run with coverage
php artisan test --coverage
```

#### Test Structure

The test suite includes:

**Feature Tests** (`tests/Feature/`):

- `ProductTest.php` - Complete CRUD operation tests
- `ProductControllerTest.php` - Controller-specific tests
- `InertiaProductTest.php` - Inertia.js integration tests

**Unit Tests** (`tests/Unit/`):

- `ProductTest.php` - Model behavior tests

#### Test Categories

**CRUD Operations Testing**:

```php
// Create operations
it('stores a new product with valid data', function () {
    $productData = ['name' => 'Test', 'price' => 99.99];
    $response = $this->post(route('products.store'), $productData);
    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', $productData);
});

// Read operations
it('displays all products', function () {
    $products = Product::factory()->count(3)->create();
    $response = $this->get(route('products.index'));
    $response->assertInertia(fn ($page) => $page->has('products', 3));
});

// Update operations
it('updates product with valid data', function () {
    $product = Product::factory()->create();
    $response = $this->put(route('products.update', $product), [
        'name' => 'Updated Name',
        'price' => 150.00
    ]);
    $response->assertRedirect(route('products.index'));
});

// Delete operations
it('deletes a product', function () {
    $product = Product::factory()->create();
    $response = $this->delete(route('products.destroy', $product));
    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
```

**Validation Testing**:

```php
it('validates required fields', function () {
    $response = $this->post(route('products.store'), []);
    $response->assertSessionHasErrors(['name', 'price']);
});

it('validates price is numeric and minimum 0', function () {
    $response = $this->post(route('products.store'), [
        'name' => 'Test',
        'price' => -5
    ]);
    $response->assertSessionHasErrors(['price']);
});
```

**Inertia.js Integration Testing**:

```php
it('renders products index with correct props', function () {
    $products = Product::factory()->count(3)->create();
    $response = $this->get(route('products.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('products/Index')
        ->has('products', 3)
        ->has('products.0', fn ($product) => $product
            ->has('id')
            ->has('name')
            ->has('price')
        )
    );
});
```

**Authentication Testing**:

```php
it('requires authentication for all routes', function () {
    auth()->logout();
    $response = $this->get(route('products.index'));
    $response->assertRedirect(route('login'));
});
```

#### Test Features

- **Database Testing**: Uses in-memory SQLite for fast, isolated tests
- **Factory Support**: Product factory for generating test data
- **Route Model Binding**: Tests automatic model resolution
- **Flash Messages**: Validates success/error message display
- **Form Validation**: Comprehensive validation rule testing
- **Authentication**: Tests protected route access

## 🔄 Key Differences from Other Frameworks

### vs React/Next.js

- **Server-Side**: Laravel handles routing and data fetching
- **No API Layer**: Direct server-to-component data passing
- **Authentication**: Built-in auth system vs custom JWT setup

### vs Angular

- **Simplicity**: Less boilerplate, more convention over configuration
- **Real-time**: Inertia handles page transitions seamlessly
- **Type Safety**: TypeScript integration without complex setup

### vs Express.js/Node.js

- **ORM**: Eloquent ORM vs manual database queries
- **Validation**: Built-in validation vs custom middleware
- **Security**: CSRF protection, SQL injection prevention out of the box

## 📁 File Structure Deep Dive

```
lara-vue-crud/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Business logic
│   │   ├── Middleware/          # Request/response processing
│   │   └── Requests/            # Form validation
│   └── Models/                  # Database models
├── database/
│   ├── migrations/              # Database schema changes
│   └── seeders/                 # Sample data
├── resources/
│   ├── js/
│   │   ├── components/          # Reusable Vue components
│   │   ├── layouts/             # Page layouts
│   │   ├── pages/               # Page components
│   │   ├── routes/              # Auto-generated route definitions
│   │   └── types/               # TypeScript type definitions
│   └── views/                   # Blade templates (fallback)
├── routes/                      # Application routes
└── storage/                     # File storage and logs
```

## 🎯 Best Practices Demonstrated

1. **Route Model Binding**: Automatic model resolution
2. **Form Validation**: Server-side validation with client feedback
3. **TypeScript Integration**: Full type safety
4. **Component Composition**: Reusable UI components
5. **Error Handling**: Graceful error display
6. **Security**: CSRF protection, SQL injection prevention
7. **Performance**: Optimized builds and lazy loading

## 🐛 Common Issues & Solutions

### Issue: 500 Internal Server Error on Delete

**Solution**: Ensure route model binding is properly configured in controller methods.

### Issue: Form Validation Errors Not Displaying

**Solution**: Check that error handling is implemented in Vue components.

### Issue: Routes Not Found

**Solution**: Run `php artisan route:list` to verify route registration.

## 📖 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Guide](https://vuejs.org/guide/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)

## 📦 Git Repository Setup

This project is now initialized as a Git repository with a comprehensive commit history.

### Git Commands

```bash
# Check repository status
git status

# View commit history
git log --oneline

# Create a new branch for development
git checkout -b feature/new-feature

# Add changes to staging
git add .

# Commit changes with descriptive message
git commit -m "Add new feature with tests"

# Push to remote repository (when configured)
git push origin main
```

### Repository Structure

The repository includes:

- **287 files** in the initial commit
- **27,877 lines** of code
- Complete Laravel application structure
- Vue.js components and TypeScript files
- Comprehensive test suite
- Documentation and configuration files

### .gitignore Configuration

The repository includes a comprehensive `.gitignore` file that excludes:

- Node modules and build artifacts
- Environment files and secrets
- Database files and logs
- IDE and editor configurations
- Cache and temporary files

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes
4. Add tests if applicable
5. Run the test suite: `composer run test`
6. Commit your changes: `git commit -m "Add amazing feature"`
7. Push to the branch: `git push origin feature/amazing-feature`
8. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

**Ready to build your next CRUD application?** This starter kit provides everything you need to get started with modern Laravel and Vue.js development!
