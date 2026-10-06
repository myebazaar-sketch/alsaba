<?php
declare(strict_types=1);

require __DIR__ . '/includes/seo.php';

$faqs = require __DIR__ . '/data/faqs.php';
$site = seo_site();
?>
<!doctype html>
<html lang="en">
<head>
<?php
seo_head([
    'title'       => 'FAQ – Halal Spices, Shipping & Storage | ' . $site['name'],
    'description' => 'Answers about Al Saba Spices products: Halal certification, preservatives, storage, custom blends and private labelling.',
    'canonical'   => '/faq',
    'type'        => 'website',
    'image'       => '/img/alsabaposter.png',
    'keywords'    => ['Al Saba Spices FAQ', 'is chicken powder halal', 'spices preservatives'],
]);

seo_json(seo_graph([
    seo_organization_node(),
    seo_website_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'FAQ', 'url' => '/faq'],
    ]),
    seo_faq_node($faqs),
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
      <a href="/faq" aria-current="page">FAQ</a>
      <a href="/#contact">Contact</a>
    </div>
  </nav>

  <nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol>
      <li><a href="/">Home</a></li>
      <li aria-current="page">FAQ</li>
    </ol>
  </nav>

  <main class="section" style="max-width:840px; margin: 2rem auto; padding: 0 1rem;">
    <div class="section-heading">
      <p class="section-tag">Help</p>
      <h1>Frequently Asked Questions</h1>
    </div>

    <section class="faq-list">
      <?php foreach ($faqs as $item): ?>
        <details class="faq-item" style="margin-bottom:1rem; background:var(--cream-2); border:1px solid var(--sand); padding:1rem; border-radius:6px;">
          <summary style="font-weight:600; cursor:pointer;"><?= htmlspecialchars($item['question'], ENT_QUOTES, 'UTF-8') ?></summary>
          <div style="margin-top:0.5rem; color:var(--ink);"><?= htmlspecialchars($item['answer'], ENT_QUOTES, 'UTF-8') ?></div>
        </details>
      <?php endforeach; ?>
    </section>

    <p style="margin-top:1.5rem;"><a class="btn" href="/">Back to home</a></p>
  </main>
</body>
</html>
