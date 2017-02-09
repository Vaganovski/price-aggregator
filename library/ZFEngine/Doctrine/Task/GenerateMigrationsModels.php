<?php
/**
 * ZFEngine
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://zfengine.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zfengine.com so we can send you a copy immediately.
 *
 * @category   ZFEngine
 * @package    ZFEngine_Doctrine
 * @subpackage Task
 * @copyright  Copyright (c) 2009-2010 Stepan Tanasiychuk (http://stfalcon.com)
 * @license    http://zfengine.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Generate migration classes for an existing set of models
 *
 * @category   ZFEngine
 * @package    ZFEngine_Doctrine
 * @subpackage Task
 * @copyright  Copyright (c) 2009-2010 Stepan Tanasiychuk (http://stfalcon.com)
 * @license    http://zfengine.com/license/new-bsd     New BSD License
 */
class ZFEngine_Doctrine_Task_GenerateMigrationsModels extends ZFEngine_Doctrine_Task_Abstract
{
    /**
     * Task description
     * @var string
     */
    public $description = 'Generate migration classes for an existing set of models';

    /**
     * Task required arguments
     * @var array
     */
    public $requiredArguments    =   array(
        'migrations_path' => 'Specify the path to your migration classes folder.',
        'temp_schema_path' => 'Path to temp shema files',
        'old_schema_path' => 'Path to oldest shema files',
        'modules_path' => 'Path to modules directories'
    );

    /**
     * Task name
     * @var string
     */
    public $taskName = 'zfengine-generate-migrations-models';
    
    /**
     * Execute task
     *
     * @return void
     */
    public function execute()
    {
        // @todo translate comments

        $modulesPath = $this->getArgument('modules_path');
        // Список модулей
        $modulesDirectories = scandir($modulesPath);

        $modelsDirectories = array();
        // Пробегаемся по директории с модулями
        foreach ($modulesDirectories as $directory) {
            if ($directory[0] != '.') {
                // Первая буква модуля - заглавная
                $moduleName = ucwords($directory);
                $modelsPath = $modulesPath . $directory . '/models/';

                // Если есть такая папка
                if (is_dir($modelsPath)) {
                    // Пробегаемся по списку моделей
                    foreach (scandir($modelsPath) as $file) {
                        // Если это файл, а не папка
                        if ($file[0] != '.' && !is_dir($modelsPath . $file)) {
                            // Грузим модель
                            Doctrine_Core::loadModel($moduleName . '_Model_' . substr($file, 0, strlen($file)-4));
                        }
                   }
                }
            }
        }

        // Генерируем миграции на основании моделей
        Doctrine_Core::generateMigrationsFromModels($this->getArgument('migrations_path'), null);

        $this->notify('Generated migration classes successfully from models');

        // Обновляем yaml-файлы для последующих сравнений миграций
        $oldSchemaPath = $this->getArgument('old_schema_path');
        $this->copyShema($oldSchemaPath);
        $this->notify('Shema files copied');
    }
}