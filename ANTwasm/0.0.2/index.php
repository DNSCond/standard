<?php

require_once '../../createHeader.php' ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <div><?= $GLOBALS['applicableWarning'] ?></div>
    <p><dfn id=ANTwasm>ANT.wasm</dfn> is a way to make games in WebAssembly and have it run in the ANTRequest
        Ecosystem. The guiding principle is simple: <strong>it either runs in WebAssembly, or it doesnt run at
            all</strong>.
    <p>to use <a href=#ANTwasm>ANT.wasm</a> you can use This Specification to insure interoperability. If you do not
        Like where this is going please fork.
    <h2 id=TableOfContents>Table Of Contents</h2>
    <details open>
        <summary>Table Of Contents</summary>
        <!-- Favicond-render-TableOfContents -->
    </details>
    <h2 id=documentStatus>Status of this document</h2>
    <p>this document written on
        <time datetime="<?= $GLOBALS['mtime'] ?>" data-tolocaltime=date-only><?= $GLOBALS['htMTime'] ?></time>
        is <a href=https://semver.org/>Semantic version</a>
        <span><?= "{$GLOBALS['major']}.{$GLOBALS['minor']}.{$GLOBALS['patch']}" ?></span>.
        this document is self-published independently.
    <p>i am still Figuring things out. this is not final design and might be vague, impossible, or otherwise not
        implementable. im still seeking to bring this closer to my vision.
    <h2 id=internal>This Specification Depends On</h2>
    <ul><?= (function (array $array): string {
            $result = '';
            foreach ($array as $item) {
                if (preg_match('/^([A-Za-z0-9_\\-]+)\\/(\\d+)\\.(\\d+)\\.(\\d+)$/D',
                        $item, $matches)) {
                    [, $name, $major, $minor, $patch] = $matches;
                    $result .= "<li><a href=\"/standard/$item\">$name: ($major.$minor.$patch)</a>";
                }
            }
            return $result;
        })(['Foundation/0.4.0', 'ANTzip/0.1.1']);
        /** @noinspection HtmlUnknownAnchorTarget */
        $errorCodes = [
                '0' => [
                        'name' => 'GENERIC_SUCCESS',
                        'desc' => 'An Operation completed without any error.',
                ],
                '-1' => [
                        'name' => 'GENERIC_FAILURE',
                        'desc' => 'A Generic Failure.',
                ],
                '-2' => [
                        'name' => 'PATH_RESOLUTION_ERROR',
                        'desc' => <<<HTML
                        A path cant be resolved due to any reason, including bt not limited to
                        <ul>
                            <li>a path contains a <a href="/standard/ANTzip/0.1.1/#PathSanitization"
                                >Banned Character</a>
                            <li>a path contains an <a href="/standard/ANTzip/0.1.1/#noUpwardDirectory"
                                >upward directory indicators (<code>..</code>) or current directory indicators
                                    (<code>.</code>)</a>.
                            <li>a path <a href="/standard/ANTzip/0.1.1/#noUpwardDirectory"
                                >referenced any file located outside the ZIPRoot</a>.
                        </ul>
                        HTML,
                ],
                '-3' => [
                        'name' => 'RESOLUTION_ERROR',
                        'desc' => 'A Path requiring a <a href=#WasmModule>Wasm Module</a>, but doesnt point to a valid compilable'
                                . ' <a href=#WasmModule>Wasm Module</a>.',
                ],
                '-4' => [
                        'name' => 'INVALID_GAME_STATE_ERROR',
                        'desc' => 'the game is in a invalid state. formerly <code>GAME_HASNT_STARTED_YET_ERROR</code>' .
                                ' (-4) and <code>GAME_IS_RUNNING_ERROR</code> (-5)',
                ],
                '-5' => [
                        'name' => 'RESERVED',
                        'desc' => 'reserved for future use.'
                ],
                '-6' => [
                        'name' => 'NO_MORE_MEMORY_ERROR',
                        'desc' => 'The Operation requires more memory but it cant be granted.',
                ],
                '-7' => [
                        'name' => 'WASM_PORT_MISSING',
                        'desc' => 'An Import or Export is missing.',
                ],
                '-8' => [
                        'name' => 'LACKS_PERMS',
                        'desc' => 'The <a href=#WasmModule>Wasm Module</a> lacks the permissions to perform this' .
                                ' action. (or the host lacks the permissions too)',
                ],
                '-9' => [
                        'name' => 'SCREEN_DISPLAY_ERROR',
                        'desc' => 'An error at the Screen\'s display happened for any reason.',
                ],
        ];
        function errorcode(int $int): string
        {
            global $errorCodes;
            return "<a href=#{$errorCodes[$int]['name']}><code>{$errorCodes[$int]['name']}</code> (<code>$int</code>)</a>";
        }

        foreach ($errorCodes as $code => $errorCode) {
            $errorCodeEnglish = new NumberToEnglish($code)->toString();
            $errorCodes[$code]['pref'] = "<code>$code</code> ($errorCodeEnglish)";
            $errorCodes[$code]['desc'] = preg_replace('/\\s+/', ' ', $errorCode['desc']);
            $errorCodes[$code]['engl'] = "$errorCodeEnglish";
        } ?></ul>
    <h2 id=external>This Specification uses external references</h2>
    <ul class=li-margin05>
        <li><a href=https://www.rfc-editor.org/rfc/rfc2119>The keywords "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL
                NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
                interpreted as described in RFC 2119.</a>
        <li><a href=https://en.wikipedia.org/wiki/WebAssembly>WebAssembly (Wikipedia)</a>
        <li><a href=https://www.usb.org/document-library/hid-usage-tables-112>USB HID Usage Tables v1.12</a>
    </ul>
    <h2 id=WebAssembly>WebAssembly</h2>
    <p>As a refresher here are some concepts you need to know.
    <ul>
        <li>The <dfn id=WASMBorder>WASM Border</dfn> is the Linear Memory Wasm has. it is the only way to communicate to
            the Wasm program and the only way for wasm to take inputs.
        <li>WebAssembly only uses the following types. we assume everyone can use those across the
            <a href=#WASMBorder>WASM Border</a>.
            <ul class=sl>
                <li><code>f32</code>: a float 32.
                <li><code>f64</code>: a float 64.
                <li><code>i32</code>: an int 32.
                <li><code>i64</code>: an int 64.
            </ul>
        <li>A <dfn id=WasmModule>Wasm Module</dfn> is a Wasm File.
        <li>An <dfn id=Exports>Export</dfn> is What the Wasm Module gives to the Edge.
        <li>An <dfn id=Imports>Import</dfn> is What the Edge gives to the Wasm Module.
        <li>WASM (WebAssembly) is not WASI (WebAssembly System Interface), ANT.wasm uses (Wasm, not Wasi).
    </ul>
    <h2 id=CoordinateSystem>Coordinate System</h2>
    <p>All drawing coordinates and mouse coordinates are in CSS pixels, as defined by the
        <a href="https://www.w3.org/TR/css-values-3/#reference-pixel">CSS Reference Pixel</a>.</p>

    <p>1 CSS pixel is the visual angle of one pixel on a device with a pixel density of 96dpi
        at arm's length. Edges MUST scale coordinates appropriately for the display's physical
        pixel density. Games SHOULD NOT assume that 1 CSS pixel equals 1 device pixel.</p>

    <p>For example, on a Retina display (2x scaling), a rectangle drawn at <code>draw_rect(0, 0, 100, 100)</code>
        will occupy 200 device pixels. The game does not need to account for this scaling—the Edge handles it.</p>
    <h2 id=supported-formats>Supported Formats</h2>
    <p>the Following Formats MUST be supported.
    <ul class=sl><?= implode(array_map(fn(string $string): string => "<li><code>$string</code>",
                explode(',', 'image/webp,video/webm,audio/webm,application/json,application/zip,text/plain'))) ?></ul>
    <p>the Following Formats MAY be supported too.
    <ul class=sl><?= implode(array_map(fn(string $string): string => "<li><code>$string</code>",
                explode(',', 'image/png,image/avif,text/markdown,audio/mpeg,video/mp4,image/svg+xml'))) ?></ul>
    <aside class='info warnbox warnbox-margin'>
        <p><strong>Info!</strong> <a href=#draw_path>draw_path</a> uses the same pathing syntax as svg. it does not
            depend on the svg format being supported. i just borrowed the pathing syntax is what i mean.
    </aside>

    <h2 id=Phases>Phases</h2>
    <p>Your Wasm package is called a game. Your Game has the following phases. in order of appearance</p>
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Description" ?></thead>
            <tbody>
            <tr>
                <td>Loading Phase
                <td>Here is where the <a href="<?= "../../Foundation/0.4.0/#Edge" ?>">Edge</a> loads and verifies the
                    <a href="<?= "/standard/ANTzip/0.1.1/#ANT.zip" ?>">ANT.zip</a>. if it is marked as Untrusted the
                    Edge MUST abort proceeding.
            <tr>
                <td>Initialization Phase
                <td>Here is where you Initialize your game before it runs.
            <tr>
                <td>game Phase
                <td>Here is where you let the player play. this is activated by calling
                    <a href=#start_game><code><var>start_game</var></code></a>.
            <tr>
                <td>closing Phase
                <td>Here is where the <a href="<?= "../../Foundation/0.4.0/#Edge" ?>">Edge</a> cleans up the game's
                    resources.
            </tbody>
        </table>
    </div>
    <div class=sl>
        <h2 id=InternalAbstractOperation>Internal Abstract Operation</h2>
        <p>It is assumed Imports and Exports are called to the invoking <a href=#WasmModule>Wasm Module</a>
            unless otherwise stated.
        <ul>
            <li>the notation <code>[[VariableType]]</code> is an internal variable type. wasm doesnt support strings so
                <code>[[String]]</code> is for the specification only is what i mean.
            <li>the notation <code>[[%FunctionName%]]</code> is an internal function name. they MUST NOT be exposed to
                any <a href=#WasmModule>Wasm Module</a>.
            <li>the notation <code>$Variablename</code> is an internal variable name used only in the internal function.
                they are unobservable outside it. even if they share the same name
        </ul>
        <p>If any of the following Invariants are violated, that is a spec bug.
        <h3 id=StringFromBytes><code><var>[[%StringFromBytes%]]</var> (<var>of</var>: [[FileHandler]], <var>ptr</var>:
                i32, <var>len</var>: i32): [[String]]</code></h3>
        <p>Do the following in order
        <ol>
            <li>Go to <var>ptr</var> (inclusive) until (<var>ptr</var> + <var>len</var>) (exclusive) of <var>of</var>
                and take the raw bytes as <var>$string</var>.
            <li>Assume: <var>$string</var> is the UTF-8 bytes of the intended string.
            <li>If <var>$string</var> contains illegal UTF-8 characters return
                <span><?= errorcode(-1) ?></span>.
            <li>Return <var>$string</var>.
        </ol>
        <h3 id=BytesFromString><code><var>[[%BytesFromString%]]</var> (<var>string</var>: [[String]]): i32</code></h3>
        <p>Do the following in order
        <ol>
            <li><a href=https://tc39.es/ecma262/#assert>Assert:</a> <var>$string</var> is encoded in UTF-8 bytes (is a
                MUST due to the Foundation's Global Rules).
            <li>Set <var>$length</var> to the length of <var>$string</var> in UTF-8 bytes.
            <li>If <code><var>host_malloc</var></code> is not exported return <span><?= errorcode(-7) ?></span>.
            <li>Set <var>$pointer</var> to the result of calling <code><var>host_malloc</var></code> with
                <var>$length</var>.
            <li>Write the bytes of <var>$string</var> at <var>$pointer</var> (inclusive) until (<var>$pointer</var> +
                <var>$length</var>) (exclusive) in the current <a href=#WasmModule>Wasm Module</a>'s Linear Memory.
            <li>Return <var>$pointer</var> .
        </ol>
        <h3 id=LoadModule><code><var>[[%LoadModule%]]</var> (<var>path</var>: [[String]],
                <var>Imports</var>: [[ImportLibrary]])>: i32</code></h3>
        <p>Do the following in order
        <ol>
            <li>If Game has Started Return
                <span><?= errorcode(-4) ?></span>.
            <li>Resolve <var>$path</var> using
                <a href="<?= "/standard/ANTzip/0.1.1/#applicationBehavior" ?>">ANT.zip's Application
                    Behavior</a> to be <var>$resolved</var>.
            <li>If the above step fails return the appropriate <a href=#ErrorCodes>Error Code</a>.
            <li>If <var>$resolved</var> is not a valid <a href=#WasmModule>Wasm Module</a>, Return
                <span><?= errorcode(-3) ?></span>.
            <li>Initialize and load <var>$resolved</var> as a <a href=#WasmModule>Wasm Module</a> with
                <var>Imports</var>
                as Imports.
            <li>If Initializing and loading fails then return <span><?= errorcode(-3) ?></span>.
            <li>add 1 to the integer <var>$wasmModulesLoaded</var>.
            <li>call <var>$resolved</var>'s exported <code><a href=#on_module_load>on_module_load</a></code> if
                present with (<var>$wasmModulesLoaded</var> as <var>ModuleId</var>).
            <li>Return <var>$wasmModulesLoaded</var>.
        </ol>
    </div>
    <h2 id=BasicGlobalLibrary>Basic Global Library</h2>
    <p class=sl>All <a href=#WasmModule>Wasm Modules</a> MUST expose or be exposed to the following functions, the <a
                href=#BasicGlobalLibrary>Basic Global Library's</a> WASM namespace is <code>BasicGlobalLibrary</code>.
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Types<th scope=col>Import/Export<th scope=col>Description" ?></thead>
            <tbody>
            <tr id=host_malloc>
                <td><code><var>host_malloc</var></code>
                <td><code>(<var>bytes</var>: i32): i32</code>
                <td>Export
                <td><code><var>host_malloc</var></code> MUST return a positive number indicating a pointer (<var>$pointer</var>)
                    on that Wasm's Linear Memory, <var>$pointer</var> until (<var>$pointer</var> + <var>bytes</var>)
                    MUST be free for the Edge to store whatever is the Edge needs. If that fails or there isnt memory to
                    allocate to the Edge, Return the appropriate <a href=#ErrorCodes>Error Code</a>.
                    <aside class='info warnbox warnbox-margin'>
                        <p><strong>Info!</strong> Wasm only provides a Linear Memory. think of it as a single <a
                                    href=https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Uint8Array
                            >Uint8Array</a>, without dictating how it stores its memory, the Edge still needs to write
                            to that Linear Memory in order for the wasm to do stuff. that's why
                            <code><var>host_malloc</var></code> MUST be exported, to provide a way for the Edge to ask
                            where to write the amount of <var>bytes</var> to.
                    </aside>
                    <aside class='warn warnbox warnbox-margin'>
                        <p><strong>Warning!</strong> for internal operations not crossing the <a href=#WASMBorder>WASM
                                Border</a>, please use your language's internal Memory Allocator.
                            <a href=#host_malloc><code><var>host_malloc</var></code></a> is only fo the Edge to request
                            memory space to write data for the <a href=#WasmModule>Wasm Module</a> to read.
                    </aside>
            <tr id=on_module_load>
                <td><code><var>on_module_load</var></code>
                <td><code>(<var>ModuleId</var>: i32): void</code>
                <td>Export
                <td>called when the <a href=#WasmModule>Wasm Module</a> is loaded via
                    <code><a href=#load_module>load_module</a></code>.
            <tr id=tick>
                <td><code><var>tick</var></code>
                <td><code>(<var>delta</var>: f32): void</code>
                <td>Export
                <td>for as long as the game is running, this export is called with with <var>delta</var> as the
                    timedelta since the previous call in milliseconds.
            <tr id=current_mouse_press>
                <td><code><var>current_mouse_press</var></code>
                <td><code>(): i32</code>
                <td>Import
                <td>The Mouse Key Pressed currently. <a href=#MouseKey>a bitwise flag of the following below</a>
            <tr id=current_mouse_x>
                <td><code><var>current_mouse_x</var></code>
                <td><code>(): i64</code>
                <td>Import
                <td>The X coordinate of the Mouse. 0, 0 is the top left. X goes positive the more right the mouse is.
            <tr id=current_mouse_y>
                <td><code><var>current_mouse_y</var></code>
                <td><code>(): i64</code>
                <td>Import
                <td>The X coordinate of the Mouse. 0, 0 is the top left. Y goes positive the more down the mouse is.
            <tr id=current_mouse_wheel>
                <td><code><var>current_mouse_wheel</var></code>
                <td><code>(): i32</code>
                <td>Import
                <td>The Mouse's wheel direction. <a href=#MouseWheel>as bitwise flag of the following below</a>
            <tr id=on_key_down>
                <td><code><var>on_key_down</var></code>
                <td><code>(<var>keycode</var>: i32): i32</code>
                <td>Export
                <td><p>MUST be called everytime a keyboard key is pressed.
                    <p>Key codes MUST follow the USB HID Usage Tables specification,
                        using the Usage IDs from the Keyboard/Keypad page (0x07).</p>
                    <p>Reference: <a href="https://www.usb.org/document-library/hid-usage-tables-112">
                            USB HID Usage Tables v1.12</a>, published by the USB Implementers Forum.</p>
            <tr id=on_key_up>
                <td><code><var>on_key_up</var></code>
                <td><code>(<var>keycode</var>: i32): i32</code>
                <td>Export
                <td>MUST be called everytime a keyboard key is released.
                    <p>Key codes MUST follow the USB HID Usage Tables specification,
                        using the Usage IDs from the Keyboard/Keypad page (0x07).</p>
                    <p>Reference: <a href="https://www.usb.org/document-library/hid-usage-tables-112">
                            USB HID Usage Tables v1.12</a>, published by the USB Implementers Forum.</p>
            </tbody>
        </table>
    </div>
    <h3 id=Mouse>Mouse bitwise</h3>
    <h4 id=MouseKey>Mouse Key</h4>
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Bitwise";
            function bitwiseOf(int $int): string
            {
                $bitwise = (1 << $int);
                $english = new NumberToEnglish($bitwise);
                return "<code>$bitwise</code>, <code>(1 &lt;&lt; $int)</code> (<span>$english</span>)";
            } ?></thead>
            <tbody>
            <tr>
                <td>Left Mouse Button
                <td><?= bitwiseOf(0) ?></td>
            <tr>
                <td>Right Mouse Button
                <td><?= bitwiseOf(1) ?></td>
            <tr>
                <td>Mouse Wheel Button
                <td><?= bitwiseOf(2) ?></td>
            </tbody>
        </table>
    </div>
    <h4 id=MouseWheel>Mouse Wheel</h4>
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Bitwise"; ?></thead>
            <tbody>
            <tr>
                <td>Not Scrolling
                <td><code><?= 0 ?></code> (Zero)
            <tr>
                <td>Scrolling Up
                <td><?= bitwiseOf(3) ?></td>
            <tr>
                <td>Scrolling Down
                <td><?= bitwiseOf(4) ?></td>
            </tbody>
        </table>
    </div>
    <h3 id=BasicPaintLibrary>Basic Paint Library</h3>
    <p class=sl>All <a href=#WasmModule>Wasm Modules</a> MUST expose or be exposed to the following functions too, the
        <a href=#BasicGlobalLibrary>Basic Paint Library's</a> WASM namespace is <code>screenpaint</code>.
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Types<th scope=col>Import/Export<th scope=col>Description" ?></thead>
            <tbody>
            <tr id=draw_rect>
                <td><code><var>draw_rect</var></code>
                <td><code>(<var>x</var>: i32, <var>y</var>: i32, <var>w</var>: i32, <var>h</var>: i32, <var>color</var>:
                        i32): i32</code>
                <td>Import
                <td>If Game has not Started Return <span><?= errorcode(-4) ?></span>. draw a Rectangle at the offset
                    <var>x</var> (the x/horizontal coordinate), <var>y</var> (the y/vertical coordinate) from the top
                    left which is 0, 0 with <var>color</var> as RGBA8 color. the rectangle MUST be <var>w</var> (width),
                    <var>h</var> (height) in size. Return <span><?= errorcode(0) ?></span>.
            <tr id=draw_path>
                <td><code><var>draw_path</var></code>
                <td><code>(<var>ptr</var>: i32, <var>len</var>: i32, <var>fillColor</var>: i32, <var>strokeColor</var>:
                        i32, <var>strokeWidth</var>: i32): i32</code>
                <td>Import
                <td>
                    <ul>
                        <li>If Game has not Started Return <span><?= errorcode(-4) ?></span>.
                        <li>Set <var>$path</var> to Calling <code><var>[[%StringFromBytes%]]</var></code> giving the
                            current <a href=#WasmModule>Wasm Module</a>'s Linear Memory, <code><var>ptr</var></code> and
                            <code><var>len</var></code>.
                        <li>Use an SVG pathing library and parse <var>$path</var> in the same way SVG <a
                                    href=https://developer.mozilla.org/en-US/docs/Web/SVG/Reference/Element/path>path</a>
                            <a href=https://developer.mozilla.org/en-US/docs/Web/SVG/Reference/Attribute/d>d</a> is
                            parsed.
                        <li>If <var>strokeWidth</var> is less than 0 then Set <var>strokeWidth</var> to 0.
                        <li>Draw that shape parsed from the <var>$path</var> above with <var>fillColor</var> as <code>fill</code>,
                            <var>strokeColor</var> as <code>stroke</code>, <var>strokeWidth</var> as
                            <code>stroke-width</code>.
                        <li>Return <span><?= errorcode(0) ?></span>.
                    </ul>
                    <aside class='info warnbox warnbox-margin'>
                        <p><strong>Info!</strong> <a href=#draw_path>draw_path</a> uses the same pathing syntax as svg.
                            it does not depend on the svg format being supported. i just borrowed the pathing syntax is
                            what i meant.
                    </aside>
            <tr id=load_texture>
                <td><code><var>load_texture</var></code>
                <td><code>(<var>ptr</var>: i32, <var>len</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has not Started Return <span><?= errorcode(-4) ?></span>.
                        <li>Set <var>$path</var> to Calling <code><var>[[%StringFromBytes%]]</var></code> giving the
                            current <a href=#WasmModule>Wasm Module</a>'s Linear Memory, <code><var>ptr</var></code>
                            and <code><var>len</var></code>.
                        <li>Resolve <var>$path</var> using
                            <a href="<?= "/standard/ANTzip/0.1.1/#applicationBehavior" ?>">ANT.zip's Application
                                Behavior</a> to be <var>$resolved</var>.
                        <li>If <var>$resolved</var> does not point to a valid file, Return
                            <span><?= errorcode(-2) ?></span>.
                        <li>If <var>$resolved</var> does not point to a valid image file, Return
                            <span><?= errorcode(-3) ?></span>.
                        <li>If <var>$resolved</var> does not point to a supported image file, Return
                            <span><?= errorcode(-3) ?></span>.
                        <li>Create a Texture Handler with <var>$resolved</var> as texture.
                        <li>Return an <a href="<?= "../../Foundation/0.4.0/#Edge-Defined" ?>">Edge-Defined</a> positive
                            nonzero unique for this game session integer representing the Texture Handler created in the
                            previous step.
                    </ol>
            <tr id=draw_texture>
                <td><code><var>draw_texture</var></code>
                <td><code>(<var>texture_handler</var>: i32, <var>x</var>: i32, <var>y</var>: i32, <var>w</var>: i32,
                        <var>h</var>: i32, <var>deg</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has not Started Return <span><?= errorcode(-4) ?></span>.
                        <li>If <var>texture_handler</var> does not correlate to any files (not counting directories)
                            that
                            were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>Draw the image of <code><var>texture_handler</var></code> at <code><var>x</var></code>,
                            <code><var>y</var></code> relative to the top left interpreted in css pixels, rotated
                            <code><var>deg</var></code> degrees clockwise.
                        <li>If the above step failed Return <span><?= errorcode(-9) ?></span>
                        <li>Return <span><?= errorcode(0) ?></span>.
                    </ol>
            <tr id=texture_css_height>
                <td><code><var>texture_css_height</var></code>
                <td><code>(<var>texture_handler</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has not Started Return <span><?= errorcode(-4) ?></span>.
                        <li>If <var>texture_handler</var> does not correlate to any files (not counting directories)
                            that were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>Return the height of <var>texture_handler</var> in css pixels.
                    </ol>
            <tr id=texture_css_width>
                <td><code><var>texture_css_width</var></code>
                <td><code>(<var>texture_handler</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has not Started Return <span><?= errorcode(-4) ?></span>.
                        <li>If <var>texture_handler</var> does not correlate to any files (not counting directories)
                            that were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>Return the width of <var>texture_handler</var> in css pixels.
                    </ol>
            <tr id=texture_free>
                <td><code><var>texture_free</var></code>
                <td><code>(<var>texture_handler</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has not Started Return <span><?= errorcode(-4) ?></span>.
                        <li>If <var>texture_handler</var> does not correlate to any files (not counting directories)
                            that were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>Close <var>texture_handler</var> and free it up.
                        <li>Return <span><?= errorcode(0) ?></span>
                    </ol>
            </tbody>
        </table>
    </div>
    <h3 id=Texture>Texture</h3>
    <div class=sl>
        <h5 id=FileHandler><code>[[TextureHandler]]</code></h5>
        <p>A <dfn>[[TextureHandler]]</dfn> is an <a
                    href="<?= "../../Foundation/0.4.0/#Edge-Defined" ?>">Edge-Defined</a>
            opaque identifier representing an image. It MUST:</p>
        <ul>
            <li>Be unique among all Texture Handlers.
            <li>Become invalid after <code><var>texture_free</var></code> is called.
            <li>Not be reusable once closed.
            <li>Be automatically closed when the current game session ends. Games SHOULD explicitly close files with
                <code>texture_free</code>, but the Edge MUST clean up any remaining open handlers at game termination.
        </ul>
    </div>
    <h2 id=Mains>Main</h2>
    <p class=sl>A <dfn>Main</dfn> is a wasm file that is called after a <a href=#cores>Core</a>. it is the main
        function.
    <p>If we are talking about User Generated Content games, A <a href=#cores>Core</a> is the actual Game where A
        <a href=#Mains>Main</a> is the core for a sharable package a user has created.
    <p>A Main MUST expose or be exposed to the following functions, the <a href=#Mains>Main's</a> WASM namespace is
        <code class=sl>MainModule</code>.
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Types<th scope=col>Import/Export<th scope=col>Description" ?></thead>
            <tbody>
            <tr id=load_module>
                <td><code><var>load_module</var></code>
                <td><code>(<var>ptr</var>: i32, <var>len</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has not Started Return <span><?= errorcode(-4) ?></span>.
                        <li>Set <var>$path</var> to Calling <code><var>[[%StringFromBytes%]]</var></code> giving the
                            current <a href=#WasmModule>Wasm Module</a>'s Linear Memory, <code><var>ptr</var></code>
                            and <code><var>len</var></code>.
                        <li>If <var>$path</var> starts with <code>/core/</code> (case-insensitive), Return
                            <span><?= errorcode(-2) ?></span>.
                        <li>If <var>$path</var> is <code>/core.wasm</code> (case-insensitive), Return
                            <span><?= errorcode(-2) ?></span>.
                        <li>If <var>$path</var> is <code>/main.wasm</code> (case-insensitive), Return
                            <span><?= errorcode(-2) ?></span>.
                        <li>Return the result of Calling <code><var>[[%LoadModule%]]</var></code> given
                            <var>$path</var> and (<a href=#BasicGlobalLibrary>Basic Global Library</a> as
                            <var>Imports</var>).
                    </ol>
                    <aside class='info warnbox warnbox-margin'>
                        <p><strong>Info!</strong> usually called "import" in other languages, Wasm terminology already
                            uses that word for something else. <code><var>load_module</var></code> fits as name.
                    </aside>
            <tr id=start_game>
                <td><code><var>start_game</var></code>
                <td><code>(<var>width</var>: i32, <var>height</var>: i32): void</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>Start the Game.
                        <li>Initialize a Screen (called <var>Canvas</var>) with (<var>width</var>) width and (<var>height</var>)
                            height. if these values are negative or zero abort the game and show an error to the user.
                            then Return <span><?= errorcode(-9) ?></span>. if these values cant be used then return
                            <span><?= errorcode(-9) ?></span>.
                        <li>for as long as the game is running, call an export
                            <code><var>tick</var></code> with <var>delta</var> as the timedelta
                            since the previous call in milliseconds.
                        <li>Return the <span><?= errorcode(0) ?></span>.
                    </ol>
            </tbody>
        </table>
    </div>
    <h2 id=cores>Cores</h2>
    <p class=sl>A <dfn>Core</dfn> is a wasm file that is first called. it is the engine so games can stay self-contained
        within their <a href="<?= "/standard/ANTzip/0.1.1/#ANT.zip" ?>">ANT.zip</a>. the core MUST live at
        <code>"/core.wasm"</code> (case-sensitive) ziproot path.
    <p>If we are talking about User Generated Content games, A <a href=#cores>Core</a> is the actual Game where A
        <a href=#Mains>Main</a> is the core for a sharable package a user has created.
    <p>A Core MUST expose or be exposed to the following functions, the <a href=#cores>Core's</a> WASM namespace is
        <code class=sl>CoreModule</code>.
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Types<th scope=col>Import/Export<th scope=col>Description" ?></thead>
            <tbody>
            <tr id=load_module_core>
                <td><code><var>load_module_core</var></code>
                <td><code>(<var>ptr</var>: i32, <var>len</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has Started Return
                            <span><?= errorcode(-4) ?></span>.
                        <li>Set <var>$path</var> to Calling <code><var>[[%StringFromBytes%]]</var></code> giving
                            the current <a href=#WasmModule>Wasm Module</a>'s Linear Memory, <code><var>ptr</var></code>
                            and <code><var>len</var></code>.
                        <li>If <var>$path</var> is <code>/core.wasm</code> (case-insensitive), Return
                            <span><?= errorcode(-2) ?></span>.
                        <li>If <var>$path</var> does not start with <code>/core/</code> (case-insensitive), Return
                            <span><?= errorcode(-2) ?></span>.
                        <li>Return the result of Calling <code><var>[[%LoadModule%]]</var></code> given
                            <var>$path</var> and (<a href=#BasicGlobalLibrary>Basic Global Library</a> as
                            <var>Imports</var>).
                    </ol>
                    <aside class='info warnbox warnbox-margin'>
                        <p><strong>Info!</strong> usually called "import" in other languages, Wasm terminology already
                            uses that word for something else. <code><var>load_module</var></code> fits as name.
                    </aside>
            <tr>
                <td><code><var>load_main</var></code>
                <td><code>(): void</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has Started Return <span><?= errorcode(-4) ?></span>.
                        <li>Return the result of Calling <code><var>[[%LoadModule%]]</var></code> given
                            <code>"/main.wasm"</code> and (<a href=#BasicGlobalLibrary>Basic Global Library</a> and <a
                                    href="#Mains">Main</a> as <var>Imports</var>).
                    </ol>
                    <aside class='info warnbox warnbox-margin'>
                        <p><strong>Info!</strong> usually called "import" in other languages, Wasm terminology already
                            uses that word for something else. <code><var>load_module</var></code> fits as name.
                    </aside>
            </tbody>
        </table>
    </div>
    <!--<h2 id=Namespaces>Alternate WASM Namespaces</h2>-->
    <h2 id=Extensions>Extensions</h2>
    <h3 id=FileSystemExtension>File System Extension</h3>
    <p>the File System Extension is an Extension of the <a href=#BasicGlobalLibrary>Basic Global Library</a>, anywhere
        where the <a href=#BasicGlobalLibrary>Basic Global Library</a> is imported or exported an <a
                href="<?= "../../Foundation/0.4.0/#Edge" ?>">Edge</a> SHOULD choose to import this too. if they do not
        wish to implement it then the <a href=#Stubs>Methods not implemented MUST be stubbed</a>.
    <p>the File System Extension MUST be everywhere it is supposed to be or nowhere, implementing it partially (unless
        otherwise specified) is a violation of this specification. when it is implemented the <a href=#WasmModule>Wasm
            Module</a> MUST be exposed to the following functions, the WASM namespace is <code
                class=sl>FileExtensionV1</code>.
    <p>the Game is the Same Game if the sha256 of the ANT.json is the same. the game MUST be verified to be the same
        game.
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Types<th scope=col>Import/Export<th scope=col>Description" ?></thead>
            <tbody>
            <tr id=FileExtensionV1.open_file>
                <td><code><var>file_open</var></code>
                <td><code>(<var>ptr</var>: i32, <var>len</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>Set <var>$path</var> to Calling <code><var>[[%StringFromBytes%]]</var></code> giving
                            the current <a href=#WasmModule>Wasm Module</a>'s Linear Memory,
                            <code><var>ptr</var></code> and <code><var>len</var></code>.
                        <li>If <var>$path</var> violates the Rules of
                            <a href="<?= "/standard/ANTzip/0.1.1/#PathSanitization" ?>">ANT.zip's Path Sanitization
                                Behavior</a>, Return <span><?= errorcode(-2) ?></span>. the exception is that paths are
                            allowed to reference paths outside the ziproot.
                        <li>If <var>$path</var> doesnt start with one of the defined <a
                                    href=#FileSystemExtension-VirtualDirectories>Virtual Directories</a>, Return
                            <span><?= errorcode(-2) ?></span>
                        <li>Create a <a href=#FileHandler><code>[[FileHandler]]</code></a> and set its <code>$permissions</code>
                            as Defined in the <a href=#FileSystemExtension-VirtualDirectories>Virtual Directories</a>
                            table.
                        <li>Return an <a href="<?= "../../Foundation/0.4.0/#Edge-Defined" ?>">Edge-Defined</a> positive
                            nonzero unique for this game session integer representing the File Handler created in the
                            previous step. if this <var>$path</var> references the same file the <a
                                    href="<?= "../../Foundation/0.4.0/#Edge" ?>">Edge</a> MAY choose to return the same
                            integer.
                    </ol>
            <tr id=FileExtensionV1.file_read>
                <td><code><var>file_read</var></code>
                <td><code>(<var>file_handler</var>: i32, <var>wasm_ptr</var>: i32, <var>file_ptr</var>:
                        i32, <var>file_len</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If <var>file_handler</var> does not correlate to any files (not counting directories) that
                            were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>Assume: there is no <var>wasm_len</var> due to <var>file_len</var> being free at <var>wasm_ptr</var>.
                        <li>Go to <var>file_ptr</var> (inclusive) until (<var>file_ptr</var> + <var>file_len</var>)
                            (exclusive) of <var>file_handler</var>'s pointed file and take the raw bytes as
                            <var>$bytes</var>.
                        <li>Go to <var>wasm_ptr</var> (inclusive) until (<var>wasm_ptr</var> + <var>file_len</var>)
                            (exclusive) of the current <a href=#WasmModule>Wasm Module</a>'s Linear Memory and put the
                            raw bytes <var>$bytes</var> there.
                        <li>Return <span><?= errorcode(0) ?></span>.
                    </ol>
            <tr id=FileExtensionV1.file_write>
                <td><code><var>file_write</var></code>
                <td><code>(<var>file_handler</var>: i32, <var>wasm_ptr</var>: i32, <var>file_ptr</var>: i32,
                        <var>wasm_len</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If <var>file_handler</var> does not correlate to any files (not counting directories) that
                            were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>Go to <var>wasm_ptr</var> (inclusive) until (<var>wasm_ptr</var> + <var>wasm_len</var>)
                            (exclusive) of the current <a href=#WasmModule>Wasm Module</a>'s Linear Memory take the raw
                            bytes as
                            <var>$bytes</var>.
                        <li>Go to <var>wasm_ptr</var> (inclusive) until (<var>file_ptr</var> + <var>wasm_len</var>)
                            (exclusive) of <var>file_handler</var>'s pointed file and and put the
                            raw bytes <var>$bytes</var> there.
                        <li>Return <span><?= errorcode(0) ?></span>.
                    </ol>
            <tr id=FileExtensionV1.file_type>
                <td><code><var>file_type</var></code>
                <td><code>(<var>ptr</var>: i32, <var>len</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>Set <var>$path</var> to Calling <code><var>[[%StringFromBytes%]]</var></code> giving
                            the current <a href=#WasmModule>Wasm Module</a>'s Linear Memory,
                            <code><var>ptr</var></code> and <code><var>len</var></code>.
                        <li>If <var>$path</var> violates the Rules of
                            <a href="<?= "/standard/ANTzip/0.1.1/#applicationBehavior" ?>">ANT.zip's Application
                                Behavior</a> Return <span><?= errorcode(-2) ?></span>.
                        <li>If <var>$path</var> points to a File, Return 1.
                        <li>If <var>$path</var> points to a Directory, Return 2.
                        <li>If <var>$path</var> points to a Symlink, Return 0.
                        <li>If <var>$path</var> doesnt point to anything, Return 0.
                        <li>Return 0.
                    </ol>
                    <aside class='info warnbox warnbox-margin'>
                        <p><strong>Info!</strong> Symlinks, No Such File Or Directory, and all other cases (like future
                            things) all resolve to 0, that is intentional for privacy and security.
                    </aside>
            <tr id=FileExtensionV1.file_size>
                <td><code><var>file_size</var></code>
                <td><code>(<var>file_handler</var>: i32): i64</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If <var>file_handler</var> does not correlate to any files (not counting directories) that
                            were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>Return the size of <var>file_handler</var> in bytes.
                    </ol>
            <tr id=FileExtensionV1.file_close>
                <td><code><var>file_close</var></code>
                <td><code>(<var>file_handler</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If <var>file_handler</var> does not correlate to any files (not counting directories) that
                            were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>Close File <code><var>file_handler</var></code>.
                        <li>Return <span><?= errorcode(0) ?></span>.
                    </ol>
            <tr id=FileExtensionV1.file_mtime>
                <td><code><var>file_mtime</var></code>
                <td><code>(<var>file_handler</var>: i32): i64</code>
                <td>OPTIONAL Import
                <td>Do the following in order
                    <ol>
                        <li>If <var>file_handler</var> does not correlate to any files (not counting directories) that
                            were opened Return <span><?= errorcode(-3) ?></span>.
                        <li>If <code><a href=#antdatetime.utc_now_ms>antdatetime.utc_now_ms</a></code> is stubbed,
                            Return <span><?= errorcode(-7) ?></span>.
                        <li>Return the mtime of <code><var>file_handler</var></code> as milliseconds since the unix
                            epoch. the Edge MAY round the value to reduce fingerprinting. If rounding is applied, the
                            rounded value MUST still be consistent with the actual mtime (e.g., rounded to nearest hour,
                            not arbitrary).
                    </ol>
                    <aside class='warn warnbox warnbox-margin'>
                        <p><strong>Warning!</strong> there is a very real possibility of a game getting the current time
                            by using <a href=#FileExtensionV1.file_mtime><code><var>file_mtime</var></code></a>, by
                            returning <span><?= errorcode(-7) ?></span>. we make it look stubbed and not allow access to
                            the current time unless <code><a
                                        href="#antdatetime.utc_now_ms">antdatetime.utc_now_ms</a></code> exists too.
                        <p>To get the current time without <code><a href="#antdatetime.utc_now_ms">antdatetime.utc_now_ms</a></code>
                        <ol>
                            <li>Write to any file you have access to.
                            <li>Call <a href=#FileExtensionV1.file_mtime><code><var>file_mtime</var></code></a> on that
                                file.
                            <li>It is that simple.</li>
                        </ol>
                    </aside>
            </tbody>
        </table>
        <aside class='info warnbox warnbox-margin'>
            <p class=sl><strong>Info!</strong> <code><var>file_exists</var></code> is no longer with us, you can use <a
                        href=#FileExtensionV1.file_type><code><var>file_type</var></code></a> instead!
        </aside>
    </div>
    <h4 id=FileSystemExtension-VirtualDirectories>Virtual Directories</h4>
    <p>Virtual Directories are a security measure to not allow games to write anywhere on the user's file system.
        currently there are 2</p>
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Permissions<th scope=col>Description" ?></thead>
            <tbody>
            <tr>
                <td><code>/user/</code>
                <td>ReadWrite
                <td>The Game's User Directory. for save files, photos, and anything the game wants to save.
            <tr>
                <td><code>/game/</code>
                <td>ReadOnly
                <td>The Game's Asset Directory. Direct to the ANTzip.
            </tbody>
        </table>
    </div>
    <h4 id=FileSystemExtension-TypeDefinition>Type Definitions</h4>
    <div class=sl>
        <h5 id=FileHandler><code>[[FileHandler]]</code></h5>
        <p>An <dfn>[[FileHandler]]</dfn> is an <a href="<?= "../../Foundation/0.4.0/#Edge-Defined" ?>">Edge-Defined</a>
            opaque identifier representing an open file. It MUST:</p>
        <ul>
            <li>Be unique among all files handlers.
            <li>Become invalid after <code><var>file_close</var></code> is called.
            <li>Not be reusable once closed.
            <li>Be automatically closed when the current game session ends. Games SHOULD explicitly close files with
                <code>file_close</code>, but the Edge MUST clean up any remaining open handlers at game termination.
            <li>Have a <code><var>$permissions</var></code> property with values being <code>ReadWrite</code> or
                <code>ReadOnly</code>
            <li>MUST NOT traverse Symlinks nor acknowledge their existence.
        </ul>
    </div>
    <h4 id=FileSystemExtension-ResourceLimits>Resource Limits</h4>
    <p>Edges SHOULD impose limits on:
    <ul>
        <li>Maximum number of simultaneously open file handlers
        <li>Maximum file size that can be read
        <li>Total disk usage for captured screenshots/videos
    </ul>
    <p>If a limit is exceeded, the Edge SHOULD return <code>NO_MORE_MEMORY_ERROR (-6)</code>
        or <code>LACKS_PERMS (-8)</code> as appropriate.</p>
    <h3 id=SCAE>Screen Capture Api Extension</h3>
    <p>the Screen Capture Api is an Extension of the <a href=#BasicGlobalLibrary>Basic Global Library</a>, anywhere
        where the <a href=#BasicGlobalLibrary>Basic Global Library</a> is imported or exported an <a
                href="<?= "../../Foundation/0.4.0/#Edge" ?>">Edge</a> SHOULD choose to import this too. if they do not
        wish to implement it then the <a href=#Stubs>Methods not implemented MUST be stubbed</a>.
    <p>the Screen Capture Api Extension MUST only be present if the <a href=#FileSystemExtension>File System
            Extension</a> is present.
    <p>the Screen Capture Api Extension MUST be everywhere it is supposed to be or nowhere, implementing it partially
        (unless otherwise specified) is a violation of this specification. when it is implemented the <a
                href=#WasmModule>Wasm Module</a> MUST be exposed to the following functions, the WASM namespace is <code
                class=sl>screenvideoV1</code>.
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Types<th scope=col>Import/Export<th scope=col>Description" ?></thead>
            <tbody>
            <tr id=screenvideoV1.take_screenshot>
                <td><code><var>take_screenshot</var></code>
                <td><code>(): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If the game Has Not started yet, Return <span><?= errorcode(-4) ?></span>.
                        <li>If 5 real time seconds have not past yet since the last call, Return
                            <span><?= errorcode(-8) ?></span>. this cooldown MUST be shared with all <a
                                    href=#WasmModule>Wasm Modules</a> in the game.
                        <li>Take a screenshot of the Game's Screen and save it to a file. It is very important that you
                            only save the Game's Screen as required for privacy.
                        <li>Return an <a href="<?= "../../Foundation/0.4.0/#Edge-Defined" ?>">Edge-Defined</a> integer
                            representing a file handler of this screenshot according to the <a
                                    href=#FileSystemExtension>File System Extension</a>. MUST be saved in the image/webp
                            format.
                    </ol>
                    <aside class='info warnbox warnbox-margin'>
                        <p><strong>Info! (non-normative)</strong> in the browser, when working with the
                            <code><a href=https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement>HTMLCanvasElement</a></code>
                            <code><a href=https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/toBlob
                                >HTMLCanvasElement.prototype.toBlob</a></code>
                            can be used.
                    </aside>
            <tr id=screenvideoV1.start_video_recording>
                <td><code><var>start_video_recording</var></code>
                <td><code>(): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If the game Has Not started yet, Return <span><?= errorcode(-4) ?></span>.
                        <li>If an Active Video Handler is still open then return <span><?= errorcode(-8) ?></span>.
                            <aside class='info warnbox warnbox-margin'>
                                <p><strong>Info!</strong> Future Specifications may allow more than one video stream at
                                    the same time. this one does not.
                            </aside>
                        <li>Return an <a href="<?= "../../Foundation/0.4.0/#Edge-Defined" ?>">Edge-Defined</a> integer
                            representing a <a href=#VideoHandlerType>Video Handler</a>.
                    </ol>
            <tr id=screenvideoV1.take_video_snapshot>
                <td><code><var>take_video_snapshot</var></code>
                <td><code>(<var>video_handler</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If the game Has Not started yet, Return <span><?= errorcode(-4) ?></span>.
                        <li>If <var>video_handler</var> does not correlate to any Active Video Handler, Return
                            <span><?= errorcode(-3) ?></span>.
                        <li>Take a screenshot of the Game's Screen and save it as a video frame. It is very important
                            that you only save the Game's Screen as required for privacy.
                        <li>Return <span><?= errorcode(0) ?></span>.
                    </ol>
            <tr id=screenvideoV1.stop_video_recording>
                <td><code><var>stop_video_recording</var></code>
                <td><code>(<var>video_handler</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If the game Has Not started yet, Return <span><?= errorcode(-4) ?></span>.
                        <li>If <var>video_handler</var> does not correlate to any Active Video Handler, Return
                            <span><?= errorcode(-3) ?></span>.
                        <li>convert the <var>video_handler</var> to an image/webm file and store it.
                        <li>close the <var>video_handler</var>.
                        <li>If no frames were captured Return <span><?= errorcode(0) ?></span>. No File is created.
                        <li>open that webm file and Return an <a href="<?= "../../Foundation/0.4.0/#Edge-Defined" ?>">Edge-Defined</a>
                            integer representing a <a href=#FileHandler><code>[[FileHandler]]</code></a> of this
                            recording according to the <a href=#FileSystemExtension>File System Extension</a>. MUST be
                            saved in the image/webm format.
                    </ol>
            </tbody>
        </table>
    </div>
    <h4 id=SCAE-TypeDefinition>Type Definitions</h4>
    <div class=sl>
        <h5 id=VideoHandlerType><code>[[VideoHandler]]</code></h5>
        <p>An <dfn>[[VideoHandler]]</dfn> is an <a href="<?= "../../Foundation/0.4.0/#Edge-Defined" ?>">Edge-Defined</a>
            opaque identifier representing an active video recording session. It MUST:</p>
        <ul>
            <li>Be unique among all active video handlers.
            <li>Become invalid after <code><var>stop_video_recording</var></code> is called.
            <li>Not be reusable once closed.
            <li>Be <a href=#screenvideoV1.stop_video_recording>closed</a> after the current game is closed, the result
                MAY be saved to the usual storage.
            <li>If no frames were captured it MUST NOT generate anything upon closure.
            <li>Never belonger than the current game's session.
            <li><strong>Edges MAY impose additional resource limits (maximum duration, file size, disk space) and SHOULD
                    communicate these limits to the user before recording starts. If a limit is exceeded during
                    recording, the Edge SHOULD automatically stop recording and return a partial video upon
                    <code><var>stop_video_recording</var></code>.</strong>
        </ul>
        <h5 id=CanvasType><code>[[Canvas]]</code></h5>
        <p>A <dfn>[[Canvas]]</dfn> is the rendering surface initialized by <code><var>start_game</var></code>.
            It has the following internal properties:</p>
        <ul>
            <li><var>[[Width]]</var>: the width in pixels.
            <li><var>[[Height]]</var>: the height in pixels.
            <li><var>[[Bitmap]]</var>: the current pixel data.
        </ul>
    </div>
    <h3 id=UserClock>Datetime Api Extension</h3>
    <p>the Datetime Api is an Extension of the <a href=#BasicGlobalLibrary>Basic Global Library</a>, anywhere
        where the <a href=#BasicGlobalLibrary>Basic Global Library</a> is imported or exported an <a
                href="<?= "../../Foundation/0.4.0/#Edge" ?>">Edge</a> SHOULD choose to import this too. if they do not
        wish to implement it then the <a href=#Stubs>Methods not implemented MUST be stubbed</a>.
    <p>the Datetime Api Extension MUST be everywhere it is supposed to be or nowhere, implementing it partially
        (unless otherwise specified) is a violation of this specification. when it is implemented the <a
                href=#WasmModule>Wasm Module</a> MUST be exposed to the following functions, the WASM namespace is <code
                class=sl>antdatetime</code>.
    <aside class='info warnbox warnbox-margin'>
        <p><strong>Info!</strong> Datetime can be a subjective matter. and it can be very locale dependant. ANTRequest
            chooses not to engage with that.
    </aside>
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Types<th scope=col>Import/Export<th scope=col>Description" ?></thead>
            <tbody>
            <tr id=antdatetime.utc_now_ms>
                <td><code><var>utc_now_ms</var></code>
                <td><code>(): i64</code>
                <td>Import
                <td>Return the milliseconds since the unix epoch. the Edge MAY round the value to reduce fingerprinting.
                    If rounding is applied, the rounded value MUST still be consistent with the actual mtime (e.g.,
                    rounded to nearest hour, not arbitrary).
            <tr id=antdatetime.utc_offset_ms_at>
                <td><code><var>utc_offset_ms_at</var></code>
                <td><code>(timestamp: i64): i64</code>
                <td>Import
                <td>Return the utc offset in milliseconds at that timestamp in the user's local time. an Edge SHOULD
                    consider rounding it to the nearest 15, 30, or 60 minutes to reduce fingerprinting. an Edge MAY
                    choose to always return 0 instead, representing UTC. Positive for East, Negative for West.
            </tbody>
        </table>
        <aside class='warn warnbox warnbox-margin'>
            <p><strong>Warning!</strong> just with these two functions. a game could get a rough estimate of the user's
                timezone.
        </aside>
        <aside class='info warnbox warnbox-margin'>
            <p><strong>Info!</strong> ANTRequest chooses that Formatting, Other Timezones, and datetime math belong in
                your Language's Library. Timezone Info like the Iana Timezone Database are planned as future extensions.
        </aside>
    </div>
    <h2 id=Stubs>Methods not implemented MUST be stubbed</h2>
    <p>Any of the following methods not chosen to be implemented MUST return <span class=sl><?= errorcode(-7) ?></span>
        without doing anything. (including but not limited to)
    <ul class=sl><?= implode(array_map(fn(string $string): string => "<li><code><a href=#$string>$string</a></code>",
                array(
                        'FileExtensionV1.file_open',
                        'FileExtensionV1.file_read',
                        'FileExtensionV1.file_type',
                        'FileExtensionV1.file_size',
                        'FileExtensionV1.file_write',
                        'FileExtensionV1.file_mtime',
                        'screenvideoV1.start_video_recording',
                        'screenvideoV1.stop_video_recording',
                        'screenvideoV1.take_video_snapshot',
                        'screenvideoV1.take_screenshot',
                        'antdatetime.utc_offset_ms_at',
                        'antdatetime.utc_now_ms',
                ))) ?></ul>
    <h2 id=ErrorCodes>Error Codes</h2>
    <p>the following table will list the error codes as negative numbers, their canonical upper snakecase name, and
        their description.
    <p>ANTRequest would like to reserve <span><?= -0x1 ?></span> to <span><?= -0xff ?></span> for the ANTRequest
        Specifications. while allowing the community to use <span><?= -0x100 ?></span> to
        <span><?= str_replace('_', ' ', underscoreNumber(-2 ** 31)) ?></span> (i32 limit) however they like.
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Code<th scope=col>UPPER_CASE_NAME<th scope=col>Description" ?></thead>
            <tbody><?= '<!-- AutomatedPHP -->';
            foreach ($errorCodes as $errorCode => $errorData) {
                echo "<tr id={$errorData['name']}><td>{$errorData['pref']}<td><code>"
                        . "{$errorData['name']}</code><td>{$errorData['desc']}";
            } ?></tbody>
        </table>
    </div>
</div>
