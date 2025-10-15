<?php

use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Product Index', function () {
    it('displays the products index page', function () {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('products/Index')
            ->has('products')
        );
    });

    it('shows empty state when no products exist', function () {
        $response = $this->get(route('products.index'));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('products/Index')
            ->has('products', 0)
        );
    });

    it('displays all products', function () {
        $products = Product::factory()->count(3)->create();

        $response = $this->get(route('products.index'));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('products/Index')
            ->has('products', 3)
            ->where('products.0.name', $products[0]->name)
            ->where('products.1.name', $products[1]->name)
            ->where('products.2.name', $products[2]->name)
        );
    });

    it('requires authentication', function () {
        auth()->logout();

        $response = $this->get(route('products.index'));

        $response->assertRedirect(route('login'));
    });
});

describe('Product Create', function () {
    it('displays the create product form', function () {
        $response = $this->get(route('products.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('products/Create')
        );
    });

    it('stores a new product with valid data', function () {
        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
            'description' => 'A test product description'
        ];

        $response = $this->post(route('products.store'), $productData);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('message', 'Product added successfully');
        
        $this->assertDatabaseHas('products', $productData);
    });

    it('stores a new product with minimal data', function () {
        $productData = [
            'name' => 'Minimal Product',
            'price' => 50.00
        ];

        $response = $this->post(route('products.store'), $productData);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', $productData);
    });

    it('validates required fields', function () {
        $response = $this->post(route('products.store'), []);

        $response->assertSessionHasErrors(['name', 'price']);
    });

    it('validates name field', function () {
        $testCases = [
            ['name' => '', 'price' => 10],
            ['name' => str_repeat('a', 256), 'price' => 10], // too long
        ];

        foreach ($testCases as $data) {
            $response = $this->post(route('products.store'), $data);
            $response->assertSessionHasErrors(['name']);
        }
    });

    it('validates price field', function () {
        $testCases = [
            ['name' => 'Test', 'price' => ''],
            ['name' => 'Test', 'price' => 'invalid'],
            ['name' => 'Test', 'price' => -10], // negative price
        ];

        foreach ($testCases as $data) {
            $response = $this->post(route('products.store'), $data);
            $response->assertSessionHasErrors(['price']);
        }
    });

    it('accepts long description', function () {
        $data = [
            'name' => 'Test Product',
            'price' => 10,
            'description' => str_repeat('a', 1000) // long but valid description
        ];

        $response = $this->post(route('products.store'), $data);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', $data);
    });
});

describe('Product Edit', function () {
    it('displays the edit product form', function () {
        $product = Product::factory()->create();

        $response = $this->get(route('products.edit', $product));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('products/Edit')
            ->has('product')
            ->where('product.id', $product->id)
            ->where('product.name', $product->name)
            ->where('product.price', $product->price)
            ->where('product.description', $product->description)
        );
    });

    it('returns 404 for non-existent product', function () {
        $response = $this->get(route('products.edit', 999));

        $response->assertNotFound();
    });

    it('updates product with valid data', function () {
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 50.00,
            'description' => 'Original Description'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'price' => 75.50,
            'description' => 'Updated Description'
        ];

        $response = $this->put(route('products.update', $product), $updateData);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('message', 'Product updated successfully');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            ...$updateData
        ]);
    });

    it('validates data when updating', function () {
        $product = Product::factory()->create();

        $response = $this->put(route('products.update', $product), [
            'name' => '',
            'price' => 'invalid'
        ]);

        $response->assertSessionHasErrors(['name', 'price']);
    });

    it('returns 404 when updating non-existent product', function () {
        $response = $this->put(route('products.update', 999), [
            'name' => 'Test',
            'price' => 10
        ]);

        $response->assertNotFound();
    });
});

describe('Product Delete', function () {
    it('deletes a product', function () {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('message', 'Product deleted successfully');
        
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    });

    it('returns 404 when deleting non-existent product', function () {
        $response = $this->delete(route('products.destroy', 999));

        $response->assertNotFound();
    });

    it('handles multiple product deletions', function () {
        $products = Product::factory()->count(3)->create();

        // Delete first product
        $this->delete(route('products.destroy', $products[0]));
        $this->assertDatabaseMissing('products', ['id' => $products[0]->id]);
        $this->assertDatabaseHas('products', ['id' => $products[1]->id]);
        $this->assertDatabaseHas('products', ['id' => $products[2]->id]);

        // Delete second product
        $this->delete(route('products.destroy', $products[1]));
        $this->assertDatabaseMissing('products', ['id' => $products[1]->id]);
        $this->assertDatabaseHas('products', ['id' => $products[2]->id]);
    });
});

describe('Product Model', function () {
    it('creates product with fillable attributes', function () {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'description' => 'Test description'
        ]);

        expect($product->name)->toBe('Test Product');
        expect($product->price)->toBe(99.99);
        expect($product->description)->toBe('Test description');
        expect($product->id)->not->toBeNull();
    });

    it('has timestamps', function () {
        $product = Product::factory()->create();

        expect($product->created_at)->not->toBeNull();
        expect($product->updated_at)->not->toBeNull();
    });

    it('can be soft deleted if configured', function () {
        // This test assumes you might want to implement soft deletes
        // Remove or modify if you don't plan to use soft deletes
        
        $product = Product::factory()->create();
        $productId = $product->id;
        
        $product->delete();
        
        // For hard delete (current implementation)
        $this->assertDatabaseMissing('products', ['id' => $productId]);
        
        // For soft delete (if implemented), uncomment:
        // $this->assertSoftDeleted('products', ['id' => $productId]);
    });
});
