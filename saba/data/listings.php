<?php
declare(strict_types=1);

/**
 * Curated marketplace listings (Amazon + Meesho).
 *
 * WHY MANUAL:
 * Amazon blocks server-side requests (robots/CAPTCHA), so fetching product
 * pages from PHP returns an error page — that is why the old scraper found
 * nothing. Listing details are therefore stored here and rendered instantly:
 * fast, reliable, no blocking, no terms-of-service issues.
 *
 * TO ADD A LISTING, copy a block and fill it in:
 *   'title' => 'Product name shown on the card',
 *   'image' => '/img/file.png'   (local) or 'https://...' (remote),
 *   'price' => 'From Rs499.00',  (optional — omit to hide the price)
 *   'url'   => 'https://www.amazon.in/dp/XXXXXXXXXX',
 *
 * TIP: get Amazon links from your Seller Central / Associates (SiteStripe)
 * dashboard, and Meesho links from the product's Share / affiliate button.
 */

return [
    'amazon' => [
        // Add your Amazon listings here (example format):
        // [
        //     'title' => 'Stone Ground Yellow Chilli Powder Jar 100g',
        //     'image' => '/img/yellojar.png',
        //     'price' => 'From Rs149.00',
        //     'url'   => 'https://www.amazon.in/dp/XXXXXXXXXX',
        // ],
    ],

    'meesho' => [
        [
            'title' => 'Halal Chicken Soup Seasoning Powder 150gm',
            'image' => '/img/chicken01.png',
            'price' => 'From Rs425.00',
            'url'   => 'https://www.meesho.com/halal-chicken-broth-soup-powder-150gm-mix-with-spices-and-vegetable-60-chicken/p/ft3jgg?ms=2&source=Meri+Shop',
        ],
        [
            'title' => 'Halal Chicken Soup Seasoning Powder 50gm',
            'image' => '/img/chciken50gm.png',
            'price' => 'From Rs170.00',
            'url'   => 'https://www.meesho.com/halal-chicken-broth-soup-powder-50gm-mix-with-spices-and-vegetable-60-chicken/p/gl4byi?ms=2&source=Meri+Shop',
        ],
        [
            'title' => 'IMLI Chatni Powder',
            'image' => '/img/imlifront.png',
            'price' => 'From Rs229.00',
            'url'   => 'https://www.meesho.com/al-saba-spices-instant-imli-tamarind-chutney-powder-mix-sweet-tangy-indian-chatni-just-add-cold-water/p/guba1y?ms=2',
        ],
        [
            'title' => 'Chai Masala',
            'image' => '/img/chaifront.png',
            'price' => 'From Rs149.00',
            'url'   => 'https://www.meesho.com/al-saba-spices-chai-masala-powder-adrak-elaichi-dalchini-laung-kali-mirch-blend-50g/p/gunjy3?ms=2',
        ],
    ],
];
