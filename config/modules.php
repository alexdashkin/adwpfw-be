<?php

namespace AlexDashkin\Adwpfw\Modules;

return [
    'facade' => [
        'class' => Facade::class,
        'single' => true,
    ],
    'logger' => [
        'class' => Logger::class,
        'single' => true,
    ],
    'twig' => [
        'class' => Twig::class,
        'single' => true,
    ],
    'helpers' => [
        'class' => Helpers::class,
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
    'admin_ajax' => [
        'class' => Api\AdminAjax::class,
    ],
    'rest' => [
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
    'field.actions' => [
        'class' => AlexDashkin\Adwpfw\Fields\Actions::class,
    ],
    'field.button' => [
        'class' => AlexDashkin\Adwpfw\Fields\Button::class,
    ],
    'field.checkbox' => [
        'class' => AlexDashkin\Adwpfw\Fields\Checkbox::class,
    ],
    'field.custom' => [
        'class' => AlexDashkin\Adwpfw\Fields\Custom::class,
    ],
    'field.heading' => [
        'class' => AlexDashkin\Adwpfw\Fields\Heading::class,
    ],
    'field.hidden' => [
        'class' => AlexDashkin\Adwpfw\Fields\Hidden::class,
    ],
    'field.number' => [
        'class' => AlexDashkin\Adwpfw\Fields\Number::class,
    ],
    'field.password' => [
        'class' => AlexDashkin\Adwpfw\Fields\Password::class,
    ],
    'field.radio' => [
        'class' => AlexDashkin\Adwpfw\Fields\Radio::class,
    ],
    'field.select' => [
        'class' => AlexDashkin\Adwpfw\Fields\Select::class,
    ],
    'field.select2' => [
        'class' => AlexDashkin\Adwpfw\Fields\Select2::class,
    ],
    'field.text' => [
        'class' => AlexDashkin\Adwpfw\Fields\Text::class,
    ],
    'field.textarea' => [
        'class' => AlexDashkin\Adwpfw\Fields\Textarea::class,
    ],
];