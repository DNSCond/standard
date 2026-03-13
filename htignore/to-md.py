from markdownify import markdownify
import pathlib
import aiohttp
import asyncio
import re

sem = asyncio.Semaphore(75)


async def fetch(path: str, session: aiohttp.ClientSession):
    if bool(match := re.search(
            '/([^/]+)/(\\d+)\\.(\\d+)\\.(\\d+)/index\\.php$',
            path.replace('\\', '/'))):
        async with sem:
            async with session.get(
                    f'http://localhost/standard/{match.group(1)}/{match.group(2)}.{match.group(3)}.{match.group(4)}/'
            ) as resp:
                new_path = pathlib.Path(path).with_suffix('.md')
                with open(new_path, 'wb') as file:
                    file.write(await resp.read())
                with open(new_path, 'rt', encoding='utf8') as file:
                    text = file.read()
                text = markdownify(re.sub('\\s+',' ',text))
                with open(new_path, 'wt', encoding='utf8') as file:
                    file.write(text)
            pass
    pass


async def main():
    tasks = set()
    async with aiohttp.ClientSession() as session:
        async with asyncio.TaskGroup() as taskgrp:
            for path in [*pathlib.Path('..').glob('*/*/*.php'), ]:
                task = taskgrp.create_task(fetch(
                    str(path).replace('\\', '/'), session))
                task.add_done_callback(tasks.discard)
                tasks.add(task)
            pass
    pass


asyncio.run(main())
