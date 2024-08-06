<?php

declare(strict_types=1);

namespace CoubExport\Entity\Coub;

final class Audio
{
    public const string QUALITY_MEDIUM = 0;
    public const string QUALITY_HIGH = 1;
    
    private string $url;
    private float $sampleDuration;
    private int $quality;

    private function __construct(string $url, float $sampleDuration, int $quality)
    {
        $this->url= $url;
        $this->sampleDuration = $sampleDuration;

        switch ($quality) {
            case self::QUALITY_HIGH:
                $this->quality = self::QUALITY_HIGH;
                break;
            
            case self::QUALITY_MEDIUM:
                $this->quality = self::QUALITY_MEDIUM;
                break;
            
            default:
                throw new \InvalidArgumentException('Invalid quality');
                break;
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getDuration(): ?float
    {
        return $this->sampleDuration;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }
}