<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_aoeipauth_domain_model_ip'] = array(
	'ctrl' => $TCA['tx_aoeipauth_domain_model_ip']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden, ip',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, ip'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:aoe_ipauth/Resources/Private/Language/locallang_db.xml:tx_aoeipauth_domain_model_ip.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'ip' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:aoe_ipauth/Resources/Private/Language/locallang_db.xml:tx_aoeipauth_domain_model_ip.ip',
			'config' => array(
				'type' => 'input',
				'size' => 60,
				'eval' => 'trim,required'
			),
		),
		'fe_user' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'fe_group' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'range_type' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
	),
);

?>