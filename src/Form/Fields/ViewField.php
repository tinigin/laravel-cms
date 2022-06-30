<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Field;

/**
 * Class ViewField.
 *
 * @method ViewField name(string $value = null)
 * @method ViewField help(string $value = null)
 */
class ViewField extends Field
{
    /**
     * @param string $view
     *
     * @return ViewField
     */
    public function view(string $view): self
    {
        $this->view = $view;

        return $this;
    }
}
