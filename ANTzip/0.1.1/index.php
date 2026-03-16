<?php use function Helpers\json_fromArray;
use function Helpers\htmlspecialchars12;

require_once '../../createHeader.php' ?>
<div class=divs>
    <div class=sl>
        <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
        <p><dfn id=ANT.zip>ANT.zip</dfn> and <dfn id=ANT.enx>ANT.enx</dfn> (ENcrypted eXchange) are a signed format and
            an encrypted format respectively. they are used throughout the Favispecs which defines all sorts of use
            cases.
        <div><?= $GLOBALS['applicableWarning'] ?></div>
        <h2 id=TableOfContents>Table Of Contents</h2>
        <details open>
            <summary>Table Of Contents</summary>
            <!-- Favicond-render-TableOfContents -->
        </details>
        <h2 id=documentStatus>Status of this document</h2>
        <p>this document written on
            <time datetime=2026-03-09 data-tolocaltime=date-only>Mon Mar 09 2026</time>
            is <a href=https://semver.org/>Semantic version</a>
            <span><?= "{$GLOBALS['major']}.{$GLOBALS['minor']}.{$GLOBALS['patch']}" ?></span>.
            this document is self-published independently.
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
            })(['Foundation/0.3.0', 'FaviDiD/0.3.0', 'PlanetSpec/0.0.2']) ?></ul>
        <h2 id=external>This Specification uses external references</h2>
        <ul class=li-margin05>
            <li><a href=https://www.rfc-editor.org/rfc/rfc2119>The keywords "MUST", "MUST NOT", "REQUIRED", "SHALL",
                    "SHALL NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
                    interpreted as described in RFC 2119.</a>
            <li><a href=https://en.wikipedia.org/wiki/PNG>PNG (Wikipedia)</a>
            <li><a href=https://en.wikipedia.org/wiki/ZIP_(file_format)>ZIP (Wikipedia)</a>
            <li><a href=https://en.wikipedia.org/wiki/DEFLATE>DEFLATE (Wikipedia)</a>
            <li><a href=https://en.wikipedia.org/wiki/JSON>JSON (Wikipedia)</a>
        </ul>
        <h2 id=ANT-zip>ANT.zip</h2>
        <h3 id=validity>The Zipfile Structure</h3>
        <p>to be a valid ANT.zip, the file MUST
        <ul>
            <li>Be a valid Zip Archive.
            <li>Use the DEFLATE algorithm.
            <li>Have a file called <code>ANT.json</code> in the ziproot.
            <li>Have a file called <code>ANT.sig</code> in the ziproot.
        </ul>
        <h3 id=ANT-json>The ANT.json Structure</h3>
        <p>to be a valid ANT.json, the file MUST
        <ul>
            <li>The Root Object MUST have a <code>did</code> key set to the author's FaviDiD.
            <li>The Root Object MUST have a <a href="<?= "/standard/PlanetSpec/0.0.2/#planet_json" ?>">Compat Object</a>
                at the <code>compat</code> key.
            <li>The Root Object MUST have a key called <code>fileIntegrity</code> with an array of
                <code><a href=#fileIntegrity>fileIntegrity</a></code> objects.
        </ul>
    </div>
    <h4 id=schema>The JSON Schema</h4>
    <details>
        <summary>Formal JSON Schema</summary>
        <pre><code><?= htmlEncodeMinimal(json_fromArray($jsonContent = json_decode(
                        file_get_contents('schema.json'),
                        true), 2));
                require_once '../../require.php' ?></code></pre>
    </details>
    <div class=sl>
        <h5 id=Root>The Root Object</h5>
        <div style=overflow-x:scroll;margin-top:1em>
            <table><?= createTBody($jsonContent) ?></table>
        </div>
        <h5 id=fileIntegrity>The fileIntegrity Object</h5>
        <div style=overflow-x:scroll;margin-top:1em>
            <table><?= createTBody($jsonContent['definitions']['fileIntegrity']) ?></table>
        </div>
        <h3 id=sign>Signing it</h3>
        <p>to sign an ANT.zip sign the <code>ANT.json</code> with the user's
            <a href="<?= "/standard/FaviDiD/0.0.1/#Private-Key" ?>"><var>Private-Key</var></a> and put it in
            <code>ANT.sig</code>
        <h3 id=PathSanitization>PathSanitization</h3>
        <p>While the Full Path Sanitization is implementation-defined,
            implementations MUST make sure the path follows these rules.
        <ul>
            <li>File names and directory names MUST NOT contain any of the following characters:
                <ul><?= '<li><code>' . implode("</code><li><code>",
                            ["\\", "/", ":", "*", "?", '&quot;', "&lt;", "&gt;", "|"]
                    ) . '</code>' ?></ul>
                These characters are forbidden on Windows; restricting them ensures maximum cross-platform
                interoperability.
            <li>Paths MUST use <code>/</code> as the path separator.
            <li id=noUpwardDirectory>Paths MUST NOT contain upward directory indicators (<code>..</code>) or current
                directory indicators (<code>.</code>).
            <li><strong id=noOutsideReferencesAllowed>Paths MUST NOT reference any files located outside the
                    ZIPRoot.</strong>
        </ul>
        <h3 id=applicationBehavior>Application Behavior</h3>
        <p>Implementations MUST handle errors gracefully to maintain user data integrity. If a required file is missing,
            the application SHOULD NOT crash, but rather notify the user. If the <code>ANT.json</code>
            file is invalid or corrupted, the application MUST alert the user and refuse to load the data.
            <a href=#PathSanitization>Sanitization of file paths</a> is REQUIRED to prevent path traversal
            vulnerabilities.
        <p>other than that programs SHOULD attempt to do the following when a spec Violation Occurs:
        <dl class=dd-margin25>
            <dt>A Path references a file outside the ZIPRoot.
            <dt>A Path references a no file nor directory.
            <dd>Alert the user and refuse to load the data.
            <dt>A Path contains an upward directory indicators (<code>..</code>)
                or current directory indicators (<code>.</code>).
            <dd>Alert the user and refuse to load the data.
            <dt>A Path contains Invalid Characters
            <dd>Alert the user and refuse to load the data.
            <dt>A Path's file does not match the integrity claimed in the <code>ANT.json</code>.
            <dt>The <code>ANT.json</code> doesnt verify with the <code>ANT.sig</code>.
            <dt>A path's File is missing.
            <dt>A JSON schema Required property is missing or otherwise violates the schema
            <dd>mark the entire <code>ANT.zip</code> as Untrusted
        </dl>
        <p> when an <code>ANT.zip</code> is marked as Untrusted, a Program MUST notify the user of the Untrusted status,
            a Program SHOULD be specific about what error was violated.
        <h3 id=Algorithms>Internal Algorithms</h3>
        <h4 id=Sign_ANT_Zip><var>Sign_ANT_Zip</var> (<var>ZipFile</var>, <var>My-Private-Key</var>)</h4>
        <p>to sign an <code>ANT.zip</code> (<var>ZipFile</var>) using <var>My-Private-Key</var>, MUST follow these
            steps.
        <ol class=li-margin05>
            <li>Validate <var>ZipFile</var> using the Requirements for a ZipFile Or Throw.
            <li>if any file is named <code>"ANT"</code> ignoring the extension then throw an Error.
                <aside class='info warnbox warnbox-margin'>
                    <p><strong>Info!</strong> therefore <code>ANT.zip</code> and <code>ANT.sig</code> have to be removed
                        before signing.
                </aside>
            <li>set <var>JSON</var> to
                <pre><code><?= htmlEncodeMinimal(json_fromArray([
                                'fileIntegrity' => array(),
                                'specVersion' => "{$GLOBALS['major']}.{$GLOBALS['minor']}.{$GLOBALS['patch']}",
                                'compat' => [
                                        "https://antrequest.nl/standard/PlanetSpec/0.0.2/" => [
                                                'main' => true,
                                        ],
                                        "https://antrequest.nl/standard/ANTzip/{$GLOBALS['major']}.{$GLOBALS['minor']}.{$GLOBALS['patch']}/" => [
                                                'main' => true,
                                                'DEFLATE' => true,
                                                'SHA256' => true,
                                                'SHA512' => true,
                                                'BLAKE3' => true,
                                        ],
                                ]
                        ], 2));
                        $json = htmlEncodeMinimal(json_fromArray([
                                'path' => '/*PATH*/',
                                'integrity' => '/*integrity*/',
                                'mimetype' => '/*mimetype*/'
                        ], 2)) ?></code></pre>
                (indentation whitespace is <a href="<?= "/standard/FaviDiD/0.3.0/#Edge-Defined" ?>">Edge-Defined</a>)
            <li>Set <var>Array</var> to the property <code>fileIntegrity</code> of <var>JSON</var>.
            <li>For every file in <var>ZipFile</var> Perform
                <ol class=li-margin05>
                    <li>append
                        <pre><code><?= str_replace('"/*PATH*/"', '&lt;The File\'s Path>',
                                        str_replace('"/*integrity*/"', 'The File\'s Integrity' .
                                                ' with an supported algorithm in the format &lt;algo>-&lt;base58Bytes>',
                                                str_replace('"/*mimetype*/"',
                                                        '&lt;the best guess of its mimetype>', $json)))
                                ?></code></pre>
                        to <var>Array</var> replacing my placeholders.
                </ol>
                <aside class='info warnbox'>
                    <p><strong>Info!</strong> needs work, splitting up the steps.
                </aside>
            <li><a href=https://tc39.es/ecma262/#assert>Assert:</a> <var>Array</var> is changed in the json i provided.
            <li>Write <var>JSON</var> to <var>ZipFile</var> as <code>"ANT.json"</code>.
                <aside class='info warnbox warnbox-margin'>
                    <p><strong>Info!</strong> If you want formatting do it now. after signing the content MUST NOT
                        change.
                </aside>
            <li>Store the bytes of <var>Bytes</var> in <var>ZipFile</var> as <code>"ANT.json"</code>.
            <li>Set <var>Bytes</var> to the detached Ed25519 signature of signing <var>Bytes</var> using
                <var>My-Private-Key</var>.
            <li>Set <var>BytesBase58</var> to encoding <var>Bytes</var> as Base58 (bitcoin alphabet, no paddng).
            <li>Store the UTF8 bytes of <var>BytesBase58</var> in <var>ZipFile</var> as <code>"ANT.sig"</code>.
            <li>Return <var>ZipFile</var>.
        </ol>
        <h2 id=ANT-enx>ANT.enx</h2>
        <h3 id=Encrypt_ANT_Zip><var>Encrypt_ANT_Zip</var> (<var>ANTZip</var>, <var>Public-Key-Recipient</var>)</h3>
        <p>to create an <code>ANT.enx</code> from an <code>ANT.zip</code> (<var>ANTZip</var>), MUST follow these steps.
        <ol class=li-margin05>
            <li>Validate <var>ANTZip</var> using <a href=#ANT-zip>the Requirements above</a> Or Throw.
            <li><a href=https://tc39.es/ecma262/#assert>Assert:</a> <var>Public-Key-Recipient</var> is the result of <a
                        href="<?= "/standard/FaviDiD/0.3.0/#Resolution" ?>">FaviDiD Resolution which returns the bytes
                    of a Public Key</a>
            <li>Convert the raw bytes of <var>Public-Key-Recipient</var> from Ed25519 to X25519 to produce
                <var>$XKeyPublic</var>. (The conversion algorithm is defined by the cryptography library,
                e.g., libsodium's <code>crypto_sign_ed25519_pk_to_curve25519</code>.)
            </li>
            <li>Generate an ephemeral X25519 keypair (<var>$ephemeralPrivate</var>, <var>$ephemeralPublic</var>).</li>
            <li>Compute <var>$sharedSecret</var> by performing an X25519 key exchange between
                <var>$ephemeralPrivate</var> and <var>$XKeyPublic</var>.
                <strong>X25519</strong> is an elliptic curve Diffie‑Hellman (ECDH) function
                (see <a href="https://en.wikipedia.org/wiki/Curve25519">Curve25519</a>). It combines a private key
                and a public key to produce a shared secret that only those two keys can generate.
                The recipient will independently compute the same secret using their private key
                and <var>$ephemeralPublic</var> (which will be included in the output).
            </li>
            <li>Generate a 24-byte random nonce <var>$nonce</var>.</li>
            <li>Encrypt <var>ANTZip</var> using XChaCha20-Poly1305 with <var>$sharedSecret</var> and <var>$nonce</var>
                to produce <var>$ciphertext</var>. If encryption fails, throw an error.
            </li>
            <li>Create <var>$ANTzip-Encrypted</var> as the byte concatenation of
                <var>$ephemeralPublic</var> (32 bytes) + <var>$nonce</var> (24 bytes) + <var>$ciphertext</var>.
            </li>
            <li>Create a new <code>ANT.zip</code> archive called <var>$new-ANT-enx</var>.</li>
            <li>Add <var>$ANTzip-Encrypted</var> to <var>$new-ANT-enx</var> as a file named <code>"Blob.enx"</code>.
            </li>
            <li>Change the file extension of <var>$new-ANT-enx</var> to <code>".enx"</code>.</li>
            <li><a href=#Sign_ANT_Zip>Sign <var>$new-ANT-enx</var> using <var>Sign_ANT_Zip</var></a>.</li>
            <li>Return <var>$new-ANT-enx</var>.</li>
        </ol>
        <aside class='info warnbox'>
            <p><strong>Info!</strong> This Zip in Zip structure is to store metadata about the ENX package. while its
                Zip in Zip, the inner Zip is encrypted.
        </aside>
    </div>
</div>
