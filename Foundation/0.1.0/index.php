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
    <h2 id=GlobalRules>Global Rules</h2>
    <p>All interactions MUST follow these global rules. If these rules are violated, the Implementation MUST abort the
        interaction and MAY notify the user (if any).
    <ul>
        <li><p>Applications MUST NOT connect to plain HTTP, Applications SHOULD try to change HTTP to HTTPS.
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
    </ul>
    <h2 id=defn>Definitions</h2>
    <dl class=sl>
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
</div>