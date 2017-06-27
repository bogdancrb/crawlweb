<?php

namespace AppBundle\Repository;

/**
 * TemplateElementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TemplateElementRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * @param $templateId
	 * @return array
	 */
	public function findByTemplateId($templateId)
	{
		$query = $this->createQueryBuilder('te')
			->select('te.id')
			->where('te.template = ?1')
			->setParameter(1, $templateId)
			->getQuery();

		$result = $query->getArrayResult();

		$query->free();
		unset($query);

		return $result;
	}

	/**
	 * @param $templateElementIds
	 * @return array
	 */
	public function findByIds($templateElementIds)
	{
		$queryBuilder = $this->createQueryBuilder('te');

		$query = $queryBuilder
			->select('te')
			->andWhere($queryBuilder->expr()->in('te.id', ':templateElementIds'))
			->setParameter('templateElementIds', $templateElementIds)
			->getQuery();

		$result = $query->getResult();

		$query->free();
		unset($query);

		return $result;
	}

	/**
	 * @return array
	 */
	public function getTemplateElementNames()
	{
		$query = $this->createQueryBuilder('te')
			->select('te.name')
			->groupBy('te.name')
			->getQuery();

		$result = $query->getArrayResult();

		$query->free();
		unset($query);

		return $result;
	}
}
