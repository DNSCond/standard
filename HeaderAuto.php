<?php

use function Helpers\htmlspecialchars12;

if (!function_exists('array_last')) {
    /**
     * Gets the last value of an array without modifying the pointer.
     *
     * @param array $array
     * @return mixed|null Returns null if array is empty.
     */
    function array_last(array $array): mixed
    {
        if (empty($array)) {
            return null;
        }

        // array_key_last was introduced in PHP 7.3
        return $array[array_key_last($array)];
    }
}

class ReferenceArray implements JsonSerializable
{
    private(set) public array $array;
    private(set) public ?ReferenceArray $upsteam_array;

    public function __construct(array $array = array(), ?ReferenceArray $upsteam_array = null)
    {
        $this->array = $array;
        $this->upsteam_array = $upsteam_array;
    }

    public function add($object): self
    {
        $this->array[] = $object;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->array;
    }
}

// ... (rest of your code remains the same)

class HeaderAuto
{
    private array $array;
    private string $tagName;

    public function __construct(array $array, string $tagName)
    {
        $this->array = $array;
        $this->tagName = $tagName;
    }

    public function callRecursive(array $array, bool $topLevel): string
    {
        $tagName = $this->tagName;
        $result = $topLevel ? "<$tagName class='TableOfContents sl'>" : "<$tagName>";

        foreach ($array as $item) {
            // If the item is an array, it's a sub-menu
            if (is_array($item)) {
                // If the sub-menu is the first item, we might need a dummy text or handle it differently
                // Based on your logic, you might want to adjust this based on how ReferenceArray builds the structure
                $result .= $this->callRecursive($item, false);
            } else {
                // If it's a string (the link), add it
                $result .= "<li>$item";
            }
        }

        return "$result</$tagName>";
    }

    public function __toString(): string
    {
        return $this->callRecursive($this->array, true);
    }
}

ob_start(function (string $string): string {
    if (preg_match_all('/<h([1-6]) id=([^\s>]+|"[^"]+")>(.+?)<\\/h\\1/s',
        $string, $matches, PREG_SET_ORDER)) {
        // Initialize structure
        /** @noinspection HtmlUnknownAnchorTarget */
        $rootArray = new ReferenceArray(array());
        $currentArray = $rootArray;
        $currentIndentation = 1;

        foreach ($matches as $match) {
            $result = "<a href=#$match[2]>$match[3]</a>";
            $indentation = (int)$match[1];

            // Note: This logic assumes input headers strictly increase/decrease by 1 level
            if ($indentation > $currentIndentation) {
                // Create a new sub-array
                $newSub = new ReferenceArray([$result], $currentArray);
                $currentArray->add($newSub);
                $currentArray = $newSub;
                $currentIndentation = $indentation;
            } elseif ($indentation === $currentIndentation) {
                // Add to current level
                $currentArray->add($result);
            } else {
                // Move up in hierarchy
                while ($indentation < $currentIndentation && $currentArray->upsteam_array !== null) {
                    $currentArray = $currentArray->upsteam_array;
                    $currentIndentation--;
                }
                $currentArray->add($result);
            }
        }

        // Convert to array for rendering
        $finalArray = json_decode(json_encode($rootArray,
            JSON_UNESCAPED_SLASHES), true);
        $replace = new HeaderAuto($finalArray, 'ol');
        $replace = preg_replace('/\\s+/', ' ', $replace);
        return str_replace('<!-- Favicond-render-TableOfContents -->', "<nav>$replace</nav>", $string);
    } else {
        return $string;
    }
});