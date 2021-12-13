<?php
/*
 * This file is part of JusMuseology module for OC3.x
 * (c) 2021 jigius@gmail.com
 */

use Jus\Core;

require_once DIR_SYSTEM . "/library/jus/autoloader.php";

class ControllerExtensionModuleJusMuseology extends Controller
{
    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    public function index()
    {
		$text =
			(new Core\LocalizedText($this->language))
				->withLoaded("extension/module/jus_museology");
		$url =
			(new Core\Url())
				->withParam("user_token", $this->session->data['user_token'])
				->withParam("type", "module");
		$this->load->model('setting/setting');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$m = new Core\Messages();
			try {
				$this->validate();
				$this->model_setting_setting->editSetting('module_jus_museology', $this->request->post);
				$url = $url->withPath('marketplace/extension');
				$m =
					$m
						->with(
							Core\MessageInterface::TYPE_SUCCESS,
							$text->fetch('text_success')
						);
				$sessionPrn = new Core\Messages\Stock\SessionPrn();
			} catch (Exception $ex) {
				$m =
					$m
						->with(
							Core\MessageInterface::TYPE_ERROR,
							$text->fetch($ex->getMessage())
						);
				$url =
					(new Core\UrlPrn($url))
						->with(
							'params',
							[
								'sort' => null,
								'order' => null,
								'page' => null,
								'module_jus_museology_status' => null
							]
						)
						->with('request', $this->request)
						->finished()
						->withPath('extension/module/jus_museology');
				$sessionPrn = new Core\Messages\SessionPrn();
			}
			$m
				->printed(
					$sessionPrn
						->with('session', $this->session)
				);
			$this->response->redirect($url->url($this->url));
		}
		$m =
			(new Core\Messages\MessagesPrn(
				new Core\Messages()
			))
				->with('session', $this->session)
				->finished();
		$this->document->setTitle($text->fetch('heading_title'));
		$this->load->model('extension/module/jus_museology');
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $text->fetch('text_home'),
			'href' => $url->withPath('common/dashboard')->url($this->url)
		);
		$data['breadcrumbs'][] = array(
			'text' => $text->fetch('text_extension'),
			'href' => $url->withPath('marketplace/extension')->url($this->url)
		);
		$url =
			(new Core\UrlPrn($url))
				->with('params', ['sort' => null, 'order' => null, 'page' => null])
				->with('request', $this->request)
				->finished();
		$data['breadcrumbs'][] = array(
			'text' => $text->fetch('heading_title'),
			'href' => $url->withPath('extension/module/jus_museology')->url($this->url)
		);
		$settings = $this->model_setting_setting->getSetting('module_jus_museology');
		$status = isset($settings['module_jus_museology_status'])?  (int)$settings['module_jus_museology_status']: 0;
		if ($status) {
			$data['sync'] = $url->withPath('extension/module/jus_museology/sync')->url($this->url);
			$data['tab_active'] = 'categories';
		} else {
			$data['sync'] = "";
			$data['tab_active'] = 'general';
		}
		$data['action'] = $url->withPath('extension/module/jus_museology')->url($this->url);
		$data['cancel'] = $url->withPath('marketplace/extension')->url($this->url);
		$data['categories'] = array();
		$filter_data = array(
			'sort'  => isset($this->request->get['sort'])? $this->request->get['sort']: 'id',
			'order' => isset($this->request->get['order'])? $this->request->get['order']: 'ASC',
			'start' => ((isset($this->request->get['page'])? (int)$this->request->get['page']: 1) - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$data['msg'] = $m->printed(new Core\Messages\ArrayPrn());
		try {
			$category_total = $this->model_extension_module_jus_museology->getTotalCategories();
			if (empty($data['msg']) && $status && $category_total == 0) {
				$this->load->model("catalog/category");
				if ($this->model_catalog_category->getTotalCategories()) {
					$m =
						$m
							->with(
								Core\MessageInterface::TYPE_WARNING,
								$text->fetch('text_no_categories')
							);
				}
			}
			$results = $this->model_extension_module_jus_museology->getCategories($filter_data);
		} catch (Exception $ex) {
			$m =
				$m
					->with(
						Core\MessageInterface::TYPE_ERROR,
						$ex->getMessage()
					);
		} finally {
			$data['msg'] = $m->printed(new Core\Messages\ArrayPrn());
		}
		foreach ($results as $result) {
			$data['categories'][] = array(
				  'id' => $result['id'],
				'name' => $result['name'],
				'edit' =>
					$url
						->withPath('extension/module/jus_museology/edit')
						->withParam('id', (int)$result['id'])
						->url($this->url)
			);
		}
		if (!isset($this->request->get['order']) || $this->request->get['order'] == "ASC") {
			$order = "ASC";
		} else {
			$order = "DESC";
		}
		if (!isset($this->request->get['sort']) || $this->request->get['sort'] == "id") {
			$sort = "id";
		} else {
			$sort = "name";
		}
		$url0 =
			$url
				->withParam(
					'order',
					$order == "ASC"? "DESC": "ASC"
				)
				->withOutParam('sort')
				->withPath("extension/module/jus_museology");
		$data['sort_name'] = $url0->withParam('sort', "name")->url($this->url);
		$data['sort_id'] = $url0->withParam('sort', "id")->url($this->url);
		$pagination = new Pagination();
		$pagination->total = $category_total;
		$page = isset($this->request->get['page'])? $this->request->get['page']: 1;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $url0->withParam('page', "{page}")->url($this->url);
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));
		$data['sort'] = $sort;
		$data['order'] = $order;
        if (isset($this->request->get['module_jus_museology_status'])) {
            $data['status'] = (int)$this->request->get['module_jus_museology_status'];
        } else {
			$settings = $this->model_setting_setting->getSetting('module_jus_museology');
			$data['status'] = isset($settings['module_jus_museology_status'])?  (int)$settings['module_jus_museology_status']: 0;
        }
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['entry_title'] = $this->language->get('heading_title');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
		(new Core\Messages\SessionPrn())
			->with('messages', $m)
			->with('session', $this->session)
			->finished();
        $this->response->setOutput($this->load->view('extension/module/jus_museology_list', $data));
    }

	public function sync() {
		$text =
			(new Core\LocalizedText($this->language))
				->withLoaded("extension/module/jus_museology");
		$m = new Jus\Core\Messages();
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('module_jus_museology');
		$status = isset($settings['module_jus_museology_status'])?  (int)$settings['module_jus_museology_status']: 0;
		if (!$status) {
			$m = $m->with(Core\MessageInterface::TYPE_ERROR, $text->fetch('text_failure_due_status'));
		} else {
			$this->load->model('extension/module/jus_museology');
			try {
				$this->model_extension_module_jus_museology->sync();
				$m = $m->with(Core\MessageInterface::TYPE_SUCCESS, $text->fetch('text_sync_success'));
			} catch (Exception $ex) {
				$m = $m->with(Core\MessageInterface::TYPE_ERROR, $text->fetch($ex->getMessage()));
			}
		}
		$m
			->printed(
				(new Core\Messages\SessionPrn())
					->with('session', $this->session)
			);
		$this
			->response
			->redirect(
				(new Core\UrlPrn(
					(new Core\Url())
						->withParam(
							'user_token',
							$this->session->data['user_token']
						)
						->withParam('type', "module")
						->withPath('extension/module/jus_museology')
				))
					->with('request', $this->request)
					->with('params', ['sort' => null, 'order' => null, 'page' => null])
					->finished()
					->url($this->url)
			);
	}

	public function edit() {
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		try {
			if (!isset($this->request->get['id']) || !is_numeric($this->request->get['id'])) {
				throw new InvalidArgumentException('error_args_are_invalid');
			}
			$this->load->language('extension/module/jus_museology');
			$this->load->model('extension/module/jus_museology');
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				$this->model_extension_module_jus_museology->updateTpl($this->request->get['id'], $this->request->post);
				$this->redirect($url, array('success' => $this->language->get('text_success')));
			}
			$this->document->setTitle($this->language->get('heading_edit_title'));
			$this->load->model('extension/module/jus_museology');
			$data['breadcrumbs'] = array();
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/jus_museology', 'user_token=' . $this->session->data['user_token'] . '&type=module' . $url, true)
			);
			$data['action'] = $this->url->link('extension/module/jus_museology/edit', 'user_token=' . $this->session->data['user_token'] . "&type=module" . $url, true);
			$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module' . $url, true);
			$this->load->model('localisation/language');
			$data['languages'] = $this->model_localisation_language->getLanguages();
			try {
				$data['tpl'] = $this->model_extension_module_jus_museology->getTpl($this->request->get['id']);
			} catch (LogicException $ex) {
				throw new InvalidArgumentException('error_args_are_invalid', 0, $ex);
			}
			$data['entry_title'] = $this->language->get('heading_title');
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->response->setOutput($this->load->view('extension/module/jus_museology_form', $data));
		} catch (InvalidArgumentException $ex) {
			$this->redirect($url, ['error' => $ex->getMessage()]);
		}
	}

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/jus_museology')) {
            throw new DomainException('error_permission');
        }
    }

    public function install() {
        $this->load->model('extension/module/jus_museology');
		$this->model_extension_module_jus_museology->install();
    }

    public function uninstall() {
		$this->load->model('extension/module/jus_museology');
		$this->model_extension_module_jus_museology->uninstall();
    }

}
