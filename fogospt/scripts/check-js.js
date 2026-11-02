// Syntax-check every hand-written script served from public/js (they are not
// bundled, so nothing else would catch a syntax error before production).
const { execFileSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const roots = ['public/js', 'public/js/vendor'];
let failed = 0;

for (const dir of roots) {
    for (const file of fs.readdirSync(dir)) {
        if (!file.endsWith('.js')) continue;
        const full = path.join(dir, file);
        try {
            execFileSync(process.execPath, ['--check', full], { stdio: 'pipe' });
        } catch (e) {
            failed++;
            console.error(`✗ ${full}\n${e.stderr.toString()}`);
        }
    }
}

if (failed) process.exit(1);
console.log('All public/js scripts parse.');
