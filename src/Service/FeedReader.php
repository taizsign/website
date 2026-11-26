<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Lecteur de flux RSS / Atom avec cache disque.
 * Le serveur va chercher les flux (au plus une fois par heure) : le navigateur
 * du visiteur ne contacte jamais de site tiers.
 */
final class FeedReader
{
    private const TTL = 3600;
    private const TIMEOUT = 4;

    public function __construct(private readonly string $cacheDir)
    {
    }

    /**
     * Les derniers articles d'un flux, filtrés sur leur titre.
     *
     * @param ?string $match on ne garde que les titres qui correspondent (expression régulière)
     * @param ?string $skip  on écarte les titres qui correspondent
     * @return list<array{title: string, url: string, date: ?int}>
     */
    public function latest(string $url, int $max = 4, ?string $match = null, ?string $skip = null): array
    {
        $keep = static fn (array $i): bool => ($match === null || preg_match($match, $i['title']) === 1)
            && ($skip === null || preg_match($skip, $i['title']) !== 1);

        return array_slice(array_values(array_filter($this->all($url), $keep)), 0, $max);
    }

    /**
     * Relit le flux tout de suite et met le cache à jour. Lancé par cron
     * (php bin/console feeds:refresh) pour qu'aucun visiteur n'attende les sites des flux.
     *
     * @return ?int le nombre d'articles lus, ou null si le flux n'a pas répondu
     */
    public function refresh(string $url): ?int
    {
        $items = $this->fetch($url);
        if ($items === null) {
            return null;
        }

        file_put_contents($this->cacheFile($url), json_encode($items, JSON_UNESCAPED_UNICODE), LOCK_EX);

        return count($items);
    }

    /**
     * @return list<array{title: string, url: string, date: ?int}>
     */
    private function all(string $url): array
    {
        $cacheFile = $this->cacheFile($url);
        $cached = is_file($cacheFile) ? json_decode((string) file_get_contents($cacheFile), true) : null;

        if (is_array($cached) && time() - filemtime($cacheFile) < self::TTL) {
            return $cached;
        }

        $items = $this->fetch($url);

        if ($items !== null) {
            file_put_contents($cacheFile, json_encode($items, JSON_UNESCAPED_UNICODE), LOCK_EX);
            return $items;
        }

        // Flux injoignable : on ressert l'ancien cache, même périmé, plutôt que rien,
        // et on ne réessaie que dans une heure, pour ne pas faire attendre chaque visiteur.
        if (is_array($cached)) {
            @touch($cacheFile);
            return $cached;
        }

        return [];
    }

    private function cacheFile(string $url): string
    {
        return $this->cacheDir . '/feed-' . md5($url) . '.json';
    }

    /**
     * @return list<array{title: string, url: string, date: ?int}>|null
     */
    private function fetch(string $url): ?array
    {
        $context = stream_context_create([
            'http' => [
                'timeout'    => self::TIMEOUT,
                'user_agent' => 'Mozilla/5.0 (compatible; portfolio-rss/2.0)',
                'header'     => "Accept: application/rss+xml, application/atom+xml, application/xml, text/xml\r\n",
            ],
        ]);

        $raw = @file_get_contents($url, false, $context);
        if ($raw === false || $raw === '') {
            return null;
        }

        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($raw, options: LIBXML_NOCDATA | LIBXML_NONET);
        libxml_use_internal_errors($previous);

        if ($xml === false) {
            return null;
        }

        $items = [];

        // RSS 2.0
        foreach ($xml->channel->item ?? [] as $item) {
            $items[] = $this->item((string) $item->title, (string) $item->link, (string) $item->pubDate);
        }

        // Atom
        foreach ($xml->entry ?? [] as $entry) {
            $link = '';
            foreach ($entry->link as $l) {
                if ((string) ($l['rel'] ?? 'alternate') === 'alternate') {
                    $link = (string) $l['href'];
                    break;
                }
            }
            $items[] = $this->item((string) $entry->title, $link, (string) ($entry->published ?: $entry->updated));
        }

        return array_values(array_filter($items, static fn (array $i): bool => $i['title'] !== '' && str_starts_with($i['url'], 'http')));
    }

    /**
     * @return array{title: string, url: string, date: ?int}
     */
    private function item(string $title, string $url, string $date): array
    {
        $time = $date === '' ? false : strtotime($date);

        return [
            'title' => trim(html_entity_decode(strip_tags($title), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
            'url'   => trim($url),
            'date'  => $time === false ? null : $time,
        ];
    }
}
