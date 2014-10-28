<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

//@todo - add email stats/analytics
$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'email');
$view['slots']->set("headerTitle", $email->getSubject());
$isVariant = $email->isVariant(true);
$view['slots']->start('actions');
if ($security->hasEntityAccess($permissions['email:emails:editown'], $permissions['email:emails:editother'],
    $email->getCreatedBy())): ?>
    <a href="<?php echo $this->container->get('router')->generate(
        'mautic_email_action', array("objectAction" => "edit", "objectId" => $email->getId())); ?>"
        data-toggle="ajax"
        class="btn btn-default"
        data-menu-link="#mautic_email_index">
        <i class="fa fa-fw fa-pencil-square-o"></i> <?php echo $view["translator"]->trans("mautic.core.form.edit"); ?>
    </a>
<?php endif; ?>
<?php if ($security->hasEntityAccess($permissions['email:emails:deleteown'], $permissions['email:emails:deleteother'],
    $email->getCreatedBy())): ?>
    <a href="javascript:void(0);"
        class="btn btn-default"
        onclick="Mautic.showConfirmation(
           '<?php echo $view->escape($view["translator"]->trans("mautic.email.confirmdelete",
           array("%name%" => $email->getSubject() . " (" . $email->getId() . ")")), 'js'); ?>',
           '<?php echo $view->escape($view["translator"]->trans("mautic.core.form.delete"), 'js'); ?>',
           'executeAction',
           ['<?php echo $view['router']->generate('mautic_email_action',
           array("objectAction" => "delete", "objectId" => $email->getId())); ?>',
           '#mautic_email_index'],
           '<?php echo $view->escape($view["translator"]->trans("mautic.core.form.cancel"), 'js'); ?>','',[]);">
        <span><i class="fa fa-fw fa-trash-o"></i> <?php echo $view['translator']->trans('mautic.core.form.delete'); ?></span>
    </a>
<?php endif; ?>
<?php if (!$isVariant && $permissions['email:emails:create']): ?>
    <a href="<?php echo $view['router']->generate('mautic_email_action', array("objectAction" => "abtest", "objectId" => $email->getId())); ?>"
        data-toggle="ajax"
        class="btn btn-default"
        data-menu-link="mautic_email_index">
    <span><i class="fa fa-sitemap"></i> <?php echo $view['translator']->trans('mautic.email.form.abtest'); ?></span>
    </a>
<?php endif; ?>
<?php if (!$isVariant): ?>
    <a href="javascript:void(0);"
        class="btn btn-default"
        onclick="Mautic.showConfirmation(
           '<?php echo $view->escape($view["translator"]->trans("mautic.email.form.confirmsend",
           array("%name%" => $email->getSubject() . " (" . $email->getId() . ")")), 'js'); ?>',
           '<?php echo $view->escape($view["translator"]->trans("mautic.email.send"), 'js'); ?>',
           'executeAction',
           ['<?php echo $view['router']->generate('mautic_email_action',
           array("objectAction" => "send", "objectId" => $email->getId())); ?>',
           '#mautic_email_index'],
           '<?php echo $view->escape($view["translator"]->trans("mautic.core.form.cancel"), 'js'); ?>','',[]);">
        <span><i class="fa fa-fw fa-send"></i> <?php echo $view['translator']->trans('mautic.email.send'); ?></span>
    </a>
<?php endif; ?>
<?php $view['slots']->stop(); ?>



<!-- start: box layout -->
<div class="box-layout">
    <!-- left section -->
    <div class="col-md-9 bg-white height-auto">
        <div class="bg-auto">
            <!-- page detail header -->
            <div class="pr-md pl-md pt-lg pb-lg">
                <div class="box-layout">
                    <div class="col-xs-6 va-m">
                        <h4 class="fw-sb text-primary">
                            <?php
                            if ($category = $email->getCategory()):
                                $catSearch = $view['translator']->trans('mautic.core.searchcommand.category') . ":" . $category->getAlias();
                                $catName = $category->getTitle();
                            else:
                                $catSearch = $view['translator']->trans('mautic.core.searchcommand.is') . ":" .
                                    $view['translator']->trans('mautic.core.searchcommand.isuncategorized');
                                $catName = $view['translator']->trans('mautic.core.form.uncategorized');
                            endif;
                            ?>
                            <a href="<?php echo $view['router']->generate('mautic_asset_index', array('search' => $catSearch))?>"
                               data-toggle="ajax">
                                <?php echo $catName; ?>
                            </a>
                            <span> | </span>
                            <span>
                                <?php
                                $author     = $email->getCreatedBy();
                                $authorId   = ($author) ? $author->getId() : 0;
                                $authorName = ($author) ? $author->getName() : "";
                                ?>
                                <a href="<?php echo $view['router']->generate('mautic_user_action', array(
                                    'objectAction' => 'contact',
                                    'objectId'     => $authorId,
                                    'entity'       => 'asset.asset',
                                    'id'           => $email->getId(),
                                    'returnUrl'    => $view['router']->generate('mautic_asset_action', array(
                                        'objectAction' => 'view',
                                        'objectId'     => $email->getId()
                                    ))
                                )); ?>">
                                    <?php echo $authorName; ?>
                                </a>
                            </span>
                            <span> | </span>
                            <span>
                            <?php $langSearch = $view['translator']->trans('mautic.asset.asset.searchcommand.lang').":".$email->getLanguage(); ?>
                                <a href="<?php echo $view['router']->generate('mautic_asset_index', array('search' => $langSearch)); ?>"
                                   data-toggle="ajax">
                                    <?php echo $email->getLanguage(); ?>
                                </a>
                            </span>
                        </h4>
                        <p class="text-white dark-lg mb-0">Created on <?php echo $view['date']->toDate($email->getDateAdded()); ?></p>
                    </div>
                    <div class="col-xs-6 va-m text-right">
                        <?php switch ($email->getPublishStatus()) {
                            case 'published':
                                $labelColor = "success";
                                break;
                            case 'unpublished':
                            case 'expired'    :
                                $labelColor = "danger";
                                break;
                            case 'pending':
                                $labelColor = "warning";
                                break;
                        } ?>
                        <?php $labelText = strtoupper($view['translator']->trans('mautic.core.form.' . $email->getPublishStatus())); ?>
                        <h4 class="fw-sb"><span class="label label-<?php echo $labelColor; ?>"><?php echo $labelText; ?></span></h4>
                    </div>
                </div>
            </div>
            <!--/ page detail header -->

            <!-- page detail collapseable -->
            <div class="collapse" id="page-details">
                <div class="pr-md pl-md pb-md">
                    <div class="panel shd-none mb-0">
                        <table class="table table-bordered table-striped mb-0">
                            <tbody>
                                <tr>
                                    <td width="20%"><span class="fw-b"><?php echo $view['translator']->trans('mautic.core.author'); ?></span></td>
                                    <td><?php echo $email->getAuthor(); ?></td>
                                </tr>
                                <tr>
                                    <td width="20%"><span class="fw-b"><?php echo $view['translator']->trans('mautic.core.category'); ?></span></td>
                                    <td><?php echo is_object($email->getCategory()) ? $email->getCategory()->getTitle() : ''; ?></td>
                                </tr>
                                <tr>
                                    <td width="20%"><span class="fw-b"><?php echo $view['translator']->trans('mautic.core.date.added'); ?></span></td>
                                    <td><?php echo (!is_null($email->getDateAdded())) ? $view['date']->toFull($email->getDateAdded()) : ''; ?></td>
                                </tr>
                                <tr>
                                    <td width="20%"><span class="fw-b"><?php echo $view['translator']->trans('mautic.core.date.modified'); ?></span></td>
                                    <td><?php echo (!is_null($email->getDateModified())) ? $view['date']->toFull($email->getDateModified()) : ''; ?></td>
                                </tr>
                                <tr>
                                    <td width="20%"><span class="fw-b"><?php echo $view['translator']->trans('mautic.email.form.template'); ?></span></td>
                                    <td><?php echo $email->getTemplate(); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ page detail collapseable -->
        </div>

        <div class="bg-auto bg-dark-xs">
            <!-- page detail collapseable toggler -->
            <div class="hr-expand nm">
                <span data-toggle="tooltip" title="Detail">
                    <a href="javascript:void(0)" class="arrow" data-toggle="collapse" data-target="#page-details"><span class="caret"></span></a>
                </span>
            </div>
            <!--/ page detail collapseable toggler -->

            <!--
            some stats: need more input on what type of form data to show.
            delete if it is not require
            -->
            <div class="pa-md">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel">
                            <div class="panel-body box-layout">
                                <div class="col-xs-12 va-m">
                                    <h5 class="text-white dark-md fw-sb mb-xs">
                                        <span class="fa fa-download"></span>
                                        @todo Chart here 
                                    </h5>
                                </div>
                                
                            </div>
                            <div class="pt-0 pl-15 pb-10 pr-15">
                                <div>
                                    <canvas id="download-chart" height="80"></canvas>
                                </div>
                            </div>
                            <div id="download-chart-data" class="hide"><?php //echo json_encode($stats['downloads']['timeStats']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ some stats -->
        </div>

        <!-- start: tab-content -->
        <div class="tab-content pa-md">
            <?php if (!empty($variants['parent']) || !empty($variants['children'])): ?>
            <?php echo $view->render('MauticEmailBundle:AbTest:details.html.php', array(
                'email'         => $email,
                'abTestResults' => $abTestResults,
                'variants'      => $variants
            )); ?>
            <?php endif; ?>
        </div>
        <!--/ end: tab-content -->
    </div>
    <!--/ left section -->

    <!-- right section -->
    <div class="col-md-3 bg-white bdr-l height-auto">
        <!-- preview URL -->
        <div class="panel bg-transparent shd-none bdr-rds-0 bdr-w-0 mt-sm mb-0">
            <div class="panel-heading">
                <div class="panel-title"><?php echo $view['translator']->trans('mautic.email.recipient.lists'); ?> (<?php
                echo $stats['combined']['sent'] . '/' . $stats['combined']['total']; ?>)</div>
            </div>
            <div class="panel-body pt-xs">
                <ul class="fa-ul">
                    <?php
                    $lists = $email->getLists();
                    foreach ($lists as $l):
                    ?>
                    <li><i class="fa-li fa fa-send"></i><?php echo $l->getName(); ?> (<?php echo (isset($stats[$l->getId()]) ?
                            $stats[$l->getId()]['sent'] . '/' . $stats[$l->getId()]['total'] : '0/0/0'); ?>)</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <!--/ preview URL -->

        <hr class="hr-w-2" style="width:50%">

        <!-- activity feed -->
        <?php echo $view->render('MauticCoreBundle:Default:recentactivity.html.php', array('logs' => $logs)); ?>
    </div>
    <!--/ right section -->
    <input id="itemId" value="<?php echo $email->getId(); ?>" />
</div>
<!--/ end: box layout -->
