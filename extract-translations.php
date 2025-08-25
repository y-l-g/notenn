<?php

$sourceDirectory = './resources/js';
$outputFile = './translations2.json';
$existingTranslationsFile = './translations.json';
$extensions = ['vue', 'js', 'ts'];

$existingTranslations = [];
if (file_exists($existingTranslationsFile)) {
    $existingContent = file_get_contents($existingTranslationsFile);
    $existingTranslations = json_decode($existingContent, true) ?: [];
}

function scanDirectory($dir, $extensions)
{
    $results = [];
    $files = scandir($dir);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if ($file === '.' || $file === '..') {
            continue;
        }
        if (is_dir($path)) {
            $results = array_merge($results, scanDirectory($path, $extensions));
        } else {
            $fileInfo = pathinfo($path);
            if (isset($fileInfo['extension']) && in_array($fileInfo['extension'], $extensions)) {
                $results[] = $path;
            }
        }
    }
    return $results;
}

function extractStrings($filePath)
{
    $content = file_get_contents($filePath);
    $strings = [];

    preg_match_all("/\\bt\\((['\"])(.*?)\\1[^)]*\\)/", $content, $matches1);
    if (!empty($matches1[2])) {
        $strings = array_merge($strings, $matches1[2]);
    }

    preg_match_all('/(?:const|let|var)\s+textToTranslate\s*=\s*(["\'])(.*?)\1/', $content, $matches3);
    if (!empty($matches3[2])) {
        $strings = array_merge($strings, $matches3[2]);
    }

    preg_match_all('/textToTranslate\s*=\s*(["\'])(.*?)\1/', $content, $matches5);
    if (!empty($matches5[2])) {
        $strings = array_merge($strings, $matches5[2]);
    }

    preg_match_all("/\\b\\\$t\\((['\"])(.*?)\\1[^)]*\\)/", $content, $matches7);
    if (!empty($matches7[2])) {
        $strings = array_merge($strings, $matches7[2]);
    }

    preg_match_all("/\\b__\\((['\"])(.*?)\\1[^)]*\\)/", $content, $matches9);
    if (!empty($matches9[2])) {
        $strings = array_merge($strings, $matches9[2]);
    }

    $strings = array_filter(array_unique($strings), function ($str) {
        return !empty(trim($str));
    });

    return $strings;
}

echo "🚀 Début de l'extraction des nouvelles chaînes...\n";

$files = scanDirectory($sourceDirectory, $extensions);
echo "📁 Nombre de fichiers analysés : " . count($files) . "\n";

$fileTypes = [];
foreach ($files as $file) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $fileTypes[$ext] = ($fileTypes[$ext] ?? 0) + 1;
}
foreach ($fileTypes as $ext => $count) {
    echo "   - $ext: $count fichier(s)\n";
}

$newTranslations = [];
$totalStrings = 0;

foreach ($files as $file) {
    $strings = extractStrings($file);
    $totalStrings += count($strings);

    foreach ($strings as $string) {
        if (!array_key_exists($string, $existingTranslations)) {
            $newTranslations[$string] = $string;
        }
    }
}

echo "🔍 Total chaînes trouvées : $totalStrings\n";
echo "✨ Nouvelles chaînes trouvées : " . count($newTranslations) . "\n";

if (!empty($newTranslations)) {
    ksort($newTranslations);

    $json = json_encode($newTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($outputFile, $json);
    echo "💾 Nouvelles traductions sauvegardées dans {$outputFile}\n";

    echo "\n📝 Aperçu des nouvelles chaînes :\n";
    $preview = array_slice(array_keys($newTranslations), 0, 5);
    foreach ($preview as $key) {
        echo "   - \"$key\"\n";
    }
    if (count($newTranslations) > 5) {
        echo "   ... et " . (count($newTranslations) - 5) . " autre(s)\n";
    }
} else {
    echo "✅ Aucune nouvelle chaîne trouvée.\n";
    if (file_exists($outputFile)) {
        unlink($outputFile);
        echo "🗑️  Fichier temporaire supprimé.\n";
    }
}

echo "\n🎉 Extraction terminée !\n";
