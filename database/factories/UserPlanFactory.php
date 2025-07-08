<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPlan>
 */
class UserPlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserPlan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = Carbon::now();
        $period = fake()->randomElement(['monthly', 'semiannual', 'annual']);
        
        $expiresAt = match($period) {
            'monthly' => $startedAt->copy()->addMonth(),
            'semiannual' => $startedAt->copy()->addMonths(6),
            'annual' => $startedAt->copy()->addYear(),
        };

        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'status' => 'active',
            'started_at' => $startedAt,
            'expires_at' => $expiresAt,
            'billing_period' => $period,
            'amount_paid' => fake()->randomFloat(2, 9.99, 999.99),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the user plan is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'expires_at' => Carbon::now()->addMonth(),
        ]);
    }

    /**
     * Indicate that the user plan is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
            'expires_at' => Carbon::now()->subMonth(),
        ]);
    }

    /**
     * Indicate that the user plan is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expires_at' => Carbon::now()->subMonth(),
        ]);
    }

    /**
     * Indicate that the user plan is monthly.
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_period' => 'monthly',
            'expires_at' => Carbon::now()->addMonth(),
        ]);
    }

    /**
     * Indicate that the user plan is semiannual.
     */
    public function semiannual(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_period' => 'semiannual',
            'expires_at' => Carbon::now()->addMonths(6),
        ]);
    }

    /**
     * Indicate that the user plan is annual.
     */
    public function annual(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_period' => 'annual',
            'expires_at' => Carbon::now()->addYear(),
        ]);
    }

    /**
     * Indicate that the user plan has no expiration.
     */
    public function permanent(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => null,
        ]);
    }
} 