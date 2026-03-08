<?php use ANTHeader\ANTNavArbitraryHTML;
use ANTHeader\ANTNavLinkTag;
use function ANTHeader\ANTNavBinary;
use function ANTHeader\ANTNavFavicond;
use function ANTHeader\create_head2;
use ANTHeader\ANTNavScript;

require_once "{$_SERVER['DOCUMENT_ROOT']}/require/createHead2.php";
$spec = '';
$major = 0;
$minor = 0;
$patch = 0;
if (preg_match('/\\/([A-Za-z0-9_\\-]+)\\/(\\d+)\\.(\\d+)\\.(\\d+)\\D?$/iD',
    str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])),
    $matches)) {
    $spec = $matches[1];
    $major = $matches[2];
    $minor = $matches[3];
    $patch = $matches[4];
}
$GLOBALS['spec'] = ($spec);
$GLOBALS['major'] = $major;
$GLOBALS['minor'] = $minor;
$GLOBALS['patch'] = $patch;
create_head2($title = "ANTRequest's $spec Specification (Version $major.$minor.$patch)", [
    'base' => "/standard/$spec/$major.$minor.$patch/",
], [new ANTNavLinkTag('stylesheet', ['/standard/layerzip.css', '/standard/w3sWarnings.css']),
    new ANTNavScript('/standard/variableSelection.js', true),
    new ANTNavArbitraryHTML('classstyle','<style id=varstyle></style>'),
], [ANTNavFavicond('https://ANTRequest.nl', 'ANT\'s Gallery'),
    ANTNavBinary('https://ANTRequest.nl/standard/', 'ANTRequest\'s Technical Specifications'),
    ANTNavBinary(".", $title, true)]);
$GLOBALS['title'] = $title;
require_once __DIR__ . '/HeaderAuto.php';
