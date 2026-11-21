const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(__dirname + '/../Assets/js/orkpdf.js', 'utf8');
let clicked;
let printed = 0;
let prevented = false;
const printButton = { addEventListener(type, fn) { if(type === 'click') clicked = fn; } };
const document = { readyState: 'complete', getElementById(id) {
    if (id === 'orkpdf-print-button') return printButton;
    if (id === 'orkpdf-form') return null;
    return null;
} };
vm.runInNewContext(source, { document, window: { print(){printed++;} } });
assert.equal(typeof clicked, 'function', 'print click listener should be installed');
clicked({ preventDefault(){prevented = true;} });
assert.equal(printed, 1);
assert.equal(prevented, true);
console.log('PASS: print click handler without configuration form');
