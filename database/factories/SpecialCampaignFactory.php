<?php

namespace Database\Factories;

use App\Models\Charity;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialCampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $charity = Charity::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        return [
            //
            "name" =>  $this->faker->words(5, true),
            "description" =>  $this->faker->words(5, true),
            "banner_text" =>  $this->faker->words(10, true),
            "charity_id" => $charity->id,
            "start_date" => "1990-01-01",
            "end_date" => today(),
            "image" => $file->name,
        ];
    }
}
