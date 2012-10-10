<?php

namespace SlidesLive\SlidesLiveBundle\DependencyInjection;

class Privacy {

  const P_PUBLIC    = 1;
  const P_UNLISTED  = 2;
  const P_PRIVATE   = 3;

  public static function getValues() {
  	return array(P_PUBLIC, P_UNLISTED, P_PRIVATE);
  }

  public static function getChoices() {
  	return array (
		Privacy::P_PUBLIC => 'Public', 
		Privacy::P_UNLISTED => 'Unlisted',
		Privacy::P_PRIVATE => 'Private',
	);
  }

  const ACCOUNT       = 30;
  const FOLDER        = 20;
  const PRESENTATION  = 10;
}                                                                              