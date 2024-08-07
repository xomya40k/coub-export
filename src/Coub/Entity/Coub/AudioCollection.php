<?php

declare(strict_types=1);

namespace CoubExport\Entity\Coub;

final class AudioCollection
{
    private array $audioTracks = [];
    
    private function __construct(array $audioTracks = [])
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

    public function has(Audio $audio): bool
    {
        $quality = $audio->getQuality();
        return $this->getByQuality($quality) !== null;
    }

    public function getHighQuality(): ?Audio
    {
        return $this->getByQuality(Audio::QUALITY_HIGH);
    }

    public function getMediumQuality(): ?Audio
    {
        return $this->getByQuality(Audio::QUALITY_MEDIUM);
    }

    public function removeHighQuality()
    {
        $this->removeByQuality(Audio::QUALITY_HIGH);    
    }

    public function removeMediumQuality()
    {
        $this->removeByQuality(Audio::QUALITY_MEDIUM);    
    }

    private function getByQuality(int $quality): ?Audio
    {
        return $this->audioTracks[$quality];
    }

    private function removeByQuality(int $quality)
    {
        unset($this->audioTracks[$quality]);
    }
}