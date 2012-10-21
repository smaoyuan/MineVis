<?php

/**
 * ProjectConfig form.
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ProjectConfigForm extends BaseProjectConfigForm {

    public function configure() {
        $db_name = $this->object->getProject()->getExternalDatabase();
        $external_db = new MineVisDb($db_name);
        $tables = $external_db->getTables();

        $fieldsA = array();
        $fieldsB = array();
        $fieldsAxB = array();
        if ($this->getObject()->isNew()) {
            $fieldsA[''] = "No table selected";
            $fieldsB[''] = "No table selected";
            $fieldsAxB[''] = "No table selected";
        } else {
            $fieldsA = $external_db->getFields($this->getObject()->getTableA());
            $fieldsB = $external_db->getFields($this->getObject()->getTableB());
            $fieldsAxB = $external_db->getFields($this->getObject()->getTableAxb());
        }

        $this->widgetSchema['project_id'] = new sfWidgetFormInputHidden();
        $this->widgetSchema['table_a'] = new sfWidgetFormSelect(array('choices' => $tables));
        $this->widgetSchema['table_axb'] = new sfWidgetFormSelect(array('choices' => $tables));
        $this->widgetSchema['table_b'] = new sfWidgetFormSelect(array('choices' => $tables));

        $this->widgetSchema['table_a_id_field'] = new sfWidgetFormSelect(array('choices' => $fieldsA));
        $this->widgetSchema['table_b_id_field'] = new sfWidgetFormSelect(array('choices' => $fieldsA));
        $this->widgetSchema['table_a_description_field'] = new sfWidgetFormSelect(array('choices' => $fieldsB));
        $this->widgetSchema['table_b_description_field'] = new sfWidgetFormSelect(array('choices' => $fieldsB));
        $this->widgetSchema['table_axb_table_a_id_field'] = new sfWidgetFormSelect(array('choices' => $fieldsAxB));
        $this->widgetSchema['table_axb_table_b_id_field'] = new sfWidgetFormSelect(array('choices' => $fieldsAxB));

        $this->setDefaults(array(
                'table_a_id_field' => 'id',
                'table_b_id_field' => 'id',
                'table_a_description_field' => 'name',
                'table_b_description_field' => 'name',
                'table_axb_table_a_id_field' => 'table_a_id',
                'table_axb_table_b_id_field' => 'table_b_id',));
    }

}
