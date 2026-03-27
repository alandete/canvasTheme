<?php
/**
 * Compila mobile y desktop CSS desde el master de un proyecto.
 * Mobile: mantiene hasta 992px (elimina @media >= 1200px y html[data-theme="dark"])
 * Desktop: todo excepto html[data-theme="dark"]
 */
function compileCssFromMaster($projectPath, $slug) {
    $masterFile = $projectPath . '/css/' . $slug . '-master.css';
    if (!file_exists($masterFile)) return false;

    $master = file_get_contents($masterFile);

    // ── Eliminar bloque de ambiente de pruebas ──
    // 1. Comentario + bloque principal html[data-theme="dark"] { variables }
    $clean = preg_replace(
        '/\/\*[═\s]*DARK MODE\s*—\s*Ambiente de pruebas[^*]*\*\/\s*html\[data-theme="dark"\]\s*\{[^}]*\}/s',
        '',
        $master
    );
    // 2. Todas las reglas individuales html[data-theme="dark"] .selector { ... }
    $clean = preg_replace(
        '/html\[data-theme="dark"\]\s+[^{]+\{[^}]*\}\s*/s',
        '',
        $clean
    );

    // ── Desktop: todo limpio ──
    $desktop = preg_replace('/\n{3,}/', "\n\n", $clean);
    $desktop = trim($desktop) . "\n";

    // ── Mobile: eliminar @media >= 1200px ──
    $mobile = $clean;

    // Eliminar @media con comentario (X-Large, XX-Large)
    $mobile = preg_replace(
        '!/\*[^*]*(?:X-Large|XX-Large)[^*]*\*/\s*@media\s*\(\s*min-width\s*:\s*(1200|1400)px\s*\)\s*\{(?:[^{}]*|\{[^{}]*\})*\}\s*!s',
        '',
        $mobile
    );

    // Fallback: eliminar cualquier @media (min-width >= 1200px) sin comentario
    $mobile = preg_replace(
        '/@media\s*\(\s*min-width\s*:\s*(1[2-9]\d{2}|[2-9]\d{3,})px\s*\)\s*\{(?:[^{}]*|\{[^{}]*\})*\}\s*/s',
        '',
        $mobile
    );

    $mobile = preg_replace('/\n{3,}/', "\n\n", $mobile);
    $mobile = trim($mobile) . "\n";

    // ── Escribir ──
    file_put_contents($projectPath . '/css/' . $slug . '-mobile.css', $mobile);
    file_put_contents($projectPath . '/css/' . $slug . '-desktop.css', $desktop);

    return true;
}
