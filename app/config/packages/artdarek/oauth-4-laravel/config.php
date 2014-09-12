<?php 

return array( 
	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------

	*/
	/**
	 * Storage
	 */
	
	'storage' => 'Session', 

	/**
	 * Consumers
	 */
	'consumers' => array(
		
		/**
		 * Facebook
		 */
        'Facebook' => array(
            'client_id'     => '330790510432926',
            'client_secret' => '2aaba767b2d56c986fec562b32383466',
            'scope'         => array('email','read_friendlists','user_online_presence'),
        ),
	)
);