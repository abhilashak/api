<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Schema\SchemaManager;
use Directus\Exception\ErrorException;
use Directus\Util\ArrayUtils;

class PermissionsService extends AbstractService
{
    /**
     * @var string
     */
    protected $collection;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->collection = SchemaManager::COLLECTION_PERMISSIONS;
    }

    /**
     * @param array $data
     * @param array $params
     *
     * @return array
     */
    public function create(array $data, array $params = [])
    {
        $this->enforcePermissions($this->collection, $data, $params);
        $this->validatePayload($this->collection, null, $data, $params);

        $tableGateway = $this->getTableGateway();
        $newGroup = $tableGateway->updateRecord($data, $this->getCRUDParams($params));

        return $tableGateway->wrapData(
            $newGroup->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    public function find($id, array $params = [])
    {
        $params['id'] = $id;

        return $this->getItemsAndSetResponseCacheTags($this->getTableGateway(), $params);
    }

    public function findByIds($id, array $params = [])
    {
        return $this->getItemsByIdsAndSetResponseCacheTags($this->getTableGateway(), $id, $params);
    }

    public function update($id, array $data, array $params = [])
    {
        $this->enforcePermissions($this->collection, $data, $params);
        $this->validatePayload($this->collection, array_keys($data), $data, $params);

        $tableGateway = $this->getTableGateway();
        $data['id'] = $id;
        $newGroup = $tableGateway->updateRecord($data, $this->getCRUDParams($params));

        return $tableGateway->wrapData(
            $newGroup->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    public function delete($id, array $params = [])
    {
        $this->enforcePermissions($this->collection, [], $params);
        $this->validate(['id' => $id], $this->createConstraintFor($this->collection, ['id']));
        $tableGateway = $this->getTableGateway();
        $this->getItemsAndSetResponseCacheTags($tableGateway, [
            'id' => $id
        ]);

        $tableGateway->deleteRecord($id, $this->getCRUDParams($params));

        return true;
    }

    public function findAll(array $params = [])
    {
        return $this->getItemsAndSetResponseCacheTags($this->getTableGateway(), $params);
    }

    /**
     * @return \Directus\Database\TableGateway\RelationalTableGateway
     */
    protected function getTableGateway()
    {
        return $this->createTableGateway($this->collection);
    }
}
