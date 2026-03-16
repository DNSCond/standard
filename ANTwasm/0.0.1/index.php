<?php

require_once '../../createHeader.php' ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <div><?= $GLOBALS['applicableWarning'] ?></div>
    <p><dfn id=ANTwasm>ANT.wasm</dfn> is a Brand New way to run wasm safely and securely (or that us the goal).
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
        })(['Foundation/0.3.0', 'ANTzip/0.1.1']);
        /** @noinspection HtmlUnknownAnchorTarget */
        $errorCodes = [
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
                        'desc' => <<<HTML
                        A Path requiring a <a href=#WasmModule>Wasm Module</a>, but doesnt point to a valid compilable
                        <a href=#WasmModule>Wasm Module</a>.
                        HTML,
                ],
                '-4' => [
                        'name' => 'GAME_HASNT_STARTED_YET_ERROR',
                        'desc' => 'the <code><var>start_game</var></code> function hasnt been called by the' .
                                ' <a href=#cores>Core</a> when the function requires the game to be started.'
                ],
                '-5' => [
                        'name' => 'GAME_IS_RUNNING_ERROR',
                        'desc' => 'the <code><var>start_game</var></code> function has been called by the <a href=#cores>Core</a>' .
                                'when the function requires the game to be in the initialization phase (before' .
                                '<code><var>start_game</var></code>).'
                ],
                '-6' => [
                        'name' => 'NO_MORE_MEMORY_ERROR',
                        'desc' => 'The Operation requires more memory but it cant be granted',
                ],
                '-7' => [
                        'name' => 'WASM_PORT_MISSING',
                        'desc' => 'An Import or Export is missing',
                ],
        ];
        function errorcode(int $int): string
        {
            global $errorCodes;
            return "<a href=#{$errorCodes[$int]['name']}><code>{$errorCodes[$int]['name']}</code> ($int)</a>";
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
    </ul>
    <h2 id=Phases>Phases</h2>
    <p>Your Wasm package is called a game. Your Game has the following phases. in order of appearance</p>
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th scope=col>Name<th scope=col>Description" ?></thead>
            <tbody>
            <tr>
                <td>Loading Phase
                <td>Here is where the <a href="<?= "../../Foundation/0.3.0/#Edge" ?>">Edge</a> loads and verifies the
                    <a href="<?= "/standard/ANTzip/0.1.1/#ANT.zip" ?>">ANT.zip</a>. if it is marked as Untrusted the
                    Edge MUST abort proceeding.
            <tr>
                <td>Initialization Phase
                <td>Here is where you Initialize your game before it runs.
            <tr>
                <td>game Phase
                <td>Here is where you let the player play. this is activated by calling
                    <a href=#start_game><code><var>start_game</var></code></a>.
            </tbody>
        </table>
    </div>
    <div class=sl>
        <h2 id=InternalAbstractOperation>Internal Abstract Operation</h2>
        <p>It is assumed Imports and Exports are called to the invoking <a href=#WasmModule>Wasm Module</a>
            unless otherwise stated.
        <ul>
            <li>the notation <code>[[Variablename]]</code> is an internal variable type. wasm doesnt support strings so
                <code>[[String]]</code> is for the specification only is what i mean.
            <li>the notation <code>[[%FunctionName%]]</code> is an internal function name. they MUST NOT be exposed to
                any <a href=#WasmModule>Wasm Module</a>.
            <li>the notation <code>$Variablename</code> is an internal variable name used only in the internal function.
                they are unobservable outside it. even if they share the same name
        </ul>
        <p>If any of the following Invariants are violated, that is a spec bug.
        <h3 id=StringFromBytes><code><var>[[%StringFromBytes%]]</var> (<var>ptr</var>:
                i32, <var>len</var>: i32): [[String]]</code></h3>
        <p>Do the following in order
        <ol>
            <li>Go to <var>ptr</var> (inclusive) until (<var>ptr</var> + <var>len</var>) (exclusive) of the current <a
                        href=#WasmModule>Wasm Module</a>'s Linear Memory and take the raw bytes as <var>$string</var>.
            <li>Assume: <var>$string</var> is the UTF-8 bytes of the intended string.
            <li>If <var>$path</var> contains illegal UTF-8 characters return
                <span><?= errorcode(-1) ?></span>.
            <li>Return <var>$string</var>.
        </ol>
        <h3 id=BytesFromString><code><var>[[%BytesFromString%]]</var> (<var>string</var>: [[String]]): i32</code></h3>
        <p>Do the following in order
        <ol>
            <li><a href=https://tc39.es/ecma262/#assert>Assert:</a>: <var>$string</var> is encoded in UTF-8 bytes (is a
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
                <span><?= errorcode(-5) ?></span>.
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
            <li>call <var>$resolved</var>'s exported <code><a href=#load_module>load_module</a></code> if
                present with (<var>$wasmModulesLoaded</var> as <var>ModuleId</var>).
            <li>Return <var>$wasmModulesLoaded</var>.
        </ol>
    </div>
    <h2 id=BasicGlobalLibrary>Basic Global Library</h2>
    <p>All <a href=#WasmModule>Wasm Modules</a> MUST expose or be exposed to the following functions
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
            </tbody>
        </table>
    </div>
    <h2 id=Mains>Main</h2>
    <p class=sl>A <dfn>Main</dfn> is a wasm file that is called after a <a href=#cores>Core</a>. it is the main
        function.
    <p>If we are talking about User Generated Content games, A <a href=#cores>Core</a> is the actual Game where A
        <a href=#Mains>Main</a> is the core for a sharable package a user has created.
    <p>A Main MUST expose or be exposed to the following functions
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
                        <li>If Game has Started Return
                            <span><?= errorcode(-5) ?></span>.
                        <li>Set <var>$path</var> to Calling <code><var>[[%StringFromBytes%]]</var></code> giving
                            <code><var>ptr</var></code> and <code><var>len</var></code>.
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
                <td><code>(): void</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>Start the Game.
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
    <p>A Core MUST expose or be exposed to the following functions
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th>Name<th>Types<th>Import/Export<th>Description" ?></thead>
            <tbody>
            <tr id=load_module_core>
                <td><code><var>load_module_core</var></code>
                <td><code>(<var>ptr</var>: i32, <var>len</var>: i32): i32</code>
                <td>Import
                <td>Do the following in order
                    <ol>
                        <li>If Game has Started Return
                            <span><?= errorcode(-5) ?></span>.
                        <li>Set <var>$path</var> to Calling <code><var>[[%StringFromBytes%]]</var></code> giving
                            <code><var>ptr</var></code> and <code><var>len</var></code>.
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
                        <li>If Game has Started Return
                            <span><?= errorcode(-5) ?></span>.
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
    <h2 id=ErrorCodes>Error Codes</h2>
    <p>the following table will list the error codes as negative numbers, their canonical upper snakecase name, and
        their description.
    <div class=table-div>
        <table class='flow-width sl'>
            <thead><?= "<tr><th>Code<th>UPPER_CASE_NAME<th>Description" ?></thead>
            <tbody><?= '<!-- AutomatedPHP -->';
            foreach ($errorCodes as $errorCode => $errorData) {
                echo "<tr id={$errorData['name']}><td>{$errorData['pref']}<td><code>"
                        . "{$errorData['name']}</code><td>{$errorData['desc']}";
            } ?></tbody>
        </table>
    </div>
</div>
