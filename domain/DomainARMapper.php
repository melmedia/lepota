<?php
namespace lepota\domain;

use yii\db\ActiveRecord;

class DomainARMapper
{

    /**
     * @param DomainModel $domainModel
     * @param ActiveRecord $modelAR
     * @return StorageSpecification
     */
    public function domainToAR(DomainModel $domainModel, ActiveRecord $modelAR): StorageSpecification
    {
        $modelAR->setAttributes($domainModel->getAttributes(null, ['id']), false);
        return new StorageSpecification;
    }

    /**
     * @param DomainModel $domainModel
     * @param ActiveRecord $modelAR
     * @param bool $isBulkRequest
     */
    public function arToDomain(ActiveRecord $modelAR, DomainModel $domainModel, bool $isBulkRequest)
    {
        $domainModel->setAttributes($modelAR->attributes, false);
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
    )
    {
        /** @var DomainModel $relationDomainModel */
        $relationDomainModel = $domainModel->$relationName;

        $storageSpecification = new StorageSpecification;
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

}
