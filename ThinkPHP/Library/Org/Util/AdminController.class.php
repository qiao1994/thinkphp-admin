<?php
namespace Org\Util;

class AdminController extends \Think\Controller {

	/**
	 * 初始化操作
	 */
	protected function _initialize() {
        //测试内容结束
		$model = D(CONTROLLER_NAME);
		//权限验证
		$this->authorValidate();
		
		//系统信息
		$system = D('System')->find();
		$this->assign('system', $system);

		//前端页面基本信息渲染
		$this->assign('modelName', $model->modelName);
		$this->assign('modelIcon', $model->modelIcon);
		$this->assign('crumbsMap', $this->crumbsMap);

		//页面渲染
		$notAutoGenerate = C('NOT_AUTO_GENERATE');
		if ($notAutoGenerate === false) {
			//所有页面都生成
			$this->generateView();
		} elseif (C('NOT_AUTO_GENERATE') === true) {
			//所有页面都不生成
		} elseif (is_array($notAutoGenerate)) {
			//取到不需要生成的action给到generateView就行
			//判断当前module
			if (!array_key_exists(MODULE_NAME, $notAutoGenerate)) {
				//当前module不在不生成的列表里,需要正常生成
				$this->generateView();
			} else if (count($notAutoGenerate[MODULE_NAME]) != 0) {
				//当前module下指定的controller不生成
				//判断当前controller
				$notAutoGenerateController = $notAutoGenerate[MODULE_NAME];
				if (!array_key_exists(CONTROLLER_NAME, $notAutoGenerateController)) {
					//当前controller不在不生成的列表里,需要正常生成
					$this->generateView();
				} else if (count($notAutoGenerateController[CONTROLLER_NAME]) != 0) {
					//当前controller下指定的action不生成
					$this->generateView($notAutoGenerateController[CONTROLLER_NAME]);
				}
			}			
		}
	}

	/**
	 * 验证权限，判断当前用户是否允许访问当前action
	 */
	public function authorValidate() {
		$user = session(MODULE_NAME.'-user');
		if (!$user) {
			$user['role_id'] = 0; //预留游客是0，如果需要允许所有人访问，增加游客权限即可
		}
        
        //当前用户有权限的controller
        $authorControllerInfo = false;
        //所有权限
        $authors = [];
        $authors[] = D('Author')->where(['role_id'=>$user['role_id'], 'controller'=>'*'])->find();
        $authors[] = D('Author')->where(['role_id'=>$user['role_id'], 'module'=>'*'])->find();
        foreach($authors as $author) {
            if ($author) {
                $ret = eval($author['rule']);
                if ($ret) {
                    $authorControllerInfo = true;
                }
            }
        }
        //个别权限
        $authors = D('Author')->where(['role_id'=>$user['role_id'], 'controller'=>['neq', '*']])->select();
        foreach($authors as $author) {
            if ($author) {
                $ret = eval($author['rule']);
                if ($ret) {
                    //白名单 任意action都可以上白名单
                    $authorControllerInfoWhite[] = $author['controller'];
                } else {
                    //黑名单 action是*才能上黑名单
                    if ($author['action'] == '*') {
                        $authorControllerInfoBlack[] = $author['controller'];
                    }
                } 
            }
        }
        $this->assign('authorControllerInfo', $authorControllerInfo);
        $this->assign('authorControllerInfoBlack', $authorControllerInfoBlack);
        $this->assign('authorControllerInfoWhite', $authorControllerInfoWhite);


        $authors = [];
		//当前action
		$authors[] = D('Author')->where(['role_id'=>$user['role_id'], 'module'=>MODULE_NAME, 'controller'=>CONTROLLER_NAME, 'action'=>ACTION_NAME])->find();
        //当前controller
		$authors[] = D('Author')->where(['role_id'=>$user['role_id'], 'module'=>MODULE_NAME, 'controller'=>CONTROLLER_NAME, 'action'=>'*'])->find();
		//当前模块*
		$authors[] = D('Author')->where(['role_id'=>$user['role_id'], 'module'=>MODULE_NAME, 'controller'=>'*'])->find();
		//全局*
		$authors[] = D('Author')->where(['role_id'=>$user['role_id'], 'module'=>'*'])->find();
        //从小到大匹配
        foreach($authors as $author) {
            if ($author) {
                $ret = eval($author['rule']);
                if ($ret) {
                    return true;
                } else {
                    $this->error('抱歉，您没有当前页面的访问权限!');
                    return false;
                }    
            }
        }
        $this->error('抱歉，您没有当前页面的访问权限!');
        return false;
    }

    /**
     * 生成当前controller下指定action的view
     * @params $notGenerateActions=''时生成所有页面
     * @params $notGenerateActions=['list', 'update']时生成除了数组中的action以外的view
     */
    public function generateView($notGenerateActions = '') {
		//CREATE+UPDATE
		$updateView = $this->updateView();
		//LIST
		$searchView = $this->searchView();
		$tableView = $this->tableView();
		$listView = $searchView.$tableView;
		$contentListView = $this->tableView('', true);
		//DETAIL
		$detailView = $this->detailView();
		//IMPORT
		$importView = $this->importView();
		mkdir('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/');
		//生成所有的页面
		if ($notGenerateActions == '') {
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/list.html', $listView);
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/content_list.html', $contentListView);
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/update.html', $updateView);
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/detail.html', $detailView);
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/import.html', $importView);
		}
		//$notGenerateActions中有的不生成
		if (!in_array('list', $notGenerateActions)) {
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/list.html', $listView);
		}
		if (!in_array('update', $notGenerateActions)) {
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/update.html', $updateView);
		}
		if (!in_array('detail', $notGenerateActions)) {
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/detail.html', $detailView);
		}
		if (!in_array('import', $notGenerateActions)) {
			file_put_contents('./App/'.MODULE_NAME.'/View/'.CONTROLLER_NAME.'/import.html', $importView);
		}
	}

	//CREATE
	/**
	 * create之前要做的事情
	 * 在子类中覆盖即可执行相关动作
	 */
	public function interfaceBeforeCreate() {
	}

	/**
	 * create之后要做的事情
	 * 在子类中覆盖即可执行相关动作
	 */
	public function interfaceAfterCreate($id) {
	}

	/**
	 * 典型create操作
	 */
	public function create() {
		if (IS_POST) {
			$this->interfaceBeforeCreate();
			$model = D(CONTROLLER_NAME);
			//处理文件上传
			if (count($_FILES) !== 0) {
				$upload = new \Think\Upload();
				$upload->maxSize = 0;
				$upload->rootPath = './Public/file/'; 
				$upload->savePath = '';
				$upload->autoSub = false;
				$info = $upload->upload();
				if (!$info) {
					$this->error($upload->getError());
					return false;
				}
				foreach ($_FILES as $field => $file) {
					$_POST[$field] = $info[$field]['savename'];
				}
			}
            foreach ($_POST as $key=>$value) {
                if (is_array($value)) {
                    $_POST[$key] = implode(',', $value);
                }
            }
			if (!$model->create()) {
				$this->error($model->getError());
				return false;
			}
			$newId = $model->add();
            //特殊逻辑
            foreach ($model->fieldMap as $field=>$modelParams) {
                //hasmany类型特殊处理
                if ($modelParams['type'] === 'hasmany') {
                    // 把所有值的外键都更新成本$newId
                    $hasmanyModel = D($this->getModelNameByTableName($modelParams['params']['foreign_table']));
                    $hasmanyModel->where(['id'=>['IN', I('post.'.$field)]])->data([$modelParams['params']['foreign_key']=>$newId])->save();
                } 
            }
			$this->interfaceAfterCreate($newId);
			$this->success('新增成功!', U('list'), ['newId'=>$newId]);
		}
		$this->display('update');
	}

	//DELETE
	
	/**
	 * delete之前要做的事情
	 * 在子类中覆盖即可执行相关动作
	 */
	public function interfaceBeforeDelete($id) {
	}
	/**
	 * delete之后要做的事情
	 * 在子类中覆盖即可执行相关动作
	 */
	public function interfaceAfterDelete($id) {
	}

	/**
	 * 典型delete操作
	 */
	public function delete() {
		if (IS_POST) {
			$this->interfaceBeforeDelete(I('post.id'));
			$model = D(CONTROLLER_NAME);
			//删除文件
			foreach ($model->fieldMap as $field => $modelParams) {
				if ($modelParams['type'] === 'file') {
					$deleteModels = $model->select(I('post.id'));
					foreach ($deleteModels as $deleteModel) {
						unlink('./Public/file/'.$deleteModel[$field]);
					}
				}
			}
            $model->delete(I('post.id'));
            //处理特殊逻辑
            foreach ($model->fieldMap as $field => $modelParams) {
                if ($modelParams['type'] == 'hasmany') {
                    //hasmany需要把关联表的内容删除
                    $hasmanyModel = D($this->getModelNameByTableName($modelParams['params']['foreign_table']));
                    $hasmanyModel->where([$modelParams['params']['foreign_key']=>I('post.id')])->delete();
                }
            }
			$this->interfaceAfterDelete(I('post.id'));
			$data['info'] = '删除成功!';
			$data['status'] = 1;
			$this->ajaxReturn($data, 'JSON');
		}
	}

	//UPDATE
	
	/**
	 * update之前要做的事情
	 * 在子类中覆盖即可执行相关动作
	 */
	public function interfaceBeforeUpdate($id) {
	}
	/**
	 * update之后要做的事情
	 * 在子类中覆盖即可执行相关动作
	 */
	public function interfaceAfterUpdate($id) {
	}

	/**
	 * 典型update操作
	 */
	public function update($id = 0, $params = []) {

		$model = D(CONTROLLER_NAME);
		if (IS_POST) {
			$this->interfaceBeforeUpdate($id);
			$_POST['id'] = $id;
			if (count($_FILES) !== 0) {
				$upload = new \Think\Upload();
				$upload->maxSize = 0; 
				$upload->rootPath = './Public/file/';
				$upload->savePath = '';
				$upload->autoSub = false;
				$info = $upload->upload();
				if ($info) {
					//上传成功，删除原有文件
                    foreach ($_FILES as $field => $file) {
                        if ($file['name']) {
                            $_POST[$field] = $info[$field]['savename'];
                            if ($info[$field]['savename']) {
                                if (isset($id)) {
                                    $modelTemp = $model->find($id);
                                    unlink('./Public/file/'.$modelTemp[$field]);
                                }
                            }
                        }
                    }
                }
            }
            foreach ($_POST as $key=>$value) {
                if (is_array($value)) {
                    $_POST[$key] = implode(',', $value);
                }
            }
            //特殊逻辑
            foreach ($model->fieldMap as $field=>$modelParams) {
                if ($modelParams['type'] === 'hasmany') {
                    $hasmanyModel = D($this->getModelNameByTableName($modelParams['params']['foreign_table']));
                    //oldArr
                    $oldDatas = $hasmanyModel->where([$modelParams['params']['foreign_key']=>$id])->select();
                    foreach ($oldDatas as $oldData) {
                        $oldArr[] = $oldData['id'];
                    }
                    $newArr = explode(',', I('post.'.$field));
                    if (!$oldArr) {
                        $oldArr = [];
                    } 
                    if (!$newArr) {
                        $newArr = [];
                    } 
                    $need2Delete = array_diff($oldArr, $newArr);
                    $need2Update = array_diff($newArr, $oldArr);
                    if ($need2Delete) {
                        $hasmanyModel->where(['id'=>['IN', $need2Delete]])->delete();
                    }
                    if ($need2Update) {
                        $hasmanyModel->where(['id'=>['IN', $need2Update]])->data([$modelParams['params']['foreign_key']=>$id])->save();
                    }
                }
            }
            if (!$model->create($_POST)) {
                $this->error($model->getError());
				return false;
			}
			$model->save();

			$this->interfaceAfterUpdate($id);
			if (!$params['redirect']) {
				$this->success('修改成功!', U('list'));
			} else {
				$this->success('修改成功!', $params['redirect']);
			}
		}
		//展示页面
		$modelData = $model->getById($id);
		if (!$modelData) {
			$this->error('非法操作!');
			return false;
		}

        //特殊逻辑
        foreach ($model->fieldMap as $field => $modelParams) {
            if ($modelParams['type'] == 'hasmany') {
                //hasmany需要把关联表的内容放到list里
                $hasmanyModel = D($this->getModelNameByTableName($modelParams['params']['foreign_table']));
                $list = $hasmanyModel->where([$modelParams['params']['foreign_key']=>$model->id])->select();
                $this->assign('list', $list);
            }
        }
		$this->assign('model', $modelData);
		$this->display();
	}

	//UPDATE FROM LIST
	/**
	 * 通过list修改
	 */
	public function updateFromList() {
		$model = I('post.dataModel');
		$model = D($model);
		$id = I('post.dataId');
		$field = I('post.dataField');
		$newValue = I('post.dataNewValue');
		unset($data);
		//判断当前记录是否存在
		if (!$model->where(['id'=>$id])->find()) {
			$data['info'] = '当前记录不存在!';
			$data['status'] = 0;
			$this->ajaxReturn($data, 'JSON');
		}
		unset($updateData);
		$updateData['id'] = $id;
		$updateData[$field] = $newValue;
		if (!$model->create($updateData)) {
			$data['info'] = $model->getError();
			$data['status'] = 0;
			$this->ajaxReturn($data, 'JSON');
		} else {
			$model->save();
			$data['info'] = '操作成功！';
			$data['status'] = 1;
			$this->ajaxReturn($data, 'JSON');
		}
	}
	//LIST
	/**
	 * 典型list操作
	 */
	public function list() {
        $this->assign('searchMap', $_GET["searchMap"]);
		$this->page();

		$this->display();
	}

	/**
	 * 分页数据渲染
	 * @params array $searchMap 寻找元素时的条件,默认为$_GET['searchMap']的值
	 */
	public function page() {
		$searchMap = $this->handleSearchMap($_GET["searchMap"]);

		$model = D(CONTROLLER_NAME);
		$sortStr = trim($_GET['sortStr']);
        //过滤无效的排序项
        $sortArr = explode(',', $sortStr);
        foreach($sortArr as $key => $sortItem) {
            $field = explode(' ', $sortItem)[0];
            if (!in_array($field, $model->getDbFields())) {
                unset($sortArr[$key]);
            }
        }
        $sortStr = implode(',', $sortArr);

		if (!$sortStr) {
			if ($model->sortStr != '') {
				$sortStr = $model->sortStr;
			} else {
				$sortStr = 'id';
			}
		}
		$count = $model->where($searchMap)->count();
		$page = new \Think\AdminPage($count, C('PAGE_NUM'));
		$show = $page->show();
		$list = $model->where($searchMap)->order($sortStr)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
	}

    //ContentList
    /**
     * 只显示内容的list页面
     * @param idStr 1,2,3...
     */
    public function contentList($idStr = '') {
        $model = D(CONTROLLER_NAME);
        $idArr = explode(',', $idStr);
        $list = $model->where(['id'=>['IN', $idArr]])->select();
        $this->assign('list', $list);
        $this->display('content_list');
    }

	//DETAIL
	/**
	 * 典型detail操作
	 */
	public function detail($id = 0) {
		$model = D(CONTROLLER_NAME);
		$detail = $model->find($id);
		$this->assign('detail', $detail);
		$this->display('detail');
	}

	//EXPORT
	/**
	 * 典型export操作
	 * @params array $searchMap 寻找元素时的条件,默认为$_GET['searchMap']的值
	 */
	public function export() {
		$searchMap = $this->handleSearchMap($_GET['searchMap']);
		$sortStr = I('get.sortStr');
		if (!$sortStr) {
			$sortStr = 'id';
		}
		$model = D(CONTROLLER_NAME);
		$excelCell = [];
		$fieldStr = '';
		foreach ($model->fieldMap as $field => $modelParams) {
			if (($modelParams['excel'] !== false) && ($modelParams['excel'] !== 'import')) {
				$excelCell[] = [$field, $modelParams['title']];
				$fieldStr .= $field.',';
			}
		}

		$fieldStr = substr($fieldStr, 0, strlen($fieldStr) - 1);
		$excelData = $model->where($searchMap)->field($fieldStr)->order($sortStr)->select();
		foreach ($excelData as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$modelExcelConf = $model->fieldMap[$k1]['excel'];
				if (($modelExcelConf !== false) && ($modelExcelConf !== 'import')) {
					$excelData[$k][$k1] = $this->exportValueHandle($k1, $v1);
				}
			}
		}

		if ((count($excelData) == 0)||(count($excelCell) == 0)) {
			$this->error('无数据可导出,请增加数据或修改筛选条件!');
		}
		\exportExcel($model->modelName, $excelCell, $excelData);
	}

	/**
	 * 导出excel指定类型值处理
	 * @params $field : 字段
	 * @params $value : 需要处理的值
	 * @return $returnValue : 处理完成的值
	 */
	public function exportValueHandle($field = '', $value = '') {
		$model = D(CONTROLLER_NAME);
		$param = $model->fieldMap[$field];
		$type = $param['type'];
        if ($param['export_specify'] != '') {
            return eval($param['export_specify']);
        }
		$returnValue = $value;
		switch ($type) {
		case 'file':
            $returnValue = 'http://'.$_SERVER['SERVER_NAME'].'/Public/file/'.$value;
			break;
		case 'foreign':
			$returnValue = D($this->getModelNameByTableName($param['params']['foreign_table']))->where([$param['params']['foreign_table_key'] => $value])->find()[$param['params']['foreign_show']];
			break;
        case 'foreigns':
			$checkboxforeignItems = D($this->getModelNameByTableName($param['params']['foreign_table']))->where([$param['params']['foreign_table_key'] => ['IN', $value]])->select();
            $returnArr = [];
            foreach ($checkboxforeignItems as $checkboxforeignItem) {
                $returnArr[] = $checkboxforeignItem[$param['params']['foreign_show']];
            }
            $returnValue = implode(',', $returnArr);
            break;
		case 'date':
            if ($value != 0) {
			    $returnValue = date('Y-m-d', $value);
            } else {
			    $returnValue = '';
            }
			break;
		case 'datetime':
            if ($value != 0) {
			    $returnValue = date('Y-m-d H:i:s', $value);
            } else {
			    $returnValue = '';
            }
			break;
		}
		return $returnValue;
	}

	//IMPORT
	/**
	 * 典型import操作
	 * 无任何定制化需求的model可以直接复用本action
	 * @params array $searchMap 寻找元素时的条件,默认为$_GET['searchMap']的值
	 */
	public function import() {
		if (IS_POST) {
			if (isset($_FILES["import"]) && ($_FILES["import"]["error"] == 0)) {
				$model = D(CONTROLLER_NAME);
				$excelCell = [];
				foreach ($model->fieldMap as $field => $param) {
					if (($param['excel'] !== false) && ($param['excel'] !== 'export')) {
						$excelCell[] = $field;
					}
				}
				$result = \importExecl($_FILES["import"]["tmp_name"]);
				if ($result["error"] == 1) {
					$excelData = $result["data"][0]["Content"];
					foreach ($excelData as $k => $v) {
						if ($k <= 2) {
							//跳过表头
							continue;
						}
						$data = [];
						foreach ($excelCell as $i => $field) {
							$data[$field] = $this->importValueHandle($field, $v[$i]);
						}
						if (!($model->create($data) && $model->add())) {
							$this->error($model->getError());
						}
					}
				}
			}
			$this->success('导入成功!', U(CONTROLLER_NAME.'/list'));
		}
		$this->display();
	}

	/**
	 * 批量操作
	 */
	public function batchOperation() {
		//执行model中对应的语句
		$model = D(CONTROLLER_NAME);
		eval($model->batchOperation[$_POST['operationKey']]['code']);
		unset($data);
		$data['info'] = '批量操作成功!';
		$data['status'] = 1;
		$this->ajaxReturn($data, 'JSON');
	}

	/**
	 * 导入excel指定类型值处理
	 * @params $field : 字段
	 * @params $value : 需要处理的值
	 * @return $returnValue : 处理完成的值
	 */
	public function importValueHandle($field = '', $value = '') {
		$model = D(CONTROLLER_NAME);
		$param = $model->fieldMap[$field];
		$type = $param['type'];
		$returnValue = $value;
		switch ($type) {
		case 'file':
			break;
		case 'foreign':
			$returnValue = D($this->getModelNameByTableName($param['params']['foreign_table']))->where([$param['params']['foreign_show'] => $value])->find()[$param['params']['foreign_table_key']];
			break;
        case 'foreigns':
            if ($value) {
			    $checkboxforeignItems = D($this->getModelNameByTableName($param['params']['foreign_table']))->where([$param['params']['foreign_show'] => ['IN', $value]])->select();
            }
            $returnArr = [];
            foreach ($checkboxforeignItems as $checkboxforeignItem) {
                $returnArr[] = $checkboxforeignItem[$param['params']['foreign_table_key']];
            }
            $returnValue = implode(',', $returnArr);
            break;
		case 'date':
			break;
		case 'datetime':
			break;
		}
		return $returnValue;
	}

	/**
	 * 对searchMap中需要特殊处理的值进行处理
	 * @params $searchMap : 待处理的searchMap
	 * @return $searchMap : 处理完成的searchMap
	 */
	public function handleSearchMap($searchMap = '') {
		$searchMap = array_filter($searchMap);
		$model = D(CONTROLLER_NAME);
		
		foreach ($searchMap as $field => $value) {
			if (strstr($field, 'Start')) {
				//时间筛选,起止范围,需要特殊处理
				$startField = $field;
				$endField = str_replace("Start", "End", $field);
				$field = str_replace('Start', '', $field); //此后的field为真实field
				$startTime = $searchMap[$startField];
				$endTime = $searchMap[$endField];
				unset($searchMap[$startField]);
				unset($searchMap[$endField]);
				$searchMap[$field] = ['between', [strtotime($startTime), strtotime($endTime)]];
			}
			$param = $model->fieldMap[$field];
			if (($param['type'] == 'foreign') && ($param['params']['search_type'] == 'input')) {
				//input类型的外键，需要转换成id
				if ($searchMap[$field]) {
					$retArr = D($this->getModelNameByTableName($param['params']['foreign_table']))->where([$param['params']['foreign_show'] => $searchMap[$field]])->select();
					$retStr = '';
					foreach ($retArr as $key => $value) {
					$retStr .= $value[$param['params']['foreign_table_key']].',';
					}
					$retStr = rtrim($retStr, ",");
					$searchMap[$field] = ['IN', $retStr];
				}
			}
            if (($param['type'] == 'foreigns') || ($param['type'] == 'checkbox')) {
                $searchMap[$field] = ['LIKE', '%'.$value.'%'];
            }
		}
		return $searchMap;
	}

	//VIEW
	//CREATE+UPDATE
	public function updateView($model = '', $onlyContent = false) {
        if (!$model) {
	        $model = D(CONTROLLER_NAME);
        }
		//updateStart
		$updateView = $this->updateStart($model, $onlyContent);
		//updateContent
		if (count($model->fieldMap) !== 0) {
			foreach ($model->fieldMap as $field => $param) {
				$updateView .= $this->updateOption($field, $param, $onlyContent);
			}
		}
		//updateEnd
		$updateView .= $this->updateEnd($onlyContent);
        //特殊操作
		if (count($model->fieldMap) !== 0) {
			foreach ($model->fieldMap as $field => $param) {
				$updateView .= $this->updateOptionAfterEnd($field, $param);
			}
		}
		return $updateView;
	}
	//LIST
	/**
	 * list页面搜索部分
	 */
	public function searchView() {
		$model = D(CONTROLLER_NAME);
		//searchStart
		$searchView = $this->searchStart();
		//searchContent
		if (count($model->fieldMap) !== 0) {
			foreach ($model->fieldMap as $field => $param) {
                if ($param['type'] != 'hasmany') {
                    //hasmany不参与搜索
				    $searchView .= $this->searchOption($field, $param);
                }
			}
		}
		//searchEnd
		$searchView .= $this->searchEnd();
		//批量操作样式
		$searchView .= $this->seaerchBatchOperation();
		return $searchView;
	}

	/**
	 * list页面批量操作
	 */
	public function seaerchBatchOperation() {
		$model = D(CONTROLLER_NAME);
		if (!$model->batchOperation) {
			return '';
		} else {
			$returnView = '        
		<div class="row hidden-480 operation-row">
            <div class="col-md-12 operation-div">';
			foreach ($model->batchOperation as $batchOperationKey => $batchOperationItem) {
				$returnView .= '
                <button type="button" class="btn btn-xs operation-btn" data-operation-id="'.$batchOperationKey.'"><i class="'.$batchOperationItem['icon'].'"></i> '.$batchOperationItem['name'].'</button>';
			}
			$returnView .= '
			</div>
    	</div>';
    		return $returnView;
		}
	}

	/**
	 * list页面核心表格
	 */
	public function tableView($model = '', $onlyContent = false) {
        if (!$model) {
		    $model = D(CONTROLLER_NAME);
        }
		//tableStart
		$tableView = $this->tableStart($model, $onlyContent);
		//tableContent
		if (count($model->fieldMap) !== 0) {
			foreach ($model->fieldMap as $field => $param) {
				$tableView .= $this->tableOption($field, $param, $onlyContent);
			}
		}
		//tableEnd
		$tableView .= $this->tableEnd($model, $onlyContent);
		return $tableView;
	}
	//DETAIL
	public function detailView() {
		$model = D(CONTROLLER_NAME);
		//detailStart
		$detailView = $this->detailStart();
		//detailContent
		if (count($model->fieldMap) !== 0) {
			foreach ($model->fieldMap as $field => $param) {
				$detailView .= $this->detailOption($field, $param);
			}
		}
		//detailEnd
		$detailView .= $this->detailEnd();
		return $detailView;
	}
	//IMPORT
	public function importView() {
		$model = D(CONTROLLER_NAME);
		//importStart
		$importView = $this->updateStart($model, false, true);
		$importView .= $this->importContent();
		//importEnd
		$importView .= $this->updateEnd();
		return $importView;
	}

	//以下为具体实现
	
	/**
	 * model的中文名称，需要在子类中覆盖
	 */
	protected $modelName = '';

	/**
	 * model的icon，需要在子类中覆盖
	 */
	protected $modelIcon = '';

	/**
	 * 面包屑导航map
	 * 用法$crumbsMap[ACTION_NAME]
	 */
	protected $crumbsMap = [
		'list' => '列表',
		'create' => '新增',
		'update' => '修改',
		'index' => '概况',
		'detail' => '详情',
		'import' => '导入',
	];

	//LIST
	/**
	 * searchInput数量
	 */
	protected $searchNum = 0;

	/**
	 * searchInputMove的值map
	 */
	protected $searchMoveMap = [
		0 => 10,
		1 => 7,
		2 => 4,
		3 => 1,
	];

	public function searchOption($field, $param) {
		if ($param['search'] === true) {
            if ($param['search_specify'] != '') {
            	$this->searchNum++;
				$searchView = '
                <div class="col-md-3 search-input-div">
                    '.$param['search_specify'].'
                </div>';
                return $searchView;
            }
			if (($param['type'] == 'password') || ($param['type'] == 'richtextbox') || ($param['type'] == 'file') || ($param['type'] == 'textarea')) {
				$param['type'] = 'text';
			}
			$searchOption = 'search'.ucfirst($param['type']);
			$searchView = $this->$searchOption($field, $param);
			return $searchView;
		} else {
			return '';
		}
	}

	//LIST-SEARCH
	public function searchStart() {
		$startView = '
<include file="Public/ace_header" />
<div class="row">
    <div class="col-xs-12">
        <div class="row hidden-480 search">
            <form id="search-form">';
		return $startView;
	}

	public function searchSelect($field, $param) {
		$this->searchNum++;
		//option
		$optionView = '
                        <option value="" class="search-select-default-option">'.$param['title'].'</option>';
		$options = explode(',', $param['params']['options']);
		foreach ($options as $option) {
			$optionView .= '
                        <option value="'.$option.'" <if condition ="$searchMap['.$field.'] eq \''.$option.'\'"> selected </if>>'.$option.'</option>';
		}
		//select
		$selectView = '
                <div class="col-md-3 search-input-div">
                    <select name="searchMap['.$field.']" id="'.$field.'" class="width-100 search-select">'.$optionView.'
                    </select>
                </div>';
		return $selectView;
	}
	public function searchCheckbox($field, $param) {
		$this->searchNum++;
		//option
		$optionView = '
                        <option value="" class="search-select-default-option">'.$param['title'].'</option>';
		$options = explode(',', $param['params']['options']);
		foreach ($options as $option) {
			$optionView .= '
                        <option value="'.$option.'" <if condition ="$searchMap['.$field.'] eq \''.$option.'\'"> selected </if>>'.$option.'</option>';
		}
		//select
		$selectView = '
                <div class="col-md-3 search-input-div">
                    <select name="searchMap['.$field.']" id="'.$field.'" class="width-100 search-select">'.$optionView.'
                    </select>
                </div>';
		return $selectView;
	}
	public function searchForeign($field, $param) {
		$this->searchNum++;
		if ($param['params']['search_type'] === 'select') {
			//foreignCondition
			if ($param['params']['foreign_condition']) {
			    $foreignCondition = $param['params']['foreign_condition'];
			} else {
			    $foreignCondition = 1;
			}
			$optionView = '
                        <option value="" class="search-select-default-option">'.$param['title'].'</option>
                        <php>$foriegnModel = D("'.$param['params']['foreign_table'].'")->where('.$foreignCondition.')->select();</php>
                        <volist name="foriegnModel" id="vo">
                        <option value="{$vo[\''.$param['params']['foreign_table_key'].'\']}" <if condition ="$searchMap[\''.$field.'\'] eq $vo[\''.$param['params']['foreign_table_key'].'\']"> selected </if>>{$vo[\''.$param['params']['foreign_show'].'\']}</option>
                        </volist>';
			$foreignView = '
                <div class="col-md-3 search-input-div">
                    <select name="searchMap['.$field.']" id="'.$field.'" class="width-100 search-select">'.$optionView.'
                    </select>
                </div>';
			return $foreignView;
		} elseif ($param['params']['search_type'] === 'input') {
			$foreignView = '
                <div class="col-md-3 search-input-div">
                    <input type="text" class="col-md-12 search-input" name="searchMap['.$field.']" id="'.$field.'" value="{$searchMap['.$field.']}" placeholder="'.$param['title'].'"/>
                </div>';
			return $foreignView;
		}
	}
	public function searchForeigns($field, $param) {
		$this->searchNum++;
		if ($param['params']['search_type'] === 'select') {
			$optionView = '
                        <option value="" class="search-select-default-option">'.$param['title'].'</option>
                        <php>$foreignsModel = D("'.$this->getModelNameByTableName($param['params']['foreign_table']).'")->select();</php>
                        <volist name="foreignsModel" id="vo">
                        <option value="{$vo[\''.$param['params']['foreign_table_key'].'\']}" <if condition ="$searchMap[\''.$field.'\'] eq $vo[\''.$param['params']['foreign_table_key'].'\']"> selected </if>>{$vo[\''.$param['params']['foreign_show'].'\']}</option>
                        </volist>';
			$foreignView = '
                <div class="col-md-3 search-input-div">
                    <select name="searchMap['.$field.']" id="'.$field.'" class="width-100 search-select">'.$optionView.'
                    </select>
                </div>';
			return $foreignView;
		} elseif ($param['params']['search_type'] === 'input') {
			$foreignView = '
                <div class="col-md-3 search-input-div">
                    <input type="text" class="col-md-12 search-input" name="searchMap['.$field.']" id="'.$field.'" value="{$searchMap['.$field.']}" placeholder="'.$param['title'].'"/>
                </div>';
			return $foreignView;
		}
	}

	public function searchDate($field, $param) {
		$this->searchNum++;
		$this->searchNum++;
		//日期/日期时间，用范围查询
		$dateView = '
                <div class="col-md-3 search-input-div">
                    <input type="text" class="col-md-12 search-input" name="searchMap['.$field.'Start]" id="'.$field.'Start" value="{$searchMap['.$field.'Start]}" placeholder="'.$param['title'].'开始时间" onClick="WdatePicker()" readonly="" onChange="'.$field.'StartChange()"/>
                </div>
                <div class="col-md-3 search-input-div">
                    <input type="text" class="col-md-12 search-input" name="searchMap['.$field.'End]" id="'.$field.'End" value="{$searchMap['.$field.'End]}" placeholder="'.$param['title'].'结束时间" onClick="WdatePicker()" readonly="" onChange="'.$field.'EndChange()"/>
                </div>
                <script>
                    function '.$field.'StartChange() {
                        var '.$field.'End = $("#'.$field.'End").val();
                        if ('.$field.'End != "") {
                            var '.$field.'Start = $("#'.$field.'Start").val();
                            if ('.$field.'End < '.$field.'Start) {
                                alert("起始时间必须早于结束时间!");
                                $("#'.$field.'Start").val("");
                            }
                        }
                    }
                    function '.$field.'EndChange() {
                        var '.$field.'Start = $("#'.$field.'Start").val();
                        if ('.$field.'Start != "") {
                            var '.$field.'End = $("#'.$field.'End").val();
                             if ('.$field.'Start > '.$field.'End) {
                                alert("结束时间必须晚于起始时间!");
                                $("#'.$field.'End").val("");
                            }
                        }
                    }
                    </script>';
		return $dateView;
	}

	public function searchDatetime($field, $param) {
		$this->searchNum++;
		$this->searchNum++;
		//日期/日期时间，用范围查询
		$datetimeView = '
                <div class="col-md-3 search-input-div">
                    <input type="text" class="col-md-12 search-input" name="searchMap['.$field.'Start]" id="'.$field.'Start" value="{$searchMap['.$field.'Start]}" placeholder="'.$param['title'].'开始时间" onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" readonly="" onChange="'.$field.'StartChange()"/>
                </div>
                <div class="col-md-3 search-input-div">
                    <input type="text" class="col-md-12 search-input" name="searchMap['.$field.'End]" id="'.$field.'End" value="{$searchMap['.$field.'End]}" placeholder="'.$param['title'].'结束时间" onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" readonly="" onChange="'.$field.'EndChange()"/>
                </div>
                <script>
                    function '.$field.'StartChange() {
                        var '.$field.'End = $("#'.$field.'End").val();
                        if ('.$field.'End != "") {
                            var '.$field.'Start = $("#'.$field.'Start").val();
                            if ('.$field.'End < '.$field.'Start) {
                                alert("起始时间必须早于结束时间!");
                                $("#'.$field.'Start").val("");
                            }
                        }
                    }
                    function '.$field.'EndChange() {
                        var '.$field.'Start = $("#'.$field.'Start").val();
                        if ('.$field.'Start != "") {
                            var '.$field.'End = $("#'.$field.'End").val();
                             if ('.$field.'Start > '.$field.'End) {
                                alert("结束时间必须晚于起始时间!");
                                $("#'.$field.'End").val("");
                            }
                        }
                    }
                    </script>';
		return $datetimeView;
	}

	public function searchText($field, $param) {
		$this->searchNum++;
		$inputView = '
                <div class="col-md-3 search-input-div">
                    <input type="text" class="col-md-12 search-input" name="searchMap['.$field.']" id="'.$field.'" value="{$searchMap['.$field.']}" placeholder="'.$param['title'].'"/>
                </div>';
		return $inputView;

	}

	public function searchEnd() {
		$searchMove = $this->searchMoveMap[$this->searchNum % 4] - 1;
		$endView = '
                <div class="col-md-3 col-md-offset-'.$searchMove.' center search-input-div">';
		if ($this->searchNum !== 0) {
			$endView .= '
                    <button type="submit" class="btn btn-success btn-xs">查询</button>
                    <button class="btn btn-warning btn-xs" type="button" href="#" onclick="clearFind()">清空</button>';
		}
		$model = D(CONTROLLER_NAME);
		if (($model->excel === true) || ($model->excel === 'import')) {
			$endView .= '
                    <a class="btn btn-purple btn-xs" href="{:U(import)}">导入</a>';
		}
		if (($model->excel === true) || ($model->excel === 'export')) {
			$endView .= '
                    <a class="btn btn-pink btn-xs" id="export-btn" href="{:U(export)}" onclick="return exportExcel()">导出</a>';
		}
		if ($model->delete !== false) {
			$endView .= '
                    <button class="btn btn-danger btn-xs" type="button" href="javascript:void(0);" id="delall">删除</button>';
		}
		$endView .= '
                </div>
                <input id="sort-str" class="sort-str" type="hidden" name="sortStr" value="{$_GET[\'sortStr\']}"/>
            </form>
        </div>';
		return $endView;
	}


	public function tableForeigns($field, $param) {

        $checkboxforeignView = '';
        if ($param['params']['foreign_href'] === true ) {
            //外键链接
            $checkboxforeignView .= '
                        <php>
                            $checkboxForeignResultArr = D("'.$this->getModelNameByTableName($param['params']['foreign_table']).'")->where(["'.$param['params']['foreign_table_key'].'"=>["IN", $vo['.$field.']]])->select();
                            $checkboxForeignTempArr = [];
                            foreach ($checkboxForeignResultArr as $checkboxForeignResultItem) {
                                $checkboxForeignTempArr[] = \'<a href="__MODULE__/Cate/detail?'.$param['params']['foreign_table_key'].'=\'.$checkboxForeignResultItem['.$param['params']['foreign_table_key'].'].\'">\'.$checkboxForeignResultItem['.$param['params']['foreign_show'].'].\'</a>\';
                            }
                            $checkboxForeignStr = implode(",", $checkboxForeignTempArr);
                        </php>
                        <td '.$param['class'].'>{$checkboxForeignStr}</td>';
        } else {
            $checkboxforeignView .= '
                        <php>
                            $checkboxForeignResultArr = D("'.$this->getModelNameByTableName($param['params']['foreign_table']).'")->where(["'.$param['params']['foreign_table_key'].'"=>["IN", $vo['.$field.']]])->select();
                            $checkboxForeignTempArr = [];
                            foreach ($checkboxForeignResultArr as $checkboxForeignResultItem) {
                                $checkboxForeignTempArr[] = $checkboxForeignResultItem["'.$param['params']['foreign_show'].'"];
                            }
                            $checkboxForeignStr = implode(",", $checkboxForeignTempArr);
                        </php>
                        <td '.$param['class'].'>{$checkboxForeignStr}</td>';
        }
		return $checkboxforeignView;
    }

	public function tableDate($field, $param) {
		$dateView = '
                        <td '.$param['class'].'><if condition="$vo['.$field.'] neq 0">{$vo['.$field.']|default=\'\'|date="Y-m-d",###}</if></td>';
		return $dateView;
	}

	public function tableDatetime($field, $param) {
		$datetimeView = '
                        <td '.$param['class'].'><if condition="$vo['.$field.'] neq 0">{$vo['.$field.']|default=\'\'|date="Y-m-d H:i:s",###}</if></td>';
		return $datetimeView;
	}

	public function tableRichtextbox($field, $param) {
		$ueditorView = '
                        <td '.$param['class'].'>{$vo['.$field.']|html_entity_decode}</td>';
		return $ueditorView;
	}

	public function tableForeign($field, $param, $onlyContent = false) {
        if ($onlyContent == $param['params']['foreign_table']) {
            return '';
        }
        if ($param['params']['foreign_href'] === true) {
        	$showContent = '<a href="__MODULE__/'.$this->getModelNameByTableName($param['params']['foreign_table']).'/detail?'.$param['params']['foreign_table_key'].'={$vo[\''.$field .'\']}"><php> echo D("'.$this->getModelNameByTableName($param['params']['foreign_table']).'")->where(["'.$param['params']['foreign_table_key'].'"=>$vo["'.$field.'"]])->find()["'.$param['params']['foreign_show'].'"];</php></a>';
            
        } else {
        	$showContent = '<php> echo D("'.$this->getModelNameByTableName($param['params']['foreign_table']).'")->where(["'.$param['params']['foreign_table_key'].'"=>$vo["'.$field.'"]])->find()["'.$param['params']['foreign_show'].'"];</php>';
        }
        if ($param['list_update'] === true) {
        	$showContent = '
        					<div id="list-show-div-'.$field.'-{$vo[\'id\']}">
			                    <span class="list-show-span">'.$showContent.'</span> <i class="fa fa-edit list-update-btn" data-field="'.$field.'" data-id="{$vo[\'id\']}"></i>
			                </div>
			                <div id="list-update-div-'.$field.'-{$vo[\'id\']}" class="hidden-only">
			                <php>$foreigns = D("'.$this->getModelNameByTableName($param['params']['foreign_table']).'")->select();</php>
			                <select data-model="'.CONTROLLER_NAME.'" data-field="'.$field.'" data-id="{$vo[\'id\']}" class="list-update-select">
			                    <volist name="foreigns" id="foreign">
			                        <option value="{$foreign["'.$param['params']['foreign_table_key'].'"]}" <if condition="$vo[\''.$field.'\'] eq $foreign[\''.$param['params']['foreign_table_key'].'\']"> selected </if>>{$foreign["'.$param['params']['foreign_show'].'"]}</option>
			                    </volist>
			                </select>
			                </div>
        	';
        }
        $foreignView = '
						<td '.$param['class'].'>'.$showContent.'
						</td>
        ';
		return $foreignView;
	}

	public function tableSelect($field, $param) {
    	$showContent = '
							<div id="list-show-div-'.$field.'-{$vo[\'id\']}">
			                    <span class="list-show-span">{$vo["'.$field.'"]}</span> <i class="fa fa-edit list-update-btn" data-field="'.$field.'" data-id="{$vo[\'id\']}"></i>
			                </div>
			                <div id="list-update-div-'.$field.'-{$vo[\'id\']}" class="hidden-only">
			                <php>$selects = explode(",", "'.$param['params']['options'].'")</php>
			                <select data-model="'.CONTROLLER_NAME.'" data-field="'.$field.'" data-id="{$vo[\'id\']}" class="list-update-select">
			                    <volist name="selects" id="select">
			                        <option value="{$select}" <if condition="$vo[\''.$field.'\'] eq $select"> selected </if>>{$select}</option>
			                    </volist>
			                </select>
			                </div>
        ';
        if ($param['list_update'] === true) {
            $foreignView = '
						<td '.$param['class'].'>'.$showContent.'
						</td>
            ';
        } else {
            $foreignView = '
						<td '.$param['class'].'>{$vo["'.$field.'"]}
						</td>
            ';
        }
		return $foreignView;
	}


	public function tableText($field, $param) {
		if ($param['list_update'] === true) {
			$inputView = '
						<td '.$param['class'].'>
							<div id="list-show-div-'.$field.'-{$vo["id"]}">
                    			<span class="list-show-span">{$vo[\''.$field.'\']}</span> <i class="fa fa-edit list-update-btn" data-field="'.$field.'" data-id="{$vo[\'id\']}"></i>
                			</div>
			                <div id="list-update-div-'.$field.'-{$vo[\'id\']}" class="hidden-only">
			                    <input data-model="'.CONTROLLER_NAME.'" data-field="'.$field.'" data-id="{$vo[\'id\']}" class="list-update-input" value="{$vo[\''.$field.'\']}"/>
			                </div>
		                </td>';
		} else {
			$inputView = '
						<td '.$param['class'].'>{$vo['.$field.']}</td>';
		}
		return $inputView;
	}


	public function tableFile($field, $param) {
		//file的content img,100,200 //图片/文件 宽 高
		$fileStr = '{$vo['.$field.']}'; //img类型，update时显示图片,create时不显示
		if ($param['params']['img'] === true) {
			if (!$param['params']['width']) {
				$imgWidth = "120";
			} else {
				$imgWidth = $param['params']['width'];
			}
			if (!$param['params']['height']) {
				$imgHeight = "60";
			} else {
				$imgHeight = $param['params']['height'];
			}
			$fileStr = '<img src="__PUBLIC__/file/{$vo[\''.$field.'\']}" width="'.$imgWidth.'" height="'.$imgHeight.'" />';
        } else {
            $fileStr = '<a href="__PUBLIC__/file/{$vo[\''.$field.'\']}" target="__blank">{$vo[\''.$field.'\']}</a>';
        }
		$inputView = '
                        <td '.$param['class'].'>'.$fileStr.'</td>';
		return $inputView;
	}

	public function tableHasmany($field, $param) {
        $hasmanyView = '';
        $hasmanyView .= '
        <php>
            $hasmanyResultArr = D("'.$this->getModelNameByTableName($param['params']['foreign_table']).'")->where(["'.$param['params']['foreign_key'].'"=> $vo["id"]])->select();
            $hasmanyTempArr = [];
            foreach ($hasmanyResultArr as $hasmanyResultItem) {
                $hasmanyTempArr[] = $hasmanyResultItem["'.$param['params']['foreign_show'].'"];
            }
            $hasmanyStr = implode(",", $hasmanyTempArr);
        </php>
        <td '.$param['class'].'>{$hasmanyStr}</td>';
        return $hasmanyView;
	}


	public function tableOption($field, $param, $onlyContent = false) {
		//响应式区分，只在手机上显示;只在电脑上显示.
		if ($param['list'] === true) {
			$classStr = 'class="center"';
		} elseif ($param['list'] == 'pc') {
			$classStr = 'class="center visible-md visible-lg hidden-sm hidden-xs"';
		} elseif ($param['list'] == 'wap') {
			$classStr = 'class="center visible-xs visible-sm hidden-md hidden-lg"';
		}
        //如果是onlyContent，屏蔽部分内容
        if ($onlyContent) {
            $param['list_update'] = false;
        }
		$param['class'] = $classStr;
		if ($param['list'] === false) {
		} else if ($param['list_specify'] != '') {
			$tableView = '
                        <td '.$param['class'].'>'.$param['list_specify'].'</td>';
		} else {
			if (($param['type'] == 'password') || ($param['type'] == 'textarea') || ($param['type'] == 'checkbox')) {
                if ($param['type'] == 'checkbox') {
                    $param['list_update'] = false;
                }
				$param['type'] = 'text';
			}
			$tableOption = 'table'.ucfirst($param['type']);
			
			$tableView = $this->$tableOption($field, $param, $onlyContent);
		}
		return $tableView;
	}

	public function tableStart($model, $onlyContent = false) {
        if ($onlyContent) {
            $startView = '
            <table class="table table-striped table-bordered table-hover only-content-table">
                <thead>
                    <tr>';

        } else {
            $startView = '
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="center">
                                <label>
                                    <input type="checkbox" class="ace" id="checkall"/>
                                    <span class="lbl"></span>
                                </label>
                            </th>';
        }
		foreach ($model->fieldMap as $field => $param) {
			if ($param['list'] !== false) {
                if (($onlyContent != false) && ($param['params']['foreign_table'] == $onlyContent)) {
                    //hasmany的话则跳过
                    continue;
                }
				if ($param['list'] === true) {
					$classStr = 'class="center"';
				} elseif ($param['list'] == 'pc') {
					$classStr = 'class="center visible-md visible-lg hidden-sm hidden-xs"';
				} elseif ($param['list'] == 'wap') {
					$classStr = 'class="center visible-xs visible-sm hidden-md hidden-lg"';
				}
                if ($onlyContent || ($param['sort'] === false)) {
                    $startView .= '
                        <th '.$classStr.'>
                            '.$param['title'].'
                        </th>';
                } else {
                    $startView .= '
                        <th '.$classStr.'>
                            <if condition="!$_GET[\'sortStr\']">
                                <a class="sort-btn width-100 inline-block" href="#" data-value="'.$field.' asc" data-new-value="'.$field.' asc">'.$param['title'].' <i class="fa fa-sort"></i></a>
                            <elseif condition="(preg_match(\'/,'.$field.' desc/\', $_GET[\'sortStr\']) OR preg_match(\'/^'.$field.' desc/\', $_GET[\'sortStr\']))" />
                                <a class="sort-btn width-100 inline-block" href="#" data-value="'.$field.' desc" data-new-value="'.$field.' asc">'.$param['title'].' <i class="fa fa-sort-alpha-desc"></i></a>
                            <else/>
                                <a class="sort-btn width-100 inline-block" href="#" data-value="'.$field.' asc" data-new-value="'.$field.' desc">'.$param['title'].' <i class="fa fa-sort-alpha-asc"></i></a>
                            </if>
                        </th>';
                }
			}
		}
        if ($onlyContent) {
            $startView .= '
                            <th class="center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <volist name="list" id="vo">
                        <tr>';
        } else {
            $startView .= '
                            <th class="center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <volist name="list" id="vo">
                        <tr>
                            <td class="center">
                                <label>
                                    <input type="checkbox" class="ace" name="chkid" value="{$vo[\'id\']}"/>
                                    <span class="lbl"></span>
                                </label>
                            </td>';
        }
		return $startView;
	}

	/**
	 * 增加table里的手机操作按钮用,在controller里覆盖
	 * return String
	 */
	public function wapTableOpeationButton() {
		return '';
	}

	/**
	 * 增加table里的操作按钮用,在controller里覆盖
	 * return String
	 */
	public function tableOpeationButton() {
		return '';
	}

	public function tableEnd($model, $onlyContent = false) {
		$deleteButton = '';
		$wapDeleteButton = '';
		$updateButton = '';
		$wapUpdateButton = '';
		$detailButton = '';
		$wapDetailButton = '';
        if ($onlyContent) {
            $onlyContentClass = '-only-content';
        }
		if ($model->delete !== false) {
			$deleteButton = '
                                    <a class="delete-btn'.$onlyContentClass.'" id="{$vo[\'id\']}" >
                                        <button type="button" class="btn btn-xs btn-danger border-1">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </a>';
			$wapDeleteButton = '
                                            <li>
                                                <a class="delete-btn'.$onlyContentClass.'" id="{$vo[\'id\']}">
                                                    <span class="red">
                                                        <i class="icon-trash bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>';

		}
		if (($model->update !== false) && (!$onlyContent)) {
			$updateButton = '
                                    <a href="{:U(update, [\'id\'=>$vo[\'id\']])}">
                                        <button type="button" class="btn btn-xs btn-info border-1">
                                            <i class="icon-edit mr-0"></i>
                                        </button>
                                    </a>';
			$wapUpdateButton = '
                                            <li>
                                                <a href="{:U(update, [\'id\'=>$vo[\'id\']])}" class="tooltip-success" data-rel="tooltip" title="Edit">
                                                    <span class="green">
                                                        <i class="icon-edit bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>';

		}
		if (($model->detail !== false) && (!$onlyContent)){
			$detailButton = '
                                    <a href="{:U(detail, [\'id\'=>$vo[\'id\']])}">
                                        <button type="button" class="btn btn-xs btn-warning border-1">
                                            <i class="icon-search mr-0"></i>
                                        </button>
                                    </a>';
			$wapDetailButton = '
                                            <li>
                                                <a href="{:U(detail, [\'id\'=>$vo[\'id\']])}" class="tooltip-success" data-rel="tooltip" title="Edit">
                                                    <span class="yellow">
                                                        <i class="icon-search bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>';

		}

		$tableOpeationButton = $this->tableOpeationButton();
		$wapTableOpeationButton = $this->wapTableOpeationButton();
		$endView = '
                            <td class="center">
                                <div class="visible-md visible-lg hidden-sm hidden-xs btn-group">'
           .$tableOpeationButton
           .$detailButton
           .$updateButton
		.$deleteButton.'
                                </div>
                                <div class="visible-xs visible-sm hidden-md hidden-lg">
                                    <div class="inline position-relative">
                                        <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-cog icon-only bigger-110"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-only-icon dropdown-yellow pull-right dropdown-caret dropdown-close">' 
           .$wapTableOpeationButton
           .$wapDetailButton
           .$wapUpdateButton
		.$wapDeleteButton.'
                                        </ul>
                                    </div>
                                </div>
                            </td>';
		$endView .= '
                        </tr>
                    </volist>
                </tbody>
            </table>';
        if (!$onlyContent) {
            $endView .= '
            {$page}
        </div><!-- /.table-responsive -->
    </div><!-- /span -->
    </div><!-- /row -->
<include file="Public/ace_footer" />';
        }
		return $endView;
	}


	//UPDATE
	public function updateStart($model, $onlyContent = false, $isImport = false) {
		//文件上传表单enctype
		$fileStr = '';
        if ($isImport) {
	        $fileStr = 'enctype="multipart/form-data"';
        } else {
            foreach ($model->fieldMap as $field => $param) {
                if ($param['type'] == 'file') {
                    $fileStr = 'enctype="multipart/form-data"';
                }
            }
        }
        $updateView = '';
        if (!$onlyContent) {
            $updateView .= '<include file="Public/ace_header" />';
        }
		$updateView .= '
    <div class="row">';
        if (!$onlyContent) {
		    $updateView .= '
        <form class="form-horizontal update-form pr-10 pl-10" role="form" method="post" '.$fileStr.'>';
        }
		return $updateView;
	}
	public function updatePassword($field, $param, $onlyContent = false) {
        if ($onlyContent) {
            $onlyContentClass = '-only-content';
        }
		$inputView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right">
                        <input type="password" class="width-100" placeholder="'.$param['title'].'" id="'.$field.$onlyContentClass.'" name="'.$field.'"  value="{$model[\''.$field.'\']|default=\'\'}" '.$param['require'].' '.$param['readonly'].'/>
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $inputView;
	}
	public function updateText($field, $param, $onlyContent = false) {
        if ($onlyContent) {
            $onlyContentClass = '-only-content';
        }
		$inputView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right">';
        if (!$onlyContent) {
            $inputView .= '
                        <input type="text" class="width-100" placeholder="'.$param['title'].'" id="'.$field.$onlyContentClass.'" name="'.$field.'"  value="{$model[\''.$field.'\']|default=\'\'}" '.$param['require'].' '.$param['readonly'].'/>';
        } else {
            $inputView .= '
                        <input type="text" class="width-100" placeholder="'.$param['title'].'" id="'.$field.$onlyContentClass.'" name="'.$field.'"  value="" '.$param['require'].' '.$param['readonly'].'/>';
        }
        $inputView .='
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $inputView;
	}

	public function updateDate($field, $param, $onlyContent = false) {
        if ($onlyContent) {
            $onlyContentClass = '-only-content';
        }
		$dateView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                <span class="block input-icon input-icon-right">';
        if (!$onlyContent) {
            $dateView .= '
                        <input type="text" class="width-100" placeholder="'.$param['title'].'" id="'.$field.$onlyContentClass.'" name="'.$field.'"  value="{$model[\''.$field.'\']|default=\'\'|date=\'Y-m-d\',###}" '.$param['require'].' onClick="WdatePicker()" readonly=""/>';
        } else {
            $dateView .= '
                        <input type="text" class="width-100" placeholder="'.$param['title'].'" id="'.$field.$onlyContentClass.'" name="'.$field.'"  value="" '.$param['require'].' onClick="WdatePicker()" readonly=""/>';
        }
        $dateView .= '
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $dateView;
	}

	public function updateDatetime($field, $param, $onlyContent = false) {
        if ($onlyContent) {
            $onlyContentClass = '-only-content';
        }
		$datetimeView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                <span class="block input-icon input-icon-right">';
        if (!$onlyContent) {
		    $datetimeView .= '
                        <input type="text" class="width-100" placeholder="'.$param['title'].'" id="'.$field.$onlyContentClass.'" name="'.$field.'"  value="{$model[\''.$field.'\']|default=\'\'|date=\'Y-m-d H:i:s\',###}" '.$param['require'].' onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" readonly=""/>';
        } else {
		    $datetimeView .= '
                        <input type="text" class="width-100" placeholder="'.$param['title'].'" id="'.$field.$onlyContentClass.'" name="'.$field.'"  value="" '.$param['require'].' onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" readonly=""/>';
        }
		    $datetimeView .= '
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $datetimeView;
	}

	public function updateTextarea($field, $param, $onlyContent = false) {
        if ($onlyContent) {
            $onlyContentClass = '-only-content';
        }
		$inputView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                <span class="block input-icon input-icon-right">';
        if (!$onlyContent) {
            $inputView .= '
                <textarea class="col-xs-12 col-sm-6 width-100 p-54" rows="5" id="'.$field.$onlyContentClass.'" name="'.$field.'" placeholder="'.$param['title'].'" '.$param['require'].' '.$param['readonly'].'>{$model["'.$field.'"]|default=\'\'}</textarea>';
        } else {
            $inputView .= '
                <textarea class="col-xs-12 col-sm-6 width-100 p-54" rows="5" id="'.$field.$onlyContentClass.'" name="'.$field.'" placeholder="'.$param['title'].'" '.$param['require'].' '.$param['readonly'].'></textarea>';
        }
        $inputView .= '
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $inputView;
	}

	public function updateRichtextbox($field, $param, $onlyContent = false) {
        if ($onlyContent) {
            $onlyContentClass = '-only-content';
        }
		$ueditorView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                <span class="block input-icon input-icon-right">';
        if (!$onlyContent) {
		    $ueditorView .= '
                        <textarea class="col-xs-12 col-sm-6 width-100 p-54" rows="5" id="'.$field.$onlyContentClass.'" name="'.$field.'" placeholder="'.$param['title'].'" '.$param['require'].' '.$param['readonly'].'>{$model["'.$field.'"]|default=""}</textarea>';
        } else {
		    $ueditorView .= '
                        <textarea class="col-xs-12 col-sm-6 width-100 p-54" rows="5" id="'.$field.$onlyContentClass.'" name="'.$field.'" placeholder="'.$param['title'].'" '.$param['require'].' '.$param['readonly'].'></textarea>';
        }
		$ueditorView .= '
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <script type="text/javascript">var editor = UE.getEditor("'.$field.$onlyContentClass.'");</script>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $ueditorView;
	}
	public function updateFile($field, $param, $onlyContent = false) {
		$showStr = ''; //img类型，update时显示图片,create时不显示
		if ($param['params']['img'] === true) {
			if (!$param['params']['width']) {
				$imgWidth = "120";
			} else {
				$imgWidth = $param['params']['width'];
			}
			if (!$param['params']['height']) {
				$imgHeight = "60";
			} else {
				$imgHeight = $param['params']['height'];
			}
			$showStr = '<img src="__PUBLIC__/file/{$model[\''.$field.'\']}" width="'.$imgWidth.'" height="'.$imgHeight.'" />';
        } else {
            $showStr = '<a href="__PUBLIC__/file/{$model[\''.$field.'\']}" target="__blank">{$model[\''.$field.'\']}</a>';
        }
        if ($onlyContent) {
            $onlyContentClass = '-only-content';
        }
		$fileView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right">
                        <input type="file" class="width-100" placeholder="'.$param['title'].'" id="'.$field.$onlyContentClass.'" name="'.$field.'"  value="{$model[\''.$field.'\']|default=\'\'}" <if condition="ACTION_NAME neq \'update\'">'.$param['require'].'</if> '.$param['readonly'].'/>
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>
            <if condition="ACTION_NAME eq update">
			<div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="imgShow"></label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right">
                    	'.$showStr.'
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>
            </if>';
		return $fileView;
	}

    public function updateHasmanyAfterEnd($field, $param) {
        $hasmanyModelStr = $this->getModelNameByTableName($param['params']['foreign_table']);
        $hasmanyModel = D($hasmanyModelStr);
        $view = '
            <div class="modal fade" id="hasmany-modal-'.$field.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">新增</h4>
                        </div>
                        <div class="modal-body">
                        <form method="post" action="__MODULE__/'.$hasmanyModelStr.'/create" id="hasmany-modal-'.$field.'-form" enctype="multipart/form-data">
                            '.$this->updateView($hasmanyModel, true).'
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">关闭</button>
                        <button type="submit" class="btn btn-sm btn-primary" onclick="submitHasManyModal(\''.$field.'\');">提交</button>
                    </div>
                </div>
            </div>
            <script>
                function submitHasManyModal(field) {
                    var formData = new FormData(document.getElementById("hasmany-modal-"+field+"-form"));
                    $.ajax({
                        url:"__MODULE__/'.$hasmanyModelStr.'/create",
                        type:"post",
                        contentType: false,
                        processData: false,
                        data:formData,
                        timeout:30000,
                        success:function(data){
                            if (data.status == 1) {
                            //刷新modal
                            $("#hasmany-modal-"+field+"-form")[0].reset();
                            //关闭modal
                            $("#hasmany-modal-"+field).modal("hide");
                            //给input增加值
                            var value = $("#"+field).val(); 
                            if (value) {
                                var valueArr = value.split(",");
                            } else {
                                var valueArr = new Array();
                            }
                            valueArr.push(data.newId);
                            var newValue = valueArr.join(",");
                            $("#"+field).val(newValue);
                            //更新列表,ajax获取内容填充到table
                            $.ajax({
                                url:"__MODULE__/'.$hasmanyModelStr.'/contentList/idStr/"+newValue,
                                type:"get",
                                timeout:30000,
                                success:function(table) {
                                    $("#hasmany-table-"+field).html(table);
                                    monitorHasmanyEvent();
                                }
                            });
                            } else {
                                alert(data.info);
                                return false;
                            }
                        }
                    });
                }
            </script>';
    return $view;
    }
    public function updateHasmany($field, $param, $onlyContent = false) {
        $hasmanyModelStr = $this->getModelNameByTableName($param['params']['foreign_table']);
        $hasmanyModel = D($hasmanyModelStr);
		$inputView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right">
                        <php>
                            $hasmanyResultArr = D("'.$this->getModelNameByTableName($param['params']['foreign_table']).'")->where(["'.$param['params']['foreign_key'].'"=> $model["id"]])->select();
                            $hasmanyTempArr = [];
                            foreach ($hasmanyResultArr as $hasmanyResultItem) {
                                $hasmanyTempArr[] = $hasmanyResultItem[id];
                            }
                            $hasmanyStr = implode(",", $hasmanyTempArr);
                        </php>
                        <input type="hidden" class="width-100" placeholder="'.$param['title'].'" id="'.$field.'" name="'.$field.'"  value="{$hasmanyStr|default=\'\'}" '.$param['require'].' '.$param['readonly'].'/>
                        <button class="btn btn-xs" style="margin-bottom:3px;" data-toggle="modal" data-target="#hasmany-modal-'.$field.'" id="hasmany-modal-trigger-'.$field.'">新增</button>
                        <div id="hasmany-table-'.$field.'">
                        '.$this->tableView($hasmanyModel, $this->getTableNameByModelName(CONTROLLER_NAME)).'
                        </div>
                        <i></i>
                        <small class="color-gray">'.$param['tips'].'</small>
                    </span>
                </div>
                <script>
                    function monitorHasmanyEvent(){
                        $(".delete-btn-only-content").click(function(){
                            //删除input的内容
                            var input = $(this).parents("table").parent().prev().prev();
                            var tableEle = $(this).parents("table");
                            var value = input.val(); 
                            if (value) {
                                var valueArr = value.split(",");
                            } else {
                                var valueArr = new Array();
                            }
                            removeByValue(valueArr, $(this).attr("id"));
                            var newValue = valueArr.join(",");
                            input.val(newValue);
                            //更新table
                            $.ajax({
                                url:"__MODULE__/'.$hasmanyModelStr.'/contentList/idStr/"+newValue,
                                type:"get",
                                timeout:30000,
                                success:function(table) {
                                    tableEle.html(table);
                                    monitorHasmanyEvent();
                                }
                            });
                        });
                    }
                    function removeByValue(arr, val) {
                        for(var i=0; i<arr.length; i++) {
                            if(arr[i] == val) {
                                arr.splice(i, 1);
                                break;
                            }
                        }
                    }
                    monitorHasmanyEvent();
                </script>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $inputView;
	}


	/**
	 * 根据表名获取到model名
	 */
	public function getModelNameByTableName($tableName) {
		$tableNames = explode('_', $tableName);
		$modelName = '';
		foreach ($tableNames as $tableName) {
			$modelName .= ucfirst($tableName);
		}
		return $modelName;
	}
    /**  
     * 根据model名获取到表名
     */
    public function getTableNameByModelName($modelName) {
        $realTableName = D($modelName)->getTableName();
        $tablePrefix = C('DB_PREFIX');
        $tableNameArr = explode($tablePrefix, $realTableName);
        if (count($tableNameArr) > 1) {
            return $tableNameArr[1];
        } else {
            return $realTableName;
        }
    }


	public function updateForeign($field, $param, $onlyContent = false) {
        if ($onlyContent) {
            //如果本外键就是对方的hasmany字段，那么跳过显示
            if ($onlyContent == $param['params']['foreign_table']) {
                return '<input type="hidden" name="'.$field.'" value="0"/>';
            }
        }
		//获取foreignModel信息
		$foreignModel = $this->getModelNameByTableName($param['params']['foreign_table']);
		//foreignCondition
		if ($param['params']['foreign_condition']) {
		    $foreignCondition = $param['params']['foreign_condition'];
		} else {
		    $foreignCondition = 1;
		}
		$optionContent = '
                            <php> $'.$param['params']['foreign_table'].' = D("'.$foreignModel.'")->where('.$foreignCondition.')->select(); </php>
                            <volist name="'.$param['params']['foreign_table'].'" id="vo">
                            <option value="{$vo[\''.$param['params']['foreign_table_key'].'\']}" <if condition ="$model[\''.$field.'\'] eq $vo[\''.$param['params']['foreign_table_key'].'\']"> selected </if>>{$vo[\''.$param['params']['foreign_show'].'\']}</option>
                            </volist>';
		$foreignView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right">
                        <select name="'.$field.'" id="'.$field.'" class="width-100" '.$param['readonly'].'>'.$optionContent.'
                        </select>
                        <i></i>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
             </div>';
		return $foreignView;
	}

	public function updateCheckbox($field, $param) {
		$options = explode(',', $param['params']['options']);
		$optionContent = '';
		foreach ($options as $option) {
			$optionContent .= '
                        <label><input type="checkbox" name="'.$field.'[]" class="checkbox-item" value="'.$option.'" <if condition="in_array(\''.$option.'\', explode(\',\', $model['.$field.']))">checked="checked"</if> >'.$option.'</label><br/>';
		}
		$checkboxView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <div class="checkbox">'.$optionContent.'
                    </div>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $checkboxView;
	}

	public function updateForeigns($field, $param) {
		//获取foreignModel信息
		$checkboxforeignModel = $this->getModelNameByTableName($param['params']['foreign_table']);
		$optionContent = '
                        <php> $checkboxforeignTempArr = D("'.$checkboxforeignModel.'")->select(); </php>
                        <volist name="checkboxforeignTempArr" id="vo">
                            <label><input type="checkbox" name="'.$field.'[]"  value="{$vo['.$param['params']['foreign_table_key'].']}" <if condition="in_array($vo['.$param['params']['foreign_table_key'].'], explode(\',\', $model['.$field.']))">checked="checked"</if> >{$vo['.$param['params']['foreign_show'].']}</label><br/>
                        </volist>';
		$checkboxforeignView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <div class="checkbox">'.$optionContent.'
                    </div>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $checkboxforeignView;
	}

	public function updateSelect($field, $param) {
		$options = explode(',', $param['params']['options']);
		$optionContent = '';
		foreach ($options as $option) {
			$optionContent .= '
                             <option value="'.$option.'" <if condition ="$model[\''.$field.'\'] eq \''.$option.'\'"> selected </if> >'.$option.'</option>';
		}
		$selectView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right">
                        <select name="'.$field.'" id="'.$field.'" class="width-100" '.$param['readonly'].'>'.$optionContent.'
                        </select>
                        <i></i>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
           </div>';
		return $selectView;
	}

	public function updateEnd($onlyContent = false) {
        if (!$onlyContent) { 
            $endView .= '
            <div class="clearfix form-actions">
                <div style="margin-left: 25%;">
                    <button class="btn btn-info btn-sm" type="submit">
                        <i class="icon-ok bigger-110"></i>
                        提交
                    </button>
                    &nbsp; &nbsp; &nbsp;
                    <button class="btn btn-sm return" type="button">
                        <i class="icon-reply bigger-110"></i>
                        返回
                    </button>
                </div>
            </div>
        </form>
    </div>
<include file="Public/ace_footer" />';
        } else {
            $endView .= '
        </div>';
        }
		return $endView;
	}

    public function updateOptionAfterEnd($field, $param) {
        $updateOption = 'update'.ucfirst($param['type']).'AfterEnd';
        if (method_exists($this, $updateOption)) {
            $updateView = $this->$updateOption($field, $param);
        }
        return $updateView;
    }

	public function updateOption($field, $param, $onlyContent = false) {
		if ($param['input'] === false) {
			return '';
		} elseif ($param['input'] === true) {
			$readonlyStr = '';
		} elseif ($param['input'] === 'create') {
			$readonlyStr = '<if condition="ACTION_NAME eq update">readonly</if>';
		} elseif ($param['input'] === 'update') {
			$readonlyStr = '<if condition="ACTION_NAME eq create">readonly</if>';
		}
		$param['readonly'] = $readonlyStr;
		//必填
		$requireStr = '';
		if ($param['required'] !== false) {
			$requireStr = 'validate-required="required:请填写'.$param['title'].'"';
			$param['require'] = $requireStr;
		}
		$updateOption = 'update'.ucfirst($param['type']);
		if ($param['update_specify'] != '') {
			$updateView = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="'.$field.'"> '.$param['title'].' </label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right">
                        '.$param['update_specify'].'
                        <i></i>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
             </div>';
			// $updateView = $param['update_specify'];
		} else {
			$updateView = $this->$updateOption($field, $param, $onlyContent);
		}
		return $updateView;
	}


	public function detailOption($field, $param) {
		if ($param['detail'] === false) {
			return '';
		}
		if ($param['detail_specify'] != '') {
			$detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <td class="center">'.$param['detail_specify'].'</td>
                </tr>';
			return $detailView;
		}

		if (($param['type'] == 'text') || ($param['type'] == 'textarea') || ($param['type'] == 'select') || ($param['type'] == 'checkbox')) {
			$detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <td class="center">{$detail["'.$field.'"]}</td>
                </tr>';
		} elseif ($param['type'] == 'richtextbox') {
			$detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <td class="center">{$detail["'.$field.'"]|html_entity_decode}</td>
                </tr>';
		} elseif ($param['type'] == 'file') {
			//file的content img,100,200 //图片/文件 宽 高
			if ($param['params']['img'] === true) {
				if (!$param['params']['width']) {
					$imgWidth = "120";
				} else {
                    $imgWidth = $param['params']['width'];
				}
				if (!$param['params']['height']) {
					$imgHeight = "60";
				} else {
					$imgHeight = $param['params']['height'];
				}
				$fileStr = '<img src="__PUBLIC__/file/{$detail[\''.$field.'\']}" width="'.$imgWidth.'" height="'.$imgHeight.'" />';
            } else {
                $fileStr = '<a href="__PUBLIC__/file/{$detail[\''.$field.'\']}" target="_blank">{$detail[\''.$field.'\']}</a>';
            }

			$detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <td class="center">'.$fileStr.'</td>
                </tr>';
		} elseif ($param['type'] == 'date') {
			$detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <td class="center"><if condition="$detail['.$field.'] neq 0">{$detail["'.$field.'"|date="Y-m-d",###]}</if></td>
                </tr>';
		} elseif ($param['type'] == 'datetime') {
			$detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <td class="center"><if condition="$detail['.$field.'] neq 0">{$detail["'.$field.'"|date="Y-m-d H:i:s",###]}</if></td>
                </tr>';
		} elseif ($param['type'] == 'foreign') {
			$foreignModel = $this->getModelNameByTableName($param['params']['foreign_table']);
            if ($param['params']['forign_href'] === true) {
                //外键链接
			    $detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <td class="center"><a href="__MODULE__/'.$foreignModel.'/detail?'.$param['params']['foreign_table_key'].'={$detail["'.$field .'"]}"><php> echo D("'.$foreignModel.'")->where(["'.$param['params']['foreign_table_key'].'"=>$detail['.$field.']])->find()["'.$param['params']['foreign_show'].'"]; </php></a></td>
                </tr>';
            } else {
			    $detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <td class="center"><php> echo D("'.$foreignModel.'")->where(["'.$param['params']['foreign_table_key'].'"=>$detail['.$field.']])->find()["'.$param['params']['foreign_show'].'"]; </php></td>
                </tr>';
            }
        } elseif ($param['type'] == 'foreigns') {
			$checkboxforeignModel = $this->getModelNameByTableName($param['params']['foreign_table']);
            if ($param['params']['forign_href'] === true) {
                $detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <php>
                        $checkboxForeignResultArr = D("'.$checkboxforeignModel.'")->where(["'.$param['params']['foreign_table_key'].'"=>["IN", $detail['.$field.']]])->select();
                        $checkboxForeignTempArr = [];
                        foreach ($checkboxForeignResultArr as $checkboxForeignResultItem) {
                            $checkboxForeignTempArr[] = \'<a href="__MODULE__/Cate/detail?'.$param['params']['foreign_table_key'].'=\'.$checkboxForeignResultItem['.$param['params']['foreign_table_key'].'].\'">\'.$checkboxForeignResultItem['.$param['params']['foreign_show'].'].\'</a>\';
                        }
                        $checkboxForeignStr = implode(",", $checkboxForeignTempArr);
                    </php>
                    <td class="center">{$checkboxForeignStr}</td>';
            } else {
                $detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <php>
                        $checkboxForeignResultArr = D("'.$checkboxforeignModel.'")->where(["'.$param['params']['foreign_table_key'].'"=>["IN", $detail['.$field.']]])->select();
                        $checkboxForeignTempArr = [];
                        foreach ($checkboxForeignResultArr as $checkboxForeignResultItem) {
                                $checkboxForeignTempArr[] = $checkboxForeignResultItem["'.$param['params']['foreign_show'].'"];
                        }
                        $checkboxForeignStr = implode(",", $checkboxForeignTempArr);
                    </php>
                    <td class="center">{$checkboxForeignStr}</td>';
            }
        } elseif ($param['type'] == 'hasmany') {
            $hasmanyModelStr = $this->getModelNameByTableName($param['params']['foreign_table']);
            $hasmanyModel = D($hasmanyModelStr);
            $hasmanyModel->where(['id'=>['IN', I('post.'.$field)]])->data([$param['params']['foreign_key']=>$newId])->save();
            $detailView = '
                <tr>
                    <td class="center" width="20%">'.$param['title'].'</td>
                    <php>
                        $hasmanyResultArr = D("'.$hasmanyModelStr.'")->where(["'.$param['params']['foreign_key'].'"=>["IN", $detail[id]]])->select();
                        $hasmanyTempArr = [];
                        foreach ($hasmanyResultArr as $hasmanyResultItem) {
                                $hasmanyTempArr[] = $hasmanyResultItem["'.$param['params']['foreign_show'].'"];
                        }
                        $hasmanyStr = implode(",", $hasmanyTempArr);
                    </php>
                    <td class="center">{$hasmanyStr}</td>
                </tr>';
        }
		return $detailView;
	}

	public function detailStart() {
		$detailView = '<include file="Public/ace_header" />
<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                <if condition="detail[name]">
                <thead>
                    <tr>
                        <th class="center" colspan="2">{$detail["name"]}信息</th>
                    </tr>
                </thead>
                </if>
                <tbody>';
		return $detailView;
	}

	public function detailEnd() {
		$detailView = '
                </tbody>
            </table>
            <div class="clearfix form-actions">
                <div style="margin-left: 40%;">
                    <button class="btn btn-sm return" type="button">
                        <i class="icon-reply bigger-110"></i>
                        返回
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<include file="Public/ace_footer" />';
		return $detailView;
	}


	public function importContent() {
		$importContent = '
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 control-label no-padding-right" for="import"> 文件 </label>
                <div class="col-xs-12 col-sm-6">
                    <span class="block input-icon input-icon-right" style="padding-top: 6px;">
                        <input type="file" class="width-100"  id="import" name="import" validate-required="required:请选择需要导入的文件"/>
                        <i></i>
                    </span>
                </div>
                <div class="help-block col-xs-12 col-sm-reset inline mb-5"></div>
            </div>';
		return $importContent;
	}
}
