<?php

declare(strict_types=1);

namespace CoubExport\Entity\Coub;

final class VideoCollection
{
    private array $videos = [];
    
    private function __construct(array $videos = [])
    {
        foreach ($videos as $video) {
            if ($video instanceof Video) {
                $this->add($video);
            } else {
                throw new \InvalidArgumentException('Array has invalid video object');
            }
        }
    }

    private function add(Video $video)
    {
        if ($this->has($video)) {
            throw new \DomainException('Video with same quality already exists.');
        }
        $quality = $video->getQuality();
        $this->videos[$quality] = $video;
    }

    public function getHighQuality(): ?Video
    {
        return $this->getByQuality(Video::QUALITY_HIGH);
    }

    public function getMediumQuality(): ?Video
    {
        return $this->getByQuality(Video::QUALITY_MEDIUM);
    }

    private function has(Video $video): bool
    {
        $quality = $video->getQuality();
        return $this->getByQuality($quality) !== null;
    }

    private function getByQuality(int $quality): ?Video
    {
        return $this->videos[$quality];
    }
}