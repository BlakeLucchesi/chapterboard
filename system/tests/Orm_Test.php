<?php

class Orm_Test extends Unit_Test_Case {
  
  // protected $table = 'orm';
  // 
  // protected $object;
  // 
  // function setup() {
  //   Event::add('orm.before_insert', array($this, 'before_insert'));
  //   Event::add('orm.before_update', array($this, 'before_update'));
  //   Event::add('orm.after_insert', array($this, 'after_insert'));
  //   Event::add('orm.after_update', array($this, 'after_update'));
  // }
  // 
  // function teardown() {
  //   $this->object->delete();
  // }
  // 
  // function orm_callbacks_test() {
  //   $this->object = ORM::factory($this->table);
  //   $this->object->name = 'Pike';
  //   $this->object->save();
  //   $this->object->name = 'Pika';
  //   $this->object->save();
  // }
  // 
  // function before_insert() {
  //   $this->assert_false($this->object->loaded);
  //   $this->object->name = 'Pikappa';
  // }
  // 
  // function after_insert() {
  //   $this->assert_equal('Pikappa', $this->object->name);
  // }
  // 
  // function before_update() {
  //   $this->assert_true($this->object->loaded);
  //   $this->object->name = 'Changed';
  // }
  // 
  // function after_update() {
  //   $this->assert_true($this->object->loaded);
  //   $this->assert_equal('Changed', $this->object->name);
  // }
  // 
  //   
}