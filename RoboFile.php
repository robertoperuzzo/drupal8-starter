<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks {

  use \Boedah\Robo\Task\Drush\loadTasks;

  const PROJECT_FOLDER = __DIR__;
  const DRUPAL_ROOT_FOLDER = self::PROJECT_FOLDER . '/web';
  const DATABASE_DUMP_FOLDER = self::PROJECT_FOLDER . '/backups';

  /**
   * Install Drupal profile.
   *
   * @param string $profile
   * @param string $username
   * @param string $password
   * @param string $mail
   * @param string $locale
   *
   * @return $this
   */
  public function install($profile = 'standard', $username = 'admin', $password = 'admin', $mail = "admin@example.com", $locale = 'en') {
    // Build task.
    $task = $this->initDrush()
      ->drush('site:install')
      ->arg($profile);

    if ($username && $password && $mail) {
      $task->option('account-name', $username, '=')
        ->option('account-pass', $password, '=')
        ->option('account-mail', $mail);
    }
    if ($locale) {
      $task
        ->option('locale', $locale);
    }

    $task_list = [
      'install' => $task->printOutput(TRUE),
      'cacheRebuild' => $this->initDrush()->drush('cache:rebuild'),
    ];
    $this->getBuilder()->addTaskList($task_list);
    return $this->getBuilder();
  }

  /**
   * Set up the local dev environment ready to start working on.
   */
  public function build() {
    /*
     * @todo
     *  - check if containers are already running.
     *  - if task fails stop running next tasks.
     */

    // Drupal init.
    $this->taskExecStack()
      ->stopOnFail()
      ->exec('drupal init --destination=/var/www/html/console/ --no-interaction')
      ->run();

    // Local settings.
    $this->taskFilesystemStack()
      ->stopOnFail()
      ->chmod('web/sites/default', 0755)
      ->symlink('../../../settings.local.php', 'web/sites/default/settings.local.php')
      ->chmod('web/sites/default', 0555)
      ->run();

    // Drupal install.
    $this->taskExecStack()
      ->stopOnFail()
      ->exec('drupal site:install --force --no-interaction')
      ->exec('drush cr')
      ->run();

    // Remove database settings from settings.php.
    $this->taskFilesystemStack()
      ->chmod('web/sites/default/settings.php', 0644)
      ->run();
    $this->taskReplaceInFile('web/sites/default/settings.php')
      ->regex('/\$databases\[([.\S\s]*)\);/i')
      ->to('')
      ->run();
    $this->taskFilesystemStack()
      ->chmod('web/sites/default/settings.php', 0444)
      ->run();



    // Import configuration
//    $this->taskExecStack()
//      ->stopOnFail()
//      //->exec('make drush cset system.site uuid 71b4bfb2-b7eb-4a4b-b513-98cf42d112be')
//      //->exec('docker-compose exec --user 82 php drupal config:import --no-interaction')
//      //->exec('make drush "drush ev \'\Drupal::entityManager()->getStorage(\"shortcut_set\")->load(\"default\")->delete();\'"')
//      //->exec('make drush "cim -y"')
//      ->run();
  }

  /**
   * Setup from database.
   *
   * @command install:database
   *
   * @param string $dump_file
   *
   * @return \Robo\Collection\CollectionBuilder
   */
  public function installDatabase($dump_file) {
    $task_list = [
      'sqlDrop' => $this->initDrush()
        ->drush('sql:drop')
        ->option("--yes"),
      'sqlCli' => $this->initDrush()
        ->drush('sql:cli < ')
        ->arg($dump_file),
      'cacheRebuild' => $this->initDrush()->drush('cache:rebuild'),
    ];
    $this->getBuilder()->addTaskList($task_list);
    return $this->getBuilder();
  }

  /**
   * Export configuration of drupal.
   *
   * @command config:export
   *
   * @arg config_export Destination directory_sync to save the configurations.
   *
   * @aliases dce
   * @usage dce
   */
  public function configExport($config_export = 'sync') {
    $task_list = [
      'cacheRebuild' => $this->initDrush()->drush('cache:rebuild'),
      'exportConfig' => $this->initDrush()->drush('config:export -y'),
      'cacheRebuild' => $this->initDrush()->drush('cache:rebuild'),
    ];
    $this->getBuilder()->addTaskList($task_list);
    return $this->getBuilder();
  }

  /**
   * Import configuration of drupal.
   *
   * @return $this
   */
  public function configImport() {
    $task_list = [
      'cacheRebuild' => $this->initDrush()->drush('cache:rebuild'),
      'configImport' => $this->initDrush()
        ->drush('config:import')
        ->option("--yes"),
      'cacheRebuild' => $this->initDrush()->drush('cache:rebuild'),
      'configImport' => $this->initDrush()
        ->drush('config:import')
        ->option("--yes"),
      'cacheRebuild' => $this->initDrush()->drush('cache:rebuild'),
    ];
    $this->getBuilder()->addTaskList($task_list);
    return $this;
  }

  /**
   * Export content.
   *
   * @return \Robo\Collection\CollectionBuilder
   */
  public function contentExport() {
    $task_list = [
      'install' => $this->initDrush()
        ->drush('pm:enable')
        ->args(['default_content_deploy', 'better_normalizers']),
      'exportNode' => $this->initDrush()
        ->drush('dcder node'),
      'exportMenuLinkContent' => $this->initDrush()
        ->drush('dcder menu_link_content'),
      'exportTaxonomyTerm' => $this->initDrush()
        ->drush('dcder taxonomy_term'),
      'exportBlockContent' => $this->initDrush()
        ->drush('dcder block_content'),
      'uninstall' => $this->initDrush()
        ->drush('pm:uninstall')
        ->args([
          'default_content_deploy',
          'default_content',
          'serialization',
          'hal',
          'better_normalizers',
        ]),
    ];
    $this->getBuilder()->addTaskList($task_list);
    return $this->getBuilder();
  }

  /**
   * Import content.
   *
   * @return \Robo\Collection\CollectionBuilder
   */
  public function contentImport() {
    $task_list = [
      'install' => $this->initDrush()
        ->drush('pm:enable')
        ->args(['default_content_deploy', 'better_normalizers']),
      'import' => $this->initDrush()
        ->drush('default-content-deploy-import')
        ->option("--force-update"),
      'uninstall' => $this->initDrush()
        ->drush('pm:uninstall')
        ->args([
          'default_content_deploy',
          'default_content',
          'serialization',
          'hal',
          'better_normalizers',
        ]),
    ];
    $this->getBuilder()->addTaskList($task_list);
    return $this->getBuilder();
  }

  /**
   * Compute various metrics.
   *
   * @command analyze:php
   */
  public function computeMetrics() {
    $this->taskExec('vendor/bin/phpqa')
      ->option('analyzedDirs', 'web/modules/custom')
      ->option('buildDir', 'reports')
      ->option('ignoredFiles', '*\\\.css,*\\\.md,*\\\.txt,*\\\.info,*\\\.yml')
      ->option(
        'tools',
        'phpcpd:0,phpcs:0,phpmd:0,phpmetrics,phploc,pdepend,security-checker,phpstan'
      )
      ->option('execution', 'no-parallel')
      ->option('report')
      ->run();
  }

  /**
   * Sets up drush defaults.
   *
   * @param string $site
   * @return \Boedah\Robo\Task\Drush\DrushStack
   */
  protected function initDrush($site = 'default') {
    return $this->taskDrushStack(self::PROJECT_FOLDER . '/vendor/bin/drush')
      ->drupalRootDirectory(self::DRUPAL_ROOT_FOLDER)
      ->uri($site);
  }

}
