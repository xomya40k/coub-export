<?php

declare(strict_types=1);

namespace CoubExport\Query\GetCoubByUrl;

use GuzzleHttp\ClientInterface;

final class Fetcher
{
    public function __construct(private ClientInterface $client, private string $apiUrl)
    {
    }

    public function fetch(Query $query): ?Coub
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

        $videoUrls = $data['file_versions']['html5']['video'];
        $audioUrls = $data['file_versions']['html5']['audio'];

        $coub = new Coub($query->permaLink, $data['id'], $data['title'], 
            $videoUrls['high']['url'], $videoUrls['med']['url'],
            $audioUrls['high']['url'], $audioUrls['med']['url']);

        return $coub;
    }
}