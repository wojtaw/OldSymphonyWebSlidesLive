<?php

namespace SlidesLive\SlidesLiveBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FolderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FolderRepository extends EntityRepository {
  
  public function findPresentationsSortedByDate($folderId) {
    $em = $this->getEntityManager();
    $query = $em->createQuery(
      'SELECT p
      FROM MetaBundle:Presentation p
      JOIN p.folder f
      WHERE f.id = :id
      ORDER BY p.dateRecorded DESC')
      ->setParameter('id', $folderId)
      ->getResult();
    if (count($query) < 1) {
      $query = null;
    }
    return $query;
  }
  
  /**
   * Nacteni pole folderu (razenych abecende) patricich vybranemu accountu podle
   * urovne ochrany soukromy folderu (public/unlisted).      
   */     
  public function findAccountFolders($accountId, $privacyLevel) {
    $em = $this->getEntityManager();
    $query = $em->createQuery(
      'SELECT f
      FROM SlidesLiveBundle:Folder f
      JOIN f.account a
      WHERE a.id = :accountId
      AND a.privacy <= :privacyLevel
      AND f.privacy <= :privacyLevel
      ORDER BY f.name ASC')
      ->setParameters(array(
          'accountId' => $accountId,
          'privacyLevel' => $privacyLevel
        )
      );
    return $query->getResult();        
  }

}