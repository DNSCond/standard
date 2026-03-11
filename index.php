<?php use ANTHeader\ANTNavIStyle;
use function ANTHeader\ANTNavFavicond;
use function Helpers\htmlspecialchars12;
use function ANTHeader\ANTNavBinary;
use function ANTHeader\create_head2;
use ANTHeader\ANTNavLinkTag;

require_once "{$_SERVER['DOCUMENT_ROOT']}/require/createHead2.php";
create_head2($title = 'ANTRequest\'s Technical Specification', [
        'base' => '/standard/',
], [new ANTNavLinkTag('stylesheet', '/gallery/ddDL-table.css'),
        new ANTNavLinkTag('stylesheet', ['/standard/layerzip.css', '/standard/w3sWarnings.css']),
        new ANTNavIStyle('.warnbox{margin-bottom:0}.table{overflow-x:scroll;margin-top:1em}'),
], [ANTNavFavicond('https://ANTRequest.nl', 'ANT\'s Gallery'),
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
            $matches = json_decode(file_get_contents(__DIR__ . '/lastModified.json'), true)['metadata'];
            foreach ($array as $slug => $item) {
                $slugHTML = htmlspecialchars12($slug);
                $result .= "<h3 id=\"$slugHTML\">$slugHTML</h3><div class=table><table><thead><tr><th scope=col>Version" .
                        "<th scope=col>Last Modified<th scope=col>Warnings<th scope=col>Alternate Downloads</thead><tbody>";

                foreach ($item as $version) {
                    $versionHTML = "{$version['major']}.{$version['minor']}.{$version['patch']}";
                    $major = $version['major'];
                    $minor = $version['minor'];
                    $patch = $version['patch'];
                    $mtime = gmdate('Y-m-d\\TH:i:s\\Z',
                            filemtime(__DIR__ . "/$slug/$major.$minor.$patch/index.php"));
                    $warning = array();
                    if ($matches[$slug]) {
                        if ($matches[$slug]["$major.$minor.$patch"]) {
                            if ($matches[$slug]["$major.$minor.$patch"]['lastModified']) {
                                $mtime = $matches[$slug]["$major.$minor.$patch"]['lastModified'];
                            }
                            $array = $matches[$slug]["$major.$minor.$patch"];
                            $warnArray = $array['warning'];
                            if ($warnArray['warningLevel']) {
                                $warning['warningLevel'] = match ($warnArray['warningLevel']) {
                                    'warning' => 'warning',
                                    'danger' => 'danger',
                                    'info' => 'info',
                                    default => null,
                                };
                            }
                            if ($warnArray['warningContent']) {
                                $warning['warningContent'] = $warnArray['warningContent'];
                            }
                            if (empty($warnArray['warningLevel']) || empty($warnArray['warningContent'])) {
                                $warning['warningLevel'] = null;
                                $warning['warningContent'] = null;
                            }
                        }
                    }
                    $htMTime = gmdate('D M d Y', strtotime($mtime));
                    $htmlTimeTag = "<time datetime=$mtime data-tolocaltime=date-only>$htMTime</time>";
                    if (!empty($warning['warningLevel'])) {
                        $warningHTML = "<div class='warnbox {$warning['warningLevel']}'><p><strong>{$warning['warningLevel']}!</strong> {$warning['warningContent']}</div>";
                    } else {
                        $warningHTML = '<p>No applicableWarning';
                    }
                    $result .= "<tr><td><a href=$slugHTML/$versionHTML/>$versionHTML</a><td>$htmlTimeTag<td>$warningHTML";
                    $result .= "<td><a download=$slugHTML-$versionHTML.txt href=$slugHTML/$versionHTML/>$versionHTML</a>";
                }
                $result .= "</tbody></table></div>";
            }
            return $result;
        })() ?></div>
    <!--<div><?= (function () use ($array) {
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
    })() ?></div>-->
    <!--<h2>Debug Data</h2><pre><code>&lt;?= htmlspecialchars12(json_fromArray(['$array' => $array])) ?></code></pre>-->
</div>
