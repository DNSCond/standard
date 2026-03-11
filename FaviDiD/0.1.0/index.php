<?php use function Helpers\json_fromArray;

require_once '../../createHeader.php';
global $major, $minor, $patch ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <p><dfn id=FaviDiD>FaviDiD</dfn> is a Brand New way to Decentralized Identify you. using some cryptography Math you
        can identify yourself using a Private Key to sign your Requests and a Public Key for the service to verify it is
        from you.
    <p>to use <a href=#FaviDiD>FaviDiD</a> you can use This Specification to insure interoperability. If you do not Like
        where this is going please fork.
    <div><?= $GLOBALS['applicableWarning'] ?></div><h2 id=TableOfContents>Table Of Contents</h2>
    <details open>
        <summary>Table Of Contents</summary>
        <!-- Favicond-render-TableOfContents -->
    </details>
    <h2 id=documentStatus>Status of this document</h2>
    <p>this document written on
        <time datetime=2026-03-10 data-tolocaltime=date-only>Tue Mar 10 2026</time>
        is <a href=https://semver.org/>Semantic version</a> <span><?= "$major.$minor.$patch" ?></span>.
        this document is self-published independently.
    <p>i am still Figuring things out. this is not final design and might be vague, impossible, or otherwise not
        implementable. im still seeking to bring this closer to my vision.
    <!--<h2 id=internal>This Specification Depends On</h2>
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
        })(['Foundation/0.1.0']) ?></ul>-->
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
    <h2 id=Keygen>Key Generation</h2>
    <p>use the Ed25519 and create a <dfn id=Private-Key><var>Private-Key</var></dfn> and a
        <dfn id=Public-Key><var>Public-Key</var></dfn>, store them securely.
    <aside class='danger warnbox'>
        <p><strong>Danger!</strong> WHEN THESE <a href=#Private-Key><var>Private-Key</var></a> and a
            <a href=#Public-Key><var>Public-Key</var></a> PAIR ARE LOST, THE USER LOST THEIR FAVIDID IDENTITY,
            THERE IS NO RECOVERY METHOD AS OF VERSION 0.0.1
    </aside>
    <h3 id=did-definition>Decentralized id (FaviDiD way)</h3>
    <p>To create a Decentralized id, MUST follow these steps
    <ol class='sl li-margin05'>
        <li><var>$Result</var> is string <code>did:favidid:</code>.
        <li>set <var>$Result</var> to the concatenation of <var>$Result</var> and <code>ed25519:</code>.
            <aside class='warnbox info'><p><strong>Info!</strong> future Specifications MAY allow other algorithms, this
                    one doesnt.</aside>
        <li><var>$publicKey</var> is the raw 32 bytes of <a href=#Public-Key><var>Public-Key</var></a>.
        <li>set <var>$publicKey</var> to the encoded bytes of <var>$publicKey</var> using base58 (Bitcoin alphabet, no
            padding).
        <li><var>$Result</var> is <var>$Result</var> concatenated with <var>$publicKey</var>.
        <li>Return <var>$Result</var>.
    </ol>
    <h4 id=did-def-alternative>Decentralized id (W3C did:key way) (equivalent)</h4>
    <p>This produces a standard <code>did:key</code> identifier using the exact same raw 32-byte Ed25519 public key. It
        is fully equivalent for signing and encryption purposes in this suite.</p>
    <ol class='sl li-margin05'>
        <li><var>$publicKey</var> is the raw 32 bytes of the <a href=#Public-Key>Public-Key</a>.</li>
        <li>Prepend <var>$publicKey</var> with the single byte <code>0xed</code> (multicodec prefix for Ed25519 public
            key).
        <li>Encode the resulting 33 bytes (<var>$publicKey</var>) using base58 (Bitcoin alphabet, no padding).</li>
        <li>Prepend the encoded string (<var>$publicKey</var>) with <code>z</code> (multibase prefix for base58-btc).
        <li>Prepend the result (<var>$publicKey</var>) with <code>did:key:</code>.</li>
        <li>Return the final string.</li>
    </ol>
    <aside class='warnbox info'>
        <p><strong>Info!</strong> This produces a conformant W3C <code>did:key</code> that resolves to the same Ed25519
            public key material. Use it when interacting with tools that only support standard DID methods.</p>
    </aside>
    <h2 id=Keygen>Key usage</h2>
    <h3 id=Authentication>Authentication</h3>
    <h4 id=AuthenticationForPlanets>For Planets</h4>
    <p>As A Planet you need to have some endpoints open for standardized connection. This Specification does not make
        any REQUIREments about how you store your data, just that the data is delivered in these Formats.
    <p>When an Edge wants to sign up for your planet. you (reference to Your Planet) MUST
    <ol>
        <li>The Request Path MUST be <code class=sl>/Favicond_/favidid/auth</code>, abort these steps for any other
            Request Path with <code class=sl><a
                        href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/400>400</a></code>.
        <li>Generate a Nonce and keep it for 300 Seconds. how you create the Nonce is <a
                    href=#Planet-Approximated>Planet-Approximated</a> but MUST be within the base58 alphabet. Nonce
            SHOULD be >=128 bits entropy (e.g. 22–32 random bytes → base58, resulting in ~30–44 chars). Planets MUST
            NOT generate nonces shorter than 16 bytes.
        <li>REQUIRE the Edge to give you their <a href=#did-definition>FaviDiD</a>
            (it is only their <a href=#Public-Key><var>Public-Key</var></a>, it is ok)
        <li><a href=http://localhost/standard/FaviDiD/0.1.0/#AuthenticationForEdges-FirstTime>the Edge MUST sign the
                Nonce in a JWT described below.</a>
        <li>You MUST check if the Nonce in their JWT Matches exactly as you gave it and that 300 seconds MUST NOT have
            passed.
        <li>if the Nonce matches create the user account, if it fails to match and that 300 seconds MUST NOT have
            passed, then fail the creation
    </ol>
    <aside class='warnbox info'><p><strong>Info!</strong>
            FaviDiD keeps itself out of how planets use sessions, that is not what FaviDiD is about so i wanted to let
            planets use any library (if any) they wish.
    </aside>
    <h5 id=OnFinish>On Finish</h5>
    <p>when those steps finish planets MUST return the following.
    <dl>
        <dt>successful authentication.
        <dd><p>planets MUST respond with Status Code <code class=sl><a
                            href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/200>200</a></code>
                <code class=sl><a
                            href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                    >Content-Type: application/json</a></code> and a Body with At Least the following Keys.
            <pre><code><?= htmlEncodeMinimal(json_fromArray([
                            'proto' => 'FaviDiD-Auth',
                            'success' => true,
                            'nonce' => '<Nonce>'
                    ])) ?></code></pre>
            <p>Session Information and How the User Keeps their login is Planet-Defined. FaviDiD only replaces
                Passwords, it does not dictate anything else.
            <p class=sl><code>&lt;Nonce></code> MUST be replaced with the actual Nonce used for verification above,
                exactly.
        <dt class=sl>Incorrect Nonce. (error code: <code>IncorrectNonce</code>)
        <dt class=sl>Invalid Signature. (error code: <code>InvalidSignature</code>)
        <dd><p>planets MUST respond with Status Code <code class=sl><a
                            href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/401>401</a></code>
                <code class=sl><a
                            href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                    >Content-Type: application/json</a></code>
            <pre><code><?= htmlEncodeMinimal(json_fromArray([
                            'proto' => 'FaviDiD-Auth',
                            'success' => false,
                            'errorCode' => '<errorCode>',
                            'nonce' => '<Nonce>',
                    ])) ?></code></pre>
            <div class=sl>
                <p>The <a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Retry-After
                    >HTTP Retry-After Header SHOULD be set (MUST be either an integer number of seconds (e.g.
                        300) or an HTTP-date string (RFC 7231).)</a> Indicating how long an <a href=#Edge>Edge</a>
                    SHOULD wait before retrying. if the value signals a date after 1 hour compared to the <a
                            href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Date
                    >HTTP Date Header</a> the <a href=#Edge>Edge</a> MUST abort automatic Retries, and SHOULD honor the
                    Request, only retrying at User Request. if not set interpret <code>Retry-After: 15</code>
                <aside class='warnbox info'><p><strong>Info!</strong>
                        Retry-After SHOULD be included on 401 responses caused by nonce/signature failure, indicating
                        when the client MAY retry with a new nonce request.</aside>
                <p><code>&lt;Nonce></code> MUST be replaced with the Planet Generated Nonce used for verification above,
                    exactly.
                <p><code>&lt;errorCode></code> MUST be replaced with the Failure Code described above.
            </div>
        <dt class=sl>the response's <code><a
                        href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                >Content-Type</a></code> is not <code>application/json</code>
        <dd>The <a href=#Edge>Edge</a> MUST NOT automatically Retry, the Edge MUST inform the user the feature is
            unsupported. the Response body SHOULD be ignored.
    </dl>
    <h4 id=AuthenticationForEdges>For Edges</h4>
    <p>The Request Path MUST be <code class=sl>/Favicond_/favidid/auth</code>
    <p>As An Edge you need to accept the Planet Nonces and sign them with that <dfn
                id=Private-Key><var>Private-Key</var></dfn> of the user.
    <p>When You Receive a <var>Planet Nonce</var> from The User. you MUST create a JWT (compact JWS, 3 base64url parts)
        with the header being (indentation whitespace is <a href=#Edge-Defined>Edge-Defined</a>)
    <pre><code><?= htmlEncodeMinimal(json_fromArray([
                    'typ' => 'JWT', 'alg' => 'EdDSA', 'proto' => 'FaviDiD-Auth',
            ])) ?></code></pre>
    <aside class='warnbox info'><p><strong>Info! (non-normative)</strong>
            FaviDiD is simple, once the JWT is signed, bytes MUST match exactly in order to verify, any base64url
            decoder and JSON parser should be capable of parsing it.
    </aside>
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
            <td>SHOULD be set 300 integers higher than <code><var>iat</var></code>.
                <aside class='warnbox info'><p><strong>Info!</strong> For Clock Skew.</aside>
        <tr>
            <td><code><var>iat</var></code>
            <td><span><?= 'MUST be set to the current time utc since the epoch of <code class=sl>' .
                    '<time datetime=1970-01-01T00:00:00.000Z>1970-01-01T00:00:00.000Z</time></code>.'
                    ?></span>
                <aside class='warnbox info'><p><strong>Info!</strong> Clock Skew doesnt apply here.</aside>
        <tr>
            <td><code><var>aud</var></code>
            <td>MUST be set to the Planet's Domain (it is assumed to be HTTPS as Edges and Planets MUST NOT use plain
                HTTP.
        <tr>
            <td><code><var>iss</var></code>
            <td>MUST be set to your <a href=#did-definition>did (FaviDiD)</a>.
        <tr>
            <td><code><var>sub</var></code>
            <td>MUST be set to your <a href=#did-definition>did (FaviDiD)</a>.
        <tr>
            <td><code><var>nbf</var></code>
            <td>SHOULD be set 50 integers lower than <code><var>iat</var></code>.
                <aside class='warnbox info'><p><strong>Info!</strong> For Clock Skew.</aside>
        <tr>
            <td><code><var>jti</var></code>
            <td>MUST be set to an uuid (is ignored in this specification, Planets MAY use this in a
                <a href=#Planet-Defined>Planet-Defined</a> way).
        <tr>
            <td><code><var>nonce</var></code>
            <td>MUST be set to the Nonce given by the Planet.
        </tbody>
    </table>
    <p class=sl>the Edge MUST HTTP POST <code>/Favicond_/favidid/auth</code> with the <code><a
                    href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Authorization
            >Authorization</a></code>, scheme MUST be <code>FaviDiD</code> value MUST be the JWT as described above.
    <h3 id=Resolution>Did Resolution</h3>
    <div class='warnbox info sl'><p><strong>FaviDiD Overview:</strong> FaviDiD uses two resolution paths — a restricted
            profile of <code>did:key</code> (Ed25519-only) and a new lightweight method
            <code>did:favidid:ed25519:...</code> optimized for raw key operations.</div>
    <h4 id=ResolutionFull>Did Resolution (Full)</h4>
    <p>The Did Resolution (Full) Algorithm only applies to <a href=#did-def-alternative>Decentralized id
            (W3C did:key way)</a> DiDs. for <a href=#did-definition>Decentralized id (FaviDiD way)</a> DiDs use <a
                href=#ResolutionBasic>Did Resolution (Basic)</a>
    <p class=sl>To resolve a <code>did:key</code> (<var>did-key</var>), Decode <var>did-key</var> using <a
                href=https://w3c-ccg.github.io/did-key-spec/>https://w3c-ccg.github.io/did-key-spec/</a>
        and Store the result in <var>did-doc</var>. check conformance with the following RuleSet
    <ul>
        <li>Only Ed25519 public keys are accepted (multicodec prefix <code>0xed</code>).
        <li>Resolution MUST fail (return error / null) for any other key type.
        <li>X25519 public keys MAY be converted to Ed25519 public keys. otherwise they MUST e Rejected.
    </ul>
    <p>If everything succeeds Return the raw bytes of the Public Key.
    <aside class='warnbox info'><p><strong>Info!</strong> in FaviDiD, i doubt you will need everything on the DID
            Document, almost all operations happen to the public key.</aside>
    <aside class='warnbox warn'>
        <p><strong>Non-standard behavior:</strong> This does NOT return a conformant DID Document per the did:key
            specification. Use only in FaviDiD-compatible contexts.
        <p>Implementation MAY choose to implement <a href=#AlternativeFlow>Alternative Flow (described below)</a>.
    </aside>
    <h5 id=AlternativeFlow>Alternative Flow</h5>
    <p>If everything succeeds, an Implementation MAY choose to return the DID Document produced by the spec's
        algorithm.
    <aside class='warnbox info'>
        <p><strong>FaviDiD profile note:</strong> In our ecosystem, most operations use only the raw Ed25519 public key
            bytes (extractable from <code>verificationMethod[0].publicKeyMultibase</code> after decoding). The full DID
            Document is returned for standards conformance, but may be ignored in simple FaviDiD flows.</p>
    </aside>
    <h4 id=ResolutionBasic>Did Resolution (Basic)</h4>
    <p>The Did Resolution (Basic) Algorithm only applies to <a href=#did-definition>Decentralized id (FaviDiD way)</a>
        DiDs. for <a href=#did-def-alternative>Decentralized id (W3C did:key way)</a> DiDs use <a
                href=#ResolutionFull>Did Resolution (Full)</a>
    <p class=sl>To resolve a <code>did:favidid</code> (<var>did-favidid</var>), SHOULD follow these steps
    <ul class="sl">
        <li>Set <var>$match</var> to the result of matching <var>did-favidid</var> against <code>/^did:favidid:ed25519:(.+)$/</code>
        <li>If no match, return <code>null</code>.
        <li>Set <var>$base58Part</var> to <var>$match</var> capturing group 1.
        <li>Decode <var>$base58Part</var> using base58 (Bitcoin alphabet, no padding) to get <var>$publicKeyBytes</var>.
        <li>If decoding fails or length of <var>$publicKeyBytes</var> != 32 bytes, return <code>null</code>.
        <li>Validate <var>$publicKeyBytes</var> as a valid Ed25519 public key (e.g., point on curve, proper encoding).
        <li>If invalid, return <code>null</code>.
        <li>Return <var>$publicKeyBytes</var> (the raw 32-byte Ed25519 public key).
    </ul>
    <aside class='warnbox warn'>
        <p><strong>FaviDiD profile note:</strong> FaviDiD does not natively resolve to a DID Document, an implementation
            MAY choose to construct one by altering the above steps.
    </aside>
    <h2 id=ExampleUsage>Example Usage (non-normative)</h2>
    <aside class='warnbox warn'><p><strong>Warning!</strong>
            i seem to have written a completely different Authentication flow, not sure if i want to keep this.
    </aside>
    <p>Here i describe the intended Authentication flow.
        <!-- crypto.getRandomValues(new Uint8Array(67)).toHex() -->
    <ol class='sl li-margin1'>
        <li>User visits a domain.
        <li>Domain supports "LogIn with FaviDiD"
        <li>User Clicks "LogIn with FaviDiD"
        <li>Website shows a <var>random long string</var>
            (for example <code><?= /** @noinspection PhpUnhandledExceptionInspection */
                (function (string $string, int $lengths): string {
                    $result = '';
                    $index = 0;
                    foreach (str_split($string) as $str) {
                        $result .= $str . (++$index % $lengths == 0 ? '<wbr>' : '');
                    }
                    return $result;
                })($nonce = base64UrlEncode(random_bytes(17)),
                        20);
                require_once "{$_SERVER['DOCUMENT_ROOT']}/require/JSONWT.php";
                function insert_wbr(string $s, int $every = 20): string
                {
                    return implode('<wbr>', str_split($s, $every));
                }

                function base64UrlEncode(string $data): string
                {
                    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
                }

                $iat = $_SERVER['REQUEST_TIME'] ?? time() ?></code>)<br>
            and says to sign it.
        <li>User copies the string and pastes it into their FaviDiD Authenticator app (the <a href=#Edge>Edge</a>).
        <li>Their FaviDiD Authenticator app does the following:
            <ol>
                <li>it takes the <var>random long string</var> converts it into
                    <pre><code><?= htmlEncodeMinimal($json = json_fromArray([
                                    'iss' => '/*my FaviDiD*/', 'sub' => '/*my FaviDiD*/', 'aud' => 'antrequest.nl',
                                    'iat' => $iat, 'nbf' => $iat - 50, 'exp' => $iat + 300, 'nonce' => $nonce,
                            ], false)) ?></code></pre>
                    and converts it into base64url form <code><?= insert_wbr($payload = base64UrlEncode($json))
                        ?></code><br>creating the JWT Payload.
                <li>it takes the following json
                    <pre><code><?= htmlEncodeMinimal($json = json_fromArray([
                                    'typ' => 'JWT', 'alg' => 'EdDSA', 'proto' => 'FaviDiD-Auth',
                            ], false)) ?></code></pre>
                    and converts it into base64url form <code><?= insert_wbr($header = base64UrlEncode($json))
                        ?></code><br>creating the JWT Header.
                <li>then it signs the two. <code><?= insert_wbr($signature = base64UrlEncode(
                                hash_hmac('sha256', "$header.$payload", $nonce, true)
                        )) ?></code><br>creating the JWT Signature.
                <li>it gives <var>the JWT</var> <code><?= insert_wbr("$header.$payload.$signature") ?></code><br>to the
                    user.
            </ol>
            <aside class='warnbox warn'><p><strong>Warning!</strong>
                    this illustrative JWT is created using the wrong algorithms,
                    but to people, it is all gibberish anyway.
            </aside>
        <li>The User puts <var>the JWT</var> in the Planet.
        <li>The Planet uses their Planet-Defined ways to create a session for the user.
    </ol>
</div>
