<?php
namespace Common\Model;
use Think\Model;
class SystemModel extends FeasModel {
    public $modelName = '系统';
    public $modelIcon = 'icon-dashboard';
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
            'title'=>'系统名称',

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
        'description'=>[
            'type'=>'textarea',
            'title'=>'系统描述',

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
        'keyword'=>[
            'type'=>'text',
            'title'=>'系统关键字',

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
        'logo'=>[
            'type'=>'file',
            'title'=>'系统logo',

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
        'qrcode'=>[
            'type'=>'file',
            'title'=>'微信二维码 ',

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
                'width' => '150',
                'height' => '150',
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'qq'=>[
            'type'=>'text',
            'title'=>'QQ',

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
        'tel'=>[
            'type'=>'text',
            'title'=>'电话',

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
        'address'=>[
            'type'=>'text',
            'title'=>'地址',

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

        'copyright1'=>[
            'type'=>'text',
            'title'=>'版权信息1',

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

        'copyright2'=>[
            'type'=>'text',
            'title'=>'版权信息2',

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

        'url'=>[
            'type'=>'text',
            'title'=>'域名',

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
        'about'=>[
            'type'=>'richtextbox',
            'title'=>'关于我们',

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
        'menu'=>[
            'type'=>'text',
            'title'=>'菜单信息',

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
