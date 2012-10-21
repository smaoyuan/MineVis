<?php

/**
 * Feature form.
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FeatureForm extends BaseFeatureForm
{
  public function configure()
  {
      unset(
	    $this['created_at'], $this['updated_at']
    );
  }
}
