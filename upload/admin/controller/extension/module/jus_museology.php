<?php
/*
 * This file is part of JusMuseology module for OC3.x
 * (c) 2021 jigius@gmail.com
 */
class ControllerExtensionModuleJusMuseology extends Controller
{
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    public function index()
    {
		$this->load->language('extension/module/jus_museology');
		$this->load->model('setting/setting');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_jus_museology', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/jus_museology');
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}
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
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/jus_museology', 'user_token=' . $this->session->data['user_token'] . '&type=module' . $url, true)
		);
		$data['sync'] = $this->url->link('extension/module/jus_museology/sync', 'user_token=' . $this->session->data['user_token'], true);
		$data['action'] = $this->url->link('extension/module/jus_museology', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['categories'] = array();
		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$category_total = $this->model_extension_module_jus_museology->getTotalCategories();
		if (!isset($this->error['warning']) && $category_total == 0) {
			$this->load->model("catalog/category");
			if ($this->model_catalog_category->getTotalCategories()) {
				$this->error['warning'] = $this->language->get('text_no_categories');
			}
		}
		$results = $this->model_extension_module_jus_museology->getCategories($filter_data);
		foreach ($results as $result) {
			$data['categories'][] = array(
				'id' => $result['id'],
				'name'        => $result['name'],
				'edit'        =>
					$this
						->url
						->link(
							'extension/module/jus_museology/edit',
							'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url,
							true
						)
			);
		}
		if (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$data['warning'] = '';
		}
		if (isset($this->error['error'])) {
			$data['error'] = $this->error['error'];
		} elseif (isset($this->session->data['error'])) {
			$data['error'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error'] = '';
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		$url = '';
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$data['sort_name'] =
			$this
				->url
				->link(
					'extension/module/jus_museology',
					'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url,
					true
				);
		$data['sort_id'] =
			$this
				->url
				->link(
					'extension/module/jus_museology',
					'user_token=' . $this->session->data['user_token'] . '&sort=id' . $url,
					true
				);
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url =
			$this
				->url
				->link(
					'extension/module/jus_museology',
					'user_token=' . $this->session->data['user_token'] . $url . '&page={page}',
					true
				);
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));
		$data['sort'] = $sort;
		$data['order'] = $order;
        if (isset($this->request->post['module_jus_museology_status'])) {
            $data['status'] = (int)$this->request->post['module_jus_museology_status'];
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
        $this->response->setOutput($this->load->view('extension/module/jus_museology', $data));
    }

	public function sync() {
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('module_jus_museology');
		$status = isset($settings['module_jus_museology_status'])?  (int)$settings['module_jus_museology_status']: 0;
		$this->load->language('extension/module/jus_museology');
		if ($status) {
			$this->load->model('extension/module/jus_museology');
			$this->model_extension_module_jus_museology->sync();
			$this->session->data['success'] = $this->language->get('text_sync_success');
		} else {
			$this->session->data['error'] = $this->language->get('text_failure_due_status');
		}
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
		$this->response->redirect($this->url->link('extension/module/jus_museology', 'user_token=' . $this->session->data['user_token'] . '&type=module' . $url, true));
	}

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/jus_museology')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return count($this->error) === 0;
    }

    public function install()
    {
        $this->load->model('extension/module/jus_museology');
		$this->model_extension_module_jus_museology->install();
    }

    public function uninstall()
    {
		$this->load->model('extension/module/jus_museology');
		$this->model_extension_module_jus_museology->uninstall();
    }

}
