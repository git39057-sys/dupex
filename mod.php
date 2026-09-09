<?php
require __DIR__ . '/admin/helpers.php';

$slug = isset($_GET['m']) ? preg_replace(SLUG_RE, '', $_GET['m']) : '';
$mod = loadMod($slug);
if (!$mod) {
    http_response_code(404);
    $site = siteConfig();
    $brand = $site['brand'];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>not found — <?php echo htmlspecialchars($brand); ?></title>
<meta name="robots" content="noindex, follow">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main style="text-align:center; padding-top:120px;">
  <h1 class="hero-title" style="font-size:32px;">404</h1>
  <p class="hero-tag">that mod is gone, never existed, or you typo'd it.</p>
  <a class="dl-btn" style="max-width:260px; margin:0 auto;" href="/">BACK TO THE MODS</a>
</main>
</body>
</html>
    <?php
    exit;
}

$site = siteConfig();
$brand = $site['brand'];
sortFilesNewestFirst($mod['files']);
$latest = $mod['files'][0];

$title = 'Download ' . $mod['name'] . ' Mod for Minecraft ' . implode(', ', $latest['mc_versions']);
$desc = strip_tags($mod['description']);

$itemTheme = ['dirt', 'diamond', 'netherite', 'gold', 'redstone', 'emerald'];
$icon = iconExists($mod['icon']) ? $mod['icon'] : 'diamond';
$tickerItems = [
    [$icon, strtoupper($mod['name'])],
    ['diamond', 'DIAMOND'],
    ['gold', 'GOLD INGOT'],
    ['redstone', 'REDSTONE'],
    ['netherite', 'NETHERITE'],
    ['emerald', 'EMERALD'],
    ['dirt', 'DIRT'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($title); ?></title>
<meta name="description" content="<?php echo htmlspecialchars(mb_substr($desc, 0, 155)); ?>">
<meta name="keywords" content="minecraft, <?php echo htmlspecialchars(implode(', ', $mod['tags'] ?? ['mod'])); ?>, mod, download, <?php echo htmlspecialchars($mod['modloader']); ?>">
<link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/mod.php?m=<?php echo urlencode($mod['slug']); ?>">
<meta name="robots" content="index, follow">
<meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars(mb_substr($desc, 0, 155)); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/mod.php?m=<?php echo urlencode($mod['slug']); ?>">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><rect width='16' height='16' fill='%235c9d44'/></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "<?php echo htmlspecialchars($mod['name'], ENT_QUOTES); ?>",
  "description": "<?php echo htmlspecialchars($desc, ENT_QUOTES); ?>",
  "operatingSystem": "Java",
  "applicationCategory": "GameApplication",
  "softwareVersion": "<?php echo htmlspecialchars($latest['version'], ENT_QUOTES); ?>",
  "downloadUrl": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/download.php?m=<?php echo urlencode($mod['slug']); ?>",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" }
}
</script>
</head>
<body data-mod="<?php echo htmlspecialchars($mod['name']); ?>">

<header>
  <div class="topbar">
    <a class="logo" href="/"><span class="cube-mini"></span><?php echo htmlspecialchars($brand); ?></a>
    <nav>
      <a href="/">Mods</a>
      <a href="#download">Download</a>
      <a href="#features">Features</a>
      <a href="#versions">Versions</a>
      <a href="#faq">FAQ</a>
    </nav>
  </div>
</header>

<div id="builds" style="display:none"><?php echo htmlspecialchars(json_encode($mod['files'])); ?></div>

<main>
  <section class="hero">
    <span class="hero-kicker"><?php echo htmlspecialchars($mod['modloader']); ?> Mod</span>
    <h1 class="hero-title"><?php echo htmlspecialchars($mod['name']); ?><br><span class="accent">Download</span></h1>
    <p class="hero-tag"><?php echo htmlspecialchars($mod['tagline']); ?></p>

    <div class="badges">
      <span class="badge"><span class="dot"></span>v<?php echo htmlspecialchars($latest['version']); ?></span>
      <span class="badge gold"><span class="dot"></span><?php echo htmlspecialchars($mod['modloader']); ?></span>
      <span class="badge blue"><span class="dot"></span><?php echo count($latest['mc_versions']); ?> MC versions</span>
      <span class="badge gray"><span class="dot"></span><?php echo number_format($mod['downloads']); ?> downloads</span>
    </div>

    <div class="download-box" id="download">
      <div class="dl-top">
        <span class="dl-file" id="dl-file-label"><?php echo htmlspecialchars($latest['file']); ?></span>
        <span class="dl-size" id="dl-size-label"><?php echo htmlspecialchars($latest['size_mb']); ?> MB</span>
      </div>

      <div class="dl-ver">
        <label for="dl-version">Pick a build</label>
        <select id="dl-version">
          <?php foreach ($mod['files'] as $i => $f): ?>
            <option value="<?php echo htmlspecialchars($f['version']); ?>" <?php echo $i === 0 ? 'selected' : ''; ?>>
              v<?php echo htmlspecialchars($f['version']); ?> — MC <?php echo htmlspecialchars(implode(' / ', $f['mc_versions'])); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <a class="dl-btn" id="dl-link" href="download.php?m=<?php echo urlencode($mod['slug']); ?>&v=<?php echo urlencode($latest['version']); ?>">
        <span>DOWNLOAD MOD</span><span class="arrow">⇩</span>
      </a>
      <p class="dl-note">drop the jar into your mods folder. needs <?php echo htmlspecialchars($mod['modloader']); ?> Loader + API.</p>

      <div class="loaders">
        <span class="loader-chip"><b><?php echo htmlspecialchars($mod['modloader']); ?></b> Loader</span>
        <span class="loader-chip"><b><?php echo htmlspecialchars($mod['modloader']); ?></b> API</span>
        <span class="loader-chip"><b><?php echo htmlspecialchars(mcList($latest)); ?></b></span>
      </div>
    </div>
  </section>
</main>

<div class="ticker">
  <div class="ticker-track">
    <?php foreach ($tickerItems as $ti): ?>
      <span class="ticker-item">
        <span class="cube <?php echo $ti[0]; ?>"><i class="top"></i><i class="left"></i><i class="right"></i></span>
        <?php echo $ti[1]; ?>
      </span>
    <?php endforeach; ?>
  </div>
</div>

<main>
  <section id="features">
    <div class="section-head reveal">
      <h2>FEATURES</h2>
      <p>what you actually get when you stop and read the page</p>
    </div>
    <div class="feature-grid">
      <?php foreach ($mod['features'] as $i => $f): ?>
        <div class="feature-card reveal">
          <span class="crown"></span>
          <span class="cube <?php echo $itemTheme[$i % count($itemTheme)]; ?>"><i class="top"></i><i class="left"></i><i class="right"></i></span>
          <h3><?php echo htmlspecialchars($f); ?></h3>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section>
    <div class="section-head reveal">
      <h2>IN THE JAR</h2>
    </div>
    <div class="panel reveal">
      <p><?php echo nl2br(htmlspecialchars($mod['description'])); ?></p>
    </div>
  </section>

  <section id="versions">
    <div class="section-head reveal">
      <h2>VERSION HISTORY</h2>
      <p>every build, kept around. old saves stay happy.</p>
    </div>
    <div class="ver-table">
      <?php foreach ($mod['files'] as $i => $f): ?>
        <div class="ver-row reveal">
          <span class="ver-tag <?php echo $i === 0 ? 'latest' : ''; ?>"><?php echo $i === 0 ? 'LATEST · ' : ''; ?>v<?php echo htmlspecialchars($f['version']); ?></span>
          <div class="ver-mcs">
            <?php foreach ($f['mc_versions'] as $mc): ?>
              <span>MC <?php echo htmlspecialchars($mc); ?></span>
            <?php endforeach; ?>
          </div>
          <div class="ver-meta">
            <b><?php echo htmlspecialchars($f['file']); ?></b>
            <?php echo htmlspecialchars($f['size_mb']); ?> MB · <?php echo htmlspecialchars($f['date']); ?>
          </div>
          <?php if (!empty($f['changelog'])): ?>
            <div class="ver-change"><?php echo htmlspecialchars(implode(' / ', array_reverse($f['changelog']))); ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="faq">
    <div class="section-head reveal">
      <h2>FAQ</h2>
      <p>the questions everyone asks in the discord, answered</p>
    </div>
    <div class="faq reveal">
      <div class="faq-item">
        <button class="faq-q">Does it work on a server? <span class="plus">+</span></button>
        <div class="faq-a"><div>Depends on the mod. Any op-limited feature needs operator permissions. Singleplayer always works out of the box.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">Will it crash my world? <span class="plus">+</span></button>
        <div class="faq-a"><div>No entity spam, no chunk corruption, no network calls. Back up your world before big experiments anyway — that is just good habits.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">Is it a virus? <span class="plus">+</span></button>
        <div class="faq-a"><div>No. Every jar is tiny and open for inspection. Some antivirus programs flag any mod because of obfuscated class names — grab it from this page and verify the hash if you care.</div></div>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="foot-cube">
    <span class="cube <?php echo $icon; ?>"><i class="top"></i><i class="left"></i><i class="right"></i></span>
  </div>
  <p>hand made, no bloat. <?php echo htmlspecialchars($mod['name']); ?> is a fan mod by <?php echo htmlspecialchars($brand); ?>, not affiliated with Mojang or Microsoft.</p>
  <p class="small">v<?php echo htmlspecialchars($latest['version']); ?> · last release <?php echo htmlspecialchars($latest['date']); ?></p>
  <p class="small"><a class="admin-link" href="admin/">admin</a></p>
</footer>

<script src="assets/script.js"></script>
</body>
</html>