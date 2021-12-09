<?php

class ModelExtensionModuleJusMuseology extends Model {
	public function install() {
		$this
			->db
			->query("
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "jus_museology_tpl` (
					  `category_id` int(11) NOT NULL,
					  `language_id` int(11) NOT NULL,
					  `meta_title` varchar(255) NULL,
					  `meta_description` varchar(255) NULL,
					  PRIMARY KEY (`category_id`, `language_id`)
					) DEFAULT CHARSET=utf8;
                ");
		/*
		$this->load->model('setting/event');
		$this
			->model_setting_event
			->addEvent(
				'jus_museology',
				'catalog/controller/account/wishlist/before',
				'extension/module/jus_wishlist/modify'
			);
		*/
		$this->load->model('setting/setting');
		$this
			->model_setting_setting
			->editSetting(
				'module_jus_museology',
				[
					'module_jus_museology_status' => 0
				]
			);
	}

	public function uninstall() {
		$this->db->query("DROP TABLE `" . DB_PREFIX . "jus_museology_tpl`");
		/*
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('jus_museology');
		*/
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_jus_museology');
	}

	/**
	 * Returns total
	 * @return int
	 */
	public function getTotalCategories() {
		$stmt = array(
			"SELECT",
				"COUNT(1) AS total",
			"FROM (",
				"SELECT",
					"1",
				"FROM",
					"`" . DB_PREFIX . "jus_museology_tpl`",
				"WHERE",
					"TRUE",
				"GROUP BY category_id",
			") `t`"
		);
		$query = $this->db->query(implode(" ", $stmt));
		return (int)$query->row['total'];
	}

	/**
	 * @param array $filter
	 * @return array
	 */
	public function getCategories(array $data) {
		$sql =
			implode (
				" ",
				array(
					"SELECT",
						"cp.category_id AS id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name",
					"FROM", DB_PREFIX . "category_path `cp`",
					"LEFT JOIN", DB_PREFIX . "category `c1` ON",
						"cp.category_id=c1.category_id",
					"LEFT JOIN", DB_PREFIX . "category c2 ON",
						"cp.path_id=c2.category_id",
					"LEFT JOIN", DB_PREFIX . "category_description cd1 ON",
						"cp.path_id=cd1.category_id",
					"LEFT JOIN", DB_PREFIX . "category_description cd2 ON",
						"cp.category_id=cd2.category_id",
					"INNER JOIN", "`". DB_PREFIX . "jus_museology_tpl` `t` ON",
						"t.category_id=cp.category_id AND cd1.language_id=t.language_id",
					"WHERE",
						"cd1.language_id=", (int)$this->config->get('config_language_id'), "AND cd2.language_id=cd1.language_id"
				)
			);
		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		$sql .= " GROUP BY cp.category_id";
		$sort_data = array(
			'name',
			'id'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		//dump($sql);exit;
		$query = $this->db->query($sql);
		return $query->rows;
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function getTpl($id) {
		$stmt = array(
			"SELECT",
				"*",
			"FROM",
				"`" . DB_PREFIX . "jus_museology_tpl`",
			"WHERE",
				"category_id=" . (int)$id
		);
		$res = $this->db->query(implode(" ", $stmt));
		if (count($res->rows) === 0) {
			throw
				new LogicException(
					sprintf("tpls with id=`%s` are not found", $id)
				);
		}
		return $res->rows;
	}

	/**
	 * @return void
	 */
	public function sync() {
		$stmt =
			array(
				"DELETE `t`",
					"FROM", DB_PREFIX . "jus_museology_tpl `t`",
				"LEFT JOIN", DB_PREFIX . "category `c` ON",
					"c.category_id=t.category_id",
				"LEFT JOIN", DB_PREFIX . "language `l` ON",
					"l.language_id=t.language_id AND l.status=1",
				"WHERE",
					"c.category_id IS NULL OR l.language_id IS NULL"
			);
		$this->db->query(implode(" ", $stmt));
		$stmt =
			array(
				"INSERT INTO", DB_PREFIX . "jus_museology_tpl (category_id, language_id)",
					"SELECT",
					  "c.category_id,",
					  "l.language_id",
					"FROM",
						DB_PREFIX . "category AS `c`",
					"JOIN", DB_PREFIX . "language AS `l`",
					"LEFT JOIN", DB_PREFIX . "jus_museology_tpl `t` ON",
						"t.category_id=c.category_id AND t.language_id=l.language_id",
					"WHERE",
						"t.category_id IS NULL AND l.status=1"
			);
		//dump(implode(" ", $stmt));exit;
		$this->db->query(implode(" ", $stmt));
	}
}
