<?php use function Helpers\json_fromArray;

require_once '../../createHeader.php';
global $major, $minor, $patch ?>
<div class=divs>
    <h1 id=theTop><?= "{$GLOBALS['title']}" ?></h1>
    <p>The <dfn id="spec-<?= $GLOBALS['spec'] ?>"><?= "{$GLOBALS['title']}" ?></dfn> Standard is a Meta Standard that
        defines things all other ANTRequest Standards use.
    <p>to use <a href="#spec-<?= $GLOBALS['spec'] ?>"><?= "{$GLOBALS['title']}" ?></a> you can use This Specification to
        insure interoperability. If you do not Like where this is going please fork.
    <h2 id=TableOfContents>Table Of Contents</h2>
    <details open>
        <summary>Table Of Contents</summary>
        <!-- Favicond-render-TableOfContents -->
    </details>
    <h2 id=documentStatus>Status of this document</h2>
    <p>this document written on
        <time datetime=2026-03-10 data-tolocaltime=date-only>Tue Mar 10 2026</time>
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
</div>