<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 1, 1000),
            'description' => fake()->optional(0.8)->sentence(),
        ];
    }

    /**
     * Indicate that the product is expensive.
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 500, 2000),
        ]);
    }

    /**
     * Indicate that the product is cheap.
     */
    public function cheap(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 1, 50),
        ]);
    }

    /**
     * Indicate that the product has no description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }

    /**
     * Create a product with specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }
}
