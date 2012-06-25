<?php

namespace ZfcBase\Mapper;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

abstract class AbstractDbMapper
{
    /**
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var object
     */
    protected $entityPrototype;

    /**
     * @var HydratingResultSet
     */
    protected $resultSetPrototype;

    /**
     * @var Select
     */
    protected $selectPrototype;

    /**
     * @param Select $select
     * @return HydratingResultSet
     */
    public function selectWith(Select $select)
    {
        $adapter = $this->getDbAdapter();
        $statement = $adapter->createStatement();
        $select->prepareStatement($adapter, $statement);
        $result = $statement->execute();

        $resultSet = $this->getResultSet();
        $resultSet->initialize($result);

        return $resultSet;
    }

    /**
     * @return object
     */
    public function getEntityPrototype()
    {
        if (!$this->entityPrototype) {
            $className = $this->getOptions()->getClassName();
            $this->entityPrototype = new $className;
        }
        return $this->entityPrototype;
    }

    /**
     * @param object $modelPrototype
     * @return AbstractDbMapper
     */
    public function setEntityPrototype($entityPrototype)
    {
        $this->modelPrototype = $entityPrototype;
        unset($this->resultSetPrototype);
        return $this;
    }

    /**
     * @return Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param Adapter $dbAdapter
     * @return AbstractDbMapper
     */
    public function setDbAdapter(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new ClassMethods;
        }
        return $this->hydrator;
    }

    /**
     * @param HydratorInterface $hydrator
     * @return AbstractDbMapper
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        unset($this->resultSetPrototype);
        return $this;
    }

    /**
     * @return HydratingResultSet
     */
    protected function getResultSet()
    {
        if (!$this->resultSetPrototype) {
            $this->resultSetPrototype = new HydratingResultSet;
            $this->resultSetPrototype->setHydrator($this->getHydrator());
            $this->resultSetPrototype->setObjectPrototype($this->getModelPrototype());
        }
        return clone $this->resultSetPrototype;
    }

    /**
     * select
     *
     * @return Select
     */
    protected function select()
    {
        if (!$this->selectPrototype) {
            $this->selectPrototype = new Select;
        }
        return clone $this->selectPrototype;
    }
}
