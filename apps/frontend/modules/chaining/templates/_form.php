<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('chaining/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
<div><?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'chaining/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?> | <a href="<?php echo url_for('chaining/index') ?>">Back to list</a>
          <?php endif; ?></div>
  <table>
    <tbody>
      <?php echo $form ?>
    </tbody>
  </table>
<div><strong>Max Neighbors</strong> is the maximum number of neighbors the similarity
                algorithm will return for a bicluster (max 1000, min 1).</div>
            <div><strong>Threshold</strong> is the cut off value to use in the algorithm which computes
                the soergel distance between the common rows of biclusters. The value has to be between 0.0001 and 1.</div>
          <input type="submit" value="Save" />
</form>
