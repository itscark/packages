<?php

namespace Iwaves\ComposerInspector;

use Laravel\Nova\ResourceTool;

class ComposerInspector extends ResourceTool
{
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Composer Inspector';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'composer-inspector';
    }
}
