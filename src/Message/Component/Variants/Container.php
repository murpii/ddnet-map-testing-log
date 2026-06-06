<?php

namespace DDNet\MapTestingLog\Message\Component\Variants;

use DDNet\MapTestingLog\Message\Component;

// Components V2 "Container" (a LayoutView's coloured box). The bot pre-flattens its
// component tree into ordered blocks, each being one of: {md}, {separator}, {image},
// {buttons}. The template walks them directly, so no nested component variants needed.
class Container extends Component
{
    public $accentColor;
    public $blocks;

    public function __construct(array $source)
    {
        $source = $source['container'];
        $this->accentColor = $source['accent-color'] ?? null;
        $this->blocks = $source['blocks'] ?? [];
    }

    public static function isCorrectVariant($source): bool
    {
        return isset($source['container']);
    }
}
