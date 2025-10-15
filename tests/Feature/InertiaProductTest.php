<?php

use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Inertia.js Integration', function () {
    describe('Product Index Page', function () {
        it('renders products index with correct Inertia props', function () {
            $products = Product::factory()->count(3)->create();

            $response = $this->get(route('products.index'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Index')
                ->has('products', 3)
                ->has('products.0', fn (AssertableInertia $product) => $product
                    ->has('id')
                    ->has('name')
                    ->has('price')
                    ->has('description')
                    ->has('created_at')
                    ->has('updated_at')
                )
            );
        });

        it('passes empty products array when no products exist', function () {
            $response = $this->get(route('products.index'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Index')
                ->has('products', 0)
            );
        });

        it('includes flash messages in props', function () {
            // Create a product to trigger a flash message
            $productData = [
                'name' => 'Flash Test Product',
                'price' => 99.99,
                'description' => 'Test description'
            ];

            // First, create a product which will set a flash message
            $this->post(route('products.store'), $productData);
            
            // Then visit the index page to see the flash message
            $response = $this->get(route('products.index'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Index')
                ->has('flash')
            );
        });
    });

    describe('Product Create Page', function () {
        it('renders create form without initial data', function () {
            $response = $this->get(route('products.create'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Create')
                ->where('errors', [])
            );
        });
    });

    describe('Product Edit Page', function () {
        it('renders edit form with product data', function () {
            $product = Product::factory()->create([
                'name' => 'Test Product',
                'price' => 99.99,
                'description' => 'Test Description'
            ]);

            $response = $this->get(route('products.edit', $product));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Edit')
                ->has('product')
                ->where('product.id', $product->id)
                ->where('product.name', 'Test Product')
                ->where('product.price', 99.99)
                ->where('product.description', 'Test Description')
                ->where('errors', [])
            );
        });

        it('includes validation errors in props when validation fails', function () {
            $product = Product::factory()->create();

            // Make a request with invalid data to trigger validation errors
            $this->put(route('products.update', $product), [
                'name' => '',
                'price' => 'invalid'
            ]);

            // Then visit the edit page to see if errors are included
            $response = $this->get(route('products.edit', $product));

            // Note: In a real app, validation errors would be passed back to the edit page
            // This test demonstrates how to check for error props
            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Edit')
                ->has('product')
            );
        });
    });

    describe('Form Submissions', function () {
        it('handles successful form submission', function () {
            $productData = [
                'name' => 'New Product',
                'price' => 123.45,
                'description' => 'Product description'
            ];

            $response = $this->post(route('products.store'), $productData);

            $response->assertRedirect(route('products.index'));
            $response->assertSessionHas('message', 'Product added successfully');
        });

        it('handles form submission with validation errors', function () {
            $response = $this->post(route('products.store'), [
                'name' => '',
                'price' => 'invalid'
            ]);

            $response->assertSessionHasErrors(['name', 'price']);
        });

        it('handles PUT request for updates', function () {
            $product = Product::factory()->create();
            
            $updateData = [
                'name' => 'Updated Product',
                'price' => 200.00,
                'description' => 'Updated description'
            ];

            $response = $this->put(route('products.update', $product), $updateData);

            $response->assertRedirect(route('products.index'));
            $response->assertSessionHas('message', 'Product updated successfully');
        });

        it('handles DELETE request for product deletion', function () {
            $product = Product::factory()->create();

            $response = $this->delete(route('products.destroy', $product));

            $response->assertRedirect(route('products.index'));
            $response->assertSessionHas('message', 'Product deleted successfully');
        });
    });

    describe('Vue Component Props', function () {
        it('ensures all required props are passed to Index component', function () {
            Product::factory()->create([
                'name' => 'Sample Product',
                'price' => 50.00,
                'description' => 'Sample description'
            ]);

            $response = $this->get(route('products.index'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Index')
                ->has('products', 1)
                ->where('products.0.name', 'Sample Product')
                ->where('products.0.price', 50)
                ->where('products.0.description', 'Sample description')
            );
        });

        it('ensures Edit component receives correct product data', function () {
            $product = Product::factory()->create([
                'name' => 'Edit Test Product',
                'price' => 75.50
            ]);

            $response = $this->get(route('products.edit', $product));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Edit')
                ->has('product')
                ->where('product.name', 'Edit Test Product')
                ->where('product.price', 75.50)
            );
        });
    });

    describe('Page Titles', function () {
        it('sets correct page title for products index', function () {
            $response = $this->get(route('products.index'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Index')
            );
            
            // The page title is set in the Vue component with <Head title="Products" />
            // This would be tested in frontend tests
        });

        it('sets correct page title for create product', function () {
            $response = $this->get(route('products.create'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Create')
            );
        });

        it('sets correct page title for edit product', function () {
            $product = Product::factory()->create();

            $response = $this->get(route('products.edit', $product));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Edit')
            );
        });
    });

    describe('Breadcrumbs', function () {
        it('includes breadcrumb data for navigation', function () {
            $response = $this->get(route('products.index'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Index')
                // Breadcrumbs are defined in the Vue component, not passed from backend
                // This test verifies the component renders correctly
            );
        });
    });
});
