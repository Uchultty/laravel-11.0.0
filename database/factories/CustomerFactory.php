<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'pic' => $this->faker->jobTitle(),
            'alamat' => $this->faker->address(),
            'kontak' => $this->indonesianPhoneNumber(),
            'email' => $this->faker->email(),
        ];
    }

    /**
     * Generate an Indonesian mobile number, randomly formatted as 08xx or +62xx.
     */
    protected function indonesianPhoneNumber(): string
    {
        $prefixes = ['0811', '0812', '0813', '0821', '0822', '0823', '0851', '0852', '0853', '0881', '0882', '0895', '0896', '0897', '0898', '0899'];

        $number = $this->faker->randomElement($prefixes) . $this->faker->numerify('#######');

        return $this->faker->boolean() ? $number : '+62' . substr($number, 1);
    }
}
