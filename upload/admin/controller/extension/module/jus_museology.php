<?php
/*
 * This file is part of JusMuseology module for OC3.x
 * (c) 2021 jigius@gmail.com
 */

use Jus\Core;

require_once DIR_SYSTEM . "/library/jus/autoloader.php";

class ControllerExtensionModuleJusMuseology extends Controller
{
	/**
	 * Action Index
	 * @return void
	 */
    public function index()
    {
		$text =
			(new Core\LocalizedText($this->language))
				->withLoaded("extension/module/jus_museology");
		$resp =
			(new Core\Response($this->response))
				->withUrl(
					(new Core\Url())
						->withParam("user_token", $this->session->data['user_token'])
						->withParam("type", "module")
				);
		$sessionPrn = new Core\Messages\SessionPrn();
		$m = new Core\Messages();
		$this->load->model('setting/setting');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			/* handling */
			try {
				$this->validate();
				$this->model_setting_setting->editSetting('module_jus_museology', $this->request->post);
				$resp =
					$resp
						->withUrl(
							$resp
								->url()
								->withPath('marketplace/extension')
						)
						->withRedirect($this->url);
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
				$resp =
					$resp
						->withUrl(
							(new Core\UrlPrn($resp->url()))
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
								->withPath('extension/module/jus_museology')
						)
						->withRedirect($this->url);
			}
		} else {
			/* outputting */
			$m =
				(new Core\Messages\MessagesPrn($m))
					->with('session', $this->session)
					->finished();
			$this->document->setTitle($text->fetch('heading_title'));
			$this->load->model('extension/module/jus_museology');
			$data['breadcrumbs'] = array();
			$data['breadcrumbs'][] = array(
				'text' => $text->fetch('text_home'),
				'href' => $resp->url()->withPath('common/dashboard')->url($this->url)
			);
			$data['breadcrumbs'][] = array(
				'text' => $text->fetch('text_extension'),
				'href' => $resp->url()->withPath('marketplace/extension')->url($this->url)
			);
			$resp =
				$resp
					->withUrl(
					(new Core\UrlPrn($resp->url()))
						->with('params', ['sort' => null, 'order' => null, 'page' => null])
						->with('request', $this->request)
						->finished()
					);
			$data['breadcrumbs'][] = array(
				'text' => $text->fetch('heading_title'),
				'href' => $resp->url()->withPath('extension/module/jus_museology')->url($this->url)
			);
			$settings = $this->model_setting_setting->getSetting('module_jus_museology');
			$status = isset($settings['module_jus_museology_status']) ? (int)$settings['module_jus_museology_status'] : 0;
			if ($status) {
				$data['sync'] = $resp->url()->withPath('extension/module/jus_museology/sync')->url($this->url);
				$data['tab_active'] = 'categories';
			} else {
				$data['sync'] = "";
				$data['tab_active'] = 'general';
			}
			$data['action'] = $resp->url()->withPath('extension/module/jus_museology')->url($this->url);
			$data['cancel'] = $resp->url()->withPath('marketplace/extension')->url($this->url);
			$data['categories'] = array();
			$filter_data = array(
				'sort' => isset($this->request->get['sort']) ? $this->request->get['sort'] : 'id',
				'order' => isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC',
				'start' => ((isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1) - 1) * $this->config->get('config_limit_admin'),
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
			}
			$data['msg'] = $m->printed(new Core\Messages\ArrayPrn());
			$url0 = $resp->url()->withPath('extension/module/jus_museology/edit');
			foreach ($results as $result) {
				$data['categories'][] = array(
					'id' => $result['id'],
					'name' => $result['name'],
					'edit' =>
						$url0
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
				$resp
					->url()
					->withParam(
						'order',
						$order == "ASC" ? "DESC" : "ASC"
					)
					->withOutParam('sort')
					->withPath("extension/module/jus_museology");
			$data['sort_name'] = $url0->withParam('sort', "name")->url($this->url);
			$data['sort_id'] = $url0->withParam('sort', "id")->url($this->url);
			$pagination = new Pagination();
			$pagination->total = $category_total;
			$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url =
				$resp
					->url()
					->withParam('page', "{page}")
					->withPath("extension/module/jus_museology")
					->url($this->url);
			$data['pagination'] = $pagination->render();
			$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));
			$data['sort'] = $sort;
			$data['order'] = $order;
			if (isset($this->request->get['module_jus_museology_status'])) {
				$data['status'] = (int)$this->request->get['module_jus_museology_status'];
			} else {
				$settings = $this->model_setting_setting->getSetting('module_jus_museology');
				$data['status'] = isset($settings['module_jus_museology_status']) ? (int)$settings['module_jus_museology_status'] : 0;
			}
			$this->load->model('localisation/language');
			$data['languages'] = $this->model_localisation_language->getLanguages();
			$data['entry_title'] = $this->language->get('heading_title');
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$resp = $resp->withOutput($this->load->view('extension/module/jus_museology_list', $data));
		}
		$m->printed($sessionPrn->with('session', $this->session));
        $resp->process();
    }

	/**
	 * Action Sync
	 * @return void
	 */
	public function sync() {
		$text =
			(new Core\LocalizedText($this->language))
				->withLoaded("extension/module/jus_museology");
		$m = new Jus\Core\Messages();
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('module_jus_museology');
		$status = isset($settings['module_jus_museology_status'])?  (int)$settings['module_jus_museology_status']: 0;
		try {
			if (!$status) {
				throw new DomainException("text_failure_due_status");
			}
			$this->load->model('extension/module/jus_museology');
			$this->model_extension_module_jus_museology->sync();
			$m = $m->with(Core\MessageInterface::TYPE_SUCCESS, $text->fetch('text_sync_success'));
		} catch (Exception $ex) {
			$m = $m->with(Core\MessageInterface::TYPE_ERROR, $text->fetch($ex->getMessage()));
		}
		$m
			->printed(
				(new Core\Messages\SessionPrn())
					->with('session', $this->session)
			);
		(new Core\Response($this->response))
			->withUrl(
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
			)
			->withRedirect($this->url)
			->process();
	}

	/**
	 * Action Form edit
	 * @return void
	 */
	public function edit() {
		$text =
				(new Core\LocalizedText($this->language))
					->withLoaded("extension/module/jus_museology");
		$resp =
			(new Core\Response($this->response))
				->withUrl(
					(new Core\Url())
						->withParam("user_token", $this->session->data['user_token'])
						->withParam("type", "module")
				);
		$m = new Core\Messages();
		try {
			if (!isset($this->request->get['id']) || !is_numeric($this->request->get['id'])) {
				throw new InvalidArgumentException('error_args_are_invalid');
			}
			$this->load->model('extension/module/jus_museology');
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				/* request handling */
				$this->validate();
				$this->model_extension_module_jus_museology->updateTpl($this->request->get['id'], $this->request->post);
				$resp =
					$resp
						->withUrl(
							(new Core\UrlPrn($resp->url()))
								->with(
									'params',
									[
										'sort' => null,
										'order' => null,
										'page' => null
									]
								)
								->with('request', $this->request)
								->finished()
								->withPath('extension/module/jus_museology')
						);
				$m = $m->with(Core\MessageInterface::TYPE_SUCCESS, $text->fetch('text_success'));
				$resp = $resp->withRedirect($this->url);
			} else {
				/* output */
				$m =
					(new Core\Messages\MessagesPrn($m))
						->with('session', $this->session)
						->finished();
				$this->document->setTitle($this->language->get('heading_edit_title'));
				$data['breadcrumbs'] = array();
				$data['breadcrumbs'][] = array(
					'text' => $text->fetch('text_home'),
					'href' => $resp->url()->withPath('common/dashboard')->url($this->url)
				);
				$data['breadcrumbs'][] = array(
					'text' => $text->fetch('text_extension'),
					'href' => $resp->url()->withPath('marketplace/extension')->url($this->url)
				);
				$url0 =
					(new Core\UrlPrn($resp->url()))
						->with(
							'params',
							[
								'sort' => null,
								'order' => null,
								'page' => null
							]
						)
						->with('request', $this->request)
						->finished();
				$data['breadcrumbs'][] = array(
					'text' => $text->fetch('heading_title'),
					'href' => $url0->withPath('extension/module/jus_museology')->url($this->url)
				);
				$data['action'] =
					$url0
						->withPath('extension/module/jus_museology/edit')
						->withParam('id', $this->request->get['id'])
						->url($this->url);
				$data['cancel'] = $url0->withPath('extension/module/jus_museology')->url($this->url);
				$this->load->model('localisation/language');
				$data['languages'] = $this->model_localisation_language->getLanguages();
				$res = $this->model_extension_module_jus_museology->getTpl($this->request->get['id']);
				if (empty($res)) {
					throw new InvalidArgumentException('error_args_are_invalid');
				}
				$data['tpl'] = $res;
				$data['entry_title'] = $this->language->get('heading_title');
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
				$data['msg'] = $m->printed(new Core\Messages\ArrayPrn());
				$resp = $resp->withOutput($this->load->view('extension/module/jus_museology_form', $data));
			}
		} catch (InvalidArgumentException $ex) {
			$m = $m->with(Core\MessageInterface::TYPE_ERROR, $text->fetch('error_args_are_invalid'));
			$resp =
				$resp
					->withUrl(
						(new Core\UrlPrn($resp->url()))
							->with(
								'params',
								[
									'sort' => null,
									'order' => null,
									'page' => null
								]
							)
							->with('request', $this->request)
							->finished()
							->withPath('extension/module/jus_museology')
					)
					->withRedirect($this->url);
		} catch (Exception $ex) {
			$m = $m->with(Core\MessageInterface::TYPE_ERROR, $text->fetch($ex->getMessage()));
			$resp =
				$resp
					->withUrl(
						(new Core\UrlPrn($resp->url()))
							->with(
								'params',
								[
									'sort' => null,
									'order' => null,
									'page' => null
								]
							)
							->with('request', $this->request)
							->finished()
							->withPath('extension/module/jus_museology/edit')
							->withParam('id', $this->request->get['id'])
					)
					->withRedirect($this->url);
		}
		$m
			->printed(
				(new Core\Messages\SessionPrn())
					->with('session', $this->session)
			);
		$resp->process();
	}

	/**
	 * General validator
	 * @return void
	 */
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/jus_museology')) {
            throw new InvalidArgumentException('error_permission');
        }
    }

	/**
	 * Action Install
	 * @return void
	 */
    public function install() {
        $this->load->model('extension/module/jus_museology');
		$this->model_extension_module_jus_museology->install();
    }

	/**
	 * Action Uninstall
	 * @return void
	 */
    public function uninstall() {
		$this->load->model('extension/module/jus_museology');
		$this->model_extension_module_jus_museology->uninstall();
    }
}
