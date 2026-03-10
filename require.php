<?php use function Helpers\htmlspecialchars12;

require_once "{$_SERVER['DOCUMENT_ROOT']}/require/createHead2.php";

function createTBody(array $jsonContent): string
{
    $result = array();
    $requiredArray = $jsonContent['required'];
    foreach ($jsonContent['properties'] as $key => $item) {
        $description = $item['description'];
        $description = htmlspecialchars12($description);
        $description = preg_replace_callback('/\\{(&quot;|\\{)(.+?)(&quot;|})}/s',
            fn($matches) => '<code class=sl>' . ($matches[1] === '&quot' ? '&quot' : '') .
                $matches[2] . ($matches[3] === '&quot' ? '&quot' : '') . '</code>',
            $description);
        $requirements = 'None';
        if (array_key_exists('pattern', $item)) {
            $pattern = htmlspecialchars12($item['pattern']);
            $requirements = "string MUST match <code>/$pattern/</code> json regxep";
        } elseif (array_key_exists('enum', $item)) {
            $requirements = 'one of <ul class=sl><li>' . implode('<li>',
                    array_map(fn($string) => '<code>' .
                        htmlspecialchars12($string) . '</code>',
                        $item['enum'])) . '</ul>';
        } elseif (array_key_exists('minimum', $item) || array_key_exists('maximum', $item)) {
            $requirements = (array_key_exists('minimum', $item) ?
                    "{$item['minimum']} &lt;= " : '') . "N" .
                (array_key_exists('maximum', $item) ?
                    " &lt;= {$item['maximum']}" : '');
            $requirements = "<code>$requirements</code>";
        }

        $required = (in_array($key, $requiredArray) ? 'True' : 'False');
        $result[] = "<tr><td><code>$key</code><td><code>{$item['type']}</code>"
            . "<td>$requirements<td><code>$required</code><td>$description";
    }
    return "<thead><tr><th scope=col>Field Name<th scope=col>Type<th scope=col>Field Requirements"
        . "<th scope=col>Required<th scope=col>Field Description</thead><tbody>\n" .
        implode("\n", $result) . "\n</tbody>";
}
