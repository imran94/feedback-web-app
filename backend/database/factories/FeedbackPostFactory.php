<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackPostFactory extends Factory
{
    private static $categories = [
        'Product Feedback',
        'Feature Requests',
        'Bug Reports',
        'Customer Reviews & In-app Ratings',
        'Complaints & Questions',
        'Praise & Appreciation Posts',
        'Customer Surveys',
        'Net Promoter Score (NPS) Surveys',
        'Customer Satisfaction Survey',
        'Customer Effort Score Feedback',
        'Sales Objections & Feedback',
        'Customer Churn Feedback'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text(100),
            'description' => fake()->paragraphs(3, true),
            'category' => self::$categories[array_rand(self::$categories)]
        ];
    }
}
