<?php

/**
 * ProjectConfig filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProjectConfigFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'project_id'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Project'), 'add_empty' => true)),
      'table_a'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'table_axb'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'table_b'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'table_a_id_field'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'table_b_id_field'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'table_a_description_field'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'table_b_description_field'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'table_axb_table_a_id_field' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'table_axb_table_b_id_field' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'project_id'                 => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Project'), 'column' => 'id')),
      'table_a'                    => new sfValidatorPass(array('required' => false)),
      'table_axb'                  => new sfValidatorPass(array('required' => false)),
      'table_b'                    => new sfValidatorPass(array('required' => false)),
      'table_a_id_field'           => new sfValidatorPass(array('required' => false)),
      'table_b_id_field'           => new sfValidatorPass(array('required' => false)),
      'table_a_description_field'  => new sfValidatorPass(array('required' => false)),
      'table_b_description_field'  => new sfValidatorPass(array('required' => false)),
      'table_axb_table_a_id_field' => new sfValidatorPass(array('required' => false)),
      'table_axb_table_b_id_field' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('project_config_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProjectConfig';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'project_id'                 => 'ForeignKey',
      'table_a'                    => 'Text',
      'table_axb'                  => 'Text',
      'table_b'                    => 'Text',
      'table_a_id_field'           => 'Text',
      'table_b_id_field'           => 'Text',
      'table_a_description_field'  => 'Text',
      'table_b_description_field'  => 'Text',
      'table_axb_table_a_id_field' => 'Text',
      'table_axb_table_b_id_field' => 'Text',
    );
  }
}
