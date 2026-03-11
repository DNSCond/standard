<?php use function Helpers\json_fromArray;
use function Helpers\htmlspecialchars12;

require_once '../../createHeader.php' ?>
<div class=divs>
    <div class=sl>
        <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
        <p>this Standard Depends on <a href="<?= "/standard/FaviDiD/0.0.1/" ?>">FaviDiD</a> and <a
                    href="<?= "/standard/PlanetSpec/0.0.2/" ?>">PlanetSpec</a>.
        <p><dfn>ANT.zip</dfn> and <dfn>ANT.enx</dfn> (ENcrypted eXchange) are a signed format and an encrypted format
            respectively. they are used throughout the Favispecs which defines all sorts of use cases.
        <div><?= $GLOBALS['applicableWarning'] ?></div><h2 id=TableOfContents>Table Of Contents</h2>
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
        <pre><code><?= htmlspecialchars12(json_fromArray($jsonContent = json_decode(
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
            <dd>Traverse the path. if it is within the ZIPRoot the implementation SHOULD NOT
                load that  path. if the implementation does not want to do that or the path references a file
                outside the ZIPRoot they MAY alert the user and refuse to load the data, <a
                        href=#noUpwardDirectory>as the upward directory indicators (<code>..</code>)
                    and current directory indicators (<code>.</code>) are technically spec violations</a>.
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
        <h2 id=ANT-enx>ANT.enx</h2>
        <h3 id=Encrypt_ANT_Zip><var>Encrypt_ANT_Zip (<var>ANTZip</var>, <var>Public-Key-Recipient</var>)</h3>
        <p>to create an <code>ANT.enx</code> from an <code>ANT.zip</code> (<var>ANTZip</var>), MUST follow these steps.
        <ul>
            <li>Validate <var>ANTZip</var> using <a href=#ANT-zip>the Requirements above</a> Or Throw.
            <li>Set <var>$XKeyPublic</var> to converting the raw bytes of <var>Public-Key-Recipient</var> interpreted as
                Ed25519 to X25519.
            <li>Set <var>$ANTZip</var> to a newly created <code>ANT.zip</code> archive.
            <li>Set <var>$ANTZip-Encrypted</var> to the result of encrypting <var>ANTZip</var> using
                XChaCha20-Poly1305 and <var>$XKeyPublic</var> as public key.
            <li>Add <var>$ANTZip-Encrypted</var> to <var>$ANTZip</var> as <code>"/ANT.enx"</code>.
            <li>Set <var>$ANTZip</var>'s file extension to <code>"enx"</code>.
            <li>Sign <var>$ANTZip</var>.
            <li>Return <var>$ANTZip</var>.
        </ul>
    </div>
</div>
