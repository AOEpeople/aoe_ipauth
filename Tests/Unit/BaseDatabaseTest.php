<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 DEV <dev@aoemedia.de>, AOE media GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Base test case
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage AOE IP Auth
 *
 * @author DEV <dev@aoemedia.de>
 */
abstract class Tx_AoeIpauth_Tests_Unit_BaseDatabaseTest extends Tx_Phpunit_Database_TestCase {

	/**
	 * @var string
	 */
	protected $fixturePath;

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface The object manager
	 */
	protected $objectManager;

	/**
	 * @var Tx_Phpunit_Framework
	 */
	protected $testingFramework;


	/**
	 * @var bool
	 */
	protected $useTestDb = TRUE;

	/**
	 * @var array
	 */
	protected $typo3confvars;

	/**
	 *
	 */
	public function setUp() {

		$reflector = new ReflectionClass(get_class($this));

		$this->fixturePath = dirname($reflector->getFileName()) . '/Fixtures/';

		$this->typo3confvars = $GLOBALS['TYPO3_CONF_VARS'];

		// unset, otherwise we add dependency to other extensions
		unset($GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields']);

		if ($this->useTestDb) {
			$this->createDatabase();
			$db = $this->useTestDatabase();
			$this->importStdDB();
			$this->importExtensions(array('cms', 'nc_staticfilecache', 'aoe_ipauth'));
		}


		// Mock tsfe just to get the compressed tca, nothing else!
		// Beware, this is incomplete instantiation if you want to use TSFE for anything else.
		$GLOBALS['TT'] = t3lib_div::makeInstance('t3lib_TimeTrackNull');
		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], 1, 0);

		$GLOBALS['TSFE']->sys_language_mode = 'ignore';
		$GLOBALS['TSFE']->includeTCA();

		$this->testingFramework = new Tx_Phpunit_Framework('tx_aoeipauth', array('fe', 'tt', 'pages'));
	}

	/**
	 *
	 */
	public function tearDown() {

		if ($this->testingFramework) {
			$this->testingFramework->cleanUp();
		}

		unset($this->testingFramework);

		if ($this->useTestDb) {
			$this->cleanDatabase();
			$this->dropDatabase();
			$GLOBALS['TYPO3_DB']->sql_select_db(TYPO3_db);
		}

		unset($GLOBALS['TSFE'], $GLOBALS['TT']);

		$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] = $this->typo3confvars['FE']['addRootLineFields'];
	}

	/**
	 * Injects an untainted clone of the object manager and all its referencing
	 * objects for every test.
	 *
	 * @return void
	 */
	public function runBare() {
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->objectManager =  clone $objectManager;

		$extbaseObjectContainer = $this->objectManager->get('Tx_Extbase_Object_Container_Container'); // Singleton
		$extbaseObjectContainer->registerImplementation('Tx_Extbase_Persistence_IdentityMap', $this->buildAccessibleProxy('Tx_Extbase_Persistence_IdentityMap', 'public function hasIdentifier($uuid, $className) {return FALSE;}'));
		unset($extbaseObjectContainer);

		$this->fixture = $this->objectManager->get('Tx_SonyProductfeeds_Domain_Repository_ConfigurationRepository');

		parent::runBare();
	}

	/**
	 * Returns a mock object which allows for calling protected methods and access
	 * of protected properties.
	 *
	 * @param string $className Full qualified name of the original class
	 * @param array $methods
	 * @param array $arguments
	 * @param string $mockClassName
	 * @param boolean $callOriginalConstructor
	 * @param boolean $callOriginalClone
	 * @param boolean $callAutoload
	 * @return object
	 * @author Robert Lemke <robert@typo3.org>
	 * @api
	 */
	protected function getAccessibleMock($originalClassName, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE) {
		return $this->getMock($this->buildAccessibleProxy($originalClassName), $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
	}


	/**
	 * Creates a proxy class of the specified class which allows
	 * for calling even protected methods and access of protected properties.
	 *
	 * @param $className Full qualified name of the original class
	 * @param string $methods
	 * @return string Full qualified name of the built class
	 */
	protected function buildAccessibleProxy($className, $methods = '') {
		$accessibleClassName = uniqid('AccessibleTestProxy');
		$class = new ReflectionClass($className);
		$abstractModifier = $class->isAbstract() ? 'abstract ' : '';
		eval('
			' . $abstractModifier . 'class ' . $accessibleClassName . ' extends ' . $className . ' {
				public function _call($methodName) {
					$args = func_get_args();
					return call_user_func_array(array($this, $methodName), array_slice($args, 1));
				}
				public function _callRef($methodName, &$arg1 = NULL, &$arg2 = NULL, &$arg3 = NULL, &$arg4 = NULL, &$arg5= NULL, &$arg6 = NULL, &$arg7 = NULL, &$arg8 = NULL, &$arg9 = NULL) {
					switch (func_num_args()) {
						case 0 : return $this->$methodName();
						case 1 : return $this->$methodName($arg1);
						case 2 : return $this->$methodName($arg1, $arg2);
						case 3 : return $this->$methodName($arg1, $arg2, $arg3);
						case 4 : return $this->$methodName($arg1, $arg2, $arg3, $arg4);
						case 5 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5);
						case 6 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5, $arg6);
						case 7 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7);
						case 8 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7, $arg8);
						case 9 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7, $arg8, $arg9);
					}
				}
				public function _set($propertyName, $value) {
					$this->$propertyName = $value;
				}
				public function _setRef($propertyName, &$value) {
					$this->$propertyName = $value;
				}
				public function _get($propertyName) {
					return $this->$propertyName;
				}

				' . $methods . '
			}
		');
		return $accessibleClassName;
	}

}
?>