// like ecmascript
class ToggleTokenList extends Set {
    toggle(value) {
        if (this.has(value)) {
            this.delete(value);
            return false;
        } else {
            this.add(value);
            return true;
        }
    }
}

let colorInt = 0n;
// [light, dark]
const object = {
        'yellow': ['fff100'],
        'orange': ['fea700'],
        'green': ['40bf55'],
        'blue': ['00a8f3'],
        'red': ['ff4500'],
        'purple': ['8e46db'],
    }, toggleTokenList = new ToggleTokenList, colorNames = Object.keys(object),
    varstyle = document.getElementById('varstyle');
document.querySelectorAll('.sl var').forEach(each => {
    each.className += ' ' + (each.dataset.variName = `variColor-${each.textContent}`);
    each.addEventListener('click', function () {
        if (toggleTokenList.toggle(each.dataset.variName)) {
            const colorName = colorNames[colorInt++ % BigInt(colorNames.length)],
                [colorHex, _colorHexDark] = object[colorName], variName = each.dataset.variName;
            varstyle.textContent += `/*${variName}*/.${CSS.escape(variName)}.${CSS.escape(variName)} {background-color:#${colorHex};}/*${variName}*/`;
        } else {
            const variName = RegExp.escape(each.dataset.variName),
                regexp = RegExp(`\\/\\*${variName}\\*\\/[^\\/]+\\/\\*${variName}\\*\\/`, 'g');
            varstyle.textContent = varstyle.textContent.replaceAll(regexp, '');
        }
    });
});
