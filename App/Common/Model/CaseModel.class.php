<?php
namespace Common\Model;
use Think\Model;
class CaseModel extends FeasModel {
    public $modelName = '案例';
    public $modelIcon = 'icon-certificate';
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
        'tag_id'=>[
            'type'=>'foreigns',
            'title'=>'标签',

            'list'=>true,
            'detail'=>true,
            'input'=>true,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
                'foreign_table' => 'tag',
                'foreign_table_key' => 'id',
                'foreign_show' => 'name',
                'search_type' => 'select',
                'foreign_href' => false,
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'title'=>[
            'type'=>'text',
            'title'=>'标题',

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
        'image'=>[
            'type'=>'file',
            'title'=>'图片',

            'list'=>true,
            'detail'=>true,
            'input'=>true,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>false,
            'list_update'=>false,
            'params'=>[
                'img' => true,
                'width' => '',
                'height' => '',
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'summary'=>[
            'type'=>'textarea',
            'title'=>'摘要',

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
        'content'=>[
            'type'=>'richtextbox',
            'title'=>'内容',

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
        'browse'=>[
            'type'=>'text',
            'title'=>'浏览量',

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
        'real_browse'=>[
            'type'=>'text',
            'title'=>'真实浏览量',

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