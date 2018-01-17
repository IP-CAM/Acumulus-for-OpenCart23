<?php

/** @noinspection PhpUndefinedClassInspection */
/**
 * This is the Acumulus admin side controller.
 *
 * @property \Cart\User $user;
 */
class ControllerExtensionModuleAcumulus extends Controller
{
    /** @var \Siel\Acumulus\OpenCart\Helpers\OcHelper */
    static private $staticOcHelper = null;

    /** @var \Siel\Acumulus\OpenCart\OpenCart2\OpenCart23\Helpers\OcHelper */
    private $ocHelper = null;

    /**
     * Constructor.
     *
     * @param \Registry $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        if ($this->ocHelper === NULL) {
            if (static::$staticOcHelper === NULL) {
                // Load autoloader, container and then our helper that contains
                // OC1, OC2 and OC3 shared code.
                require_once(DIR_SYSTEM . 'library/siel/acumulus/SielAcumulusAutoloader.php');
                SielAcumulusAutoloader::register();
                $container = new \Siel\Acumulus\Helpers\Container($this->getShopNamespace());
                static::$staticOcHelper = $container->getInstance('OcHelper', 'Helpers', array($this->registry, $container));
            }
            $this->ocHelper = static::$staticOcHelper;
        }
    }

    /**
     * Returns the Shop namespace to use for this OC version.
     *
     * @return string
     *   The Shop namespace to use for this OC version.
     */
    protected function getShopNamespace()
    {
        $result = sprintf('OpenCart\OpenCart%1$u\OpenCart%1$u%2$u', substr(VERSION, 0, 1), substr(VERSION, 2, 1));
        return $result;
    }

    /**
     * Returns the location of the extension's files.
     *
     * @return string
     *   The location of the extension's files.
     */
    protected function getLocation()
    {
        return $this->ocHelper->getLocation();
    }

    /**
     * Install controller action, called when the module is installed.
     *
     * @throws \Exception
     */
    public function install()
    {
        $this->ocHelper->install();
    }

    /**
     * Uninstall function, called when the module is uninstalled by an admin.
     *
     * @throws \Exception
     */
    public function uninstall()
    {
        $this->ocHelper->uninstall();
    }

    /**
     * Main controller action: show/process the basic settings form for this
     * module.
     *
     * @throws \Exception
     */
    public function index()
    {
        $this->ocHelper->config();
    }

    /**
     * Controller action: show/process the advanced settings form for this
     * module.
     */
    public function advanced()
    {
        $this->ocHelper->advancedConfig();
    }

    /**
     * Controller action: show/process the batch form for this module.
     */
    public function batch()
    {
        $this->ocHelper->batch();
    }

    /**
     * Explicit confirmation step to allow to retain the settings.
     *
     * The normal uninstall action will unconditionally delete all settings.
     *
     * @throws \Exception
     */
    public function confirmUninstall()
    {
        $this->ocHelper->confirmUninstall();
    }

    /**
     * Event handler that executes on the creation or update of an order.
     *
     * The arguments passed in depend on the version of OC (and possibly if it
     * is OC self or another plugin that triggered the event).
     *
     * Note: in admin it can only be another plugin as OC self redirects to the
     * catalog part to update an order.
     */
    public function eventOrderUpdate()
    {
        $order_id = $this->ocHelper->extractOrderId(func_get_args());
        $this->ocHelper->eventOrderUpdate((int) $order_id);
    }

    /**
     * Adds our menu-items to the admin menu.
     *
     * @param string $route
     *   The current route (common/column_left).
     * @param array $data
     *   The data as will be passed to the view.
     */
    public function eventViewColumnLeft(/** @noinspection PhpUnusedParameterInspection */$route, &$data)
    {
        if ($this->user->hasPermission('access', $this->getLocation())) {
            $this->ocHelper->eventViewColumnLeft($data['menus']);
        }
    }
}
