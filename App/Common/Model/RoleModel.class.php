<?php
namespace Common\Model;
use Think\Model;
class RoleModel extends FeasModel {
    public $modelName = '角色';
    public $modelIcon = 'icon-group';
    public $excel = false;
    public $update = true;
    public $detail = true;
    public $delete = true;
    public $sortStr = '';
    public $batchOperation = [];
    protected function _before_insert(&$data,$options) {}
    protected function _after_insert($data,$options) {}
    protected function _before_delete($options) {}
    protected function _after_delete($data,$options) {}
    protected function _before_update(&$data,$options) {}
    protected function _after_update($data,$options) {}
    protected function _after_select(&$resultSet,$options) {}
    public $fieldMap = [
        'id'=>[
            'type'=>'text',
            'title'=>'ID',

            'list'=>true,
            'detail'=>true,
            'input'=>false,
            'excel'=>false,

            'search'=>true,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'name'=>[
            'type'=>'text',
            'title'=>'名称',

            'list'=>true,
            'detail'=>true,
            'input'=>true,
            'excel'=>false,

            'search'=>true,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'create_time'=>[
            'type'=>'datetime',
            'title'=>'创建时间',

            'list'=>false,
            'detail'=>true,
            'input'=>false,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'update_time'=>[
            'type'=>'datetime',
            'title'=>'更新时间',

            'list'=>false,
            'detail'=>true,
            'input'=>false,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
    ];
}