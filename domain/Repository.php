<?php

namespace lepota\domain;

use Functional;
use Yii;
use yii\db\ActiveRecord;
use lepota\exceptions\EntityStorageException;

abstract class Repository
{
    /** @var ActiveRecord[] */
    protected $arObjects = [];

    /**
     * @return Service
     */
    abstract protected function getService();

    /**
     * @return Factory
     */
    abstract protected function getDomainModelFactory();

    /**
     * @param DomainModel $domainModel
     * @return ActiveRecord
     */
    abstract protected function createActiveRecordModel(DomainModel $domainModel);

    abstract protected function getExcludedStatusForGet(): string;

    /**
     * @return string|null
     */
    abstract protected function getFilterStatusForList();

    protected function getExcludedStatusForList()
    {
        return null;
    }

    public function add(DomainModel $domainModel)
    {
        $storageSpecification = $this->domainToAR($domainModel);

        try {
            foreach ($storageSpecification->saveBefore as $relationName => $modelAR) {
                $modelAR->save(false);
                $storageSpecification->rootAR->link($relationName, $modelAR);
            }

            $storageSpecification->rootAR->save(false);

            foreach ($storageSpecification->saveAfter as $relationName => $modelAR) {
                $storageSpecification->rootAR->link($relationName, $modelAR);
                $modelAR->save(false);
            }
        } catch (\Exception $e) {
            Yii::error($e, __CLASS__);
            throw new EntityStorageException($e);
        }

        foreach ($storageSpecification->rootAR->getPrimaryKey(true) as $column => $value) {
            $domainModel->$column = $value;
        }
    }

    /**
     * @param int $id
     * @return DomainModel|null
     */
    public function get(int $id)
    {
        $modelAR = $this->getService()->get($id, $this->getExcludedStatusForGet());
        if (!$modelAR) {
            return null;
        }
        $this->arObjects[$id] = $modelAR;
        return $this->arToDomain($modelAR, false);
    }

    public function update(DomainModel $domainModel)
    {
        $modelAR = $this->arObjects[$this->id($domainModel)];
        $storageSpecification = $this->domainToAR($domainModel, $modelAR);

        try {
            foreach ($storageSpecification->saveBefore as $relationName => $modelAR) {
                $modelAR->save(false);
                $storageSpecification->rootAR->link($relationName, $modelAR);
            }
            foreach ($storageSpecification->deleteAfter as $relationName => $modelAR) {
                $storageSpecification->rootAR->unlink($relationName, $modelAR);
            }

            $storageSpecification->rootAR->save(false);

            foreach ($storageSpecification->saveAfter as $relationName => $modelAR) {
                $storageSpecification->rootAR->link($relationName, $modelAR);
                $modelAR->save(false);
            }
            foreach ($storageSpecification->deleteAfter as $relationName => $modelAR) {
                $modelAR->delete();
            }
        } catch (\Exception $e) {
            Yii::error($e, __CLASS__);
            throw new EntityStorageException($e);
        }
    }

    /**
     * @param int $limit
     * @param int|string|null $offset
     * @return DomainModel[]
     */
    public function list(int $limit, $offset = null): array
    {
        $result = Functional\map(
            $this->getService()->list(
                $limit,
                $offset,
                $this->getFilterStatusForList(),
                $this->getExcludedStatusForList()
            ),
            function ($modelAR) {
                return $this->arToDomain($modelAR, true);
            }
        );
        $this->afterBulkRequest();
        return $result;
    }

    /**
     * @param DomainModel $domainModel
     * @param ActiveRecord|null $modelAR
     * @return StorageSpecification
     */
    protected function domainToAR(DomainModel $domainModel, ActiveRecord $modelAR = null): StorageSpecification
    {
        if (!$modelAR) {
            $modelAR = $this->createActiveRecordModel($domainModel);
        }
        $storageSpecification = $this->domainToARAttributes($domainModel, $modelAR);
        $storageSpecification->rootAR = $modelAR;
        return $storageSpecification;
    }

    /**
     * Override in subclasses
     *
     * @param DomainModel $domainModel
     * @param ActiveRecord $modelAR
     * @return StorageSpecification
     */
    protected function domainToARAttributes(DomainModel $domainModel, ActiveRecord $modelAR): StorageSpecification
    {
        $modelAR->setAttributes($domainModel->getAttributes(null, ['id']), false);
        return new StorageSpecification();
    }

    /**
     * Map domain model relation to active record relation
     *
     * @param DomainModel $domainModel
     * @param ActiveRecord $modelAR
     * @param string $relationName
     * @param \Closure $relationARCreateCallback
     * @return StorageSpecification
     */
    protected function domainToARRelated(
        DomainModel $domainModel,
        ActiveRecord $modelAR,
        string $relationName,
        \Closure $relationARCreateCallback
    ) {
        /** @var DomainModel $relationDomainModel */
        $relationDomainModel = $domainModel->$relationName;

        $storageSpecification = new StorageSpecification();
        if (!$relationDomainModel) {
            if ($modelAR->$relationName) {
                $storageSpecification->deleteAfter([$relationName => $modelAR->$relationName]);
            }
        } else {

            /** @var ActiveRecord $relationAR */
            $relationAR = $modelAR->$relationName ?? $relationARCreateCallback();
            $relationAR->setAttributes($relationDomainModel->getAttributes(null, ['id']), false);

            $storageSpecification->saveBefore([$relationName => $relationAR]);
        }

        return $storageSpecification;
    }

    protected function arToDomain(ActiveRecord $modelAR, bool $isBulkRequest): DomainModel
    {
        $domainModel = $this->getDomainModelFactory()->createEmpty();
        $this->arToDomainAttributes($modelAR, $domainModel, $isBulkRequest);
        return $domainModel;
    }

    /**
     * Override in subclasses
     *
     * @param DomainModel $domainModel
     * @param ActiveRecord $modelAR
     * @param bool $isBulkRequest
     */
    protected function arToDomainAttributes(ActiveRecord $modelAR, DomainModel $domainModel, bool $isBulkRequest)
    {
        $domainModel->setAttributes($modelAR->attributes, false);
    }

    /**
     * @param DomainModel $domainModel
     * @return int|string
     */
    protected function id(DomainModel $domainModel)
    {
        return $domainModel->id;
    }

    /**
     * Do nothing by default, override if you need to process bulk requests
     * @see \lepota\data\BulkRequest
     */
    protected function afterBulkRequest()
    {
    }
}
