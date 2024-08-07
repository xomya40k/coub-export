<?php

declare(strict_types=1);

namespace App\Coub\Entity\Coub;

final class Coub
{
    private int $id;
    private string $url;
    private string $title;
    private VideoCollection $video;
    private ?AudioCollection $audio = null;

    public function __construct(int $id, string $url, string $title, 
        VideoCollection $video, AudioCollection $audio = null)
    {
        $this->id = $id;
        
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = $url;
        } else {
            throw new \InvalidArgumentException('Invalid URL');
        }
        
        $this->title = $title;
        $this->video = $video;
        $this->audio = $audio;
    }

    public function getId() : int 
    {
        return $this->id;    
    }

    public function getUrl() : string 
    {
        return $this->url;    
    }

    public function getTitle() : string 
    {
        return $this->title;
    }

    public function getVideo() : VideoCollection
    {
        return $this->video;
    }

    public function getAudio() : AudioCollection 
    {
        return $this->audio;    
    }
}