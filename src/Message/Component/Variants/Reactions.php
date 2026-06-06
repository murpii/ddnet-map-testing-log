<?php

namespace DDNet\MapTestingLog\Message\Component\Variants;

use DDNet\MapTestingLog\Message\Component;

// A message's reactions, rendered as Discord-style pills (emoji + count). Each entry
// is either a unicode emoji {"count", "emoji"} or a custom emoji {"count", "name", "id"}
// whose image lives at files/emojis/<id>.png.
class Reactions extends Component
{
    public $reactions;

    public function __construct(array $source)
    {
        $this->reactions = $source['reactions'] ?? [];
    }

    public static function isCorrectVariant($source): bool
    {
        return isset($source['reactions']);
    }
}
