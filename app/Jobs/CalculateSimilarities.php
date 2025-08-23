<?php

namespace App\Jobs;

use App\Models\Composer;
use App\Models\Tune;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CalculateSimilarities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $tunePairs = $this->findSimilarTunes();
        $composerPairs = $this->findSimilarComposers();

        Cache::put('dashboard_tune_pairs', $tunePairs, 3600);
        Cache::put('dashboard_composer_pairs', $composerPairs, 3600);
    }
    private function findSimilarTunes(): array
    {
        // Limite à 100 tunes maximum pour éviter l'explosion
        $allTunes = Tune::select('id', 'title')
            ->limit(100)
            ->orderBy('updated_at', 'desc') // Prendre les plus récentes
            ->get();

        $tunePairs = [];
        $processedPairs = [];

        foreach ($allTunes as $tune) {
            // Batch search : chercher plusieurs termes d'un coup
            $searchTerms = $this->extractSearchTerms($tune->title);

            if (empty($searchTerms))
                continue;

            $query = Tune::search(implode(' ', $searchTerms));
            $query->whereNotIn('id', [$tune->id]);
            $query->options([
                'attributesToSearchOn' => ['title', 'alternative_titles'],
                'limit' => 5, // Limite les résultats par recherche
            ]);

            $searchResults = $query->get();

            foreach ($searchResults as $similarTune) {
                $pairKey = min($tune->id, $similarTune->id) . '_' . max($tune->id, $similarTune->id);

                if (!isset($processedPairs[$pairKey])) {
                    $tunePairs[] = [
                        'tune_1' => $tune->only(['id', 'title']),
                        'tune_2' => $similarTune->only(['id', 'title']),
                        'similarity_score' => $this->calculateSimilarity($tune->title, $similarTune->title)
                    ];
                    $processedPairs[$pairKey] = true;
                }

                // Limite le nombre total de paires
                if (count($tunePairs) >= 50) {
                    break 2;
                }
            }
        }

        // Trier par score de similarité
        usort($tunePairs, fn($a, $b) => $b['similarity_score'] <=> $a['similarity_score']);

        return array_slice($tunePairs, 0, 20); // Top 20 seulement
    }

    private function findSimilarComposers(): array
    {
        $allComposers = Composer::select('id', 'name')
            ->limit(100)
            ->orderBy('updated_at', 'desc')
            ->get();

        $composerPairs = [];
        $processedPairs = [];

        foreach ($allComposers as $composer) {
            $searchTerms = $this->extractSearchTerms($composer->name);

            if (empty($searchTerms))
                continue;

            $query = Composer::search(implode(' ', $searchTerms));
            $query->whereNotIn('id', [$composer->id]);
            $query->options([
                'attributesToSearchOn' => ['name'],
                'limit' => 5,
            ]);

            $searchResults = $query->get();

            foreach ($searchResults as $similarComposer) {
                $pairKey = min($composer->id, $similarComposer->id) . '_' . max($composer->id, $similarComposer->id);

                if (!isset($processedPairs[$pairKey])) {
                    $composerPairs[] = [
                        'composer_1' => $composer->only(['id', 'name']),
                        'composer_2' => $similarComposer->only(['id', 'name']),
                        'similarity_score' => $this->calculateSimilarity($composer->name, $similarComposer->name)
                    ];
                    $processedPairs[$pairKey] = true;
                }

                if (count($composerPairs) >= 50) {
                    break 2;
                }
            }
        }

        usort($composerPairs, fn($a, $b) => $b['similarity_score'] <=> $a['similarity_score']);

        return array_slice($composerPairs, 0, 20);
    }

    private function extractSearchTerms(string $text): array
    {
        // Nettoie et extrait les mots clés importants
        $text = strtolower($text);
        $text = preg_replace('/[^\w\s]/', ' ', $text);
        $words = array_filter(explode(' ', $text));

        // Retire les mots trop courts ou communs
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by'];
        $words = array_filter($words, fn($word) => strlen($word) > 2 && !in_array($word, $stopWords));

        return array_values($words);
    }

    private function calculateSimilarity(string $str1, string $str2): float
    {
        // Calcul simple de similarité (tu peux utiliser une lib plus sophistiquée)
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        return (1 - (levenshtein($str1, $str2) / max(strlen($str1), strlen($str2)))) * 100;
    }
}
