<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SlidesLive\SlidesLiveBundle\Entity\Account;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;
use SlidesLive\SlidesLiveBundle\Form\AccountEditForm;
use SlidesLive\SlidesLiveBundle\Form\PresentationEditForm;
use SlidesLive\SlidesLiveBundle\Form\UploadForm;
use SlidesLive\SlidesLiveBundle\Form\BackgroundUploadForm;
use SlidesLive\SlidesLiveBundle\Form\LogoUploadForm;
use SlidesLive\SlidesLiveBundle\Form\AvatarUploadForm;

class AdminController extends Controller {

    protected $data = array();                                         
                                                  
    public function indexAction() {
    	$this->data['accounts'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Account')->findBy(array(), array('name' => 'ASC'));
    	return $this->render('SlidesLiveBundle:Admin:index.html.twig', $this->data);
    }

    public function editAccountAction($accountId) {
    	$account = $this->getDoctrine()->getRepository('SlidesLiveBundle:Account')->find($accountId);
    	if (!$account) {
    		return $this->render('SlidesLiveBundle:Admin:editAccount.html.twig', array('accountNotFound' => true));
    	}

    	$this->data['account'] = $account;
    	$this->data['accountEditForm'] = $this->forward('SlidesLiveBundle:Account:accountEditForm', array('account' => $account));
        $this->data['uploadBackgroundForm'] = $this->forward('SlidesLiveBundle:Account:uploadImage', array('account' => $account, 'type' => 'background-images', 'formClass' => new BackgroundUploadForm()));
        $this->data['uploadLogoForm']       = $this->forward('SlidesLiveBundle:Account:uploadImage', array('account' => $account, 'type' => 'logos', 'formClass' => new LogoUploadForm()));
        $this->data['uploadAvatarForm']     = $this->forward('SlidesLiveBundle:Account:uploadImage', array('account' => $account, 'type' => 'avatars', 'formClass' => new AvatarUploadForm()));
    	return $this->render('SlidesLiveBundle:Admin:editAccount.html.twig', $this->data);
    }

    public function resetPasswordAction($accountId) {
    	$em = $this->getDoctrine()->getEntityManager();
    	$account = $em->getRepository('SlidesLiveBundle:Account')->find($accountId);
    	$this->data['accountId'] = $accountId;
    	if ($account) {
    		$this->data['account'] = $account;
    		$this->data['password'] = substr(md5(microtime()), 0, 12);
            $account->setSalt(md5(rand()));
    		$encoder = $this->get('security.encoder_factory')->getEncoder($account);
    		$account->setPassword($encoder->encodePassword($this->data['password'], $account->getSalt()));
    		$em->flush();
    	}
    	return $this->render('SlidesLiveBundle:Admin:resetPassword.html.twig', $this->data);
    }

    // -------------------------------------------------------------------------------

    public function initializationAction() {
    	$password = '983tjr98jnre';

    	echo "<pre>\n===Administration initialization===\n";
    	$em = $this->getDoctrine()->getEntityManager();
    	$admin = $em->getRepository('SlidesLiveBundle:Account')->findByName('Administration');
    	if (count($admin) == 0) {	// administrator jiz existuje
    		echo "Creating new Administration Account ...\n";
    		$account = new Account();   	
	    	$account->setName('Administration');
	    	$account->canonizeName();
	    	$account->setUserName('administration@gmail.com');
	    	$account->setPrivacy(Privacy::P_PRIVATE);
	    	$account->setSalt(md5(microtime()));
	    	$account->setIsActive(true);
	    	$account->setRole('ROLE_ADMIN');
	    	$encoder = $this->get('security.encoder_factory')->getEncoder($account);
	    	$account->setPassword($encoder->encodePassword($password, $account->getSalt()));
	    	$account->setHash('');
	    	$account->setIsMeta(false);
	    	$em->persist($account);
	    	$em->flush();
	    	$admin = array($account);
    	}
    	else {
    		echo "Administration Account already exists\n";
    	}
    	foreach($admin as $a) {
    		echo "\nAdministrator:\n";
    		echo "\tname:\t\t".$a->getName()."\n";
    		echo "\tcanonicalName:\t".$a->getcanonicalName()."\n";
    		echo "\tusername:\t".$a->getUsername()."\n";
    		echo "\tprivacy:\t".$a->getPrivacy()."\n";
    		echo "\trole:\t\t".$a->getRole()."\n";
    	}
    	echo "\n===Initialization complete===\n";
    	return new Response('');
    }

    public function phpInfoAction() {
        return $this->render('SlidesLiveBundle:Admin:phpInfo.html.php');
    }
                     
}
