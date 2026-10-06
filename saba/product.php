<?php
declare(strict_types=1);

require __DIR__ . '/includes/seo.php';

$products = require __DIR__ . '/data/products.php';
$site = seo_site();

$slug = null;
if (isset($_GET['slug']) && $_GET['slug'] !== '') {
    $slug = (string) $_GET['slug'];
} elseif (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $slug = trim((string) $_SERVER['PATH_INFO'], '/');
}
$slug = $slug !== null ? rawurldecode($slug) : null;

$product = null;
foreach ($products as $item) {
    if ($item['slug'] === $slug) {
        $product = $item;
        break;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
if (!$product) {
    http_response_code(404);
    seo_head([
        'title'       => 'Product not found | ' . $site['name'],
        'description' => 'The requested product is not available.',
        'robots'      => 'noindex, follow',
    ]);
    seo_analytics();
    echo "</head>\n<body>\n";
    echo '<main class="section" style="text-align:center; padding-top:5rem;"><h1>Product not found</h1><p>The product you are looking for is unavailable.</p><p><a class="btn" href="/">Back to home</a></p></main>';
    echo "</body>\n</html>";
    exit;
}

$canonicalPath = '/product/' . rawurlencode($product['slug']);
$marketplace = str_contains($product['buyUrl'], 'meesho') ? 'Meesho' : (str_contains($product['buyUrl'], 'amazon') ? 'Amazon' : 'our partner stores');

seo_head([
    'title'       => $product['name'] . ' – Buy Online | ' . $site['name'],
    'description' => $product['detail'] . ' ' . $product['desc'],
    'canonical'   => $canonicalPath,
    'type'        => 'product',
    'image'       => $product['image'],
    'image_alt'   => $product['name'] . ' by ' . $site['name'],
    'keywords'    => $product['keywords'],
]);

seo_json(seo_graph([
    seo_organization_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Products', 'url' => '/#products'],
        ['name' => $product['name'], 'url' => $canonicalPath],
    ]),
    seo_product_node($product),
]));

seo_analytics();
?>
</head>
<body>
  <nav class="topbar">
    <a class="topbar-brand" href="/" aria-label="Al Saba Spices home">
      <img src="/assets/brand-mark.svg" alt="Al Saba Spices logo">
      <span class="brand-text">
        <span class="brand-name">Al Saba</span>
        <span class="brand-sub">Spices</span>
      </span>
    </a>
    <div class="topbar-links">
      <a href="/#products">Products</a>
      <a href="/blog">Recipes &amp; Blog</a>
      <a href="/faq">FAQ</a>
      <a href="/#contact">Contact</a>
    </div>
  </nav>

  <nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol>
      <li><a href="/">Home</a></li>
      <li><a href="/#products">Products</a></li>
      <li aria-current="page"><?= seo_e($product['name']) ?></li>
    </ol>
  </nav>

  <main class="section" style="padding-top:2.5rem;">
    <article class="product-page">
      <div class="product-page-media">
        <img src="<?= seo_e($product['image']) ?>" alt="<?= seo_e($product['name']) ?> – Al Saba Spices" width="900" height="900" loading="eager" decoding="async">
      </div>
      <div class="product-page-copy">
        <p class="section-tag">Al Saba Spices</p>
        <h1><?= seo_e($product['name']) ?></h1>
        <p class="lead"><?= seo_e($product['desc']) ?></p>
        <div class="product-price"><?= seo_e($product['price']) ?></div>
        <p><?= seo_e($product['detail']) ?></p>
        <ul class="product-highlights">
          <li>Authentic Indian recipe, crafted in-house by Al Saba Spices</li>
          <li>No MSG and no artificial preservatives</li>
          <li>Available online via <?= seo_e($marketplace) ?></li>
          <li>Fast delivery across India</li>
        </ul>
        <div class="hero-actions">
          <a class="btn" href="<?= seo_e($product['buyUrl']) ?>" target="_blank" rel="noopener noreferrer">Buy it now on <?= seo_e($marketplace) ?></a>
          <a class="btn btn-secondary" href="/#products">Browse all products</a>
        </div>
      </div>
    </article>

    <section class="section">
      <div class="section-heading">
        <p class="section-tag">You may also like</p>
        <h2>Related Al Saba Spices products</h2>
      </div>
      <div class="product-grid">
        <?php
          $related = array_values(array_filter($products, fn ($p) => $p['slug'] !== $product['slug']));
          foreach (array_slice($related, 0, 4) as $relatedProduct):
        ?>
          <article class="product-card">
            <a class="product-card-link" href="/product/<?= seo_e(rawurlencode($relatedProduct['slug'])) ?>">
              <img loading="lazy" decoding="async" src="<?= seo_e($relatedProduct['image']) ?>" alt="<?= seo_e($relatedProduct['name']) ?> – Al Saba Spices">
              <h3><?= seo_e($relatedProduct['name']) ?></h3>
            </a>
            <p><?= seo_e($relatedProduct['desc']) ?></p>
            <div class="product-price"><?= seo_e($relatedProduct['price']) ?></div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>Al Saba Spices</p>
    <p><?= seo_e($site['address']['locality']) ?>, <?= seo_e($site['address']['region']) ?>, <?= seo_e($site['address']['country']) ?></p>
    <p><a href="mailto:<?= seo_e($site['email']) ?>"><?= seo_e($site['email']) ?></a> · <a href="tel:<?= seo_e($site['phone_e164']) ?>"><?= seo_e($site['phone']) ?></a></p>
    <p><a href="/blog">Recipes &amp; Blog</a> · <a href="/faq">FAQ</a></p>
  </footer>
</body>
</html>
