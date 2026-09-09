<?php
require __DIR__ . '/admin/helpers.php';

$site = siteConfig();
$slugs = listModFiles();

$mods = array();
foreach ($slugs as $slug) {
    $m = loadMod($slug);
    if ($m) {
        if (!empty($m['files'])) {
            sortFilesNewestFirst($m['files']);
            $m['latest'] = $m['files'][0];
        }
        $mods[] = $m;
    }
}

usort($mods, function ($a, $b) {
    $da = !empty($a['latest']) ? $a['latest']['date'] : '0000';
    $db = !empty($b['latest']) ? $b['latest']['date'] : '0000';
    return strcmp($db, $da);
});

$totalDownloads = 0;
foreach ($mods as $m) { $totalDownloads += (int)$m['downloads']; }

$brand = $site['brand'];
$title = $brand . ' — ' . $site['tagline'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($title); ?></title>
<meta name="description" content="<?php echo htmlspecialchars(mb_substr($site['tagline'], 0, 155)); ?>">
<meta name="keywords" content="minecraft, mods, fabric, forge, download, <?php echo htmlspecialchars($brand); ?>">
<link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/">
<meta name="robots" content="index, follow">
<meta property="og:title" content="<?php echo htmlspecialchars($brand); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($site['tagline']); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><rect width='16' height='16' fill='%235c9d44'/></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body data-mod="<?php echo htmlspecialchars($brand); ?>">

<header>
  <div class="topbar">
    <a class="logo" href="/"><span class="cube-mini"></span><?php echo htmlspecialchars($brand); ?></a>
    <nav>
      <a href="#mods">Mods</a>
      <a href="<?php echo htmlspecialchars($site['discord']); ?>" target="_blank" rel="noopener">Discord</a>
    </nav>
  </div>
</header>

<main>
  <section class="hero">
    <span class="hero-kicker">Minecraft Mods</span>
    <h1 class="hero-title"><?php echo htmlspecialchars($brand); ?></h1>
    <p class="hero-tag"><?php echo htmlspecialchars($site['tagline']); ?></p>

    <div class="badges">
      <span class="badge"><span class="dot"></span><?php echo count($mods); ?> mods</span>
      <span class="badge gray"><span class="dot"></span><?php echo number_format($totalDownloads); ?> downloads</span>
    </div>
  </section>
</main>

<div class="ticker">
  <div class="ticker-track">
    <span class="ticker-item">
      <span class="cube dirt"><i class="top"></i><i class="left"></i><i class="right"></i></span>
      NO BLOAT
    </span>
    <span class="ticker-item">
      <span class="cube diamond"><i class="top"></i><i class="left"></i><i class="right"></i></span>
      CLIENT SIDE
    </span>
    <span class="ticker-item">
      <span class="cube gold"><i class="top"></i><i class="left"></i><i class="right"></i></span>
      OPEN JARS
    </span>
    <span class="ticker-item">
      <span class="cube redstone"><i class="top"></i><i class="left"></i><i class="right"></i></span>
      NO PHONE HOME
    </span>
    <span class="ticker-item">
      <span class="cube netherite"><i class="top"></i><i class="left"></i><i class="right"></i></span>
      HAND BUILT
    </span>
    <span class="ticker-item">
      <span class="cube emerald"><i class="top"></i><i class="left"></i><i class="right"></i></span>
      FREE FOREVER
    </span>
  </div>
</div>

<main>
  <section id="mods">
    <div class="section-head reveal">
      <h2>THE MODS</h2>
      <p>pick one, read nothing, press the green button</p>
    </div>

    <?php if (!$mods): ?>
      <div class="panel reveal">
        <p>no mods here yet. check back soon.</p>
      </div>
    <?php endif; ?>

    <div class="mod-grid">
      <?php foreach ($mods as $m): ?>
        <a class="mod-card reveal" href="mod.php?m=<?php echo urlencode($m['slug']); ?>">
          <span class="cube <?php echo iconExists($m['icon']) ? $m['icon'] : 'diamond'; ?>">
            <i class="top"></i><i class="left"></i><i class="right"></i>
          </span>
          <div class="mod-info">
            <div class="mod-name">
              <h3><?php echo htmlspecialchars($m['name']); ?></h3>
              <span class="badge mini"><span class="dot"></span>
                <?php echo htmlspecialchars($m['modloader']); ?>
              </span>
            </div>
            <p class="mod-tag"><?php echo htmlspecialchars($m['tagline']); ?></p>
            <div class="mod-meta">
              <span class="chip">v<?php echo htmlspecialchars($m['latest']['version']); ?></span>
              <span class="chip">MC <?php echo htmlspecialchars(implode(' · MC ', $m['latest']['mc_versions'])); ?></span>
              <span class="chip"><?php echo number_format($m['downloads']); ?> downloads</span>
            </div>
          </div>
          <span class="mod-arrow">→</span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<footer>
  <div class="foot-cube">
    <span class="cube dirt"><i class="top"></i><i class="left"></i><i class="right"></i></span>
  </div>
  <p>hand made, no bloat. <?php echo htmlspecialchars($brand); ?> mods are fan mods, not affiliated with Mojang or Microsoft.</p>
  <p class="small"><a class="admin-link" href="admin/">admin</a></p>
</footer>

<script src="assets/script.js"></script>
</body>
</html>