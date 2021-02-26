<?php

return array(
    //總部管理員採購
	'Activity'=>array(
		'access'=>'YS',
		'icon'=>'fa-calendar',
		'items'=>array(
            //訂單活動
			'Order Activity'=>array(
				'access'=>'YS03',
				'url'=>'/activity/index',
			),
            //訂貨處理(採購)
			'Order Purchase'=>array(
				'access'=>'YS01',
				'url'=>'/purchase/index',
			),
            //快速訂單
			'Fast Order'=>array(
				'access'=>'YS04',
				'url'=>'/fast/index',
			),
            //快速訂單
			'Head Email'=>array(
				'access'=>'YS05',
				'url'=>'/email/index',
			),
		),
	),
    //區域管理員下單
	'Order'=>array(
		'access'=>'YD',
		'icon'=>'fa-pencil-square-o',
		'items'=>array(
            //訂單活動(下訂單)
            'Add Order'=>array(
                'access'=>'YD04',
                'url'=>'/order/activity',
            ),
            //訂貨列表(下訂單)
            'Order List'=>array(
                'access'=>'YD03',
                'url'=>'/order/index',
            ),
            //區域管理員處理技術員的訂單
            'Technician take Goods'=>array(
                'access'=>'YD02',
                'url'=>'/delivery/index',
            ),
            //外勤領貨成本總覽
            'Technician cargo cost'=>array(
                'access'=>'YD07',
                'url'=>'/cargoCost/index',
            ),
            //倉庫管理
            'Warehouse Info'=>array(
                'access'=>'YD01',
                'url'=>'/warehouse/index',
            ),
            //多訂單限制
            'OrderAcc Info'=>array(
                'access'=>'YD05',
                'url'=>'/orderAcc/form',
				'text'=>'每次额外发送订单给总部也需要此功能授权',
            ),
            //地區審核
            'Area Audit'=>array(
                'access'=>'YD06',
                'url'=>'/areaAudit/index',
            ),
		),
	),
    //技術員入口
	'Technician'=>array(
		'access'=>'YC',
		'icon'=>'fa-truck',
		'items'=>array(
            //訂貨列表(下訂單)
			'Technician List'=>array(
				'access'=>'YC02',
				'url'=>'/technician/index',
			),
		),
	),

    'Sales Summary'=>array(
        'access'=>'YA',
		'icon'=>'fa-bar-chart',
        'items'=>array(
            'Sales Summary Entry'=>array(
                'access'=>'YA01',
                'url'=>'/monthly/index',
            ),
             'Sales Summary Approval'=>array(
                'access'=>'YA03',
                'url'=>'/monthly/indexa',
           ),
           'Sales Summary Enquiry'=>array(
                'access'=>'YA02',
                'url'=>'/monthly/indexc',
            ),
			'Sales Summary Rate'=>array(
				'access'=>'YA04',
				'url'=>'/monthlyrate/index',
			),
        ),
    ),

    'Report'=>array(
        'access'=>'YB',
		'icon'=>'fa-file-text-o',
        'items'=>array(
            'Sales Summary'=>array(
                'access'=>'YB02',
                'url'=>'/report/salessummary',
            ),
            'Business Report'=>array(
                'access'=>'YB05',
                'url'=>'/report/business',
            ),
            'Order Records'=>array(
                'access'=>'YB03',
                'url'=>'/report/orderlist',
            ),
            'Picking List'=>array(
                'access'=>'YB04',
                'url'=>'/report/pickinglist',
            ),
            'Report Manager'=>array(
                'access'=>'YB01',
                'url'=>'/queue/index',
            ),
        ),
    ),

    //物品管理
    'Goods'=>array(
        'access'=>'YG',
		'icon'=>'fa-cubes',
        'items'=>array(
            //物品管理
            'Import Summary Entry'=>array(
                'access'=>'YG01',
                'url'=>'/goodsim/index',
            ),
            //物品管理
            'Domestic Summary Entry'=>array(
                'access'=>'YG04',
                'url'=>'/goodsdo/index',
            ),
            //物品管理
            'Fast Summary Entry'=>array(
                'access'=>'YG05',
                'url'=>'/goodsfa/index',
            ),
            //標籤管理
            'Stickies Summary Entry'=>array(
                'access'=>'YG02',
                'url'=>'/stickies/index',
            ),
            //分類管理
            'Classify Summary Entry'=>array(
                'access'=>'YG03',
                'url'=>'/classify/index',
            ),
            //分類管理
            'Goods Hybrid Rules'=>array(
                'access'=>'YG06',
                'url'=>'/rules/index',
            ),
        ),
    ),
//	'System Setting'=>array(
//		'access'=>'YC',
//		'items'=>array(
//			'AAAA'=>array(
//				'access'=>'YC01',
//				'url'=>'/accttype/index',
//				'tag'=>'@',
//			),
//		),
//	),
);
