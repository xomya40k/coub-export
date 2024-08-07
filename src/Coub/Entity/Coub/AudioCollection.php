<?php

declare(strict_types=1);

namespace App\Coub\Entity\Coub;

final class AudioCollection
{
    private array $audioTracks = [];
    
    public function __construct(array $audioTracks = [])
    {
        foreach ($audioTracks as $audio) {
            if ($audio instanceof Audio) {
                $this->add($audio);
            } else {
                throw new \InvalidArgumentException('Array has invalid audio object');
            }
        }
    }

    private function add(Audio $audio)
    {
        if ($this->has($audio)) {
            throw new \DomainException('Audio with same quality already exists.');
        }
        $quality = $audio->getQuality();
        $this->audioTracks[$quality] = $audio;
    }

    public function getHighQuality() : ?Audio
    {
        return $this->getByQuality(Audio::QUALITY_HIGH);
    }

    public function getMediumQuality() : ?Audio
    {
        return $this->getByQuality(Audio::QUALITY_MEDIUM);
    }

    private function has(Audio $audio) : bool
    {
        $quality = $audio->getQuality();
        return $this->getByQuality($quality) !== null;
    }

    private function getByQuality(int $quality) : ?Audio
    {
        return $this->audioTracks[$quality];
    }
}