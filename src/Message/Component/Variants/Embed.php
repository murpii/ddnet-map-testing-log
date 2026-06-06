<?php

namespace DDNet\MapTestingLog\Message\Component\Variants;

use DDNet\MapTestingLog\Message\Component;

// A classic Discord embed. Text fields are pre-resolved markdown strings; image/
// thumbnail/author-icon are stored asset filenames under files/attachments/.
class Embed extends Component
{
    public $accentColor;
    public $author;
    public $authorIcon;
    public $title;
    public $url;
    public $description;
    public $fields;
    public $image;
    public $thumbnail;
    public $footer;

    public function __construct(array $source)
    {
        $source = $source['embed'];
        $this->accentColor = $source['accent-color'] ?? null;
        $this->author = $source['author'] ?? null;
        $this->authorIcon = $source['author-icon'] ?? null;
        $this->title = $source['title'] ?? null;
        $this->url = $source['url'] ?? null;
        $this->description = $source['description'] ?? null;
        $this->fields = $source['fields'] ?? [];
        $this->image = $source['image'] ?? null;
        $this->thumbnail = $source['thumbnail'] ?? null;
        $this->footer = $source['footer'] ?? null;
    }

    public static function isCorrectVariant($source): bool
    {
        return isset($source['embed']);
    }
}
