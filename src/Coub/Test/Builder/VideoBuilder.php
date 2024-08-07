<?php

declare(strict_types=1);

namespace App\Coub\Test\Builder;

use App\Coub\Entity\Coub\Video;

final class VideoBuilder
{
    public const int QUALITY_MEDIUM = 0;
    public const int QUALITY_HIGH = 1;

    private string $url;
    private int $width;
    private int $height;
    private int $quality;

    public function __construct()
    {
        $this->url = 'https://selcdn.net/coub_storage/coub/simple/cw_file/muted_big.mp4';
        $this->width = 1280;
        $this->height = 720;
        $this->quality = Video::QUALITY_HIGH;
    }

    public function withUrl(string $url) : self
    {
        $clone = clone $this;
        $clone->url = $url;
        return $clone;
    }

    public function withWidth(int $width) : self
    {
        $clone = clone $this;
        $clone->width = $width;
        return $clone;
    }

    public function withHeight(int $height) : self
    {
        $clone = clone $this;
        $clone->height = $height;
        return $clone;
    }

    public function withQuality(int $quality) : self
    {
        $clone = clone $this;
        $clone->quality = $quality;
        return $clone;
    }

    public function build() : Video
    {
        return new Video(
            $this->url,
            $this->width,
            $this->height,
            $this->quality
        );
    }
}