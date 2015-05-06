<?php

/*
 * Example usage:
 *
 * The configure function below automatically updatees date timestamp for us.

 *
 */
class App_Entity_Cards extends fActiveRecord {

    protected function configure() {
        fORMDate::configureDateCreatedColumn($this, 'created_on');
        fORMDate::configureDateUpdatedColumn($this, 'modified_on');
    }


}
