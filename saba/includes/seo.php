<?php
declare(strict_types=1);

/**
 * Shared SEO helpers: meta tags, Open Graph, JSON-LD schema and GA4.
 * Requires data/site.php.
 */

function seo_site(): array
{
    static $site = null;
    if ($site === null) {
        $site = require __DIR__ . '/../data/site.php';
    }
    return $site;
}

function seo_e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function seo_base_url(): string
{
    return rtrim((string) seo_site()['url'], '/');
}

/** Convert a path (or pass through an absolute URL) to an absolute URL. */
function seo_abs(string $path): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return seo_base_url() . '/' . ltrim($path, '/');
}

/** Canonical path of the current request without the query string. */
function seo_current_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return strtok($uri, '?') ?: '/';
}

/** Comma separated keyword list from config, optionally merged with extras. */
function seo_keywords(array $extra = []): string
{
    $keywords = array_merge(seo_site()['keywords'], $extra);
    return implode(', ', array_unique(array_filter($keywords)));
}

/** Turn "From Rs425.00" into "425.00". Returns null when no number is found. */
function seo_price(string $price): ?string
{
    if (preg_match('/\d[\d,]*(?:\.\d+)?/', $price, $m)) {
        return str_replace(',', '', $m[0]);
    }
    return null;
}

/**
 * Print the common <head> block (meta, canonical, OG, Twitter, favicon, CSS).
 *
 * @param array $opts title, description, canonical, type, image, image_alt,
 *                    robots, keywords, published_time, modified_time, author
 */
function seo_head(array $opts): void
{
    $site = seo_site();

    $title       = (string) ($opts['title'] ?? $site['name']);
    $description = (string) ($opts['description'] ?? $site['description']);
    $canonical   = seo_abs((string) ($opts['canonical'] ?? seo_current_path()));
    $type        = (string) ($opts['type'] ?? 'website');
    $image       = seo_abs((string) ($opts['image'] ?? $site['logo']));
    $imageAlt    = (string) ($opts['image_alt'] ?? $title);
    $robots      = (string) ($opts['robots'] ?? 'index, follow, max-image-preview:large');
    $keywords    = is_array($opts['keywords'] ?? null) ? seo_keywords($opts['keywords']) : (string) ($opts['keywords'] ?? seo_keywords());
    $author      = (string) ($opts['author'] ?? $site['name']);
    $favicon     = (string) ($site['favicon'] ?? $site['logo']);
    ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= seo_e($title) ?></title>
  <meta name="description" content="<?= seo_e($description) ?>">
  <meta name="keywords" content="<?= seo_e($keywords) ?>">
  <meta name="robots" content="<?= seo_e($robots) ?>">
  <meta name="author" content="<?= seo_e($author) ?>">
  <link rel="canonical" href="<?= seo_e($canonical) ?>">
  <meta name="theme-color" content="<?= seo_e($site['theme_color']) ?>">
  <meta name="geo.region" content="IN-HR">
  <meta name="geo.placename" content="<?= seo_e($site['address']['locality']) ?>">
  <link rel="icon" type="image/svg+xml" href="<?= seo_e($favicon) ?>">
  <link rel="apple-touch-icon" href="<?= seo_e($favicon) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="<?= seo_e(seo_base_url()) ?>">
  <meta property="og:locale" content="en_US">
  <meta property="og:type" content="<?= seo_e($type) ?>">
  <meta property="og:site_name" content="<?= seo_e($site['name']) ?>">
  <meta property="og:title" content="<?= seo_e($title) ?>">
  <meta property="og:description" content="<?= seo_e($description) ?>">
  <meta property="og:url" content="<?= seo_e($canonical) ?>">
  <meta property="og:image" content="<?= seo_e($image) ?>">
  <meta property="og:image:alt" content="<?= seo_e($imageAlt) ?>">
<?php if ($type === 'article'): ?>
  <meta property="article:publisher" content="<?= seo_e($site['name']) ?>">
<?php if (!empty($opts['published_time'])): ?>
  <meta property="article:published_time" content="<?= seo_e($opts['published_time']) ?>">
<?php endif; ?>
<?php if (!empty($opts['modified_time'])): ?>
  <meta property="article:modified_time" content="<?= seo_e($opts['modified_time']) ?>">
<?php endif; ?>
<?php endif; ?>
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= seo_e($title) ?>">
  <meta name="twitter:description" content="<?= seo_e($description) ?>">
  <meta name="twitter:image" content="<?= seo_e($image) ?>">
  <meta name="twitter:image:alt" content="<?= seo_e($imageAlt) ?>">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&display=swap">
  <link rel="stylesheet" href="/assets/app.css">
<?php
}

/** Print a JSON-LD script block. */
function seo_json(array $data): void
{
    echo "  <script type=\"application/ld+json\">\n  "
        . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        . "\n  </script>\n";
}

/** Wrap one or more schema nodes in a single @graph document. */
function seo_graph(array $nodes): array
{
    return [
        '@context' => 'https://schema.org',
        '@graph'   => array_values($nodes),
    ];
}

/** Organization + LocalBusiness node built from site config. */
function seo_organization_node(): array
{
    $site = seo_site();
    $address = $site['address'];

    return [
        '@type'   => ['Organization', 'LocalBusiness', 'Store'],
        '@id'     => seo_base_url() . '/#organization',
        'name'    => $site['name'],
        'url'     => seo_base_url(),
        'logo'    => [
            '@type' => 'ImageObject',
            'url'   => seo_abs($site['logo']),
        ],
        'image'   => seo_abs($site['logo']),
        'description' => $site['description'],
        'email'   => $site['email'],
        'telephone' => $site['phone_e164'],
        'address' => [
            '@type'           => 'PostalAddress',
            'addressLocality' => $address['locality'],
            'addressRegion'   => $address['region'],
            'addressCountry'  => $address['country_code'],
        ],
        'areaServed' => [
            '@type' => 'Country',
            'name'  => $address['country'],
        ],
        'sameAs'  => $site['socials'],
    ];
}

function seo_website_node(): array
{
    $site = seo_site();
    return [
        '@type'       => 'WebSite',
        '@id'         => seo_base_url() . '/#website',
        'name'        => $site['name'],
        'url'         => seo_base_url(),
        'description' => $site['description'],
        'publisher'   => ['@id' => seo_base_url() . '/#organization'],
        'inLanguage'  => 'en',
    ];
}

/**
 * BreadcrumbList node.
 *
 * @param array<int, array{name: string, url: string}> $items ordered trail (home first)
 */
function seo_breadcrumb_node(array $items): array
{
    $elements = [];
    $position = 1;
    foreach ($items as $item) {
        $elements[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => $item['name'],
            'item'     => seo_abs($item['url']),
        ];
    }

    return [
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $elements,
    ];
}

/** ItemList of Product nodes. */
function seo_product_item_list(array $products): array
{
    $site = seo_site();
    $items = [];
    $position = 1;

    foreach ($products as $product) {
        $node = [
            '@type'       => 'ListItem',
            'position'    => $position++,
            'name'        => $product['name'],
            'url'         => seo_abs('/product/' . rawurlencode($product['slug'])),
        ];

        $price = seo_price((string) $product['price']);
        if ($price !== null) {
            $node['item'] = [
                '@type'   => 'Product',
                'name'    => $product['name'],
                'url'     => seo_abs('/product/' . rawurlencode($product['slug'])),
                'image'   => seo_abs($product['image']),
                'description' => $product['detail'],
                'brand'   => ['@type' => 'Brand', 'name' => $site['name']],
                'offers'  => [
                    '@type'         => 'Offer',
                    'price'         => $price,
                    'priceCurrency' => 'INR',
                    'availability'  => 'https://schema.org/InStock',
                    'url'           => seo_abs('/product/' . rawurlencode($product['slug'])),
                ],
            ];
        }

        $items[] = $node;
    }

    return [
        '@type'           => 'ItemList',
        'itemListElement' => $items,
    ];
}

/** Product node for a single product landing page. */
function seo_product_node(array $product): array
{
    $site = seo_site();
    $node = [
        '@type'       => 'Product',
        '@id'         => seo_abs('/product/' . rawurlencode($product['slug'])) . '#product',
        'name'        => $product['name'],
        'description' => $product['detail'],
        'image'       => [seo_abs($product['image'])],
        'brand'       => ['@type' => 'Brand', 'name' => $site['name']],
        'category'    => 'Indian Spices',
    ];

    $price = seo_price((string) $product['price']);
    if ($price !== null) {
        $node['offers'] = [
            '@type'         => 'Offer',
            'price'         => $price,
            'priceCurrency' => 'INR',
            'availability'  => 'https://schema.org/InStock',
            'url'           => $product['buyUrl'],
            'seller'        => ['@id' => seo_base_url() . '/#organization'],
        ];
    }

    return $node;
}

/** FAQPage node from a list of ['question' => ..., 'answer' => ...]. */
function seo_faq_node(array $faqs): array
{
    $items = [];
    foreach ($faqs as $faq) {
        $items[] = [
            '@type'          => 'Question',
            'name'           => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $faq['answer'],
            ],
        ];
    }

    return [
        '@type'      => 'FAQPage',
        'mainEntity' => $items,
    ];
}

/** GA4 tracking snippet. No output when no Measurement ID is configured. */
function seo_analytics(): void
{
    $ga4 = trim((string) (seo_site()['ga4'] ?? ''));
    if ($ga4 === '') {
        return;
    }
    ?>
  <!-- Google Analytics 4 -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= seo_e($ga4) ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= seo_e($ga4) ?>');
  </script>
<?php
}
