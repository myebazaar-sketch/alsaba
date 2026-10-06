<?php
declare(strict_types=1);

$siteName = 'Al Saba Spices';
$tagline = 'Authentic Indian flavors, crafted for your kitchen.';
$products = require __DIR__ . '/data/products.php';
$listings = require __DIR__ . '/data/listings.php';

// Amazon content from includes/amazon-widget.html — accepts either:
//   * raw widget HTML (Native Shopping Ads / SiteStripe), or
//   * one product per line:  Title | Price | Image | URL   (URL always last)
$amazonSource = trim((string) preg_replace(
    '/<!--.*?-->/s',
    '',
    (string) @file_get_contents(__DIR__ . '/includes/amazon-widget.html')
));

$amazonWidget = '';
$amazonLinkListings = [];

foreach (preg_split('/\r\n|\r|\n/', $amazonSource) as $line) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    if (str_contains($line, '<')) {
        $amazonWidget .= $line . "\n";      // widget markup -> embedded as-is
        continue;
    }

    $parts = array_map('trim', explode('|', $line));
    $url = (string) array_pop($parts);
    if (!preg_match('~^(https?://|//)\S+$~i', $url)) {
        continue;                           // not a product line
    }

    $title = $price = $image = '';
    $remaining = count($parts);
    if ($remaining === 1) {
        $title = $parts[0];
    } elseif ($remaining === 2) {
        [$title, $price] = $parts;
    } elseif ($remaining >= 3) {
        [$title, $price, $image] = [$parts[0], $parts[1], $parts[2]];
    }

    // Derive a readable title from the URL when none was given.
    if ($title === '') {
        $path = (string) parse_url($url, PHP_URL_PATH);
        if (preg_match('~/([a-z0-9][a-z0-9-]{5,})/dp/~i', $path, $m)) {
            $title = ucwords(str_replace('-', ' ', $m[1]));
        } elseif (preg_match('~/dp/([A-Z0-9]{10})~', $path, $m)) {
            $title = 'Amazon product ' . $m[1];
        } else {
            $title = 'Amazon listing';
        }
    }

    $amazonLinkListings[] = [
        'title' => $title,
        'price' => $price,
        'image' => $image,
        'url'   => $url,
    ];
}

$amazonWidget = trim($amazonWidget);
$hasAmazonWidget = $amazonWidget !== '';
$listings['amazon'] = array_merge($listings['amazon'], $amazonLinkListings);

$galleryImages = [
    '/img/saba3.png',
    '/img/soup01.png',
    '/img/imliposter.png',
    '/img/chaiposter.png',
    '/img/yellojar.png',
    '/img/yellopouch.png',
];

function normalizeUrl(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return $url;
    }
    if (str_starts_with($url, '//')) {
        return 'https:' . $url;
    }
    if (!preg_match('#^https?://#i', $url)) {
        return 'https://' . ltrim($url, '/');
    }
    return $url;
}

require __DIR__ . '/data/articles.php';
require __DIR__ . '/data/faqs.php';
require __DIR__ . '/includes/seo.php';

$site = seo_site();
?>
<!doctype html>
<html lang="en">
<head>
<?php
seo_head([
    'title'       => $siteName . ' – Halal Chicken Seasoning, Chai Masala & Indian Spices Online',
    'description' => $site['description'],
    'canonical'   => '/',
    'type'        => 'website',
    'image'       => '/img/alsabaposter.png',
    'image_alt'   => 'Al Saba Spices premium Indian spice collection',
    'keywords'    => $site['keywords'],
]);

seo_json(seo_graph([
    seo_organization_node(),
    seo_website_node(),
    seo_product_item_list($products),
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
      <a href="#products">Products</a>
      <a href="#shop">Shop</a>
      <a href="#gallery">Gallery</a>
      <a href="/blog">Recipes &amp; Blog</a>
      <a href="/faq">FAQ</a>
      <a href="#contact">Contact</a>
    </div>
  </nav>

  <header class="hero hero-banner">
    <div class="hero-banner-media">
      <img src="/img/alsabaposter.png" alt="Assorted Indian spices, dried chillies and seasonings on a rustic table">
    </div>
    <div class="hero-banner-inner">
      <div class="hero-copy">
        
        <p class="eyebrow">Halal seasonings &amp; premium Indian spices</p>
        <h1>Al Saba Spices – Halal Chicken Seasoning &amp; Indian Spices</h1>
        <p class="lead">Premium Indian spice blends curated as elegant gifts and export-grade essentials for discerning kitchens.</p>
        <div class="hero-actions">
          <a class="btn" href="#products">Explore Premium Collection</a>
          <a class="btn btn-secondary" href="#shop">Buy it now</a>
        </div>
      </div>
    </div>
  </header>

  <main>
    <section class="section intro">
      <div class="section-heading">
        <p class="section-tag">Our Promise</p>
        <h2>Elevated spice craftsmanship for premium gifting and export</h2>
      </div>
      <p>Al Saba Spices brings the essence of Indian culinary heritage to your table through refined blends, rich aromas, and export-ready quality designed for boutique gifting and hospitality.</p>
    </section>

    <section id="products" class="section">
      <div class="section-heading">
        <p class="section-tag">Featured Collection</p>
        <h2>Signature Premium Indian Spices</h2>
      </div>
      <div class="product-grid">
        <?php foreach ($products as $product): ?>
          <article class="product-card" tabindex="0" role="button"
            data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
            data-desc="<?= htmlspecialchars($product['desc'], ENT_QUOTES, 'UTF-8') ?>"
            data-detail="<?= htmlspecialchars($product['detail'], ENT_QUOTES, 'UTF-8') ?>"
            data-image="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>"
            data-price="<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?>"
            data-buy-url="<?= htmlspecialchars($product['buyUrl'], ENT_QUOTES, 'UTF-8') ?>">
            <a class="product-card-link" href="/product/<?= seo_e(rawurlencode($product['slug'])) ?>">
              <img loading="lazy" decoding="async" src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> – Al Saba Spices">
              <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
            </a>
            <p><?= htmlspecialchars($product['desc'], ENT_QUOTES, 'UTF-8') ?></p>
            <div class="product-price"><?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?></div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="shop" class="section shop-section">
      <div class="section-heading">
        <p class="section-tag">E-commerce</p>
        <h2>Buy premium spices on leading marketplaces</h2>
      </div>
      <div class="marketplace-grid">
        <article class="marketplace-card">
          <h3>Meesho</h3>
          <p>Shop curated Indian spices on Meesho for convenient home delivery.</p>
          <a class="btn" href="https://www.meesho.com" target="_blank" rel="noopener">Visit Meesho</a>
        </article>
        <article class="marketplace-card">
          <h3>Flipkart</h3>
          <p>Find our premium blends on Flipkart with trusted delivery options.</p>
          <a class="btn" href="https://www.flipkart.com" target="_blank" rel="noopener">Visit Flipkart</a>
        </article>
        <article class="marketplace-card">
          <h3>Amazon</h3>
          <p>Buy export-ready Indian spices on Amazon for fast shipping.</p>
          <a class="btn" href="https://www.amazon.in" target="_blank" rel="noopener">Visit Amazon</a>
        </article>
        <article class="marketplace-card">
          <h3>JioMart</h3>
          <p>Order from JioMart for easy local delivery and grocery checkout.</p>
          <a class="btn" href="https://www.jiomart.com" target="_blank" rel="noopener">Visit JioMart</a>
        </article>
      </div>
    </section>

    <?php if ($hasAmazonWidget || !empty($listings['amazon']) || !empty($listings['meesho'])): ?>
    <section id="marketplaces" class="section">
      <div class="section-heading">
        <p class="section-tag">Shop Online</p>
        <h2>Our Amazon &amp; Meesho Listings</h2>
      </div>

      <?php if ($hasAmazonWidget): ?>
        <div class="marketplace-embed">
          <p class="listing-group-label">Amazon</p>
          <?= $amazonWidget ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($listings['amazon']) || !empty($listings['meesho'])): ?>
        <div class="listing-grid">
          <?php foreach (['amazon' => 'Amazon', 'meesho' => 'Meesho'] as $marketKey => $marketLabel): ?>
            <?php foreach ($listings[$marketKey] as $listing): ?>
              <article class="listing-card<?= empty($listing['image']) ? ' listing-card--noimg' : '' ?>">
                <span class="listing-badge listing-badge--<?= seo_e($marketKey) ?>"><?= seo_e($marketLabel) ?></span>
                <?php if (!empty($listing['image'])): ?>
                  <img loading="lazy" decoding="async" src="<?= seo_e($listing['image']) ?>" alt="<?= seo_e($listing['title']) ?> – buy on <?= seo_e($marketLabel) ?>">
                <?php endif; ?>
                <h3><?= seo_e($listing['title']) ?></h3>
                <?php if (!empty($listing['price'])): ?>
                  <div class="product-price"><?= seo_e($listing['price']) ?></div>
                <?php endif; ?>
                <a class="btn listing-buy" href="<?= seo_e(normalizeUrl((string) $listing['url'])) ?>" target="_blank" rel="noopener noreferrer nofollow sponsored">Buy on <?= seo_e($marketLabel) ?></a>
              </article>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
    <?php endif; ?>

    <section id="gallery" class="section gallery-section">
      <div class="section-heading">
        <p class="section-tag">Visual Story</p>
        <h2>Luxury spice stories in motion</h2>
      </div>
      <div class="gallery-grid">
        <?php
          $galleryAltMap = [
              'saba3'      => 'Al Saba Spices assorted premium Indian spice packs',
              'soup01'     => 'Al Saba Spices Halal chicken soup seasoning served in a bowl',
              'imliposter' => 'Al Saba Spices imli tamarind chutney powder poster',
              'chaiposter' => 'Al Saba Spices masala chai spice blend poster',
              'yellojar'   => 'Al Saba Spices stone ground yellow chilli powder jar',
              'yellopouch' => 'Al Saba Spices stone ground yellow chilli powder pouch',
          ];
        ?>
        <?php foreach ($galleryImages as $image): ?>
          <?php
            $imageFile = pathinfo((string) parse_url($image, PHP_URL_PATH), PATHINFO_FILENAME);
            $galleryAlt = $galleryAltMap[$imageFile] ?? 'Al Saba Spices premium Indian spice range';
          ?>
          <div class="gallery-card">
            <img loading="lazy" decoding="async" src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($galleryAlt, ENT_QUOTES, 'UTF-8') ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section media-section">
      <div class="section-heading">
        <p class="section-tag">Watch & Learn</p>
        <h2>Recipe inspiration for refined kitchens</h2>
      </div>
      <div class="media-grid">
        <div class="video-card">
          <h3>Spice blending tips</h3>
          <iframe src="https://www.youtube.com/embed/6Q8wAodM0iY" title="Indian spice cooking video" loading="lazy" allowfullscreen></iframe>
        </div>
        <div class="video-card">
          <h3>Traditional Indian kitchen recipes</h3>
          <iframe src="https://www.youtube.com/embed/5b0f0f7V2Q4" title="Traditional Indian kitchen recipe" loading="lazy" allowfullscreen></iframe>
        </div>
      </div>
    </section>

    <section class="section articles-section">
      <div class="section-heading">
        <p class="section-tag">Insights</p>
        <h2>Recipes &amp; Spice Knowledge</h2>
      </div>
      <div class="article-grid">
        <?php foreach ($articles as $article): ?>
          <a class="article-card" href="/article/<?= htmlspecialchars(rawurlencode($article['slug']), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="Read article: <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>">
            <img loading="lazy" decoding="async" src="<?= htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>">
            <h3><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><?= htmlspecialchars($article['text'], ENT_QUOTES, 'UTF-8') ?></p>
          </a>
        <?php endforeach; ?>
      </div>
      <p style="text-align:center; margin-top:1.6rem;"><a class="btn" href="/blog">View all recipes &amp; articles</a></p>
    </section>

    <section id="contact" class="section contact">
      <div class="contact-card">
        <p class="section-tag">Contact</p>
        <h2>Bring premium Indian flavor to your market</h2>
        <p>For wholesale orders, luxury gifting requests, and export inquiries, reach out to us today.</p>
        <div class="hero-actions contact-actions">
          <a class="btn" href="mailto:<?= seo_e($site['email']) ?>">Email Us</a>
          <a class="btn btn-secondary" href="tel:<?= seo_e($site['phone_e164']) ?>">Call <?= seo_e($site['phone']) ?></a>
          <a class="btn btn-secondary" href="https://wa.me/<?= seo_e($site['whatsapp']) ?>" target="_blank" rel="noopener">WhatsApp Inquiry</a>
        </div>
      </div>
    </section>
  </main>

  <div class="product-modal" id="productModal" aria-hidden="true">
    <div class="product-modal-backdrop" data-close></div>
    <div class="product-modal-panel" role="dialog" aria-modal="true" aria-labelledby="modalProductName">
      <button class="product-modal-close" type="button" data-close aria-label="Close product details">×</button>
      <div class="product-modal-image">
        <img src="" alt="" id="modalProductImage">
      </div>
      <div class="product-modal-content">
        <p class="section-tag">Product detail</p>
        <h2 id="modalProductName"></h2>
        <p class="product-modal-short" id="modalProductDesc"></p>
        <p class="product-modal-detail" id="modalProductDetail"></p>
        <div class="product-price" id="modalProductPrice"></div>
        <a class="btn product-modal-buy" id="modalProductBuy" href="#" target="_blank" rel="noopener">Buy it now</a>
      </div>
    </div>
  </div>

  <section id="faq" class="section faq-section" style="margin-top:2rem;">
    <div class="section-heading">
      <p class="section-tag">Help</p>
      <h2>Frequently Asked Questions</h2>
    </div>
    <div class="faq-list" style="max-width:900px; margin:0 auto;">
      <?php foreach ($faqs as $item): ?>
        <details class="faq-item" style="margin-bottom:1rem; background:var(--cream-2); border:1px solid var(--sand); padding:1rem; border-radius:6px;">
          <summary style="font-weight:600; cursor:pointer;"><?= htmlspecialchars($item['question'], ENT_QUOTES, 'UTF-8') ?></summary>
          <div style="margin-top:0.5rem; color:var(--ink);"><?= htmlspecialchars($item['answer'], ENT_QUOTES, 'UTF-8') ?></div>
        </details>
      <?php endforeach; ?>
    </div>
  </section>

  <footer class="footer">
    <p>Al Saba Spices</p>
    <p>Premium Indian spice blends for gifting, hospitality, and export-ready markets.</p>
    <p><?= seo_e($site['address']['locality']) ?>, <?= seo_e($site['address']['region']) ?>, <?= seo_e($site['address']['country']) ?></p>
    <p><a href="mailto:<?= seo_e($site['email']) ?>"><?= seo_e($site['email']) ?></a> · <a href="tel:<?= seo_e($site['phone_e164']) ?>"><?= seo_e($site['phone']) ?></a></p>
    <div class="social" aria-label="Follow Al Saba Spices on social media">
      <a href="https://www.instagram.com/alsabaspices/" target="_blank" rel="noopener" aria-label="Al Saba Spices on Instagram">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M12 8.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M17.5 6.5h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
      <a href="https://www.facebook.com/alsabaspices" target="_blank" rel="noopener" aria-label="Al Saba Spices on Facebook">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M22 12a10 10 0 10-11.5 9.9v-7h-2.3v-2.9h2.3V9.1c0-2.3 1.4-3.6 3.5-3.6 1 0 2 .08 2 .08v2.2h-1.1c-1.1 0-1.4.66-1.4 1.3v1.6h2.4l-.38 2.9h-2v7A10 10 0 0022 12z" stroke="currentColor" stroke-width="0" fill="currentColor"/>
        </svg>
      </a>
      <a href="https://www.youtube.com/@ALSabaSpices" target="_blank" rel="noopener" aria-label="Al Saba Spices on YouTube">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.97C18.88 4 12 4 12 4s-6.88 0-8.59.45A2.78 2.78 0 001.47 6.42 29.58 29.58 0 001 12a29.58 29.58 0 00.47 5.58 2.78 2.78 0 001.95 1.97C5.12 20 12 20 12 20s6.88 0 8.59-.45a2.78 2.78 0 001.95-1.97A29.58 29.58 0 0023 12a29.58 29.58 0 00-.46-5.58z" stroke="currentColor" stroke-width="0" fill="currentColor"/>
          <path d="M10 15l5-3-5-3v6z" fill="#fff"/>
        </svg>
      </a>
      <a href="https://wa.me/<?= seo_e($site['whatsapp']) ?>" target="_blank" rel="noopener" aria-label="Chat with Al Saba Spices on WhatsApp">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M8.5 7.5c.3-.6 1.6-.6 1.9 0l.5 1c.2.4 0 .8-.3 1l-.5.4c.4.9 1.1 1.6 2 2l.4-.5c.2-.3.6-.5 1-.3l1 .5c.6.3.6 1.6 0 1.9-1.9 1-5-.6-6.5-4-.3-.7-.5-1.4-.5-2z" fill="currentColor"/>
        </svg>
      </a>
    </div>
  </footer>

  <script>
    const productCards = document.querySelectorAll('.product-grid > .product-card:not(.amazon-card)');
    const amazonButtons = document.querySelectorAll('.amazon-card .amazon-button');    const modal = document.getElementById('productModal');
    const modalImage = document.getElementById('modalProductImage');
    const modalName = document.getElementById('modalProductName');
    const modalDesc = document.getElementById('modalProductDesc');
    const modalDetail = document.getElementById('modalProductDetail');
    const modalPrice = document.getElementById('modalProductPrice');
    const modalCloseTargets = document.querySelectorAll('[data-close]');
    const modalBuyButton = document.getElementById('modalProductBuy');

    function openModal(card) {
      modalImage.src = card.dataset.image;
      modalImage.alt = card.dataset.name;
      modalName.textContent = card.dataset.name;
      modalDesc.textContent = card.dataset.desc;
      modalDetail.textContent = card.dataset.detail;
      modalPrice.textContent = card.dataset.price;
      modalBuyButton.href = card.dataset.buyUrl || '#products';
      modal.classList.add('product-modal-open');
      modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
      modal.classList.remove('product-modal-open');
      modal.setAttribute('aria-hidden', 'true');
    }

    productCards.forEach(card => {
      card.addEventListener('click', () => openModal(card));
      card.addEventListener('keydown', event => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          openModal(card);
        }
      });
    });

    amazonButtons.forEach(button => {
      button.addEventListener('click', event => {
        event.stopPropagation();
      });
    });

    document.querySelectorAll('.product-card-link').forEach(link => {
      link.addEventListener('click', event => {
        event.stopPropagation();
      });
    });

    modalCloseTargets.forEach(trigger => {
      trigger.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && modal.classList.contains('product-modal-open')) {
        closeModal();
      }
    });
  </script>
</body>
</html>
