<?php use ANTHeader\ANTNavArbitraryHTML;
use function ANTHeader\ANTNavFavicond;
use function ANTHeader\ANTNavBinary;
use function ANTHeader\create_head2;
use ANTHeader\ANTNavLinkTag;
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
$GLOBALS['mtime'] = gmdate('Y-m-d\\TH:i:s\\Z',
    filemtime(__DIR__ . "/{$GLOBALS['spec']}/$major.$minor.$patch/index.php"));
$GLOBALS['warningLevel'] = null;
$GLOBALS['warningContent'] = null;
$matches = json_decode(file_get_contents(__DIR__ . '/lastModified.json'), true)['metadata'];

if ($matches[$GLOBALS['spec']]) {
    if ($matches[$GLOBALS['spec']]["$major.$minor.$patch"]) {
        if ($matches[$GLOBALS['spec']]["$major.$minor.$patch"]['lastModified']) {
            $GLOBALS['mtime'] = $matches[$GLOBALS['spec']]["$major.$minor.$patch"]['lastModified'];
        }
        $array = $matches[$GLOBALS['spec']]["$major.$minor.$patch"];
        $warnArray = $array['warning'];
        if ($warnArray['warningLevel']) {
            $GLOBALS['warningLevel'] = match ($warnArray['warningLevel']) {
                'warning' => 'warning',
                'danger' => 'danger',
                'info' => 'info',
                default => null,
            };
        }
        if ($warnArray['warningContent']) {
            $GLOBALS['warningContent'] = $warnArray['warningContent'];
        }
        if (empty($warnArray['warningLevel']) || empty($warnArray['warningContent'])) {
            $GLOBALS['warningLevel'] = null;
            $GLOBALS['warningContent'] = null;
        }
    }
}
$GLOBALS['htMTime'] = gmdate('D M d Y', strtotime($GLOBALS['mtime']));
$GLOBALS['applicableWarning'] = '';
if (!empty($GLOBALS['warningLevel'])) {
    $GLOBALS['applicableWarning'] = "<div class='warnbox {$GLOBALS['warningLevel']}'><p><strong>{$GLOBALS['warningLevel']}!</strong> {$GLOBALS['warningContent']}</div>";
}
create_head2($title = "ANTRequest's $spec Specification (Version $major.$minor.$patch)", [
    'base' => "/standard/$spec/$major.$minor.$patch/",
], [new ANTNavLinkTag('stylesheet', ['/standard/layerzip.css', '/standard/w3sWarnings.css']),
    new ANTNavScript('/standard/variableSelection.js', true),
    new ANTNavArbitraryHTML('classstyle', '<style id=varstyle></style>'),
], [ANTNavFavicond('https://ANTRequest.nl', 'ANT\'s Gallery'),
    ANTNavBinary('https://ANTRequest.nl/standard/', 'ANTRequest\'s Technical Specifications'),
    ANTNavBinary(".", $title, true)]);
$GLOBALS['title'] = $title;
require_once __DIR__ . '/HeaderAuto.php';
function htmlEncodeMinimal(string $value): string
{
    $html = str_replace('<', '&lt;',
        str_replace('&', '&amp;',
            "$value"));
    return ($html);
}

function base64UrlEncode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function insert_wbr(string $string, int $every = 20, $seperator = '<wbr>'): string
{
    return implode($seperator, str_split($string, $every));
}

function underscoreNumber(int|float $number)
{
    // If it's a float, we decide how many decimals to keep (e.g., 2)
    // If it's an int, we keep 0.
    $decimals = is_float($number) ? 2 : 0;
    return number_format($number, $decimals, '.', '_');
}

function base58_encode(string $bytes): string
{
    $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
    if ($bytes === '') return '';

    $data = array_values(unpack('C*', $bytes));
    // Count leading zero bytes
    $zeroCount = 0;
    while ($zeroCount < count($data) && $data[$zeroCount] === 0) {
        $zeroCount++;
    }
    $result = '';
    while (count($data) > 0) {
        $carry = 0;
        $next = [];
        foreach ($data as $byte) {
            $carry = ($carry << 8) + $byte; // multiply by 256 and add byte
            $digit = intdiv($carry, 58);
            $carry %= 58;
            if (count($next) > 0 || $digit !== 0) {
                $next[] = $digit;
            }
        }
        $result = $alphabet[$carry] . $result;
        $data = $next;
    }
    return str_repeat('1', $zeroCount) . $result;
}