<?php
require __DIR__ . '/admin/helpers.php';

$slug = isset($_GET['m']) ? preg_replace(SLUG_RE, '', $_GET['m']) : '';
$mod = loadMod($slug);
if (!$mod) {
    http_response_code(404);
    exit('Mod not found.');
}

$want = $_GET['v'] ?? null;
$pick = null;
foreach ($mod['files'] as $f) {
    if ($f['version'] === $want) {
        $pick = $f;
        break;
    }
}
if ($pick === null) {
    sortFilesNewestFirst($mod['files']);
    $pick = $mod['files'][0];
}

if (empty($pick['file']) || !file_exists(downloadsDir($slug) . '/' . $pick['file'])) {
    http_response_code(404);
    exit('File not found. Check back after an update.');
}

$mod['downloads'] = (int)$mod['downloads'] + 1;
saveMod($mod);

$path = downloadsDir($slug) . '/' . $pick['file'];
header('Content-Type: application/java-archive');
header('Content-Disposition: attachment; filename="' . basename($pick['file']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;