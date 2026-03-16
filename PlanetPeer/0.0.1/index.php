<?php use function Helpers\htmlspecialchars12;
use function Helpers\json_fromArray;

require_once '../../createHeader.php' ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <p>Discovery is important. but other specifications and implementations might have problems with initialization.
    <p>Like how are you going to find planets when you have no idea how to access a planet's computer address
    <p>and by <dfn>computer address</dfn> i mean all methods a computer can identify each-other, like ip and domains
        (dns).
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
        })(['Foundation/0.4.0', 'PlanetSpec/0.1.0']) ?></ul>
    <h2 id=external>This Specification uses external references</h2>
    <ul class=li-margin05>
        <li><a href=https://www.rfc-editor.org/rfc/rfc2119>The keywords "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL
                NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
                interpreted as described in RFC 2119.</a>
        <li><a href=https://www.rfc-editor.org/rfc/rfc3339>Datetime Format</a>
    </ul>
    <h2 id=Why>Why?</h2>
    <p>While you use the web like a regular person, you might not have any idea how Decentralized systems work.
        <dfn>Decentralized</dfn> means without a central authority, by usually it is an "almost". while ANTRequest's
        Specifications are also an "almost". i try to lessen the impact of ANTRequest's eventul disappearance by
        promoting <a href="<?= "../../Foundation/0.4.0/#Philosophy" ?>">"<em>Fork Build Deploy</em>" as seen in the
            Philosophy section of the Foundation</a>.
    <p>But you cant have a Decentralized Network without having a Network, and you cant have a Network without finding
        other Nodes. <a href=#planetpeer_json><code class=sl>planetpeer.json</code></a> solves the "find more Planets
        using a starting point". but you might not have a starting point, that is the <dfn>bootstrapping problem</dfn>
        how to find your first planet when you have none.
    <h2 id=planetpeer_json><code class=sl>planetpeer.json</code></h2>
    <p class=sl>A <dfn id=planetpeer.json><code>planetpeer.json</code></dfn> is a JSON file that MUST be accessible at
        <code>https://{domain}<a href="<?= "../../FaviDiD/0.3.1/#/Favicond_/" ?>"><code>/Favicond_/</code></a>planetpeer.json</code>
        where <code>{domain}</code> is the planet's domain. a <a href=#planetpeer.json><code>planetpeer.json</code></a>
        MUST follow these rules too
    <ul class=sl>
        <li>A <a href=#planetpeer.json><code>planetpeer.json</code></a> Root MUST be an object.
        <li>A <a href=#planetpeer.json><code>planetpeer.json</code></a> Root MUST contain the key
            <code>"PlanetaryPeers"</code>, it MUST be an array, even if empty.
        <li>The array at <code>"PlanetaryPeers"</code> MUST contain <a href=#PlanetaryPeer>Planetary Peer</a> objects.
        <li>A <a href=#planetpeer.json><code>planetpeer.json</code></a> MUST be valid JSON and SHOULD be sent with
            <code>appliaction/json</code>.
    </ul>
    <h3 id=PlanetaryPeer>Planetary Peers</h3>
    <p>In a normal Decentralized system, there are only peers, almost no servers , but in the ANTRequest Specification
        ecosystem Servers (called Planets) are an important part of the ecosystem, where Peer means <a
                href="<?= "../../Foundation/0.4.0/#Edge" ?>">Edges</a>, <dfn><a href=#PlanetaryPeer>Planetary
                Peer</a></dfn> means Peers as Planets.
    <p>To Support a Decentralized Network, Planets are recommended to have as many <a href=#PlanetaryPeer>Planetary
            Peers</a> as possible as they wish. <a href=#PlanetaryPeer>Planetary Peer</a> object's
        structure is at follows. (the Field description MUST be followed, if you do not wish to implement a field, do
        not include it in the JSON)
    <div class=table-div>
        <table>
            <thead><?= "<tr><th scope=col>Field Name<th scope=col>Field Type<th scope=col>Required<th scope=col>Field Description" ?></thead>
            <tbody>
            <tr>
                <td><code>Origin</code>
                <td>string
                <td>MUST
                <td>the plate's Origin, <a
                            href=https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Origin>in the
                        format of the Origin Header</a>, Ports and Plain HTTP schemes MUST not be listed at all.
            <tr>
                <td><code>LastFetched</code>
                <td>string
                <td>MAY</td>
                <td>When the current planet Last Fetched the planet's <a
                            href="<?= "../../PlanetSpec/0.4.0/#planet_json" ?>"
                    ><code>planet.json</code></a> in the RFC 3339 Format.
            </tbody>
        </table>
    </div>
    <aside class='warnbox info'><p><strong>Info!</strong> this is an object for future compatibility. Additional fields
            MAY be added in future versions. <a href="<?= "../../Foundation/0.4.0/#Edge" ?>">Edges</a> MUST ignore
            fields they don't understand.</aside>
    <h2 id=PlanetSpec>PlanetSpec FeatureSlugs</h2>
    <p class=sl>You MUST add the URL <code><a href="<?= "https://antrequest.nl/standard/PlanetPeer/$semver/" ?>"
            ><?= "https://antrequest.nl/standard/PlanetPeer/$semver/" ?></a></code> with <code>"main"</code> set to
        <code>true</code>. if <code>planetpeer.json</code> is supported <code>"planetpeerjson"</code> MUST be set to
        <code>true</code> also. like so.
    <pre><code><?= htmlEncodeMinimal(json_fromArray(['compat' => [
                    "https://antrequest.nl/standard/PlanetSpec/0.1.0/" => [
                            'main' => true,
                    ],
                    "https://antrequest.nl/standard/PlanetPeer/$semver/" => [
                            'main' => true,
                            "planetpeer_json" => true,
                    ],
            ]])) ?></code></pre>
</div>
