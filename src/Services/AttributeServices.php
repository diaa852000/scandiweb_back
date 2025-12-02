<?php
namespace App\Services;

use App\Entities\Attribute;
use App\Entities\AttributeItem;
use App\Repository\AttributeRepository;
use App\Repository\AttributeItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Error\UserError;
use App\Services\BaseServices;

class AttributeServices extends BaseServices
{
    public function __construct(
        private AttributeRepository $attrRepo,
        private AttributeItemRepository $itemRepo,
        private EntityManagerInterface $em
    ) {
    }

    public function upsertAttributeWithItemsNoFlush(string $id, string $name, string $type, array $items): Attribute
    {
        $attr = $this->attrRepo->find($id);
        if (!$attr) {
            $attr = new Attribute();
            $attr->id = $id;
        }

        $attr->name = $name;
        $attr->type = $type;
        $this->em->persist($attr);

        $existing = $this->itemRepo->findByAttribute($attr);
        $byId = [];
        foreach ($existing as $e) {
            $byId[$e->id] = $e;
        }

        $seen = [];
        foreach ($items as $i) {
            foreach (['id', 'value', 'displayValue'] as $k) {
                if (!\array_key_exists($k, $i)) {
                    throw new UserError("AttributeItem for '{$id}' missing key '{$k}'");
                }
            }

            $seen[$i['id']] = true;

            if (isset($byId[$i['id']])) {
                $it = $byId[$i['id']];
                $it->value = $i['value'];
                $it->displayValue = $i['displayValue'];
            } else {
                $it = new AttributeItem();
                $it->id = $i['id'];
                $it->value = $i['value'];
                $it->displayValue = $i['displayValue'];
                $it->attribute = $attr;
                $this->em->persist($it);
            }

            if (!$attr->items->contains($it)) {
                $attr->items->add($it);
            }
        }

        foreach ($byId as $leftId => $leftover) {
            if (!isset($seen[$leftId])) {
                $this->em->remove($leftover);
            }
        }

        return $attr;
    }

    public function create(array $data): object
    {
        $attr = new Attribute();
        $attr->id = $data['id'];
        $attr->name = $data['name'];
        $attr->value = $data['value'];
        $attr->displayValue = $data['displayValue'];
        $attr->items = new \Doctrine\Common\Collections\ArrayCollection();

        return $this->attrRepo->create($attr);
    }

    public function update(int|string $id, array $data): object
    {
        $entity = $this->attrRepo->findById($id);
        if (!$entity) {
            throw new \Exception("Entity with ID {$id} not found for update");
        }
        if (isset($data['name'])) {
            $entity->name = $data['name'];
        }
        if (isset($data['type'])) {
            $entity->type = $data['type'];
        }

        return $this->attrRepo->update($entity);
    }
    public function delete(int|string $id): bool
    {
        $entity = $this->attrRepo->findById($id);
        if (!$entity) {
            throw new \Exception("Entity with ID {$id} not found for deletion");
        }
        return $this->attrRepo->delete($entity);
    }
    public function findAll(): array
    {
        return $this->attrRepo->findAll();
    }

    public function findById(int|string $id): object|null
    {
        return $this->attrRepo->findById($id);
    }
}
