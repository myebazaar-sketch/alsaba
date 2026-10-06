<?php
declare(strict_types=1);

require __DIR__ . '/includes/seo.php';

$articles = require __DIR__ . '/data/articles.php';
$site = seo_site();

$blogDescription = 'Indian cooking recipes, spice guides and stories from Al Saba Spices — masala chai, sabut garam masala, halal chicken seasoning and more.';

$itemList = [];
$position = 1;
foreach ($articles as $article) {
    $itemList[] = [
        '@type'    => 'ListItem',
        'position' => $position++,
        'url'      => seo_abs('/article/' . rawurlencode($article['slug'])),
        'name'     => $article['title'],
        'item'     => [
            '@type'         => 'BlogPosting',
            'headline'      => $article['title'],
            'description'   => $article['text'],
            'image'         => seo_abs($article['image']),
            'url'           => seo_abs('/article/' . rawurlencode($article['slug'])),
            'author'        => ['@type' => 'Organization', 'name' => $site['name']],
            'publisher'     => ['@id' => seo_base_url() . '/#organization'],
        ],
    ];
}

?>
<!doctype html>
<html lang="en">
<head>
<?php
seo_head([
    'title'       => 'Recipes & Spice Blog | ' . $site['name'],
    'description' => $blogDescription,
    'canonical'   => '/blog',
    'type'        => 'website',
    'image'       => $articles[0]['image'] ?? '/img/alsabaposter.png',
    'image_alt'   => 'Al Saba Spices recipes and spice guides',
    'keywords'    => ['Indian spice recipes', 'masala chai recipe', 'garam masala guide', 'halal chicken seasoning'],
]);

seo_json(seo_graph([
    seo_organization_node(),
    seo_website_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Recipes & Blog', 'url' => '/blog'],
    ]),
    [
        '@type'       => 'Blog',
        '@id'         => seo_base_url() . '/blog#blog',
        'name'        => 'Recipes & Spice Blog – ' . $site['name'],
        'description' => $blogDescription,
        'url'         => seo_abs('/blog'),
        'publisher'   => ['@id' => seo_base_url() . '/#organization'],
        'inLanguage'  => 'en',
    ],
    [
        '@type'           => 'ItemList',
        'itemListElement' => $itemList,
    ],
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
      <li aria-current="page">Recipes &amp; Blog</li>
    </ol>
  </nav>

  <main class="section" style="padding-top:2.5rem;">
    <div class="section-heading">
      <p class="section-tag">Insights</p>
      <h1>Recipes &amp; Spice Knowledge</h1>
    </div>
    <p style="text-align:center; max-width:760px; margin:0 auto;">
      Cooking inspiration, spice guides and behind-the-scenes stories from the Al Saba Spices kitchen.
    </p>

    <div class="blog-grid">
      <?php foreach ($articles as $article): ?>
        <a class="article-card" href="/article/<?= seo_e(rawurlencode($article['slug'])) ?>" aria-label="Read: <?= seo_e($article['title']) ?>">
          <img loading="lazy" decoding="async" src="<?= seo_e($article['image']) ?>" alt="<?= seo_e($article['title']) ?>">
          <h2><?= seo_e($article['title']) ?></h2>
          <p><?= seo_e($article['text']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </main>

  <footer class="footer">
    <p>Al Saba Spices</p>
    <p><?= seo_e($site['address']['locality']) ?>, <?= seo_e($site['address']['region']) ?>, <?= seo_e($site['address']['country']) ?></p>
    <p><a href="mailto:<?= seo_e($site['email']) ?>"><?= seo_e($site['email']) ?></a> · <a href="tel:<?= seo_e($site['phone_e164']) ?>"><?= seo_e($site['phone']) ?></a></p>
    <p><a href="/">Home</a> · <a href="/faq">FAQ</a></p>
  </footer>
</body>
</html>
