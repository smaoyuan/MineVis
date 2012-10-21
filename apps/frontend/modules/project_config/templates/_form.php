<?php use_stylesheets_for_form($form) ?>
<?php use_javascript('projectconfig.js') ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo ($form->getObject()->isNew() ? url_for('project_config_create',$form->getObject()) : url_for('project_config_update', $form->getObject())) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
    <?php if (!$form->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />
    <?php endif; ?>
    <?php echo $form->renderHiddenFields() ?>

    <?php if ($form->hasGlobalErrors()): ?>
    <div> 
        &nbsp;<?php echo $form->renderGlobalErrors() ?>
    </div>
    <?php endif; ?>
    <div><strong>Project: <?php echo $form->getObject()->getProject()->getName() ?></strong></div>
    <div>
        <span>
            <div><strong><?php echo $form['table_a']->renderLabel() ?></strong></div>
            <?php echo $form['table_a']->renderError() ?>
            <div><?php echo $form['table_a']->render() ?></div>
            <div>
                <label>Id Field</label>
                <?php echo $form['table_a_id_field']->render() .
                        ' ' . $form['table_a_id_field']->renderError() ?>
            </div>
            <div>
                <label>Name Field</label>
                <?php echo $form['table_a_description_field']->render() .
                        ' ' . $form['table_a_description_field']->renderError() ?>
            </div>
        </span>
        <span>
            <div><strong><?php echo $form['table_axb']->renderLabel() ?></strong></div>
            <?php echo $form['table_axb']->renderError() ?>
            <div><?php echo $form['table_axb']->render() ?></div>
            <div>
                <label>Table a Id</label>
                <?php echo $form['table_axb_table_a_id_field']->render() .
                        ' ' . $form['table_axb_table_a_id_field']->renderError() ?>
            </div>
            <div>
                <label>Table b Id</label>
                <?php echo $form['table_axb_table_b_id_field']->render() .
                        ' ' . $form['table_axb_table_b_id_field']->renderError() ?>
            </div>
        </span>
        <span>
            <div><strong><?php echo $form['table_b']->renderLabel() ?></strong></div>
            <?php echo $form['table_b']->renderError() ?>
            <div><?php echo $form['table_b']->render() ?></div>
            <div>
                <label>Id Field</label>
                <?php echo $form['table_b_id_field']->render() .
                        ' ' . $form['table_b_id_field']->renderError() ?>
            </div>
            <div>
                <label>Name Field</label>
                <?php echo $form['table_b_description_field']->render() .
                        ' ' . $form['table_b_description_field']->renderError() ?>
            </div>
        </span>
    </div>
    <div>
        <?php if (!$form->getObject()->isNew()): ?>
        &nbsp;<?php echo link_to('Delete', 'project_config/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
        <?php endif; ?>
        <input type="submit" value="Save" />
    </div>
</form>
