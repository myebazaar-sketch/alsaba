<?php
declare(strict_types=1);

require __DIR__ . '/data/articles.php';
require __DIR__ . '/includes/seo.php';

$slug = null;
if (isset($_GET['slug']) && $_GET['slug'] !== '') {
    $slug = filter_var($_GET['slug'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
} elseif (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $pathSlug = trim($_SERVER['PATH_INFO'], '/');
    if ($pathSlug !== '') {
        $slug = filter_var($pathSlug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
}
$article = null;
foreach ($articles as $item) {
    if ($item['slug'] === $slug) {
        $article = $item;
        break;
    }
}

if (!$article) {
    http_response_code(404);
    $site = seo_site();
    ?>
<!doctype html>
<html lang="en">
<head>
<?php
    seo_head([
        'title'       => 'Article not found | ' . $site['name'],
        'description' => 'The requested article is not available.',
        'robots'      => 'noindex, follow',
    ]);
    seo_analytics();
?>
</head>
<body>
  <main class="section" style="text-align:center; padding-top:5rem;">
    <h1>Article not found</h1>
    <p>The requested article is not available.</p>
    <p><a class="btn" href="/">Return home</a></p>
  </main>
</body>
</html>
<?php
    exit;
}

function renderArticleContent(string $content): string
{
    $allowedTags = ['h1', 'h2', 'h3', 'h4', 'p', 'strong', 'em', 'ul', 'ol', 'li', 'br', 'a', 'img', 'iframe'];
    $allowedAttributes = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title'],
        'iframe' => ['src', 'title', 'allow', 'allowfullscreen', 'loading', 'class'],
    ];

    $html = '<div>' . $content . '</div>';
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8"?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $sanitizeNode = function (DOMNode $node) use (&$sanitizeNode, $doc, $allowedTags, $allowedAttributes) {
        if ($node instanceof DOMText) {
            return $doc->createTextNode($node->nodeValue);
        }

        if (!($node instanceof DOMElement)) {
            return $doc->createTextNode($node->textContent);
        }

        $tag = strtolower($node->tagName);
        if (!in_array($tag, $allowedTags, true)) {
            $fragment = $doc->createDocumentFragment();
            foreach ($node->childNodes as $child) {
                $fragment->appendChild($sanitizeNode($child));
            }
            return $fragment;
        }

        $element = $doc->createElement($tag);
        if (isset($allowedAttributes[$tag])) {
            foreach ($allowedAttributes[$tag] as $attrName) {
                if (!$node->hasAttribute($attrName)) {
                    continue;
                }

                $attrValue = $node->getAttribute($attrName);
                if ($attrValue === '') {
                    continue;
                }

                if (in_array($attrName, ['href', 'src'], true)) {
                    $cleanValue = trim($attrValue);
                    if (preg_match('/^\s*javascript:/i', $cleanValue)) {
                        continue;
                    }

                    if ($tag === 'iframe' && $attrName === 'src') {
                        $parsed = parse_url($cleanValue);
                        $host = $parsed['host'] ?? '';
                        $path = $parsed['path'] ?? '';
                        $allowedHosts = ['www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'youtube-nocookie.com'];

                        if ($parsed['scheme'] !== 'https' || !in_array($host, $allowedHosts, true) || !str_starts_with($path, '/embed/')) {
                            continue;
                        }
                    }

                    if ($attrName === 'href' && preg_match('/^\s*mailto:/i', $cleanValue) === 0 && parse_url($cleanValue, PHP_URL_SCHEME) !== null) {
                        $element->setAttribute('rel', 'noreferrer noopener');
                        $element->setAttribute('target', '_blank');
                    }

                    $element->setAttribute($attrName, $cleanValue);
                    continue;
                }

                $element->setAttribute($attrName, $attrValue);
            }
        }

        foreach ($node->childNodes as $child) {
            $element->appendChild($sanitizeNode($child));
        }

        return $element;
    };

    $wrapper = $doc->getElementsByTagName('div')->item(0);
    $cleanWrapper = $doc->createElement('div');
    foreach ($wrapper->childNodes as $child) {
        $cleanWrapper->appendChild($sanitizeNode($child));
    }

    $result = $doc->saveHTML($cleanWrapper);
    return preg_replace('~^<div>(.*)</div>$~s', '$1', $result) ?? '';
}
$site = seo_site();
$canonicalPath = '/article/' . rawurlencode((string) $article['slug']);
?>
<!doctype html>
<html lang="en">
<head>
<?php
seo_head([
    'title'       => $article['title'] . ' – ' . $site['name'],
    'description' => $article['text'],
    'canonical'   => $canonicalPath,
    'type'        => 'article',
    'image'       => $article['image'],
    'image_alt'   => $article['title'],
    'keywords'    => ['Indian spices', 'recipes', $site['name']],
]);

seo_json(seo_graph([
    seo_organization_node(),
    seo_website_node(),
    seo_breadcrumb_node([
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Recipes & Blog', 'url' => '/blog'],
        ['name' => $article['title'], 'url' => $canonicalPath],
    ]),
    [
        '@type'            => 'Article',
        '@id'              => seo_abs($canonicalPath) . '#article',
        'headline'         => $article['title'],
        'description'      => $article['text'],
        'image'            => [seo_abs($article['image'])],
        'author'           => ['@type' => 'Organization', 'name' => $site['name']],
        'publisher'        => ['@id' => seo_base_url() . '/#organization'],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => seo_abs($canonicalPath)],
        'inLanguage'       => 'en',
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
      <a href="/">Home</a>
      <a href="/blog">Recipes &amp; Blog</a>
      <a href="/faq">FAQ</a>
    </div>
  </nav>

  <nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol>
      <li><a href="/">Home</a></li>
      <li><a href="/blog">Recipes &amp; Blog</a></li>
      <li aria-current="page"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></li>
    </ol>
  </nav>

  <main class="section" style="padding-top: 2rem; max-width: 840px; margin: 0 auto;">
    <article class="article-card" style="width: 100%; min-width: auto; height: auto;">
      <img loading="lazy" decoding="async" src="<?= htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>">
      <h1><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h1>
      <div class="article-body" style="margin-top: 1rem; color: var(--ink); line-height: 1.9;">
        <?php
        $articleContent = $article['content'];
        if (isset($article['video'])) {
            $articleContent .= '<iframe src="' . htmlspecialchars($article['video'], ENT_QUOTES, 'UTF-8')
                . '" title="Al Saba Spices chicken powder Short" loading="lazy"'
                . ' allowfullscreen="allowfullscreen" class="blog-short-video"></iframe>';
        }
        ?>
        <?= renderArticleContent($articleContent) ?>
      </div>
      <p style="margin-top: 1.5rem;"><a class="btn" href="/">Back to home</a></p>
    </article>
  </main>
</body>
</html>
