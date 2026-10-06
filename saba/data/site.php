<?php
declare(strict_types=1);

/**
 * Central site + SEO configuration.
 * Update values here and every page (meta tags, schema, sitemap links) stays in sync.
 */
return [
    'name'        => 'Al Saba Spices',
    'url'         => 'https://alsabaspices.com',
    'tagline'     => 'Authentic Indian flavors, crafted for your kitchen.',
    'description' => 'Buy premium Halal Chicken Soup Seasoning, Chai Masala, Stone Ground Yellow Chilli and Sabut Garam Masala from Al Saba Spices. Authentic Indian flavors, handmade without preservatives.',

    // Primary keywords for the brand / homepage.
    'keywords'    => [
        'halal chicken seasoning powder',
        'chai masala powder',
        'stone ground yellow chilli powder',
        'sabut garam masala',
        'Indian spices online',
        'Al Saba Spices',
    ],

    'email'       => 'info@alsabaspices.com',
    'phone'       => '+91 8796509342',
    'phone_e164'  => '+918796509342',
    'whatsapp'    => '918796509342',

    'address'     => [
        'locality'     => 'Gurgaon',
        'region'       => 'Haryana',
        'country'      => 'India',
        'country_code' => 'IN',
    ],

    'socials'     => [
        'https://www.instagram.com/alsabaspices/',
        'https://www.facebook.com/alsabaspices',
        'https://www.youtube.com/@ALSabaSpices',
    ],

    'logo'        => '/assets/brand-mark.svg',

    // Google Analytics 4 Measurement ID. Set to '' to disable tracking.
    'ga4'         => 'G-6M7ZL6GWZ9',

    'theme_color' => '#364220',
];
