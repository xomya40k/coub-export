<?php

declare(strict_types=1);

namespace App\Coub\Test\Builder;

use App\Coub\Entity\Coub\Audio;

final class AudioBuilder
{
    public const int QUALITY_MEDIUM = 0;
    public const int QUALITY_HIGH = 1;

    private string $url;
    private float $sampleDuration;
    private int $quality;

    public function __construct()
    {
        $this->url = 'https://selcdn.net/coub_storage/coub/simple/cw_file/mp3-high.mp3';
        $this->sampleDuration = 5.5;
        $this->quality = Audio::QUALITY_HIGH;
    }

    public function withUrl(string $url) : self
    {
        $clone = clone $this;
        $clone->url = $url;
        return $clone;
    }

    public function withDuration(float $duration) : self
    {
        $clone = clone $this;
        $clone->sampleDuration = $duration;
        return $clone;
    }

    public function withQuality(int $quality) : self
    {
        $clone = clone $this;
        $clone->quality = $quality;
        return $clone;
    }

    public function build() : Audio
    {
        return new Audio(
            $this->url,
            $this->sampleDuration,
            $this->quality
        );
    }
}