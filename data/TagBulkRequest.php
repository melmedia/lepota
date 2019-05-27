<?php
namespace lepota\data;

use stdClass;
use Functional;
use Yii;
use lepota\data\BulkRequest;
use lepota\domain\Tag;

/**
 * Bulk load tags from tag service (using navigationClient dependency)
 */
class TagBulkRequest extends BulkRequest
{

    public function loadEntities(array $ids): array
    {
        $tags = Functional\map(
            Yii::$app->navigationClient->getCollection('tag', ['id' => $ids], false)->tags,
            function (stdClass $tag): Tag {
                return Tag::createFromObject($tag);
            }
        );
        return array_combine(Functional\pluck($tags, 'id'), $tags);
    }

}
