<?php

declare(strict_types=1);

namespace CoubExport\Entity\Coub;

use DateTimeImmutable ;

final class Coub
{
    private int $id;
    private string $url;
    private string $title;
    private VideoCollection $video;
    private AudioCollection $audio;

    private function __construct(int $id, string $url, string $title, 
        VideoCollection $video, AudioCollection $audio = null)
    {
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
        $this->video = $video;
        $this->audio = $audio;
    }

    public function getUrl() : string 
    {
        return $this->url;    
    }

    public function getTitle() : string 
    {
        return $this->title;
    }

    public function getVideo(): VideoCollection
    {
        return $this->video;
    }

    public function getAudio() : ?AudioCollection 
    {
        return $this->audio;    
    }
}