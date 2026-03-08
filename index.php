<?php
use function ANTHeader\ANTNavFavicond;
use function ANTHeader\ANTNavBinary;
use function ANTHeader\create_head2;
use function Helpers\htmlspecialchars12;

require_once "{$_SERVER['DOCUMENT_ROOT']}/require/createHead2.php";
create_head2($title = 'ANTRequest\'s Technical Specification', [
        'base' => '/standard/',
], [], [ANTNavFavicond('https://ANTRequest.nl', 'ANT\'s Gallery'),
        ANTNavBinary('https://ANTRequest.nl', $title, true)]);

$array = array();
foreach (glob("{$_SERVER['DOCUMENT_ROOT']}/standard/*/*/index.php") as $entry) {
    if (preg_match('/\\/([A-Za-z0-9_\\-]+)\\/(\\d+)\\.(\\d+)\\.(\\d+)\\/index\\.php$/D', $entry, $matches)) {
        if (!array_key_exists($matches[1], $array)) $array[$matches[1]] = array();
        $array[$matches[1]][] = array('major' => +$matches[2], 'minor' => +$matches[3],
                'patch' => +$matches[4], 'path' => $matches[1]);
    }
}
foreach ($array as $item) {
    usort($item, function ($x, $y) {
        /** @noinspection PhpLoopCanBeConvertedToArrayAnyInspection */
        foreach (['major', 'minor', 'patch'] as $key) {
            if ($x[$key] !== $y[$key]) return -($x <=> $y);
        }
        return +0;
    });
} ?>
<div class=divs>
    <h1>ANTRequest's Technical Specification</h1>
    <h2>Specifications</h2>
    <div><?= (function () use ($array) {
            $result = '';
            foreach ($array as $slug => $item) {
                $slugHTML = htmlspecialchars12($slug);
                $result .= "<h3 id=\"$slugHTML\">$slugHTML</h3><ul>";
                foreach ($item as $version) {
                    $versionHTML = "{$version['major']}.{$version['minor']}.{$version['patch']}";
                    $result .= "<li><a href=$slugHTML/$versionHTML/>$versionHTML</a>";
                }
                $result .= "</ul>";
            }
            return $result;
        })() ?></div>
    <!--<h2>Debug Data</h2><pre><code>&lt;?= htmlspecialchars12(json_fromArray(['$array' => $array])) ?></code></pre>-->
</div>
