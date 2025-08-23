<?php

namespace App\Services;

class TuneParser
{
    public function parse(string $tuneData): ?array
    {
        $tuneData = trim($tuneData);
        if (empty($tuneData)) {
            return null;
        }

        $tuneLines = explode("\n", $tuneData);
        $result = [
            'info' => [],
            'body_lines' => [],
            'word_lines' => [],
            'note_lines' => [],
        ];

        $keyFound = false;
        $keyLineIndex = null;

        foreach ($tuneLines as $lineIndex => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (preg_match('/^([A-Za-z]):(.+)$/', $line, $matches)) {
                $key = strtoupper($matches[1]);
                $value = trim($matches[2]);

                if ($key === 'K') {
                    $keyFound = true;
                    $keyLineIndex = $lineIndex;
                    $result['info'][$key] = $value;
                    break;
                }

                match ($key) {
                    'W' => $result['word_lines'][] = $value,
                    'N' => $result['note_lines'][] = $value,
                    'T' => $result['info'][$key] = strtolower($value),
                    'O' => $result['info'][$key] = strtolower($value),
                    'R' => $result['info'][$key] = strtolower($value),
                    'C' => $result['info'][$key] = strtolower($value),
                    default => $result['info'][$key] = $value,
                };
            }
        }

        if (! $keyFound || empty($result['info']['T'])) {
            return null;
        }

        for ($i = $keyLineIndex + 1; $i < count($tuneLines); $i++) {
            $line = trim($tuneLines[$i]);
            if (! empty($line)) {
                $result['body_lines'][] = $line;
            }
        }

        return $result;
    }
}
