<?php

    use Librarys\File\FileInfo;
    use Librarys\App\AppDirectory;
    use Librarys\App\AppLocationPath;
    use Librarys\App\AppParameter;

    define('LOADED', 1);
    require_once('global.php');

    if ($appUser->isLogin() == false)
        $appAlert->danger(lng('login.alert.not_login'), ALERT_LOGIN, 'login.php');

    if ($appDirectory->isFileExistsDirectorySeparatorName() == false)
        $appAlert->danger(lng('home.alert.path_not_exists'), ALERT_INDEX, env('app.http.host'));
    else if ($appDirectory->isPermissionDenyPath($appDirectory->getDirectory()))
        $appAlert->danger(lng('home.alert.path_not_permission', 'path', $appDirectory->getDirectoryAndName()), ALERT_INDEX, env('app.http.host'));

    $appLocationPath = new AppLocationPath($appDirectory, 'index.php');
    $appLocationPath->setIsPrintLastEntry(true);
    $appLocationPath->setIsLinkLastEntry(true);

    $appParameter = new AppParameter();
    $appParameter->add(AppDirectory::PARAMETER_DIRECTORY_URL, $appDirectory->getDirectoryEncode(), true);
    $appParameter->add(AppDirectory::PARAMETER_PAGE_URL,      $appDirectory->getPage(),            $appDirectory->getPage() > 1);
    $appParameter->add(AppDirectory::PARAMETER_NAME_URL,      $appDirectory->getNameEncode(),      true);

    $fileInfo    = new FileInfo($appDirectory->getDirectory() . SP . $appDirectory->getName());
    $isDirectory = $fileInfo->isDirectory();

    if ($isDirectory)
        $title = lng('file_rename.title_page_directory');
    else
        $title = lng('file_rename.title_page_file');

    $themes  = [ env('resource.theme.file') ];
    $appAlert->setID(ALERT_FILE_RENAME);
    require_once('header.php');

    $forms = [
        'name'     => $appDirectory->getName(),
        'path_old' => null,
        'path_new' => null
    ];

    if (isset($_POST['rename'])) {
        $forms['name'] = addslashes($_POST['name']);

        if (empty($forms['name'])) {
            if ($isDirectory)
                $appAlert->danger(lng('file_rename.alert.not_input_name_directory'));
            else
                $appAlert->danger(lng('file_rename.alert.not_input_name_file'));
        } else if (FileInfo::isNameError($forms['name'])) {
            if ($isDirectory)
                $appAlert->danger(lng('file_rename.alert.name_directory_not_validate'));
            else
                $appAlert->danger(lng('file_rename.alert.name_file_not_validate'));
        } else if ($appDirectory->getName() == $forms['name']) {
            $appAlert->danger(lng('file_rename.alert.name_not_change'));
        } else {
            $forms['path_old'] = FileInfo::validate($appDirectory->getDirectory() . SP . $appDirectory->getName());
            $forms['path_new'] = FileInfo::validate($appDirectory->getDirectory() . SP . $forms['name']);

            if (FileInfo::rename($forms['path_old'], $forms['path_new']) == false) {
                if ($isDirectory)
                    $appAlert->danger(lng('file_rename.alert.rename_directory_failed'));
                else
                    $appAlert->danger(lng('file_rename.alert.rename_file_failed'));
            } else {
                $appParameter->set(AppDirectory::PARAMETER_NAME_URL, AppDirectory::rawEncode($forms['name']), true);
                $redirect = 'file_rename.php' . $appParameter->toString(true);

                if ($isDirectory)
                    $appAlert->success(lng('file_rename.alert.rename_directory_success'), null, $redirect);
                else
                    $appAlert->success(lng('file_rename.alert.rename_file_success'), null, $redirect);
            }
        }
    }
?>

    <?php $appAlert->display(); ?>
    <?php $appLocationPath->display(); ?>

    <div class="form-action">
        <div class="title">
            <?php if ($isDirectory) { ?>
                <span><?php echo lng('file_rename.title_page_directory'); ?>: <?php echo $appDirectory->getName(); ?></span>
            <?php } else { ?>
                <span><?php echo lng('file_rename.title_page_file'); ?>: <?php echo $appDirectory->getName(); ?></span>
            <?php } ?>
        </div>
        <form action="file_rename.php<?php echo $appParameter->toString(); ?>" method="post">
            <input type="hidden" name="<?php echo $boot->getCFSRToken()->getName(); ?>" value="<?php echo $boot->getCFSRToken()->getToken(); ?>"/>

            <ul>
                <li class="input">
                    <?php if ($isDirectory) { ?>
                        <span><?php echo lng('file_rename.form.input.name_directory'); ?></span>
                    <?php } else { ?>
                        <span><?php echo lng('file_rename.form.input.name_file'); ?></span>
                    <?php } ?>
                    <input type="text" name="name" value="<?php echo $forms['name']; ?>" placeholder="<?php if ($isDirectory) echo lng('file_rename.form.placeholder.input_name_directory'); else echo lng('file_rename.form.placeholder.input_name_file'); ?>"/>
                </li>
                <li class="button">
                    <button type="submit" name="rename">
                        <span><?php echo lng('file_rename.form.button.rename'); ?></span>
                    </button>
                    <a href="index.php<?php echo $appParameter->toString(); ?>">
                        <span><?php echo lng('file_rename.form.button.cancel'); ?></span>
                    </a>
                </li>
            </ul>
        </form>
    </div>

    <ul class="menu-action">
        <li>
            <a href="file_info.php<?php echo $appParameter->toString(); ?>">
                <span class="icomoon icon-about"></span>
                <span><?php echo lng('file_info.menu_action.info'); ?></span>
            </a>
        </li>
        <?php if ($isDirectory == false) { ?>
            <li>
                <a href="file_download.php<?php echo $appParameter->toString(); ?>">
                    <span class="icomoon icon-download"></span>
                    <span><?php echo lng('file_info.menu_action.download'); ?></span>
                </a>
            </li>
        <?php } ?>
        <li>
            <a href="file_copy.php<?php echo $appParameter->toString(); ?>">
                <span class="icomoon icon-copy"></span>
                <span><?php echo lng('file_info.menu_action.copy'); ?></span>
            </a>
        </li>
        <li>
            <a href="file_delete.php<?php echo $appParameter->toString(); ?>">
                <span class="icomoon icon-trash"></span>
                <span><?php echo lng('file_info.menu_action.delete'); ?></span>
            </a>
        </li>
        <li>
            <a href="file_chmod.php<?php echo $appParameter->toString(); ?>">
                <span class="icomoon icon-key"></span>
                <span><?php echo lng('file_info.menu_action.chmod'); ?></span>
            </a>
        </li>
    </ul>

<?php require_once('footer.php'); ?>
