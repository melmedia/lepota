<?php
namespace lepota\domain;

use stdClass;
use Functional;
use Yii;

/**
 * List of Tag objects that can be converted to and from IDs list, objects.
 */
class TagList
{
    /** @var int[] */
    protected $tagIds;
    /** @var stdClass */
    protected $tags;

    /**
     * @param array|null $tagIds
     * @param array|null $tags
     */
    protected function __construct($tagIds, $tags)
    {
        if (null !== $tagIds) {
            $this->tagIds = Functional\map($tagIds, function ($id): int { return (int) $id; });
        }
        $this->tags = $tags;
    }

    public static function createEmpty()
    {
        return new TagList([], []);
    }

    /**
     * Load tags information from IDs list from navigation service
     * @param int[] $tagIds
     * @return TagList
     */
    public static function createFromIds($tagIds)
    {
        return new self($tagIds, null);
    }

    /**
     * Create from array of hashes (from http request)
     * @param array $tags
     * @return TagList
     */
    public static function createFromArray($tags)
    {
        return new self(
            null,
            Functional\map(
                $tags,
                function ($tag) { return (object) $tag; }
            )
        );
    }

    /**
     * @param string $entityType
     * @param int $entityId
     * @param int $exceptId
     * @return TagList
     */
    public static function loadForEntity(string $entityType, int $entityId, int $exceptId = null): TagList
    {
        $tags = Yii::$app->navigationClient->get("$entityType/$entityId/tag", ['tagType' => 'tag'])->tags;
        if ($exceptId) {
            $tags = array_values(Functional\filter(
                $tags,
                function (stdClass $tag) use ($exceptId): bool {
                    return $tag->id != $exceptId;
                }
            ));
        }
        return new TagList(null, $tags);
    }

    /**
     * @return int[]
     */
    public function getIds()
    {
        if (null === $this->tagIds) {
            $this->tagIds = Functional\map(
                Functional\pluck($this->tags, 'id'),
                function ($id): int { return (int) $id; }
            );
        }
        return $this->tagIds;
    }

    /**
     * Warning: lazy loading
     * @return stdClass[] Objects with attributes id, name, address
     */
    public function getTags()
    {
        if (null === $this->tags) {
            $this->tags = Yii::$app->navigationClient->getCollection('tag',
                ['id' => $this->tagIds], false)->tags;
        }
        return $this->tags;
    }

}
