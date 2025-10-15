<?php

use App\Models\Product;

describe('Product Model', function () {
    it('has correct fillable attributes', function () {
        $fillable = ['name', 'price', 'description'];
        
        expect((new Product)->getFillable())->toBe($fillable);
    });

    it('creates product with all attributes', function () {
        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'description' => 'A test product'
        ];

        $product = Product::create($data);

        expect($product->name)->toBe($data['name']);
        expect($product->price)->toBe($data['price']);
        expect($product->description)->toBe($data['description']);
        expect($product->exists)->toBeTrue();
    });

    it('creates product with minimal required attributes', function () {
        $data = [
            'name' => 'Minimal Product',
            'price' => 50.00
        ];

        $product = Product::create($data);

        expect($product->name)->toBe($data['name']);
        expect($product->price)->toBe($data['price']);
        expect($product->description)->toBeNull();
    });

    it('updates product attributes', function () {
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 50.00
        ]);

        $product->update([
            'name' => 'Updated Name',
            'price' => 75.00,
            'description' => 'Updated description'
        ]);

        expect($product->fresh()->name)->toBe('Updated Name');
        expect($product->fresh()->price)->toBe(75);
        expect($product->fresh()->description)->toBe('Updated description');
    });

    it('has proper table name', function () {
        expect((new Product)->getTable())->toBe('products');
    });

    it('uses primary key', function () {
        expect((new Product)->getKeyName())->toBe('id');
    });

    it('does not use timestamps by default', function () {
        // Laravel models use timestamps by default
        expect((new Product)->usesTimestamps())->toBeTrue();
    });

    it('can be deleted', function () {
        $product = Product::factory()->create();
        $productId = $product->id;

        $product->delete();

        expect(Product::find($productId))->toBeNull();
    });

    it('can find products by name', function () {
        $product = Product::factory()->create(['name' => 'Unique Product Name']);

        $found = Product::where('name', 'Unique Product Name')->first();

        expect($found)->not->toBeNull();
        expect($found->id)->toBe($product->id);
    });

    it('can find products by price range', function () {
        Product::factory()->create(['price' => 10.00]);
        Product::factory()->create(['price' => 50.00]);
        Product::factory()->create(['price' => 100.00]);

        $expensiveProducts = Product::where('price', '>', 75.00)->get();
        $cheapProducts = Product::where('price', '<', 25.00)->get();

        expect($expensiveProducts)->toHaveCount(1);
        expect($cheapProducts)->toHaveCount(1);
    });
});
