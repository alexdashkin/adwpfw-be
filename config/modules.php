<?php

namespace AlexDashkin\Adwpfw\Modules;

return [
    'twig' => [
        'class' => Twig::class,
        'single' => true,
    ],
    'db' => [
        'class' => Db::class,
        'single' => true,
    ],
    'query' => [
        'class' => Query::class,
    ],
    'hook' => [
        'class' => Hook::class,
    ],
    'asset.css' => [
        'class' => Assets\Css::class,
    ],
    'asset.js' => [
        'class' => Assets\Js::class,
    ],
    'api.ajax' => [
        'class' => Api\AdminAjax::class,
    ],
    'api.rest' => [
        'class' => Api\Rest::class,
    ],
    'admin_page' => [
        'class' => AdminPage::class,
    ],
    'admin_page_tab' => [
        'class' => AdminPageTab::class,
    ],
    'admin_bar' => [
        'class' => AdminBar::class,
    ],
    'dashboard_widget' => [
        'class' => DbWidget::class,
    ],
    'cron' => [
        'class' => CronJob::class,
    ],
    'metabox' => [
        'class' => Metabox::class,
    ],
    'notice' => [
        'class' => Notice::class,
    ],
    'post_state' => [
        'class' => PostState::class,
    ],
    'post_type' => [
        'class' => PostType::class,
    ],
    'profile_section' => [
        'class' => ProfileSection::class,
    ],
    'term_meta' => [
        'class' => TermMeta::class,
    ],
    'shortcode' => [
        'class' => Shortcode::class,
    ],
    'updater.plugin' => [
        'class' => Updater\Plugin::class,
    ],
    'updater.theme' => [
        'class' => Updater\Theme::class,
    ],
    'widget' => [
        'class' => Widget::class,
    ],
    'customizer.panel' => [
        'class' => Customizer\Panel::class,
    ],
    'customizer.section' => [
        'class' => Customizer\Section::class,
    ],
    'customizer.setting' => [
        'class' => Customizer\Setting::class,
    ],
    'field' => [
        'class' => Field::class,
    ],
];