<?php

class App_Entity_CardType extends fActiveRecord {

    protected function configure() {
        fORMDate::configureDateCreatedColumn($this, 'created_on');
        fORMDate::configureDateUpdatedColumn($this, 'modified_on');
    }


}
