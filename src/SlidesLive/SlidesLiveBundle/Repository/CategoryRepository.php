<?php

namespace SlidesLive\SlidesLiveBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository {
	
	public function listAllCategories() {
		$em = $this->getEntityManager();
		$query = $em->createQuery(
			'SELECT b
			FROM SlidesLiveBundle:Category b'
		);
		$recievedCategories = $query->getResult();
		return $recievedCategories;
	}
	
	public function findPresentations() {
		$em = $this->getEntityManager();
		$query = $em->createQuery(
			'SELECT b
			FROM SlidesLiveBundle:Category b'
		);
		$recievedCategories = $query->getResult();
		return $recievedCategories;
	}			

}