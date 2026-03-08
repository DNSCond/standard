<?php use function Helpers\json_fromArray;
use function Helpers\htmlspecialchars12;

require_once '../../createHeader.php';
global $major, $minor, $patch; ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <p><dfn id=FaviDiD>FaviDiD</dfn> is a Brand New way to Decentralized Identify you. using some cryptography Math you
        can identify yourself using a Private Key to sign your Requests and a Public Key for the service to verify it is
        from you.
    <p>to use <a href=#FaviDiD>FaviDiD</a> you can use This Specification to insure interoperability. If you do not Like
        where this is going please fork.
    <h2 id=TableOfContents>Table Of Contents</h2>
    <details open>
        <summary>Table Of Contents</summary>
        <!-- Favicond-render-TableOfContents -->
    </details>
    <h2 id=documentStatus>Status of this document</h2>
    <p>this document written on
        <time datetime=2026-03-07 data-tolocaltime=date-only>Sat Mar 07 2026</time>
        and
        <time datetime=2026-03-08 data-tolocaltime=date-only>Sun Mar 08 2026</time>
        is <a href=https://semver.org/>Semantic version</a> <span><?= "$major.$minor.$patch" ?></span>.
        this document is self-published independently.
    <h2 id=external>This Specification uses external references</h2>
    <ul class=li-margin05>
        <li><a href=https://www.rfc-editor.org/rfc/rfc2119>The keywords "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL
                NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
                interpreted as described in RFC 2119.</a>
        <li><a href=https://en.wikipedia.org/wiki/ZIP_(file_format)>ZIP (Wikipedia)</a>
        <li><a href=https://en.wikipedia.org/wiki/DEFLATE>DEFLATE (Wikipedia)</a>
        <li><a href=https://en.wikipedia.org/wiki/JSON>JSON (Wikipedia)</a>
        <li><a href=https://en.wikipedia.org/wiki/Decentralized_identifier>Decentralized identifier (Wikipedia)</a>
        <li><a href=https://en.wikipedia.org/wiki/EdDSA#Ed25519>Ed25519 (Wikipedia)</a>
        <li><a href=https://en.wikipedia.org/wiki/JSON_Web_Token>JSON Web Token (Wikipedia)</a>
    </ul>
    <h2 id=examples>Examples of FaviDiD in use</h2>
    link to examples, for now, there are none that fully implement this specification. should be an
    <code>&lt;ul&gt;</code> of links.
    <h2 id=defn>Definitions</h2>
    <dl class=sl>
        <dt>Planet
        <dd>A Server Implementing is called a Planet.
        <dt>Edge
        <dd>A Client Implementing is called an Edge. an edge is a client that interacts with the planet. could be a
            browser extension, the bowser, or an app.
        <dt>The Protocol
        <dd>The Protocol as set forth in This Specification
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
    </dl>
    <h2 id=GlobalRules>Global Rules</h2>
    <p>All interactions MUST follow these global rules. If these rules are violated, the Edge MUST abort the interaction
        and notify the user.
    <ul>
        <li>Applications MUST NOT connect to HTTP, Applications MAY try to change HTTP to HTTPS.
        <li>when a 404 or 500 is encountered Edges MUST read the <a
                    href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
            ><?= 'Content-Type' ?></a> Header, if it states <code>text/html</code> the Edge SHOULD ignore the body and
            state the feature isnt supported to the user.
            <aside class='warnbox info'><p><strong>Info!</strong> on shared hosting 404 or 500 with a
                    <code>text/html</code> mimetype is the default response for a file missing. when a file is missing
                    the Planet cannot properly handle this interaction</aside>
    </ul>
    <h2 id=Keygen>Key Generation</h2>
    <p>use the Ed25519 and create a <dfn id=Private-Key><var>Private-Key</var></dfn> and a
        <dfn id=Public-Key><var>Public-Key</var></dfn>, store them securely.
    <aside class='danger warnbox'>
        <p><strong>Danger!</strong> WHEN THESE <a href=#Private-Key><var>Private-Key</var></a> and a
            <a href=#Public-Key><var>Public-Key</var></a> PAIR ARE LOST, THE USER LOST THEIR FAVIDID IDENTITY,
            THERE IS NO RECOVERY METHOD AS OF VERSION 0.0.1
    </aside>
    <h3 id=did-definition>Decentralized id</h3>
    <p>To create a Decentralized id, MUST follow these steps
    <ol class='sl li-margin05'>
        <li><var>$Result</var> is string <code>did:favidid:</code>.
        <li>set <var>$Result</var> to the concatenation of <var>$Result</var> and <code>ed25519:</code>.
            <aside class='warnbox info'><p><strong>Info!</strong> future Specifications MAY allow other algorithms, this
                    one doesnt.</aside>
        <li><var>$publicKey</var> is bytes of <a href=#Public-Key><var>Public-Key</var></a>.
        <li>set <var>$publicKey</var> to the encoded bytes of <var>$publicKey</var> using base58 (Bitcoin alphabet, no
            padding).
        <li><var>$Result</var> is <var>$Result</var> concatenated with <var>$publicKey</var>.
        <li>Return <var>$Result</var>.
    </ol>
    <h3 id=Authentication>Authentication</h3>
    <h4 id=AuthenticationForPlanets>For Planets</h4>
    <h5 id=AuthenticationForPlanets-FirstTime>Signing Up</h5>
    <p>As A Planet you need to have some endpoints open for standardized connection. This Specification does not make
        any REQUIREments about how you store your data, just that the data is delivered in these Formats.
    <p>When an Edge wants to sign up for your planet. you (reference to Your Planet) MUST
    <ul>
        <li>Generate a Nonce and keep it for 30 Seconds. how you create the Nonce is <a
                    href=#Planet-Defined>Planet-Defined</a> but MUST be within the base58 alphabet.
        <li>REQUIRE the user to give you their <a href=#did-definition>FaviDiD</a>
            (it is only their <a href=#Public-Key><var>Public-Key</var></a>, it is ok)
        <li>the Edge MUST sign the Nonce in a JWT described below.
        <li>You MUST check if the Nonce in their JWT Matches exactly as you gave it and that 30 seconds MUST NOT have
            passed.
        <li>if the Nonce matches create the user account, if it fails to match and that 30 seconds MUST NOT have
            passed, then fail the creation
    </ul>
    <!---<p>when those steps finish planets MUST return the following.
    <dl>
        <dt>successful authentication.
        <dd>planets MUST
        <dt>
        <dd>
    </dl>-->
    <h4 id=AuthenticationForEdges>For Edges</h4>
    <h5 id=AuthenticationForEdges-FirstTime>Signing Up</h5>
    <p>As An Edge you need to accept the Planet Nonces and sign them with that <dfn
                id=Private-Key><var>Private-Key</var></dfn> of the user.
    <p>When You Receive a <var>Planet Nonce</var> from The User. you MUST create a JWT with the header being
        (whitespace is <a href=#Edge-Defined>Edge-Defined</a>)
    <pre><code><?= htmlspecialchars12(json_fromArray([
                    'typ' => 'JWT', 'alg' => 'EdDSA', 'proto' => 'FaviDiD-Auth',
            ])) ?></code></pre>
    <p>the Payload MUST or MUST NOT have the following claims.
    <table class='flow-width sl'>
        <thead>
        <tr>
            <th>Claim Name
            <th>Claim Value
        </thead>
        <tbody>
        <tr>
            <td><code><var>exp</var></code>
            <td>SHOULD be set 30 integers higher than <code><var>iat</var></code>.
        <tr>
            <td><code><var>iat</var></code>
            <td>MUST be set to the current time utc since the epoch of
                <time datetime=1970-01-01T00:00:00.000Z
                      style=font-family:monospace><?= '1970-01-01T00:00:00.000Z' ?></time>
                .
        <tr>
            <td><code><var>aud</var></code>
            <td>MUST be set to the Planet's Domain (assume HTTPS as Edges and Planets MUST NOT use plain HTTP.
        <tr>
            <td><code><var>iss</var></code>
            <td>MUST be set to your <a href=#did-definition>did (FaviDiD)</a>.
        <tr>
            <td><code><var>nbf</var></code>
            <td>SHOULD be set 5 integers lower than <code><var>iat</var></code>.
        <tr>
            <td><code><var>jti</var></code>
            <td>MUST be set to an uuid (is ignored in this specification, Planets MAY use this in a
                <a href=#Planet-Defined>Planet-Defined</a> way).
        <tr>
            <td><code><var>nonce</var></code>
            <td>MUST be set to the Nonce given by the Planet.
        </tbody>
    </table>
    <p>the Edge MUST return the JWT created to the planet.
</div>
