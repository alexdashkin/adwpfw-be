<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Post State
 */
class PostState extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('display_post_states', [$this, 'register']);
    }

    /**
     * Register Post States
     *
     * @param array $states States list
     * @param \WP_Post $post Post
     * @return array Modified States
     */
    public function register(array $states, \WP_Post $post): array
    {
        if ($post->ID === $this->getProp('postId')) {
            $state = $this->getProp('state');
            $states[$this->getProp('slug')] = $state;
        }

        return $states;
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        $baseProps = parent::getPropDefs();

        $fieldProps = [
            'postId' => [
                'type' => 'int',
                'required' => true,
            ],
            'state' => [
                'type' => 'string',
                'required' => true,
            ],
            'slug' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '-', $this->getProp('state')));
                },
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }

}
