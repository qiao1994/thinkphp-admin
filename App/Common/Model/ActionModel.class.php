<?php
namespace Common\Model;
use Think\Model;
class ActionModel extends FeasModel {
    public $modelName = 'ACTION';
    public $modelIcon = 'icon-globe';
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

            'list'=>false,
            'detail'=>false,
            'input'=>false,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>false,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'module'=>[
            'type'=>'text',
            'title'=>'MODULE',

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
        'controller'=>[
            'type'=>'text',
            'title'=>'CONTROLLER',

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
        'action'=>[
            'type'=>'text',
            'title'=>'ACTION',

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
        'remark'=>[
            'type'=>'textarea',
            'title'=>'备注',

            'list'=>true,
            'detail'=>true,
            'input'=>true,
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
        'file'=>[
            'type'=>'text',
            'title'=>'文件位置',

            'list'=>false,
            'detail'=>true,
            'input'=>true,
            'excel'=>false,

            'search'=>true,
            'sort'=>false,
            'required'=>false,
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
