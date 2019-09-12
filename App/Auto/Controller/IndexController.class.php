<?php
namespace Auto\Controller;
use Think\Controller;

class IndexController extends Controller {

	public $module = 'Admin'; //默认生成或删除的模块

	public $modelDir = 'Common'; //model的文件夹 默认Common

	protected function _initialize() {
		if (!session('Admin-user')) {
			$this->error('请先登录!');
		}
	}
	//字段对应名称
	protected $fieldNameMap = [
		'id' => 'ID',
		'name' => '名称',
		'content' => '内容',
		'create_time' => '创建时间',
		'update_time' => '更新时间',
	];
	//字段对应搜索
	protected $fieldSearchMap = [
		'id' => true,
		'name' => true,
	];
	//字段对应输入
	protected $fieldNotInputMap = [
		'id' => true,
		'update_time' => true,
		'create_time' => true,
	];
	//字段对应显示
	protected $fieldNotListMap = [
		'update_time' => true,
		'create_time' => true,
	];
	//类型对应必须
	protected $typeNotRequiredMap = [
		'file' => true,
	];

	public function index() {
		$this->redirect('step1');
	}
	public function step1() {
		$tables = M()->db()->getTables();
		$dbPrefix = C('DB_PREFIX');
		foreach ($tables as $k => $table) {
			$tables[$k] = substr($table, strlen($dbPrefix));
		}
		$this->assign('tables', $tables);
		$this->display('step1');
		exit();
	}
	public function step2($table = '') {
		//===获取model名称======
		session('tableName', $table);
		$tableNameArr = explode('_', $table);
		foreach ($tableNameArr as $k => $v) {
			$tableNameArr[$k] = ucfirst($v);
		}
		$modelName = implode('', $tableNameArr);
		session('modelName', $modelName);
		$this->assign('modelName', $modelName);
		//===增加create_time、update_time字段(如果没有)======
		$model = D($modelName);
		$modelFields = $model->getDbFields();
		if (!in_array('create_time', $modelFields)) {
			$sql = 'alter table ' . C('DB_PREFIX') . $table . '  add create_time int(11) default \'0\'';
			M()->execute($sql);
			$this->redirect('/Auto/Index/step2/table/' . $table);
		}
		if (!in_array('update_time', $modelFields)) {
			$sql = 'alter table ' . C('DB_PREFIX') . $table . '  add update_time int(11) default \'0\'';
			$r = M()->execute($sql);
			$this->redirect('/Auto/Index/step2/table/' . $table);
		}
		//===获取当前model字段======
		$model = D($modelName);
		$modelFields = $model->getDbFields();
		$modelInfo = $model->query('show full fields from ' . $model->getTableName());
		$this->assign('xlsFields', $modelFields); //导入导出字段显示用
		foreach ($modelFields as $k => $modelField) {
			$modelFields[$k] = [];
			$modelFields[$k]['field'] = $modelField;
			//==自动填充字段名称====
			if ($this->fieldNameMap[$modelField]) {
				$modelFields[$k]['fieldName'] = $this->fieldNameMap[$modelField];
			}
			//==用注释填充名称====
			if ($modelInfo[$k]['comment'] != '') {
				$modelFields[$k]['fieldName'] = $modelInfo[$k]['comment'];
			}
			//==字段是否需要输入====
			if ($this->fieldNotInputMap[$modelField]) {
				$modelFields[$k]['fieldInput'] = false;
			} else {
				$modelFields[$k]['fieldInput'] = true;
			}

			//==字段是否需要显示====
			if ($this->fieldNotListMap[$modelField]) {
				$modelFields[$k]['fieldList'] = false;
			} else {
				$modelFields[$k]['fieldList'] = true;
			}
			//==字段是否需要详情显示====
			if ($this->fieldNotDetailMap[$modelField]) {
				$modelFields[$k]['fieldDetail'] = false;
			} else {
				$modelFields[$k]['fieldDetail'] = true;
			}
			//==字段是否需要搜索====
			if ($this->fieldSearchMap[$modelField]) {
				$modelFields[$k]['fieldSearch'] = true;
			}
		}
		$this->assign('modelFields', $modelFields);
		$this->display();
	}
	public function step3() {
		if (IS_POST) {
			//外键提示
			$this->foreignTips($_POST);
			//header
			$this->createHeader($_POST);
			echo '<meta charset="utf-8" />';
			//model
			$this->createModel($_POST);
			//view
			// if ($_POST['xls_a'] == 3) {
			// 	//$this->createIView($_POST);
			// }
			//controller
			$this->createController($_POST);

		}
	}

	protected function createController($data) {
		$controllerContent = '<?php
namespace ' . $this->module . '\Controller;
use Think\Controller;

class ' . ucfirst(session('modelName')) . 'Controller extends \Org\Util\AdminController {
    /**
     * CRUD前后置操作
     */
    public function interfaceBeforeCreate() {
    }
    public function interfaceAfterCreate($id) {
    }
    public function interfaceBeforeDelete($id) {
    }
    public function interfaceAfterDelete($id) {
    }
    public function interfaceBeforeUpdate($id) {
    }
    public function interfaceAfterUpdate($id) {
    }

    /**
     * list页面按钮接口
     * return $html
     */
    public function tableOpeationButton() {
    	return "";
    }
    public function wapTableOpeationButton() {
    	return "";
    }
    
    /**
     * 父类中已经有典型的CRUD操作并预留入口
     * 如有较大改动则覆盖一下
     */
    public function create() {
        parent::create();
    }

    public function delete() {
        parent::delete();
    }

    public function update($id=0) {
        parent::update($id);
    }

    public function list() {
        parent::list();
    }

    public function detail($id = 0) {
        parent::detail($id);
    }

    public function import() {
        parent::import();
    }

    public function export() {
        parent::export();
    }
}';
		//==创建文件====
		if ($data['file_a'] == 2) {
			$controllerFile = './App/' . $this->module . '/Controller/' . session('modelName') . 'Controller.class.php';
			if (!file_exists($controllerFile)) {
				file_put_contents($controllerFile, $controllerContent);
			} else {
				echo 'controller文件以及存在，请复制增加：<br/>';
				dump($controllerContent);
			}
		} else {
			dump($controllerContent);
		}
	}

	protected function createModel($data) {
		//MODEL-START
		$modelStart = '<?php
namespace ' . $this->modelDir . '\Model;
use Think\Model;
class ' . session('modelName') . 'Model extends FeasModel {';
		//MODEL-CONFIG
		$modelConfig = '';
		$modelConfig .= '
    public $modelName = \''.$data['name_a'].'\';
    public $modelIcon = \''.$data['icon_a'].'\';';

		if ($data['excel_a'] == 2) {
			$modelConfig .= '
    public $excel = true;';
		} else {
			$modelConfig .= '
    public $excel = false;';
		}

		if ($data['update_a'] == 2) {
			$modelConfig .= '
    public $update = true;';
		} else {
			$modelConfig .= '
    public $update = false;';
		}

		if ($data['detail_a'] == 2) {
			$modelConfig .= '
    public $detail = true;';
		} else {
			$modelConfig .= '
    public $detail = false;';
		}

		if ($data['delete_a'] == 2) {
			$modelConfig .= '
    public $delete = true;';
		} else {
			$modelConfig .= '
    public $delete = false;';
		}
        if ($data['sortStr_a'] != '') {
            $modelConfig .= '
    public $sortStr = \''.$data['sortStr_a'].'\';';
        } else {
            $modelConfig .= '
    public $sortStr = \'\';';
        }
		//AUTO-VALIDATE-FIELD
		$autoItem = '';
		$validateItem = '';
		$fieldMapItem = '';
		foreach ($data as $field => $param) {
			if (!is_array($param)) {
				continue;
			}
            /*
			if (($param['type_a'] === 'date') || ($param['type_a'] === 'datetime')) {
				$autoItem .= '
        [\'' . $field . '\', \'strtotime\', 3, \'function\'],';
			}
			if ($param['required_a']) {
				$validateItem .= '
        [\'' . $field . '\', \'require\', \'' . $param['name_a'] . '必须填写!\'],';
			}
             */
			//前端页面和后端对应问题,临时解决方案
			if ($param['list_a'] == 1) {
				$param['list_a'] = 'true';
			} else {
				$param['list_a'] = 'false';
			}
			if ($param['search_a'] == 1) {
				$param['search_a'] = 'true';
			} else {
				$param['search_a'] = 'false';
			}
			if ($param['input_a'] == 1) {
				$param['input_a'] = 'true';
			} else {
				$param['input_a'] = 'false';
			}
			if ($param['required_a'] == 1) {
				$param['required_a'] = 'true';
			} else {
				$param['required_a'] = 'false';
			}
			if ($param['excel_a'] == 1) {
				$param['excel_a'] = 'true';
			} else {
				$param['excel_a'] = 'false';
			}
			if ($param['sort_a'] == 1) {
				$param['sort_a'] = 'true';
			} else {
				$param['sort_a'] = 'false';
			}
			if ($param['list_update_a'] == 1) {
				$param['list_update_a'] = 'true';
			} else {
				$param['list_update_a'] = 'false';
			}
			if ($param['detail_a'] == 1) {
				$param['detail_a'] = 'true';
			} else {
				$param['detail_a'] = 'false';
			}

            //参数
            $paramsStr = '[';
            foreach ($param['params'] as $paramsKey=>$paramsValue) {
                $paramsKey = explode('_a', $paramsKey)[0];
                if (($paramsValue == 'true') || ($paramsValue == 'false')) {
                $paramsStr .= '
                \''.$paramsKey.'\' => '.$paramsValue.',';
                } else {
                $paramsStr .= '
                \''.$paramsKey.'\' => \''.$paramsValue.'\',';
                }
            }
            $paramsStr .= '
            ]';

			$fieldMapItem .= '
        \'' . $field . '\'=>[
            \'type\'=>\'' . $param['type_a'] . '\',
            \'title\'=>\'' . $param['name_a'] . '\',

            \'list\'=>' . $param['list_a'] . ',
            \'detail\'=>' . $param['detail_a'] . ',
            \'input\'=>' . $param['input_a'] . ',
            \'excel\'=>' . $param['excel_a'] . ',

            \'search\'=>' . $param['search_a'] . ',
            \'sort\'=>' . $param['sort_a'] . ',
            \'required\'=>' . $param['required_a'] . ',
            \'list_update\'=>' . $param['list_update_a'] . ',
            \'params\'=>' . $paramsStr . ',

            \'list_specify\'=>\'\',
            \'search_specify\'=>\'\',
            \'update_specify\'=>\'\',
            \'detail_specify\'=>\'\',
            \'export_specify\'=>\'\',
        ],';
		}
        /*
		$autoItem .= '
        [\'create_time\', \'time\', 1, \'function\'],
        [\'update_time\', \'time\', 2, \'function\'],';
		//auto
		$autoContent .= '
    protected $_auto = [' . $autoItem . '
    ];';
		//validate
        $validateContent = '
    protected $_validate = [' . $validateItem . '
    ];';
         */
        //batchOperation
        $batchOperationContent = '
    public $batchOperation = [];';
        //before&afterOperation
        $beforeAndAfterOperationContent = '
    protected function _before_insert(&$data,$options) {}
    protected function _after_insert($data,$options) {}
    protected function _before_delete($options) {}
    protected function _after_delete($data,$options) {}
    protected function _before_update(&$data,$options) {}
    protected function _after_update($data,$options) {}
    protected function _after_select(&$resultSet,$options) {}';

        //fieldMap
        $fieldMapContent = '
    public $fieldMap = [' . $fieldMapItem . '
    ];';
        //modelEnd
        $modelEnd = '
}';
$modelContent = $modelStart . $modelConfig . $autoContent . $validateContent . $batchOperationContent . $beforeAndAfterOperationContent . $fieldMapContent . $modelEnd;

//==创建model文件====
		if ($data['file_a'] == 2) {
			$modelFile = './App/' . $this->modelDir . '/Model/' . session('modelName') . 'Model.class.php';
			if (!file_exists($modelFile)) {
				file_put_contents($modelFile, $modelContent);
			} else {
				echo 'model文件以及存在，请复制增加：<br/>';
				dump($modelContent);
			}
		} else {
			dump($modelContent);
		}
	}
	protected function foreignTips($data) {
		foreach ($data as $field => $value) {
			if ($value['type_a'] == 'foreign') {
				$temp = explode(',', $value['content_a']);
				$deleteStr = '请在' . $temp[0] . 'Controller interfaceBeforeDelete中加入如下内容:<br/>';
				$deleteStr .= 'if (D("' . session('modelName') . '")->where(["' . $field . '"=>$id])->find()) {<br/>';
				$deleteStr .= '&nbsp;&nbsp;&nbsp;&nbsp;$data["info"] = "有数据依赖本条记录，请勿删除!";<br/>';
				$deleteStr .= '&nbsp;&nbsp;&nbsp;&nbsp;$data["status"] = 1;<br/>';
				$deleteStr .= '&nbsp;&nbsp;&nbsp;&nbsp;$this->ajaxReturn($data, \'JSON\');<br/>';
				$deleteStr .= '&nbsp;&nbsp;&nbsp;&nbsp;return false;<br/>';
				$deleteStr .= '}<br/>';
				echo $deleteStr;
			}
		}
	}

	protected function createHeader($data) {
        $system = D('System')->find(1);
        $menuArr = json_decode($system['menu'], true);
        if (!$menuArr[session('modelName')]) {
            $menuArr[session('modelName')] = [];
        }
        D('System')->where(['id'=>1])->data(['menu'=>json_encode($menuArr)])->save();
    }


	//删除modelname的文件
	public function step2D($table = '') {
		//===获取model名称======
		session('tableName', $table);
		$tableNameArr = explode('_', $table);
		foreach ($tableNameArr as $k => $v) {
			$tableNameArr[$k] = ucfirst($v);
		}
		$modelName = implode('', $tableNameArr);
		session('modelName', $modelName);
		$this->assign('modelName', $modelName);
		//===删除Model======
		$modelFile = './App/' . $this->modelDir . '/Model/' . session('modelName') . 'Model.class.php';
		unlink($modelFile);
		//===删除View======
		$viewFolder = './App/' . $this->module . '/View/' . session('modelName') . '/';
		$this->deldir($viewFolder);
		//===删除Controller======
		$controllerFile = './App/' . $this->module . '/Controller/' . session('modelName') . 'Controller.class.php';
		unlink($controllerFile);
        //===删除header信息====
        $system = D('System')->find(1);
        $menuArr = json_decode($system['menu'], true);
        if (isset($menuArr[session('modelName')])) {
            unset($menuArr[session('modelName')]);
        }
        D('System')->where(['id'=>1])->data(['menu'=>json_encode($menuArr)])->save();
        
		$this->success('删除成功!', U('Index/index'));
	}

	//删除文件夹以及其下的文件
	protected function deldir($dir = '') {
		//先删除目录下的文件：
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if ($file != "." && $file != "..") {
				$fullpath = $dir . "/" . $file;
				if (!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					deldir($fullpath);
				}
			}
		}
		closedir($dh);
		//删除当前文件夹：
		if (rmdir($dir)) {
			return true;
		} else {
			return false;
		}
	}
	//不需要展示代码的字段
	protected $noCodeArr = [
		'id',
		'create_time',
		'update_time',
	];

	public function step2Code($table = '') {
		$this->assign('noCodeArr', $this->noCodeArr);
		//===获取model名称======
		session('tableName', $table);
		$tableNameArr = explode('_', $table);
		foreach ($tableNameArr as $k => $v) {
			$tableNameArr[$k] = ucfirst($v);
		}
		$modelName = implode('', $tableNameArr);
		$this->assign('modelName', $modelName);
		$this->assign('tableName', strtolower($modelName));
		//===获取当前model字段======
		$model = D($modelName);
		$modelFields = $model->getDbFields();
		$this->assign('modelFields', $modelFields);
		$this->display('step2_code');
	}
}
