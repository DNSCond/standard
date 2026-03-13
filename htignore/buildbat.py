import json, hashlib
from datetime import datetime, timezone
from pathlib import Path
from typing import Any
import fnmatch
import os, re
import shutil


def sha256_file(file_path: Path | str, truebytes: bool):
    sha256 = hashlib.sha256()
    # Read the file in chunks to handle large files
    with open(file_path, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            sha256.update(chunk)
    if truebytes:
        return sha256.digest()  # Return raw bytes
    return sha256.hexdigest()


def base58_encode(data: bytes) -> str:
    # Base58 alphabet (Bitcoin style)
    BASE58_ALPHABET = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz"
    num = int.from_bytes(data, byteorder="big")
    encode = ""
    while num > 0:
        num, rem = divmod(num, 58)
        encode = BASE58_ALPHABET[rem] + encode
    # Preserve leading zeros as '1's
    n_pad = len(data) - len(data.lstrip(b"\0"))
    return "1" * n_pad + encode


def sha256_base58_file(file_path: Path | str):
    return base58_encode(sha256_file(file_path, True))


# ai generated class
class GitIgnore:
    def __init__(self, base: Path, file_name: str | None = ".gitignore"):
        self.base = base.resolve()
        self._file_name = file_name
        self._rules = list()
        self._load_gitignores()

    def _load_gitignores(self):
        if self._file_name is None:
            self.add_ignore_rule('*.iml')
            self.add_ignore_rule('.git/')
            self.add_ignore_rule('.idea/')
            return
        for directory in [self.base, *self.base.parents]:
            ignore_file = directory / self._file_name
            if not ignore_file.is_file():
                continue

            with ignore_file.open("r", encoding="utf-8") as f:
                for line in f:
                    line = line.strip()
                    if not line or line.startswith("#"):
                        continue
                    self._rules.append((directory, line))
        self.add_ignore_rule('*.iml')
        self.add_ignore_rule('.git/')
        self.add_ignore_rule('.idea/')

    def add_ignore_rule(self, rule: str):
        self._rules.append((self.base, rule))

    def should_ignore(self, path: Path) -> bool:
        path = path.resolve()
        ignored = False
        for base, pattern in self._rules:
            negate = pattern.startswith("!")
            if negate:
                pattern = pattern[1:]

            try:
                target = path.relative_to(base)
            except ValueError:
                continue

            target_str = target.as_posix()
            name = path.name

            matched = False

            # Directory rule
            if pattern.endswith("/"):
                if path.is_dir():
                    matched = fnmatch.fnmatch(target_str + "/", pattern)
            else:
                # If pattern has no slash, match anywhere in tree
                if "/" not in pattern:
                    matched = (
                            fnmatch.fnmatch(name, pattern)
                            or fnmatch.fnmatch(target_str, f"*/{pattern}")
                            or fnmatch.fnmatch(target_str, pattern)
                    )
                else:
                    matched = fnmatch.fnmatch(target_str, pattern)

            if matched:
                ignored = not negate

        return ignored

    def copy_to(self, src: Path, dst: Path) -> dict[str, int | list[Any]]:
        all_paths = list()
        src = src.resolve()
        dst = dst.resolve()
        moved = int()

        def _copy_recursive(current_src: Path, current_dst: Path):
            nonlocal moved
            if self.should_ignore(current_src):
                return
            all_paths.append(dict(current_src=current_src, current_dst=current_dst))
            if current_src.is_dir():
                moved += 1
                current_dst.mkdir(parents=True, exist_ok=True)
                for child in current_src.iterdir():
                    _copy_recursive(child, current_dst / child.name)
            else:
                moved += 1
                current_dst.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(current_src, current_dst)

        _copy_recursive(src, dst)
        return dict(moved=moved, all_paths=all_paths)

    pass


def main():
    metadata_key = 'metadata'
    result = {metadata_key: dict()}
    prev_data = dict()
    try:
        with open('../lastModified.json', 'rt', encoding='utf8') as file:
            prev_data = json.loads(file.read())
    except FileNotFoundError:
        pass
    for p in Path('..').glob('./*/*/index.php'):
        if bool(matches := re.search(
                '/([a-zA-Z0-9]+)/(\\d+)\\.(\\d+)\\.(\\d+)/index\\.php$',
                str(p).replace('\\', '/'))):
            mtime = re.sub('\\.\\d+', '', datetime.fromtimestamp(
                os.path.getmtime(p), tz=timezone.utc).isoformat()).replace("+00:00", "Z")
            if matches.group(1) not in result[metadata_key]:
                result[metadata_key][matches.group(1)] = dict()
            semver = f'{matches.group(2)}.{matches.group(3)}.{matches.group(4)}'
            if semver not in result[metadata_key][matches.group(1)]:
                result[metadata_key][matches.group(1)][semver] = dict()

            prev = prev_data.get('metadata', dict()).get(
                matches.group(1), dict()).get(semver, dict())
            filehash = 'sha256B58-' + sha256_base58_file(p)
            if prev.get('hash') == filehash:
                result[metadata_key][matches.group(1)][semver]['lastModified'] = prev.get('lastModified', mtime)
            else:
                result[metadata_key][matches.group(1)][semver]['lastModified'] = mtime
            result[metadata_key][matches.group(1)][semver]['hash'] = filehash
            result[metadata_key][matches.group(1)][semver]['warning'] = {
                'warningLevel': None,  # 'warning' | 'danger' | 'info'
                'warningContent': None}
            default = result[metadata_key][matches.group(1)][semver]['warning']
            try:
                with open(str(p).replace('\\', '/').replace('/index.php', '/metadata.json'),
                          'rt', encoding='utf8') as file:
                    data = json.loads(file.read())
                    result[metadata_key][matches.group(1)][semver]['warning'] = data.get('warning', default)
            except FileNotFoundError:
                pass
    with open('../lastModified.json', 'wt', encoding='utf8') as file:
        file.write(json.dumps(result))
    pass
    directory = Path('..')
    dst = Path('../piout') / 'standard'
    if dst.exists():
        shutil.rmtree(dst)
    dst.mkdir(parents=True)
    # gitignored = GitIgnore(directory, '.buildbat-ignore')
    gitignored = GitIgnore(directory, None)
    gitignored.add_ignore_rule('junk')
    gitignored.add_ignore_rule('piout')
    gitignored.add_ignore_rule('*.iml')
    gitignored.add_ignore_rule('.git/')
    gitignored.add_ignore_rule('.idea/')
    r = gitignored.copy_to(directory, dst)
    print('moved', r['moved'], 'items')
    # addition = int()
    # for pathdict in r['all_paths']:
    #     src = pathdict['current_src']
    #     dst = pathdict['current_dst']
    #     if str(src).endswith('.html'):
    #         with (open(src, 'wt', encoding='utf8') as inv,
    #               open(re.sub('\\.html$', '.md', str(dst)), 'wt', encoding='utf8') as out):
    #             out.write(markdownify(inv.read()))
    #         addition += 1
    # print('moveded', r['moved'] + addition, 'items')
    pass


if __name__ == '__main__':
    main()
pass
