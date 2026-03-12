<?php use function Helpers\json_fromArray;

require_once '../../createHeader.php' ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <div><?= $GLOBALS['applicableWarning'] ?></div>
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
        })(['Foundation/0.2.0']) ?></ul>
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
    <h2 id=KeyUse>Key usage</h2>
    <h3 id=Authentication>Authentication</h3>
    <p>authentication happens as follows in order.
    <ol class='sl li-margin05'>
        <li>the Edge MUST HTTP POST <code>/Favicond_/favidid/auth</code> to the Planet. with <code>F-FaviDiD:</code> set
            to the user's FaviDiD. if the Edge possesses a non-expired PlanetaryCode for this Planet, the Edge SHOULD
            send <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Authorization
                >Authorization</a>: PlanetaryCode &lt;PlanetaryCode></code>
        <li>If the &lt;PlanetaryCode> is a non expired session token, the planet MAY
            <a href=#successfulAuthentication>skip to Successful Authentication</a>.
        <li>if the Request does not contain a valid <code><a
                        href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Authorization
                >Authorization</a></code> corrosponding to a valid user in the Planet's data, the Planet MUST respond
            with <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/401>401</a></code> and
            <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/WWW-Authenticate
                >WWW-Authenticate</a></code> with a FaviDiD challenge. <a href=#FaviDiD_challenge>FaviDiD challenge is
                described below</a>. a Planet MAY add other FaviDiD Challenges but <code><?= (
                        'FaviDiD' . ("{$GLOBALS['major']}" === '0' ?
                                "{$GLOBALS['major']}-{$GLOBALS['minor']}"
                                : "{$GLOBALS['major']}")) ?></code> MUST be present for this specification.
            <ol>
                <li id=nonce-generation>Generate a Nonce and keep it for 300 Seconds. <span id=nonce-requirements>how you
                        create the Nonce is <a href="<?= "../../Foundation/0.2.0/#Planet-defined" ?>">Planet-defined</a>
                        but MUST be within the base58 alphabet. Nonce MUST be >=128 bits entropy (e.g. 22–32 random bytes ->
                    base58, resulting in ~30–44 chars). Planets MUST NOT generate nonces shorter than 16 bytes.</span>
                <li>associate the Nonce with the <code>F-FaviDiD:</code>.
                <li>Inset the just generated Nonce in the Nonce field.
            </ol>
        <li>the Edge MUST prompt the user with the Realm, the User MUST give informed consent to sign the challenge. the
            Refuse and Accept button MUST be displayed equally visible.
            <aside class='warnbox info'>
                <p><strong>Info!</strong> This should be common sense, but look what cookie banners have become. now
                    user can claim its a protocol violation. normally this wouldnt make it ino the spec as i dont
                    dictate UI.
            </aside>
        <li>If the user does not consent, the Edge MUST abort the operation.
        <li>If the user consents. the Edge MUST HTTP POST <code>/Favicond_/favidid/auth</code> with one of the following
            in the <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Authorization
                >Authorization</a></code> Header (concatenated),
            <ol>
                <li>the FaviDiD challenge chosen and then a space
                <li>the Base 64 Url equivalent of
                    <pre><code><?= htmlEncodeMinimal($header = json_fromArray([
                                    'typ' => 'JWT', 'alg' => 'EdDSA', 'proto' => 'FaviDiD-Auth',
                            ], false)) ?></code></pre>
                    and then a dot (which is <code><?= insert_wbr(htmlEncodeMinimal(base64UrlEncode(
                                $header)), 10) . '.' ?></code>) which is the JWT Header.
                    <aside class='warnbox info'><p><strong>Info!</strong> the exact string is Edge-Defined, as long as
                            these properties match these values after verifying and decoded.</aside>
                <li><a href=#payload>the JWT Payload described below</a> and then a dot.
                <li>the signature. signed by the private key of the one login in. sign the Base 64 Url JWT header and
                    Base 64 Url JWT Payload separated by a dot.
            </ol>
        <li id=verification-steps>The Planet MUST do the following in any order
            <ul>
                <li>Verify the Signature of the JWT as Received, using the Ed25519 algorithm, if that <code>alg</code>
                    isnt <code>EdDSA</code> the Planet MUST skip to <a href=#authenticationFailure>authentication
                        Failure</a>.
                <li>Verify the JWT's <code>aud</code> claim is equal to the Domain.
                <li>Verify the JWT's <code>iss</code> and <code>sub</code> are equal and are equal to the FaviDiD
                    associated with the Nonce.
                <li>Verify the current time is within the JWT's <code>nbf</code> and <code>exp</code>. ignore
                    <code>iat</code>.
                <li>Verify the JWT's <code>nonce</code> claim is correct.
            </ul>
        <li><p>Perform either one the following
            <dl class=dd-margin05>
                <dt id=successfulAuthentication>successful authentication.
                <dd><p>planets MUST respond with Status Code <code><a
                                    href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/200>200</a></code>
                        <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                            >Content-Type: application/json</a></code> and with the following
                    <div class=table-div>
                        <table class='flow-width sl'>
                            <thead><?= "<tr><th>Name<th>Value<th>Where" ?></thead>
                            <tbody>
                            <tr>
                                <td><code><var>proto</var></code>
                                <td>MUST be set to <code>FaviDiD-Auth</code>.
                                <td>Body
                            <tr>
                                <td><code><var>success</var></code>
                                <td>MUST be set to the JSON value <code>true</code>.
                                <td>Body
                            <tr>
                                <td><code><var>PlanetaryCode</var></code>
                                <td>MUST be a Cryptographically Secure Session token of <a
                                            href="<?= "../../Foundation/0.2.0/#Planet-Defined" ?>">Planet-Defined</a>
                                    generation. the cookie expiration advertised MUST be when the session token expires.
                                    MUST set the <code>Secure</code> flag, SHOULD set the <code>HttpOnly</code> flag.
                                <td>
                                    <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Set-Cookie
                                        >Set-Cookie</a></code>
                            <tr>
                                <td><code><var>nonce</var></code>
                                <td>MUST be set with the actual Nonce used for verification above, exactly.
                                <td>Body
                            </tbody>
                        </table>
                    </div>
                <dt id=authenticationFailure>authentication Failure
                <dd><p>planets MUST respond with Status Code <code><a
                                    href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/401>401</a></code>
                        <code><a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                            >Content-Type: application/json</a></code>
                    <pre><code><?= htmlEncodeMinimal(json_fromArray([
                                    'proto' => 'FaviDiD-Auth',
                                    'success' => false,
                            ])) ?></code></pre>
                    <div>
                        <p>The <a href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Retry-After
                            >HTTP Retry-After Header SHOULD be set (MUST be either an integer number of seconds (e.g.
                                300) or an HTTP-date string (RFC 7231).)</a> Indicating how long an <a
                                    href="<?= "../../Foundation/0.2.0/#Edge" ?>">Edge</a> SHOULD wait before retrying.
                            if the value signals a date after 1 hour compared to the <a
                                    href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Date
                            >HTTP Date Header</a> the <a href="<?= "../../Foundation/0.2.0/#Edge" ?>">Edge</a> MUST
                            abort automatic Retries, and SHOULD honor the Request, only retrying at User Request. if not
                            set Edges SHOULD interpret <code>Retry-After: 15</code>
                        <aside class='warnbox info'><p><strong>Info!</strong>
                                Retry-After SHOULD be included on 401 responses caused by nonce/signature failure,
                                indicating when the client MAY retry with a new nonce request.</aside>
                    </div>
                <dt>the response's <code><a
                                href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
                        >Content-Type</a></code> is not <code>application/json</code>
                <dd>The <a href="<?= "../../Foundation/0.2.0/#Edge" ?>">Edge</a> MUST NOT automatically Retry, the Edge
                    MUST inform the user the feature is unsupported. the Response body SHOULD be ignored.
            </dl>
    </ol>
    <h4 id=FaviDiD_challenge>FaviDiD challenge</h4>
    <p class=sl>a FaviDiD <code><a
                    href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/WWW-Authenticate
            >WWW-Authenticate</a></code> Challenge or <dfn>FaviDiD challenge</dfn> is a string as Follows
    <ul class=sl>
        <li>A FaviDiD challenge MUST start with <code>FaviDiD</code>.
        <li>A FaviDiD challenge MUST have the major version number directly concatenated after <code>FaviDiD</code>.
        <li>If the major version is 0 (zero) then dash and the minor version MUST added.
        <li>A FaviDiD challenge MUST have a <code>Realm</code> param.
        <li>A FaviDiD challenge MUST have a <code>Nonce</code> param.
        <li>Other Version's FaviDiD MAY have different meanings and requirements, a FaviDiD challenge the implementation
            does not support MUST be ignored.
    </ul>
    <h5 id=FaviDiD_challenge_examples>FaviDiD challenge (examples) (non-normative)</h5>
    <p>Valid ones include
    <ul class=sl>
        <li><code>FaviDiD0-2 Realm="My Realm" Nonce="<?= /** @noinspection PhpUnhandledExceptionInspection */
                base58_encode(random_bytes(17)) ?>"</code>
        <li><code>FaviDiD0-72 Realm="My Other Realm" Nonce="<?= /** @noinspection PhpUnhandledExceptionInspection */
                base58_encode(random_bytes(17)) ?>"</code>
        <li><code>FaviDiD35 Realm="antRequest.nl" Nonce="<?= /** @noinspection PhpUnhandledExceptionInspection */
                base58_encode(random_bytes(17)) ?>"</code>
        <li><code>FaviDiD1 Nonce="<?= /** @noinspection PhpUnhandledExceptionInspection */
                base58_encode(random_bytes(17)) ?>" Realm="index.php"</code>
    </ul>
    <p>invalid ones include
    <ul class=sl>
        <li><code>FaviDiD0-2 Nonce="<?= /** @noinspection PhpUnhandledExceptionInspection */
                base58_encode(random_bytes(17)) ?>"</code> (omits Realm)
        <li><code>FaviDiD0-2 Realm="My Other Realm"</code> (omits Nonce)
        <li><code>FaviDiD1.3 Nonce="<?= /** @noinspection PhpUnhandledExceptionInspection */
                base58_encode(random_bytes(17)) ?>" Realm="index.php"</code> (uses dot instead of minus)
        <li><code>FaviDiD.3 Nonce="<?= /** @noinspection PhpUnhandledExceptionInspection */
                base58_encode(random_bytes(17)) ?>" Realm="index.php"</code> (no major version)
    </ul>
    <h4 id=payload>FaviDiD JWT Payload</h4>
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
                <a href="<?= "../../Foundation/0.2.0/#Planet-Defined" ?>">Planet-Defined</a> way).
        <tr>
            <td><code><var>nonce</var></code>
            <td>MUST be set to the Nonce given by the Planet.
        </tbody>
    </table>
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
    <ol class=sl>
        <li>Set <var>$match</var> to the result of matching <var>did-favidid</var> against <code>/^did:favidid:ed25519:(.+)$/</code>
        <li>If no match, return <code>null</code>.
        <li>Set <var>$base58Part</var> to <var>$match</var> capturing group 1.
        <li>Decode <var>$base58Part</var> using base58 (Bitcoin alphabet, no padding) to get <var>$publicKeyBytes</var>.
        <li>If decoding fails or length of <var>$publicKeyBytes</var> != 32 bytes, return <code>null</code>.
        <li>Validate <var>$publicKeyBytes</var> as a valid Ed25519 public key (e.g., point on curve, proper encoding).
        <li>If invalid, return <code>null</code>.
        <li>Return <var>$publicKeyBytes</var> (the raw 32-byte Ed25519 public key).
    </ol>
    <aside class='warnbox warn'>
        <p><strong>FaviDiD profile note:</strong> FaviDiD does not natively resolve to a DID Document, an implementation
            MAY choose to construct one by following the following steps
        <ol class=sl>
            <li>Set <var>$publicKeyBytes</var> to the Returned <var>$publicKeyBytes</var> of the steps above.
            <li>Set <var>$publicKeyBytes</var> to the byte concatenation of <code>0xed</code> and
                <var>$publicKeyBytes</var>
            <li>Encode <var>$publicKeyBytes</var> using base58 (Bitcoin alphabet, no padding) to get
                <var>$base58Part</var>.
            <li>Resolve the string concatenation of <code>did:key:z</code> <var>$base58Part</var> using <a
                        href=https://w3c-ccg.github.io/did-key-spec/>https://w3c-ccg.github.io/did-key-spec/</a> and
                Return the Resulting DIDDocument.
        </ol>
    </aside>
    <h2 is=SecurityConsiderations>Security Considerations</h2>
    <h3 is=ThreatModel>ThreatModel</h3>
    <p>This specification assumes the Planet is hosted on a
        <a href="<?= "/standard/Foundation/0.2.0/#reasonably-capable-shared-hosting" ?>">
            Reasonably Capable Shared Hosting Server</a> as defined in Foundation 0.2.0.</p>
    <p>This specification assumes attackers can:</p>
    <ul>
        <li>Read, modify, and inject network packets</li>
        <li>Attempt replay attacks</li>
        <li>Submit malformed or malicious JWTs</li>
    </ul>
    <p>The following are OUT OF SCOPE:</p>
    <ul>
        <li>Compromised end-user devices</li>
        <li>Side-channel attacks on implementations</li>
        <li>Social engineering of users</li>
    </ul>
    <h3 id=mitigated-risks>Mitigated Risks</h3>
    <p>The protocol design addresses the following threats:</p>
    <div class=table-div>
        <table class=flow-width>
            <thead>
            <tr>
                <th>Risk</th>
                <th>Mitigation</th>
                <th>Spec Reference</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Replay attacks</td>
                <td>Single-use nonces with 300s expiry</td>
                <td><a href="#nonce-generation">§ Nonce Generation</a></td>
            </tr>
            <tr>
                <td>Signature forgery</td>
                <td>Ed25519 signatures (cryptographically secure)</td>
                <td><a href="#Keygen">§ Key Generation</a></td>
            </tr>
            <tr>
                <td>Nonce prediction</td>
                <td>CSPRNG requirement, ≥128 bits entropy</td>
                <td><a href="#nonce-requirements">§ Nonce Requirements</a></td>
            </tr>
            <tr>
                <td>Session hijacking</td>
                <td>HttpOnly, Secure cookies for PlanetaryCode</td>
                <td><a href="#successfulAuthentication">§ Successful Auth</a></td>
            </tr>
            <tr>
                <td>Algorithm confusion</td>
                <td>Explicit <code>"alg": "EdDSA"</code> check, rejection of others</td>
                <td><a href="#verification-steps">§ Verification Steps</a></td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 id=residual-risks>Residual Risks</h3>
    <p>Even with all mitigations applied, the following risks remain:</p>
    <dl class=dd-margin25>
        <dt>Lost private keys:
        <dd>No recovery mechanism exists.
        <dt>Compromised User Device:
        <dd>If user device is compromised, attacker can sign as user. This is outside the threat model.
        <dt>Poor randomness:
        <dd>Relies on host system CSPRNG quality. Implementations should check for sufficient entropy.
        <dt>Timing attacks:
        <dd>Ed25519 is designed constant-time, but implementation flaws could leak information.
        <dt>Rate-Limits exceeded:
        <dd class=sl>Implementations SHOULD implement <code><a
                        href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/429>429</a></code>,
            implementations MAY send a <code><a
                        href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Retry-After
                >HTTP Retry-After</a></code>. Implementations are encouraged to look for rate limiting resources
            elsewhere as defining it is out of scope. the exact details of Rate-Limits are <a
                    href="<?= "../../Foundation/0.2.0/#Planet-defined" ?>">Planet-defined</a>.
    </dl>
</div>
