<?php

/**
 * Visualization form.
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VisualizationForm extends BaseVisualizationForm {

    /**
     *
     */
    public function configure() {
        unset(
                $this['jsondata'], $this['mining_id'], $this['project_id'], $this['created_at'], $this['updated_at']
        );

        $this->setDefaults(array(
            'name' => 'vis'
        ));
    }

    /**
     * Override doSave to set mining and project id if new.
     * @param type $values
     * @return type
     */
    public function updateObject($values = null) {
        $object = parent::updateObject($values);

        if ($object->getId() == null) {
            $chaining = Doctrine_Core::getTable('Chaining')->find($this->getValue("chaining_id"));
            $mining = $chaining->getMining();
            $project_id = $mining->getProjectId();

            //echo "Chaining id: " . $chaining->getId() . "<br>\n";
            //echo "Mining id: " . $mining->getId() . " Project id: " . $project_id . "<br>\n";

            $object->setMining($mining);
            $object->setProjectId($project_id);
        }

        return $object;
    }

}
