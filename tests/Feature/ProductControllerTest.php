<?php

use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('ProductController', function () {
    describe('index method', function () {
        it('returns products ordered by latest', function () {
            $oldProduct = Product::factory()->create(['created_at' => now()->subDay()]);
            $newProduct = Product::factory()->create(['created_at' => now()]);

            $response = $this->get(route('products.index'));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Index')
                ->has('products', 2)
                ->where('products.0.id', $newProduct->id) // Newest first
                ->where('products.1.id', $oldProduct->id)
            );
        });
    });

    describe('store method', function () {
        it('creates product and redirects with success message', function () {
            $productData = [
                'name' => 'New Product',
                'price' => 123.45,
                'description' => 'Product description'
            ];

            $response = $this->post(route('products.store'), $productData);

            $response->assertRedirect(route('products.index'));
            $response->assertSessionHas('message', 'Product added successfully');
            
            $this->assertDatabaseHas('products', $productData);
        });

        it('validates required fields', function () {
            $response = $this->post(route('products.store'), []);

            $response->assertSessionHasErrors(['name', 'price']);
        });

        it('validates name is string and max 255 characters', function () {
            $testCases = [
                ['name' => 123, 'price' => 10], // not string
                ['name' => str_repeat('a', 256), 'price' => 10], // too long
            ];

            foreach ($testCases as $data) {
                $response = $this->post(route('products.store'), $data);
                $response->assertSessionHasErrors(['name']);
            }
        });

        it('validates price is numeric and minimum 0', function () {
            $testCases = [
                ['name' => 'Test', 'price' => 'not-numeric'],
                ['name' => 'Test', 'price' => -5],
            ];

            foreach ($testCases as $data) {
                $response = $this->post(route('products.store'), $data);
                $response->assertSessionHasErrors(['price']);
            }
        });

        it('accepts nullable description', function () {
            $productData = [
                'name' => 'Test Product',
                'price' => 50.00,
                'description' => null
            ];

            $response = $this->post(route('products.store'), $productData);

            $response->assertRedirect(route('products.index'));
            $this->assertDatabaseHas('products', $productData);
        });
    });

    describe('update method', function () {
        it('updates product with valid data', function () {
            $product = Product::factory()->create([
                'name' => 'Original Name',
                'price' => 50.00
            ]);

            $updateData = [
                'name' => 'Updated Name',
                'price' => 75.00,
                'description' => 'Updated description'
            ];

            $response = $this->put(route('products.update', $product), $updateData);

            $response->assertRedirect(route('products.index'));
            $response->assertSessionHas('message', 'Product updated successfully');

            $product->refresh();
            expect($product->name)->toBe('Updated Name');
            expect($product->price)->toBe(75);
            expect($product->description)->toBe('Updated description');
        });

        it('validates data when updating', function () {
            $product = Product::factory()->create();

            $response = $this->put(route('products.update', $product), [
                'name' => '',
                'price' => 'invalid'
            ]);

            $response->assertSessionHasErrors(['name', 'price']);
        });

        it('uses route model binding correctly', function () {
            $product = Product::factory()->create(['name' => 'Test Product']);

            $response = $this->get(route('products.edit', $product));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Edit')
                ->has('product')
                ->where('product.name', 'Test Product')
            );
        });
    });

    describe('destroy method', function () {
        it('deletes product and redirects with success message', function () {
            $product = Product::factory()->create();

            $response = $this->delete(route('products.destroy', $product));

            $response->assertRedirect(route('products.index'));
            $response->assertSessionHas('message', 'Product deleted successfully');
            
            $this->assertDatabaseMissing('products', ['id' => $product->id]);
        });

        it('handles deletion of non-existent product', function () {
            $response = $this->delete(route('products.destroy', 999));

            $response->assertNotFound();
        });
    });

    describe('authentication', function () {
        it('requires authentication for all routes', function () {
            $product = Product::factory()->create();
            
            auth()->logout();

            $routes = [
                ['GET', route('products.index')],
                ['GET', route('products.create')],
                ['POST', route('products.store')],
                ['GET', route('products.edit', $product)],
                ['PUT', route('products.update', $product)],
                ['DELETE', route('products.destroy', $product)],
            ];

            foreach ($routes as [$method, $url]) {
                $response = $this->call($method, $url);
                $response->assertRedirect(route('login'));
            }
        });
    });

    describe('route model binding', function () {
        it('automatically resolves product model', function () {
            $product = Product::factory()->create(['name' => 'Model Binding Test']);

            // Test that the controller receives the actual Product model, not just an ID
            $response = $this->get(route('products.edit', $product));

            $response->assertInertia(fn (AssertableInertia $page) => $page
                ->component('products/Edit')
                ->has('product')
                ->where('product.id', $product->id)
                ->where('product.name', 'Model Binding Test')
            );
        });

        it('returns 404 for non-existent product in edit', function () {
            $response = $this->get(route('products.edit', 999));
            $response->assertNotFound();
        });

        it('returns 404 for non-existent product in update', function () {
            $response = $this->put(route('products.update', 999), [
                'name' => 'Test',
                'price' => 10
            ]);
            $response->assertNotFound();
        });

        it('returns 404 for non-existent product in destroy', function () {
            $response = $this->delete(route('products.destroy', 999));
            $response->assertNotFound();
        });
    });
});
