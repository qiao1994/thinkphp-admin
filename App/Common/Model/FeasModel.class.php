<?php
namespace Common\Model;
use Think\Model;

abstract class FeasModel extends Model {
	public $modelName = '默认'; //model名称，会在前端显示
	public $modelIcon = 'icon-certificate'; //model icon
	public $excel = false; //是否导入导出
	public $update = true; //是否需要修改操作
	public $detail = true; //是否需要详情操作
	public $delete = true; //是否需要删除操作
	public $sortStr = ''; //排序 例id desc
	public $batchOperation = [];
	public $fieldMap = [];

    //构造函数
    public function __construct() {
        if (count($this->fieldMap) > 0) {
            foreach ($this->fieldMap as $field=>$params) {
                //datetime和date类型增加自动完成
                if (($params['type'] == 'date')||($params['type'] == 'datetime')) {
                    $this->_auto[] = [$field, 'strtotime', 3, 'function'];
                    //问题：显的时候能否正常显示
                    //create_time 和 update_time 配置到父model里
                }
                //required增加验证
                if ($params['required'] === true) {
                    $this->_validate[] = [$field, 'require', $params['title'].'必须填写!'];
                }
            }
        }
        //增加create_time的默认操作
        $this->_auto[] = ['create_time', 'time', 1, 'function'];
        $this->_auto[] = ['update_time', 'time', 2, 'function'];
        parent::__construct();
    }
}
