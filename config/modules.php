<?php

return [
    'logger' => [
        'class' => AlexDashkin\Adwpfw\Modules\Logger::class,
        'single' => true,
    ],
    'twig' => [
        'class' => AlexDashkin\Adwpfw\Modules\Twig::class,
        'single' => true,
    ],
    'helpers' => [
        'class' => AlexDashkin\Adwpfw\Modules\Helpers::class,
        'single' => true,
    ],
    'db' => [
        'class' => AlexDashkin\Adwpfw\Items\Db::class,
    ],
    'hook' => [
        'class' => AlexDashkin\Adwpfw\Items\Hook::class,
    ],
    'asset.css' => [
        'class' => AlexDashkin\Adwpfw\Items\Assets\Css::class,
    ],
    'asset.js' => [
        'class' => AlexDashkin\Adwpfw\Items\Assets\Js::class,
    ],
    'admin_ajax' => [
        'class' => AlexDashkin\Adwpfw\Items\Api\AdminAjax::class,
    ],
    'rest' => [
        'class' => AlexDashkin\Adwpfw\Items\Api\Rest::class,
    ],
    'admin_page' => [
        'class' => AlexDashkin\Adwpfw\Items\AdminPage::class,
    ],
    'admin_page_tab' => [
        'class' => AlexDashkin\Adwpfw\Items\AdminPageTab::class,
    ],
    'admin_bar' => [
        'class' => AlexDashkin\Adwpfw\Items\AdminBar::class,
    ],
    'metabox' => [
        'class' => AlexDashkin\Adwpfw\Items\Metabox::class,
    ],
    'notice' => [
        'class' => AlexDashkin\Adwpfw\Items\Notice::class,
    ],
    'post_state' => [
        'class' => AlexDashkin\Adwpfw\Items\PostState::class,
    ],
    'post_type' => [
        'class' => AlexDashkin\Adwpfw\Items\PostType::class,
    ],
    'profile_section' => [
        'class' => AlexDashkin\Adwpfw\Items\ProfileSection::class,
    ],
    'shortcode' => [
        'class' => AlexDashkin\Adwpfw\Items\Shortcode::class,
    ],
    'updater.plugin' => [
        'class' => AlexDashkin\Adwpfw\Items\Updater\Plugin::class,
    ],
    'updater.theme' => [
        'class' => AlexDashkin\Adwpfw\Items\Updater\Theme::class,
    ],
    'widget' => [
        'class' => AlexDashkin\Adwpfw\Items\Widget::class,
    ],
    'customizer.panel' => [
        'class' => AlexDashkin\Adwpfw\Items\Customizer\Panel::class,
    ],
    'customizer.section' => [
        'class' => AlexDashkin\Adwpfw\Items\Customizer\Section::class,
    ],
    'customizer.setting' => [
        'class' => AlexDashkin\Adwpfw\Items\Customizer\Setting::class,
    ],
];