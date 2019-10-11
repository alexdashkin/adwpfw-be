<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\Notice;

/**
 * Admin notices.
 */
class Notices extends ModuleWithItems
{
    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add Notice.
     *
     * @param array $data
     *
     * @see Notice::__construct();
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Notice($data, $this->app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function init()
    {
        add_action('admin_notices', [$this, 'process']);
    }

    public function show($id)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            $item->show();
        }
    }

    public function stop($id)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            $item->stop();
        }
    }

    public function dismiss($id)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            $item->dismiss();
        }
    }

    /**
     * Process Notices.
     */
    public function process()
    {
        foreach ($this->items as $item) {
            $item->process();
        }
    }
}
