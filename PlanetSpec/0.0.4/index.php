<?php use function Helpers\json_fromArray;
use function Helpers\htmlspecialchars12;

require_once '../../createHeader.php' ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <p><dfn id=FeatureTesting>Feature Testing</dfn> is the practice of discovering which features a Planet supports.
        This specification intentionally keeps feature discovery simple.
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
        <span><?= $semver = "{$GLOBALS['major']}.{$GLOBALS['minor']}.{$GLOBALS['patch']}" ?></span>.
        this document is self-published independently.
    <h2 id=external>This Specification uses external references</h2>
    <ul class=li-margin05>
        <li><a href=https://www.rfc-editor.org/rfc/rfc2119>The keywords "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL
                NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
                interpreted as described in RFC 2119.</a>
    </ul>
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
            domain. (<code>https://{your-domain}/Favicond_/</code>)
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
    <h2 id=planet_json>planet.json</h2>
    <p class=sl><code><dfn>planet.json</dfn></code> is a file in your <a
                href="<?= "../../FaviDiD/0.3.1/#/Favicond_/" ?>"><code>/Favicond_/</code></a>
        folder (the <a href="<?= "../../FaviDiD/0.3.1/#/Favicond_/" ?>"><code>/Favicond_/</code></a> folder MUST be
        after the domain, so it MUST be at <code>https://{your-domain}/Favicond_/planet.json</code>). which states some
        information of the planet. A valid <code>planet.json</code> file follows all these requirements
    <ul class='sl li-margin05'>
        <li>Edges MUST fetch it via HTTPS and SHOULD verify the <code>Content-Type</code> is
            <code>application/json</code>.
        <li>At the Root Object in the JSON you MUST have a key called <code>compat</code> with as value an object,
            this object is called <dfn>the compat object</dfn>.
        <li>inside the compat object, keys MUST be absolute HTTPS URLs identifying the specifications and the values
            MUST be objects. the objects MUST have Feature-Slugs and values MUST be booleans
        <li>These absolute HTTPS URLs identifying the specifications SHOULD define what their Feature-Slugs mean. This
            Specification makes no demands for meanings.
        <li>Each of the Feature-Slugs MUST have a boolean indicating whether the feature of that specification is
            supported.
        <li>A Feature-Slug MUST match this ECMAScript flavored RegExp <code>/^[A-Za-z0-9\-_]+$/</code>. slugs are
            case-sensitive.
        <li>URLs MUST be HTTPS URLs
        <li>A Feature-Slug MUST NOT be
            <ul>
                <li><code>extends</code>
            </ul>
        <li>A Feature-Slug MUST NOT start with <code>Favicond_</code>.
            <aside class='warnbox info'><p><strong>Info!</strong> this is done for future compatibility.</aside>
        <li>the URL <code><?= "<a href=https://antrequest.nl/standard/PlanetSpec/$semver/>" .
                preg_replace('/(?<!<)\\//', '<wbr>/',
                        "https://antrequest.nl/standard/PlanetSpec/$semver/</a>"
                ) ?></code>
            and the Feature-Slug <code>main</code> MUST be listed as supported.
            <aside class='warnbox info'><p><strong>Info!</strong> this is done for Forks. when a fork is incompatible
                    with This Specification, it MUST list
                    <code><?= "<a href=https://antrequest.nl/standard/PlanetSpec/$semver/>" .
                        preg_replace('/(?<!<)\\//', '<wbr>/',
                                "https://antrequest.nl/standard/PlanetSpec/$semver/</a>"
                        ) ?></code> as <code>false</code> or omit the url altogether.
            </aside>
        <li>An <code>extends</code> key where the Feature-Slugs MUST link to a specification linked in the compat object
            or be Omitted entirely.
    </ul>
    <p>A <dfn>Feature-Slug</dfn> is a string set forth by a Specification Linked to by the url which is the parent
        object's key.</p>
    <aside class='warnbox info'><p><strong>Info!</strong> without a Planet Update in the files. they dont magically
            support new features. while you are in sftp you might as well flip a boolean around. that is why this works.
    </aside>
    <h2 id=examples><?= "Examples of {$GLOBALS['spec']}" ?></h2>
    <pre><code><?= htmlspecialchars12(json_fromArray(['compat' => [
                    "https://antrequest.nl/standard/PlanetSpec/$semver/" => [
                            'main' => true,
                    ],
                    'https://antrequest.nl/standard/FaviDiD/0.3.1/' => [
                            'keygen' => true,
                            'auth' => true,
                            'newProto' => false,
                    ],
                    'https://antrequest.nl/standard/FaviDiD/0.1.1/' => [
                            'extends' => 'https://antrequest.nl/standard/FaviDiD/0.3.1/',
                            'newProto' => true,
                    ],
            ]])) ?></code></pre>
    <h2 id=ErrorHandling>Error Handling</h2>
    <p>Edges SHOULD attempt to do the following when parsing:
    <dl class='dd-margin25 sl'>
        <dt>An URL is HTTP, not HTTPS.
        <dd>the Edge SHOULD ignore that url and its feature slugs.
        <dt>An <code>extends</code> URL is HTTP, not HTTPS.
        <dd>the Edge SHOULD ignore that url and its feature slugs. it SHOULD also ignore any children of the extended
            url.
        <dt>A REQUIRED feature is listed as unsupported as set forth by the Specification Linked
        <dd>the Edge SHOULD do as set forth in that Linked Specification, otherwise the Edge MAY ignore the entire
            Specification and its Feature-Slugs.
        <dt>An OPTIONAL feature is listed as unsupported as set forth by the Specification Linked
        <dd>the Edge SHOULD do as set forth in that Linked Specification. otherwise the Edge SHOULD ignore the section
            of that Specification and not too much else and its Feature-Slugs.
        <dt>a Feature-Slug is absent.
        <dt>the <code>extends</code> Feature-Slug (key) is a boolean.
        <dt>the <code>extends</code> Feature-Slug (key) points not to a standard listed in the compat object.
        <dt>a Feature-Slug (which is not <code>extends</code>) is not a boolean.
        <dd>ignore it, and treat it as if it was false.
        <dt>the <code>planet.json</code> isnt valid json or it wasnt served with 200
        <dd>Resolve <code>{"compat":{}}</code> instead, basically nothing is supported
        <dt>a <code>planet.json</code> lists <code><?= "<a href=https://antrequest.nl/standard/PlanetSpec/$semver/>" .
                preg_replace('/(?<!<)\\//', '<wbr>/',
                        "https://antrequest.nl/standard/PlanetSpec/$semver/</a>"
                ) ?></code> as <code>false</code>.
        <dd>this is <a href=#Edge-Approximated>Edge-Approximated</a>. the ideal behavior is as follows: Edges MAY cancel
            the whole operation. Edges SHOULD do as the Fork (if any) set forth unless that is impossible or otherwise
            unachievable (for example violates applicable law).
    </dl>
    <h2 id=Extendable>Extendable Features</h2>
    <p class=sl>if a standard is forked (like i encourage mine to be) you might inherit features from them. which is
        where the <dfn id=extends><code>extends</code></dfn> comes in. if a feature doesnt exist in this feature object
        the Edge MUST go to the <code>extends</code> object and look there.
    <p class=sl>Edges MUST detect cycles in the <code>extends</code> chain (e.g., A -&gt; B -&gt; C -&gt; A) and return
        <code>false</code> for the feature if a cycle is detected.
    <div class='sl li-margin05'>
        <h2 id=ResolveSupported><var>Resolve_Supported</var> (<var>compat</var>, <var>url</var>, <var>slug</var>,
            <var>seen</var>)</h2>
        <p>to determine whether a feature is supported follow these steps given a compat object (<var>compat</var>), a
            Specification URL (<var>url</var>), a Feature-Slug (<var>slug</var>), and an array (<var>seen</var>) SHOULD
            be Empty if it isnt a Recursive Call.
        <ul>
            <li>Set <var>$SlugObject</var> to the Result of getting the property <var>url</var> of <var>compat</var>
                (that is <code><var>compat</var>[<var>url</var>]</code> in ECMAScript)
            <li>Set <var>$BooleanResult</var> to Result of getting the property <var>slug</var> of<var>$SlugObject</var>
            <li>If <var>$BooleanResult</var> is a boolean:
                <ul>
                    <li>Return <var>$BooleanResult</var>
                </ul>
            <li>Else:
                <ul>
                    <li>If <var>url</var> is in <var>seen</var> then Return <code>false</code>.
                    <li>append <var>url</var> to <var>seen</var>.
                    <li>Set <var>$RecursiveURL</var> to Result of getting the property <code>extends</code> of
                        <var>$SlugObject</var>
                    <li>If <var>$RecursiveURL</var> is not a string then Return <code>false</code>.
                    <li>Call <var>Resolve_Supported</var> with (<var>compat</var>, <var>$RecursiveURL</var>,
                        <var>slug</var>, <var>seen</var>) and Return the result.
                </ul>
        </ul>
    </div>
    <h2 id=caching>Caching</h2>
    <!--(fn(string$string):string=>str_replace('<','&lt;',str_replace('&','&amp;',$string)))-->
    <p>Edges SHOULD respect caching headers. caching headers Planets set is Planet-Defined.
    <h2 id=Authornotes>Author's Notes</h2>
    <p>please tell me how i did. it was my first time writing something like this. this is self-published independently.
</div>
