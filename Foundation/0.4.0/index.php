<?php require_once '../../createHeader.php' ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <p>The <dfn id="spec-<?= $GLOBALS['spec'] ?>"><?= "{$GLOBALS['title']}" ?></dfn> Standard is a Meta Standard that
        defines things all other ANTRequest Standards use.
    <p>to use <a href="#spec-<?= $GLOBALS['spec'] ?>"><?= "{$GLOBALS['title']}" ?></a> you can use This Specification to
        insure interoperability. If you do not Like where this is going please fork.
    <div><?= $GLOBALS['applicableWarning'] ?></div>
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
    <h2 id=external>This Specification uses external references</h2>
    <ul class=li-margin05>
        <li><a href=https://www.rfc-editor.org/rfc/rfc2119>The keywords "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL
                NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
                interpreted as described in RFC 2119.</a>
    </ul>
    <h2 id=Scope>Scope of The Protocol</h2>
    <p>When "All interactions" is said i mean all interactions that happen within the protocol-defined-space, whatever
        happens outside the protocol-defined-space is not in scope and not goverened by these Specifications.
    <p>In simpler terms (non-normative), everything that hasnt anything to do with these specifications is not goverened
        by these Specifications.
    <h2 id=GlobalRules>Global Rules</h2>
    <p>All interactions MUST follow these global rules. If these rules are violated, the Implementation MUST abort the
        interaction and MAY notify the user (if any).
    <ul>
        <li><p>Applications MUST NOT connect to plain HTTP, Applications MUST try to change HTTP to HTTPS.
        <li><p>If TLS fails or Certificate is not valid, the Edge MUST abort the connection.
        <li><p>when a 404 or 500 is encountered Edges MUST read the <a
                        href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                ><?= 'Content-Type' ?></a> Header, if it states <code>text/html</code> the Edge SHOULD ignore the body
                and state the feature isnt supported to the user.
            <aside class='warnbox info'><p><strong>Info!</strong> on shared hosting 404 or 500 with a
                    <code>text/html</code> mimetype is the default response for a file missing. when a file is missing
                    the Planet cannot properly handle this interaction</aside>
        <li><p>Signature Verification MUST be done using the bytes As-Is (As Received), no Normalization,
                Canonicalization, Encoding change, or altering is allowed.
            <aside class='warnbox info'><p><strong>Info!</strong> This is important due to almost all algorithms
                    assuming the bytes havent been altered at all. swapping the keys or changing indentation of a JSON
                    or anything else will break the signature.
                <p>Normalization and Canonicalization is complex, and difficult. by Requiring no alteration, i can save
                    a lot of difficulties for those that dont want to. You can Pretty Print it after you verified the
                    thing.
                <p>Most Languages and Libraries come with JSON parsers, they do not come with JSON Canonicalization
                    Engines. Most Languages and Libraries come with cryptographic functions that work on bytes. so by
                    not allowing JSON Canonicalization at all, i can save a lot of difficulties for those that dont want
                    to. and again, You can Pretty Print it after you verified the thing.</aside>
        <li>Interactions MUST be in the encoding UTF-8.
    </ul>
    <h2 id=Philosophy>Philosophy</h2>
    <p>ANTRequest's Specicications MUST follow these.
    <ul class='li-margin05 sl'>
        <li><p>The core specifications should be minimal, stable, and widely implementable.
                They define the foundation, the shared rules that let everything else work together.
            <p>Everything else belongs in:
            <ul>
                <li>Extensions: Optional additions that build on the core
                <li>Forks: Divergent visions that explore different trade-offs
                <li>Companion specs: Entirely new specifications that depend on this one
            </ul>
            <p>This isn't about saying "no" to features. Its about saying "that belongs elsewhere".
        <li><p>When considering changes, i prioritize
            <ul>
                <li>Security vulnerabilities: Anything that breaks safety must be fixed.
                <li>Ambiguities: Unclear specifications lead to incompatible implementations.
                <li>Implementability: Specs that can't be built are useless.
            </ul>
            <p>Feature requests are valuable, but they usually belong in extensions or forks,
                not the core. This keeps the foundation stable while the ecosystem innovates.
        <li><p><strong>Forks Are Features, Not Failures</strong>
            <p>A healthy ecosystem has multiple approaches. The <code>extends</code> mechanism exists
                to make forking technical and explicit.
            <p>When i say "please fork" i mean it. i will:
            <ul>
                <li>Link to interesting forks from our documentation
                <li>Celebrate divergence that explores new ideas
                <li>Never treat forks as competition
            </ul>
            <p>If you need something the core doesn't provide, <em>build it</em>. That's how the ecosystem grows.
        <li>Features cannot be bought. Period. ANTRequest Specifications serve everyone, not just those who can pay.
            Forking can be done instead (which is free!).
        <li>Everything MUST work on a Standard Supported Shared Host, however i do not appease anyone who just calls
            themselves shared host. <a href=#Standard-Supported-Shared-Host>they must be a Standard Supported Shared
                Host and MUST support at least this.</a>
    </ul>
    <h3 id=Governance>Governance</h3>
    <div class=sl>
        <p>Today and in all future releases as far as i know, all decisions related to the ANTRequest Specifications on
            this site are made by me. this might have some implications which is why Forking is built into the
            ANTRequest Specifications. when im gone, the dominant fork probably exists, you can move to them. but if
            your complaints
        <ul>
            <li>raise a security vulnerability?
            <li>raise an ambiguity that should have been standardized?
            <li>raise ways in how the standard became illegal to implement as written?
        </ul>
        <p>i will listen. if you disagree with a core decision, please fork.
            The <a href="<?= "../../FaviDiD/0.3.1/#extends" ?>"><code>extends</code></a> mechanism exists for this.
    </div>
    <h3 id=Storage-Supported-Host>Storage Supported Host</h3>
    <p>A <dfn id=dfn-Storage-Supported-Host>Storage Supported Host</dfn> MUST (including but not limited
        to)
    <ul class='li-margin05 sl'>
        <li>Access to <a href=https://www.php.net/supported-versions.php>At Least 1 Actively Supported PHP Version</a>.
        <li>Access to HTTPS. An SSL/TLS certificate is Required for this.
        <li>Access to a way to persistent data storage.
        <li>Access to the file system. readonly is reasonable but readwrite is better.
        <li>Access to a secure Cryptographically Secure Pseudo Random Number Generator.
        <li>Access to JSON parsing and writing.
    </ul>
    <h3 id=Standard-Supported-Shared-Host>Standard Supported Shared Host</h3>
    <p>A <dfn id=dfn-Standard-Supported-Shared-Host>Standard Supported Shared Host</dfn> MUST
    <p>With Standard Supported i mean a Shared Host that lets you use Web Standards without too much hassle
    <ul class='li-margin05 sl'>
        <li>Qualify for <a href=#Storage-Supported-Host>Storage Supported Host</a>.
        <li>Access to the database via php.
        <li>Access to the file system with readwrite permission.
        <li>Access to a way to redirect End User URLs to Actual Files (for example <a
                    href=https://httpd.apache.org/docs/current/mod/mod_rewrite.html>mod_rewrite</a>).
        <li>Access to setting the following Headers (including but not limited to):
            <ul>
                <li>
                    <a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Strict-Transport-Security
                    >HTTP Strict Transport Security</a>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cache-Control
                        >Cache-Control</a></code>.
                <li>All <a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CORS>CORS Response headers</a>.
                <li><code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                        >Content-Type</a></code>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy
                        >Content-Security-Policy</a></code>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy-Report-Only
                        >Content-Security-Policy-Report-Only</a></code>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Link>Link</a></code>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Last-Modified
                        >Last-Modified</a></code>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Set-Cookie
                        >Set-Cookie</a></code>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Vary
                        >Vary</a></code>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/WWW-Authenticate
                        >WWW-Authenticate</a></code>.
                <li>Custom Headers.
            </ul>
        <li>Access to getting the following Headers unmodified (including but not limited to):
            <ul>
                <li><code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Authorization
                        >Authorization</a></code>
                <li>All <a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CORS>CORS Request headers</a>.
                <li><code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                        >Content-Type</a></code>.
                <li><code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cookie
                        >Cookie</a></code>.
                <li>
                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/X-Content-Type-Options
                        >X-Content-Type-Options</a></code>.
                <li>Custom Headers.
            </ul>
        <li>Access to a secure cryptographical Library. at time of writing: including but not limited to:
            <ul>
                <li><code>libsodium</code> - for Ed25519 signatures and XChaCha20-Poly1305 encryption.
            </ul>
        <li>MUST NOT alter body bytes in transit.
    </ul>
    <h2 id=defn>Definitions</h2>
    <dl class=sl>
        <dt id=FaviSpecs>FaviSpecs
        <dt id=ANTRequestSpecifications>ANTRequest Specifications
        <dd>The specifications on this site.
        <dt id=Planet>Planet
        <dd>A Server Implementing is called a Planet.
        <dt id=Edge>Edge
        <dd>A Client Implementing is called an Edge. an edge is a client that interacts with the planet. could be a
            browser extension, the browser, or an app.
        <dt>Implementation
        <dd>A software instance of an <a href=#Edge>Edge</a> or <a href=#Planet>Planet</a> or both.
        <dt>The Protocol
        <dd>The Protocol as set forth in This Specification.
        <dt>The Specification
        <dd>The Specification is this htmlpage.
        <dt id=/Favicond_/><code>/Favicond_/</code>
        <dd>The Base Path of the protocol interactions. the underscore is a MUST. this path is directly after the
            domain.
        <dt>REQUIRE
        <dd>RFC2119's REQUIRED
        <dt id=Edge-Defined>Edge-Defined
        <dt id=Planet-Defined>Planet-Defined
        <dd>A feature whose specific behavior is determined by the host environment rather than this specification.
            While this document may provide constraints, the Planet or Edge is free to define the exact behavior within
            those bounds.
        <dt id=Edge-Approximated>Edge-Approximated
        <dt id=Planet-Approximated>Planet-Approximated
        <dd>A feature whose specific behavior is determined by the host environment rather than this specification.
            While this document may provide constraints, the Planet or Edge is free to define the exact behavior within
            those bounds, but preferably reacts like the recommending an ideal behavior.
    </dl>
    <h2 id=ai>Ai Acknowledgement</h2>
    <p>Thanks to DeepSeek Gemini, Grok, and ChatGPTfor guidance writing all these specifications while proofreading
        (except LayerZip).
</div>
