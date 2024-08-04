<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comment;
use App\Models\FeedbackPost;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(25)->create();
        $userCount = User::count();

        shuffle(self::$feedbackSample);

        foreach (self::$feedbackSample as $sample) {
            $post = new FeedbackPost([
                'title' => $sample->title,
                'description' => $sample->description,
                'category' => $sample->category,
                'vote_count' => rand(0, $userCount)
            ]);
            $post->user()->associate(User::inRandomOrder()->first());
            $post->save();

            for ($i = 0; $i < 5; $i++) {
                $comment = new Comment([
                    'content' => fake()->paragraphs(1, true)
                ]);
                $comment->user()->associate(User::inRandomOrder()->first());
                $post->comments()->save($comment);
            }
        }
    }

    private static $feedbackSample = [
        [
            'title' => 'Add tags for solutions',
            'description' => 'Easier to search for solutions based on a specific stack.',
            'category' => 'Enhancement'
        ],
        [
            'title' => 'Q&A within the challenge hubs',
            'description' => 'Challenge-specific Q&A would make for easy reference.',
            'category' => 'Feature'
        ],
        [
            'title' => 'Ability to follow others',
            'description' => 'Stay updated on comments and solutions other people post.',
            'category' => 'Feature'
        ],
        [
            'title' => 'Preview images not loading',
            'description' => 'Challenge preview images are missing when you apply a filter',
            'category' => 'Bug'
        ],
        [
            'title' => 'Change the color scheme',
            'description' => 'The color scheme is too harsh on the eyes. consider using softer colors.',
            'category' => 'UI'
        ],
        [
            'title' => 'Checkout process needs to be improved',
            'description' => "It's too cumbersome to checkout. Streamline it for a better user experience",
            'category' => 'UX'
        ],
        [
            'title' => 'Images on gallery page not loading properly',
            'description' => '',
            'category' => 'Bug',
        ],
        [
            'title' => 'Website crashing on older browsers',
            'description' => 'When I try to visit the website on IE8, it gives an error',
            'category' => 'Bug',
        ],
        [
            'title' => 'The font size is unreadable',
            'description' => 'The font size is too small, make it more readable',
            'category' => 'UI'
        ],
        [
            'title' => 'Add a live chat support feature for immediate assistance',
            'description' => 'Users should be able to get a solution to resolve their problems quickly.',
            'category' => 'Enhancement'
        ],
        [
            'title' => 'Make buttons easier to find.',
            'description' => 'Buttons are hard to find. They should be more prominent',
            'category' => 'UI'
        ],
        [
            'title' => 'Declutter the navigation bar',
            'description' => 'The navigation bar is cluttered. Simplify the menu options.',
            'category' => 'UI'
        ],
        [
            'title' => 'Make the website layout responsive',
            'description' => 'The website layout is not responsive on mobile devices. Please fix this issue.',
            'category' => 'UI'
        ],
        [
            'title' => 'Provide an option to compare products side-by-side',
            'description' => 'So that customers can make better informed decisions about their purchase.',
            'category' => 'Enhancement',
        ],

        [
            'title' => 'Website layout is unoptimized',
            'description' => "There's too much whitespace on the homepage. Please optimize the layout",
            'category' => 'UI'
        ],
        [
            'title' => 'Icons are confusing',
            'description' => 'The icons used are not intuitive. They need to be more recognizable.',
            'category' => 'UI'
        ],
        [
            'title' => 'Header is too large',
            'description' => 'It takes up too much space on the screen. Please shrink it.',
            'category' => 'UI'
        ],
        [
            'title' => 'Add more content to the footer',
            'description' => 'The footer is too sparse. Add more useful links or information.',
            'category' => 'UI'
        ],
        [
            'title' => 'Site takes too long to load',
            'description' => "The site's loading time is too slow. Performance needs to be improved.",
            'category' => 'UX'
        ],
        [
            'title' => 'Make the site easier to navigate',
            'description' => 'Navigation between pages is confusing. The menu structure needs to be enhanced.',
            'category' => 'UX'
        ],
        [
            'title' => "Search function doesn't work well",
            'description' => 'The search function is not returning relevant results. The algorithm needs to be improved',
            'category' => 'UX'
        ],
        [
            'title' => 'Call-to-action buttons are unclear',
            'description' => "It's unclear what the call-to-action buttons are for. They need to be more descriptive.",
            'category' => 'UX'
        ],
        [
            'title' => 'Forms are a bit difficult to fill out',
            'description' => 'Add tooltips or example inputs for an easier user experience.',
            'category' => 'UX'
        ],
        [
            'title' => 'No feedback after submitting a form',
            'description' => 'Provide confirmation dialog or error messages after a form has been submitted.',
            'category' => 'UX'
        ],
        [
            'title' => 'Poor site accessibility',
            'description' => 'Please ensure compliance with accessibility standards.',
            'category' => 'UX'
        ],
        [
            'title' => 'Too many pop-ups',
            'description' => 'There are simply too many annoying pop-ups. Kindly reduce their frequency or make them less intrusive.',
            'category' => 'UX'
        ],
        [
            'title' => 'User onboarding process is too lengthy',
            'description' => 'It needs to be simplified for new users.',
            'category' => 'UX'
        ],
        [
            'title' => 'Add dark more for better nighttime use.',
            'description' => 'It would help people with light sensitivities and those who prefer dark mode.',
            'category' => 'Enhancement'
        ],
        [
            'title' => 'Ability to save user preferences',
            'description' => "So users won't have to change settings on every device",
            'category' => 'Enhancement'
        ],
        [
            'title' => 'Integrate social media sharing options on each page',
            'description' => 'So users can share a page on their social media with just the click of a button, and give the site with more publicity.',
            'category' => 'Enhancement'
        ],
        [
            'title' => 'Contrast needs to be increased',
            'description' => 'The contrast between text and background is insufficient. Please increase it',
            'category' => 'UI'
        ],

        [
            'title' => 'Include a user profile management section',
            'description' => 'Users should be able to customize their profiles as they see fit',
            'category' => 'Enhancement'
        ],
        [
            'title' => 'Add filtering options to the product search results',
            'description' => "So that users can find what they're looking for more easily",
            'category' => 'Enhancement',
        ],
        [
            'title' => 'Implement a recommendation system based on user activity',
            'description' => "It will enable users to find more things they're interested in, driving more traffic to the website.",
            'category' => 'Enhancement',
        ],

        [
            'title' => 'Add a blog or news section',
            'description' => 'To keep users updated on changes or new additions to the site.',
            'category' => 'Enhancement',
        ],
        [
            'title' => 'Include a subscription or membership mode for exclusive content or benefits',
            'description' => 'This will incentivize users to keep visiting the site, and will generate more revenue for the site',
            'category' => 'Enhancement',
        ],
        [
            'title' => 'The "Submit" button is not working on the contact form',
            'description' => 'I am unable to submit data through the contact form.',
            'category' => 'Bug',
        ],
        [
            'title' => 'Links in the footer are broken',
            'description' => 'I am getting 404 errors when I try to open any of them.',
            'category' => 'Bug',
        ],
        [
            'title' => '"Add to Cart" button not responding on certain products',
            'description' => 'When I try to add certain products to cart, the button doesn\'t actually add it to my cart.',
            'category' => 'Bug',
        ],
        [
            'title' => 'There\'s a typo in the footer text',
            'description' => 'It needs to be corrected.',
            'category' => 'Bug',
        ],
        [
            'title' => 'Dropdown menu not functioning on mobile devices',
            'description' => '',
            'category' => 'Bug',
        ],
        [
            'title' => 'Some interactive elements are not responding to clicks',
            'description' => 'Certain elements such as hyperlinks and dropdown are not opening when they are clicked.',
            'category' => 'Bug',
        ],
        [
            'title' => 'Search bar not returning any results even for common terms',
            'description' => 'The results page just shows up as blank.',
            'category' => 'Bug',
        ],
        [
            'title' => 'The website\'s SSL certificate is showing as expired.',
            'description' => 'Please update the certificate',
            'category' => 'Bug',
        ],
        [
            'title' => 'Add multi-language support for international users',
            'description' => 'This would enable users who don\'t speak English to navigate the website more easily.',
            'category' => 'Feature',
        ],
        [
            'title' => 'Implement a feature to track order status and delivery updates.',
            'description' => 'This would allow users to stay updated on the current whereabouts of their order',
            'category' => 'Feature',
        ],
        [
            'title' => 'Include a wishlist functionality',
            'description' => 'So that users can save items for buying at a later date.',
            'category' => 'Feature',
        ],
        [
            'title' => 'Provide options for users to customize their dashboard',
            'description' => 'So users can personalize the website to their liking.',
            'category' => 'Feature',
        ],
        [
            'title' => 'Add integration with third-party services like Google Maps for location-based Feature',
            'description' => '',
            'category' => 'Feature',
        ],
        [
            'title' => 'Implement a rating and review system for products or services',
            'description' => 'This provides users a way to inform other customers about the quality of the products.',
            'category' => 'Feature',
        ],
        [
            'title' => 'Include a calendar feature for booking or scheduling events',
            'description' => 'So that users can track for themselves and inform others about an upcoming event they are planning',
            'category' => 'Feature',
        ],
        [
            'title' => 'Add a video tutorial section',
            'description' => 'For giving product demonstrations or guiding new users on how to use the site',
            'category' => 'Feature',
        ],
        [
            'title' => 'Create an API for developers to integrate with other applications',
            'description' => '',
            'category' => 'Feature',
        ],
        [
            'title' => 'Offer advanced search options with more criteria for filtering results',
            'description' => 'So that users can narrow the search down to exactly what they\'re looking for',
            'category' => 'Feature',
        ]
    ];
}
