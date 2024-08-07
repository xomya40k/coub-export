<?php

declare(strict_types=1);

namespace CoubExport\Entity\Coub;

final class Video
{
    public const int QUALITY_MEDIUM = 0;
    public const int QUALITY_HIGH = 1;

    private string $url;
    private int $width;
    private int $height;
    private int $quality;

    private function __construct(string $url, int $width, int $height, int $quality)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = $url;
        } else {
            throw new \InvalidArgumentException('Invalid URL');
        }

        if ($width > 0 and $height > 0) {
            $this->width = $width;
            $this->height = $height;
        } else {
            throw new \InvalidArgumentException('Invalid width or height');
        }

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

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function isQualityHigh(): bool
    {
        return $this->quality === self::QUALITY_HIGH;
    }

    public function isQualityMedium(): bool
    {
        return $this->quality === self::QUALITY_MEDIUM;
    }
}