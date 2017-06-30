<?php

namespace AppBundle\Repository;

/**
 * AttributesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AttributesRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * @param $contentId
	 * @param $templateElementId
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByContentIdAndTemplateElementId($contentId, $templateElementId)
	{
		$query = $this->createQueryBuilder('a')
			->select('a')
			->where('a.content = :contentId')
			->andWhere('a.templateElement = :templateElementId')
			->setParameters([
				'contentId'			=> $contentId,
				'templateElementId' => $templateElementId
			])
			->getQuery();

		$result = $query->getOneOrNullResult();

		$query->free();
		unset($query);

		return $result;
	}

	/**
	 * @param array $args
	 * @return array
	 * @throws \Doctrine\DBAL\DBALException
	 * @internal param $searchTerm
	 */
	public function searchAttributesFor($args = [])
	{
		// Extract all values from array and created variables
		extract($args, EXTR_SKIP);

		$query = "
					SELECT 	
						t.name AS categoryName, 
						s.name AS siteName, 
						co.url as url, 
						co.id as contentId 
					FROM attributes AS a 
						INNER JOIN template_element AS te 
				  			ON a.template_element_id = te.id 
				  		INNER JOIN template AS t 
				  			ON te.template_id = t.id 
				  		INNER JOIN sites AS s 
				  			ON t.sites_id = s.id 
				  		INNER JOIN content AS co 
				  			ON a.content_id = co.id ";

		$parametersArr = [];

		if (!empty($searchTerm))
		{
			$query .= "WHERE CONVERT(a.value USING utf8) LIKE :searchTerm ";

			if (!empty($searchByTemplateElement))
			{
				$query .= "AND te.name LIKE :searchByTemplateElement ";
				$parametersArr['searchByTemplateElement'] = '%'. $searchByTemplateElement .'%';
			}

			$parametersArr['searchTerm'] = '%'. $searchTerm . '%';
		}

		$query .= "GROUP BY co.id, t.name ORDER BY s.id ASC";

		$stmt = $this->getEntityManager()->getConnection()->prepare($query);
		$stmt->execute($parametersArr);

		$result = $stmt->fetchAll();

		unset($stmt);

		return $result;
	}

	/**
	 * @param $contentId
	 * @return array
	 */
	public function findByContentId($contentId)
	{
		$query = $this->createQueryBuilder('a')
			->select('a.id', 'a.value', 'ate.name as template_element_name', 'ac.id as content_id')
			->innerJoin('a.templateElement', 'ate')
			->innerJoin('a.content', 'ac')
			->where('a.content = :contentId')
			->setParameter('contentId', $contentId)
			->getQuery();

		$result = $query->getArrayResult();

		$result = $this->formatContentResultArray($contentId, $result);

		$query->free();
		unset($query);

		return $result;
	}

	/**
	 * Group the content result after content id and template elements
	 *
	 * @param $contentId
	 * @param $result
	 * @return array
	 */
	private function formatContentResultArray($contentId, $result)
	{
		$retResult = [];

		// Because attribute values are BLOB (thus a resource) we need to convert it into a string
		array_walk($result, function($elem, $elemKey) use (&$result){
			array_walk($elem, function($value, $key) use(&$result, &$elem, $elemKey) {
				// For the value key
				if ($key == 'value' && is_resource($elem[$key]))
				{
					// Convert the resource to a string
					$result[$elemKey][$key] = strip_tags(fread($value, 1000000));
				}

				// If the key is id
				if ($key == 'id')
				{
					// Remove it from the result
					unset($result[$elemKey][$key]);
				}
			});
		});

		// Format the array structure after content id and template element
		foreach ($result as $elem)
		{
			$value = $elem['value'];
			$templateElementName = $elem['template_element_name'];

			// In case no other values were saved, it means that we have a single value
			if (empty($retResult[$contentId][$templateElementName]))
			{
				$retResult[$contentId][$templateElementName] = $value;
			}
			// If other values were set for the template element, it means that it's an array
			else
			{
				// Save the value to the array
				$val[] = $value;

				// Save array into the results
				$retResult[$contentId][$templateElementName] = $val;
			}
		}

		// Format single array result as string
		foreach ($retResult as $keyElem => $elem)
		{
			foreach ($elem as $key => $value)
			{
				// In case the value is an array and only has an element
				if(is_array($value) && sizeof($value) == 1)
				{
					// Convert it into a string
					$retResult[$keyElem][$key] = implode(' ', $value);
				}
			}
		}

		return $retResult;
	}
}
