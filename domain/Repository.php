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

    public function getDomainARMapper()
    {
        return new DomainARMapper;
    }

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

        $domainModel->id = $storageSpecification->rootAR->id;
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
        $modelAR = $this->arObjects[self::id($domainModel)];
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
            $this->getService()->list($limit, $offset, $this->getFilterStatusForList(),
                $this->getExcludedStatusForList()),
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
        $storageSpecification = $this->getDomainARMapper()->domainToAR($domainModel, $modelAR);
        $storageSpecification->rootAR = $modelAR;
        return $storageSpecification;
    }

    protected function arToDomain(ActiveRecord $modelAR, bool $isBulkRequest): DomainModel
    {
        $domainModel = $this->getDomainModelFactory()->createEmpty();
        $this->getDomainARMapper()->arToDomain($modelAR, $domainModel, $isBulkRequest);
        return $domainModel;
    }

    /**
     * @param DomainModel $domainModel
     * @return int|string
     */
    protected static function id(DomainModel $domainModel)
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
