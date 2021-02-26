<?php
return array(
/*
	'ops.YA03' => array(
			'validation'=>'isSalesSummaryApproved',
			'system'=>'ops',
			'function'=>'YA03',
			'message'=>Yii::t('block','Please complete Operation System - Sales Summary Report Approval before using other functions.'),
		),
	'ops.YA01' => array(
			'validation'=>'isSalesSummarySubmitted',
			'system'=>'ops',
			'function'=>'YA01',
			'message'=>Yii::t('block','Please complete Operation System - Sales Summary Report Submission before using other functions.'),
		),
	'hr.RE02' => array(
			'validation'=>'validateReviewLongTime',
			'system'=>'hr',
			'function'=>'RE02',
			'message'=>Yii::t('block','Please complete Personnel System - Appraisial before using other functions.'),
		),
*/
    'dev.EM02' => array( //台灣地區需要此驗證(新用戶三個月後限制用戶行為)（其它版本可以刪除)
        'validation'=>'validateNewStaff',
        'system'=>'quiz',
        'function'=>array('EM02','EM01','SC04'),
        'message'=>Yii::t('block','validateNewStaff'),
    ),
	'dev.EM03' => array( //台灣地區需要此驗證(不達標一個月後限制用戶行為)（其它版本可以刪除)
        'validation'=>'validateExamination',
        'system'=>'quiz',
        'function'=>array('EM02','EM01','SC04'),
        'message'=>Yii::t('block','validateExamination'),
    ),
    'dev.EM02.hint' => array( //台灣地區需要此驗證(新用戶三個月內只提示)（其它版本可以刪除)
        'validation'=>'validateNewStaffHint',
        'system'=>'quiz',
        'function'=>'',
        'message'=>Yii::t('block','validateNewStaff'),
    ),
    'dev.EM03.hint' => array( //台灣地區需要此驗證(不達標一個月內只提示)（其它版本可以刪除)
        'validation'=>'validateExaminationHint',
        'system'=>'quiz',
        'function'=>'',
        'message'=>Yii::t('block','validateExamination'),
    ),

);
?>
