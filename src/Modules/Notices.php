<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Notice;

/**
 * Admin notices.
 */
class Notices extends ModuleWithItems
{
    /**
     * @var Notice[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        add_action('admin_notices', [$this, 'process']);
    }

    /**
     * Add Notice.
     *
     * @param array $data Data passed to Notice constructor.
     *
     * @see Notice::__construct();
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Notice($this->app, $data);
    }

    /**
     * Show a notice.
     *
     * @param string $id Notice ID
     */
    public function show($id)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            $item->show();
        }
    }

    /**
     * Stop showing a notice.
     *
     * @param string $id Notice ID
     */
    public function stop($id)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            $item->stop();
        }
    }

    /**
     * Dismiss a notice.
     *
     * @param string $id Notice ID
     */
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
