<?php

declare(strict_types=1);

namespace App\Service;

use App\Database\Database;

/**
 * Sonde HTTP de mes services en ligne.
 *
 * Au plus une vague de vérifications toutes les 5 minutes, toutes lancées
 * en parallèle (curl_multi) avec un délai court : une page ne doit jamais
 * attendre plus de quelques secondes à cause d'un serveur qui ne répond pas.
 * Chaque relevé est conservé dans la table `probe` pour afficher l'historique.
 */
final class Uptime
{
    private const INTERVAL = 300;
    private const TIMEOUT = 3;
    private const HISTORY = 24;

    /**
     * @param list<array{name: string, url: string, role: string}> $targets
     */
    public function __construct(
        private readonly Database $db,
        private readonly array $targets,
    ) {
    }

    /**
     * État de chaque service et ses derniers relevés (le plus ancien en premier).
     *
     * @return list<array{name: string, url: string, role: string, last: ?array<string, mixed>, history: list<array<string, mixed>>, uptime: ?float}>
     */
    public function status(): array
    {
        $this->refreshIfStale();

        $status = [];
        foreach ($this->targets as $target) {
            $history = array_reverse($this->db->all(
                'SELECT checked_at, http_code, ms, ok FROM probe WHERE target = ? ORDER BY checked_at DESC LIMIT ' . self::HISTORY,
                [$target['url']],
            ));
            $okCount = count(array_filter($history, static fn (array $p): bool => (bool) $p['ok']));

            $status[] = $target + [
                'last'    => $history === [] ? null : $history[count($history) - 1],
                'history' => $history,
                'uptime'  => $history === [] ? null : round(100 * $okCount / count($history), 1),
            ];
        }

        return $status;
    }

    private function refreshIfStale(): void
    {
        if ($this->targets === [] || !function_exists('curl_multi_init')) {
            return;
        }

        $last = $this->db->value('SELECT MAX(checked_at) FROM probe');
        if ($last !== null && time() - strtotime((string) $last) < self::INTERVAL) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $results = $this->probeAll();

        $this->db->transaction(static function (Database $db) use ($results, $now): void {
            foreach ($results as $url => [$code, $ms]) {
                $db->execute(
                    'INSERT INTO probe (target, checked_at, http_code, ms, ok) VALUES (?, ?, ?, ?, ?)',
                    [$url, $now, $code, $ms, (int) ($code >= 200 && $code < 400)],
                );
            }
            // On ne garde qu'une semaine de relevés.
            $db->execute("DELETE FROM probe WHERE checked_at < datetime('now', 'localtime', '-7 days')");
        });
    }

    /**
     * @return array<string, array{int, float}> url => [code HTTP (0 = injoignable), durée en ms]
     */
    private function probeAll(): array
    {
        $multi = curl_multi_init();
        $handles = [];

        foreach ($this->targets as $target) {
            $handle = curl_init($target['url']);
            curl_setopt_array($handle, [
                CURLOPT_NOBODY         => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_CONNECTTIMEOUT => self::TIMEOUT,
                CURLOPT_TIMEOUT        => self::TIMEOUT,
                CURLOPT_USERAGENT      => 'portfolio-uptime/1.0 (+https://taizsys.xyz)',
            ]);
            curl_multi_add_handle($multi, $handle);
            $handles[$target['url']] = $handle;
        }

        do {
            $status = curl_multi_exec($multi, $running);
            if ($running) {
                curl_multi_select($multi, 0.2);
            }
        } while ($running && $status === CURLM_OK);

        $results = [];
        foreach ($handles as $url => $handle) {
            $results[$url] = [
                (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE),
                round((float) curl_getinfo($handle, CURLINFO_TOTAL_TIME) * 1000, 1),
            ];
            curl_multi_remove_handle($multi, $handle);
        }
        curl_multi_close($multi);

        return $results;
    }
}
