<?php

namespace AlexDashkin\Adwpfw\Modules;

return [
    'hook' => Hook::class,
    'asset.css' => Assets\Css::class,
    'asset.js' => Assets\Js::class,
    'api.ajax' => Api\AdminAjax::class,
    'api.rest' => Api\Rest::class,
    'admin_page' => AdminPage::class,
    'admin_page_tab' => AdminPageTab::class,
    'admin_bar' => AdminBar::class,
    'block' => Block::class,
    'dashboard_widget' => DbWidget::class,
    'cron' => CronJob::class,
    'metabox' => Metabox::class,
    'notice' => Notice::class,
    'post_state' => PostState::class,
    'cpt' => Cpt::class,
    'profile_section' => ProfileSection::class,
    'term_meta' => TermMeta::class,
    'shortcode' => Shortcode::class,
    'updater.plugin' => Updater\Plugin::class,
    'updater.theme' => Updater\Theme::class,
    'widget' => Widget::class,
    'customizer.panel' => Customizer\Panel::class,
    'customizer.section' => Customizer\Section::class,
    'customizer.setting' => Customizer\Setting::class,
    'field' => Fields\Field::class,
    'field.checkbox' => Fields\Checkbox::class,
    'field.select' => Fields\Select::class,
    'field.select2' => Fields\Select2::class,
];