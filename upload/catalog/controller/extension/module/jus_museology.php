<?php
/*
 * This file is part of JusMuseology module for OC3.x
 * (c) 2021 jigius@gmail.com
 */

class ControllerExtensionModuleJusMuseology extends Controller {
	/**
	 * The original customer
	 * @var object|null
	 */
	static private $original_customer = null;

	/**
	 * Cntr
	 * @param $registry
	 */
    public function __construct($registry)
    {
        parent::__construct($registry);
    }

	/**
	 * Restores the early modified state
	 * @return void
	 */
	public function restore()
	{
		if (self::$original_customer) {
			$this->customer = self::$original_customer;
			self::$original_customer = null;
		}
	}

	/**
	 * Does a some modification
	 * @return void
	 */
    public function modify() {
		if ($this->enabled()) {
			if (!$this->customer->isLogged()) {
				$this->replaceOriginalModel();
				$this->replaceOriginalCustomer();
			}
		}
    }

	/**
	 * Replaces the original wishlist model with a new one
	 * @return void
	 */
	private function replaceOriginalModel()
	{
		$this->load->model('extension/module/jus_wishlist');
		$this
			->registry
			->set(
				"model_account_wishlist",
				$this->model_extension_module_jus_wishlist
			);
	}

	/**
	 * Replaces an original customer with a fake
	 * @return void
	 */
	private function replaceOriginalCustomer()
	{
		self::$original_customer = $this->customer;
		$this->customer = new FakeLoggedCustomer();
	}

	/**
	 * Returns true if the module is enabled
	 * @return bool
	 */
	private function enabled(): bool
	{
		$this->load->model('setting/setting');
		return !!$this->model_setting_setting->getSettingValue("module_jus_wishlist_status");
	}
}
