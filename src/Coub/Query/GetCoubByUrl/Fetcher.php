<?php

declare(strict_types=1);

namespace App\Coub\Query\GetCoubByUrl;

use GuzzleHttp\ClientInterface;

final class Fetcher
{
    public function __construct(private ClientInterface $client, private string $apiUrl)
    {
    }

    public function fetch(Query $query) : ?Coub
    {
        $response = $this->client->request('GET', $this->apiUrl . $query->permaLink);
        $statusCode = $response->getStatusCode();
        
        if ($statusCode == 404) {
            return null;
        } elseif ($statusCode !== 200) {
            throw new \RuntimeException('Failed to fetch coub: ' . $response->getStatusCode());
        }
        
        $jsonData = $response->getBody()->getContents();
        $data = json_decode($jsonData, true);

        $video = $data['file_versions']['html5']['video'];
        $audio = $data['file_versions']['html5']['audio'];

        $coub = new Coub($query->permaLink, $data['id'], $data['title'], 
            $video['high']['url'], $video['med']['url'],
            $audio['high']['url'], $audio['med']['url'],
            $audio['sample_duration']);

        return $coub;
    }
}