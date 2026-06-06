<?php

namespace DDNet\MapTestingLog;

use DDNet\MapTestingLog\Message\Component;
use DDNet\MapTestingLog\User;
use DateTime;

class Message
{
    public $author;
    public $timestamp;
    public $content;

    public function __construct(array $source)
    {
        $this->author = new User($source['author']);
        $this->timestamp = new DateTime($source['timestamp']);
        $this->content = Component::instanciateMany($source['content'], [
            Component\Variants\Text::class,
            Component\Variants\Image::class,
            Component\Variants\Attachment::class,
            Component\Variants\Container::class,
            Component\Variants\Embed::class,
            Component\Variants\Reactions::class
        ]);
    }
}
