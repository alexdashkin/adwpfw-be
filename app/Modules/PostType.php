<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * singular*, plural, slug, labels, description, public
 */
class PostType extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        // Populate labels
        $this->setLabels();

        // Register CPT
        $this->addHook('init', [$this, 'register'], 20);
    }

    /**
     * Register CPT
     */
    public function register()
    {
        register_post_type($this->prefix . '_' . $this->getProp('slug'), $this->getProps());
    }

    /**
     * Generate labels from existing $singular and $plural
     */
    private function setLabels()
    {
        $labels = $this->getProp('labels');

        $singular = !empty($labels['singular']) ? $labels['singular'] : $this->getProp('singular');
        $plural = !empty($labels['plural']) ? $labels['plural'] : $this->getProp('plural');

        $defaults = [
            'name' => $plural,
            'singular_name' => $singular,
            'add_new' => 'Add New',
            'add_new_item' => 'Add New ' . $singular,
            'edit_item' => 'Edit ' . $singular,
            'new_item' => 'New ' . $singular,
            'all_items' => 'All ' . $plural,
            'view_item' => 'View ' . $singular,
            'search_items' => 'Search ' . $plural,
            'not_found' => "No $plural Found",
            'not_found_in_trash' => "No $plural Found in Trash",
        ];

        $this->setProp('labels', array_merge($defaults, $labels));
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'plural' => $this->getProp('singular') . 's',
            'slug' => function () {
                return sanitize_key(str_replace(' ', '_', $this->getProp('singular')));
            },
            'public' => true,
        ];
    }
}
