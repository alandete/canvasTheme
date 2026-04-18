<?php
/**
 * Compila mobile y desktop CSS desde el master de un proyecto.
 * Mobile: mantiene hasta 992px (elimina @media >= 1200px y html[data-theme="dark"])
 * Desktop: todo excepto html[data-theme="dark"] Y @media (prefers-color-scheme: dark)
 *
 * Canvas LMS web no soporta dark mode nativo, por eso se elimina del desktop.
 * En mobile se mantiene para que la app Canvas (iOS/Android) respete la
 * preferencia del sistema operativo del usuario.
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

    // ── Desktop: eliminar también el bloque @media (prefers-color-scheme: dark) ──
    // Canvas web no soporta dark mode, genera inconsistencia visual con el resto de la UI.
    $desktop = $clean;

    // Eliminar comentario + bloque @media (prefers-color-scheme: dark) { :root { ... } }
    $desktop = preg_replace(
        '!/\*[═\s]*DARK MODE\s*—\s*Canvas real[^*]*\*/\s*@media\s*\(\s*prefers-color-scheme\s*:\s*dark\s*\)\s*\{(?:[^{}]*|\{[^{}]*\})*\}\s*!s',
        '',
        $desktop
    );
    // Fallback: eliminar cualquier @media (prefers-color-scheme: dark) sin comentario
    $desktop = preg_replace(
        '/@media\s*\(\s*prefers-color-scheme\s*:\s*dark\s*\)\s*\{(?:[^{}]*|\{[^{}]*\})*\}\s*/s',
        '',
        $desktop
    );

    $desktop = preg_replace('/\n{3,}/', "\n\n", $desktop);
    $desktop = trim($desktop) . "\n";

    // ── Mobile: mantiene dark mode, elimina @media >= 1200px ──
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
