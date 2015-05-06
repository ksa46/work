<?php

/*
 * Example usage:
 *

    $entityMetadata = new App_Entity_Metadata();
    $entityMetadata->setMetadataId(3);
    $entityMetadata->setMetadataCid(2);
    ...
    ...
    $entity->store();

    The configure function below automatically updatees date timestamp for us.

 *
 */
class App_Entity_User extends fActiveRecord {

    protected function configure() {
        fORMDate::configureDateCreatedColumn($this, 'created_on');
        fORMDate::configureDateUpdatedColumn($this, 'modified_on');
    }
/*
    public function getSequence() {
        return new App_Entity_SequenceEntityTasks();
    }

    public function getContentId() {
        return $this->getTaskId();
    }

    public function getContentCid() {
        return $this->getTaskCid();
    }

    public function setContentId($id) {
        $this->setTaskId($id);
    }

    public function setContentCid($cid) {
        $this->setTaskCid($cid);
    }
*/

}
