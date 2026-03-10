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

function css_escape(string, cssEscape = true) {
    return String(cssEscape ? CSS.escape(string) : string).replaceAll(/\s+/g, '-');
}

// [light, dark]
const autoset = new Set, object = {
        'yellow': ['fff100'],
        'orange': ['fea700'],
        'green': ['40bf55'],
        'blue': ['00a8f3'],
        'red': ['ff8c62'],
        'purple': ['ba8de6'],
    }, toggleTokenList = new ToggleTokenList, colorNames = Object.keys(object),
    varstyle = document.getElementById('varstyle'), callback = function (each) {
        if (toggleTokenList.toggle(each)) {
            const colorName = colorNames[colorInt++ % BigInt(colorNames.length)],
                [colorHex, _colorHexDark] = object[colorName], variName = each;
            varstyle.textContent += `/*${variName}*/.${css_escape(variName)}.${css_escape(variName)} {background-color:#${colorHex};}/*${variName}*/`;
        } else {
            const variName = RegExp.escape(each), regexp =
                RegExp(`\\/\\*${variName}\\*\\/[^\\/]+\\/\\*${variName}\\*\\/`, 'g');
            varstyle.textContent = varstyle.textContent.replaceAll(regexp, '');
        }
    };
document.querySelectorAll('.sl var').forEach(each => {
    each.className += ' ' + css_escape(each.dataset.variName = `variColor-${each.textContent}`, false);
    each.addEventListener('click', () => callback(each.dataset.variName));
    autoset.add(each.dataset.variName);
});
if ((new URLSearchParams(location.search)).get('highlight') === 'vars') {
    for (const autoget of autoset) {
        callback(autoget);
    }
}
