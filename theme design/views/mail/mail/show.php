<div class="panel panel-default">

    <?php if ($message == null) { ?>

        <div class="panel-body">
            <?php echo Yii::t('MailModule.views_mail_show', 'There are no messages yet.'); ?>
        </div>
    <?php } else { ?>

        <div class="panel-heading">
            <?php echo CHtml::encode($message->title); ?>

            <div class="pull-right">
                <?php if (count($message->users) != 0) : ?>
                    <?php if (count($message->users) != 1) : ?>
                        <?php
                        $this->widget('application.widgets.ModalConfirmWidget', array(
                            'uniqueID' => 'modal_leave_conversation_' . $message->id,
                            'title' => Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> leaving conversation'),
                            'message' => Yii::t('MailModule.views_mail_show', 'Do you really want to leave this conversation?'),
                            'buttonTrue' => Yii::t('MailModule.views_mail_show', 'Leave'),
                            'buttonFalse' => Yii::t('MailModule.views_mail_show', 'Cancel'),
                            'linkContent' => '<i class="fa fa-sign-out"></i> ',
                            'class' => 'btn btn-primary btn-sm',
                            'linkHref' => $this->createUrl("//mail/mail/leave", array('id' => $message->id))
                        ));
                        ?>
                    <?php endif; ?>
                    <?php if (count($message->users) == 1) : ?>
                        <?php
                        $this->widget('application.widgets.ModalConfirmWidget', array(
                            'uniqueID' => 'modal_leave_conversation_' . $message->id,
                            'title' => Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> deleting conversation'),
                            'message' => Yii::t('MailModule.views_mail_show', 'Do you really want to delete this conversation?'),
                            'buttonTrue' => Yii::t('MailModule.views_mail_show', 'Delete'),
                            'buttonFalse' => Yii::t('MailModule.views_mail_show', 'Cancel'),
                            'linkContent' => '<i class="fa fa-times"></i> ',
                            'class' => 'btn btn-primary btn-sm',
                            'linkHref' => $this->createUrl("//mail/mail/leave", array('id' => $message->id))
                        ));
                        ?>
                    <?php endif; ?>
                    <?php foreach ($message->users as $user) : ?>
                        <a class="profile-size-sm pull-left" href="<?php echo $user->getProfileUrl(); ?>" style="margin-right:5px;">
                            <img src="<?php echo $user->getProfileImage()->getUrl(); ?>"
                                 class="img-rounded tt profile-size-sm  img_margin" height="29" width="29"
                                 data-toggle="tooltip" data-placement="top" title=""
                                 data-original-title="<strong> <?php echo CHtml::encode($user->displayName); ?></strong><br><?php echo CHtml::encode($user->profile->title); ?>">
                           	<div class="profile-overlay-img profile-overlay-img-sm tt" data-toggle="tooltip" data-placement="top" title="" data-original-title="<strong> <?php echo CHtml::encode($user->displayName); ?></strong><br><?php echo CHtml::encode($user->profile->title); ?>"></div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>


        <div class="panel-body">

            <hr style="margin-top: 0;">

            <ul class="media-list">
                <!-- BEGIN: Results -->
                <?php foreach ($message->entries as $entry) : ?>
                    <div class="media" style="margin-top: 0;">
                        <a class="profile-size-xmd pull-left" href="<?php echo $entry->user->getUrl(); ?>"> 
                        	<img class="media-object img-rounded profile-size-xmd"
                                src="<?php echo $entry->user->getProfileImage()->getUrl(); ?>"
                                data-src="holder.js/50x50" alt="50x50"
                                style="width: 50px; height: 50px;">
                            <div class="profile-overlay-img profile-overlay-img-xmd"></div>
                        </a>

                        <?php if ($entry->created_by == Yii::app()->user->id): ?>
                            <div class="pull-right">
                                <?php echo HHtml::link('<i class="fa fa-pencil-square-o"></i>', $this->createUrl("//mail/mail/editEntry", array('messageEntryId' => $entry->id)), array('data-toggle' => 'modal', 'data-target' => '#globalModal', 'class' => '')); ?>
                            </div>
                        <?php endif; ?>

                        <div class="media-body">
                            <h4 class="media-heading" style="font-size: 14px;"><?php echo CHtml::encode($entry->user->displayName); ?>
                                <small><?php echo HHtml::timeAgo($entry->created_at); ?></small>
                            </h4>

                            <span class="content">
                                <?php $this->widget('application.widgets.MarkdownViewWidget', array('markdown' => $entry->content)); ?>
                            </span>
                        </div>
                    </div>

                    <hr>

                <?php endforeach; ?>

            </ul>
            <!-- END: Results -->


            <div class="row-fluid">

                <?php
                $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'reply-message-form',
                    'enableAjaxValidation' => false
                ));
                ?>


                <?php echo $form->errorSummary($replyForm); ?>
                <div class="form-group">
                    <?php echo $form->textArea($replyForm, 'message', array('class' => 'form-control', 'id' => 'newMessage', 'rows' => '4', 'placeholder' => Yii::t('MailModule.views_mail_show', 'Write an answer...'))); ?>
                    <?php $this->widget('application.widgets.MarkdownEditorWidget', array('fieldId' => 'newMessage')); ?>
                </div>
                <hr>

                <?php
                echo HHtml::ajaxSubmitButton(Yii::t('MailModule.views_mail_show', 'Send'), $this->createUrl('//mail/mail/show', array('id' => $message->id)), array(
                    'type' => 'POST',
                    'success' => 'function(html){ $("#mail_message_details").html(html); }',
                        ), array('class' => 'btn btn-primary'));
                ?>


                <div class="pull-right">

                    <!-- Button to trigger modal to add user to conversation -->
                    <?php
                    echo CHtml::link('<i class="fa fa-plus"></i> ' . Yii::t('MailModule.views_mail_show', 'Add user'), $this->createUrl('//mail/mail/adduser', array(
                                'id' => $message->id,
                                'ajax' => 1
                            )), array(
                        'class' => 'btn btn-info',
                        'data-toggle' => 'modal',
                        'data-target' => '#globalModal'
                    ));
                    ?>

    <?php if (count($message->users) > 2 && $message->originator->id != Yii::app()->user->id): ?>
                        <a class="btn btn-danger"
                           href="<?php echo $this->createUrl('leave', array('id' => $message->id)); ?>"><i
                                class="fa fa-sign-out"></i> <?php echo Yii::t('MailModule.views_mail_show', "Leave discussion"); ?>
                        </a>
    <?php endif; ?>
                </div>

            <?php $this->endWidget(); ?>
            </div>
<?php } ?>

    </div>
</div>
