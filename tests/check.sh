#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
echo '=== PHP syntax ==='
find . -name '*.php' -print0 | while IFS= read -r -d '' f; do php -l "$f"; done
echo '=== JavaScript syntax ==='
node --check Assets/js/orkpdf.js
node tests/print-action.cjs
echo '=== Structural assertions ==='
php -r '$p=file_get_contents("Plugin.php");$s=file_get_contents("Template/dashboard/sidebar.php");$c=file_get_contents("Controller/OrkPdfController.php");foreach(["template:dashboard:sidebar","OrkPdfController","'print'"] as $k){$f=($k==="'print'")?$s:$p;if(strpos($f,$k)===false){fwrite(STDERR,"missing: $k\n");exit(1);}} foreach(["isUserAllowed","getById","getAll"] as $k) if(strpos($c,$k)===false) exit(2);echo "ok: menu, controller, permission checks\n";'
echo '=== Template smoke test ==='
php tests/render-smoke.php
php tests/render-brand.php
php tests/render-user-fields.php
ORKPDF_TEST_LOCALE=pt_BR php tests/render-i18n.php
ORKPDF_TEST_LOCALE=en_US php tests/render-i18n.php
php tests/print-fidelity.php
echo 'ALL LOCAL CHECKS PASSED'
