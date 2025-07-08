<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(),
            'price_monthly' => fake()->randomFloat(2, 9.99, 99.99),
            'price_semiannual' => fake()->randomFloat(2, 49.99, 499.99),
            'price_annual' => fake()->randomFloat(2, 99.99, 999.99),
            'features' => json_encode([
                'feature1' => fake()->word(),
                'feature2' => fake()->word(),
                'feature3' => fake()->word(),
            ]),
            'screens' => 1,
        ];
    }

    /**
     * Indicate that the plan is basic.
     */
    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Basic Plan',
            'slug' => fake()->slug(),
            'description' => 'Basic features for starters',
            'price_monthly' => 9.99,
            'price_semiannual' => 49.99,
            'price_annual' => 99.99,
            'screens' => 1,
        ]);
    }

    /**
     * Indicate that the plan is premium.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Premium Plan',
            'slug' => fake()->slug(),
            'description' => 'Premium features for professionals',
            'price_monthly' => 29.99,
            'price_semiannual' => 159.99,
            'price_annual' => 299.99,
            'screens' => 1,
        ]);
    }

    /**
     * Indicate that the plan is enterprise.
     */
    public function enterprise(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Enterprise Plan',
            'slug' => fake()->slug(),
            'description' => 'Enterprise features for large organizations',
            'price_monthly' => 99.99,
            'price_semiannual' => 499.99,
            'price_annual' => 999.99,
            'screens' => 1,
        ]);
    }
} 