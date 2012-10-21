<?php

/**
 * MiningJobMapping filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMiningJobMappingFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'mining_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => true)),
      'config_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectConfig'), 'add_empty' => true)),
      'status_code' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'start_time'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'end_time'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'mining_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mining'), 'column' => 'id')),
      'config_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ProjectConfig'), 'column' => 'id')),
      'status_code' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'start_time'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'end_time'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('mining_job_mapping_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MiningJobMapping';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'mining_id'   => 'ForeignKey',
      'config_id'   => 'ForeignKey',
      'status_code' => 'Number',
      'start_time'  => 'Date',
      'end_time'    => 'Date',
    );
  }
}
