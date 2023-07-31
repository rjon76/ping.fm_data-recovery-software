<?php
/*
Plugin Name: Addon WP Super cache
Plugin URI: http://google.com
Description: Addon for plagin WP Super Cashe
Version: 2.0
Author: rjon76
Author URI: http://google.com
*/

/*  Copyright 2021  rjon76  (email: rjon76@gmail.com)*/


class wpMyCache {
	public $WP_CACHE_PASS = 'pa55w0rd';
	public $WP_CACHE_MASTER_PASS = 'cf-pa55w0rd';
	private $cacheHome;
	private $requestUri;
	private $lang = '';
	private $cacheFile;
	private $languages = [ 'en', 'ru', 'ar', 'nl', 'fr', 'de', 'it', 'ko', 'ms', 'pl', 'es', 'tr', 'cn', 'jp', 'se', 'pt' ];
	public $defaultLang = 'en';
	private $protocol = 'https';
	private $fvmCacheDir;

	public	function __construct() {
		if ( defined( 'WP_CACHE_PASS' ) ) {
			$this->WP_CACHE_PASS = WP_CACHE_PASS;
		}
		if ( defined( 'WP_CACHE_MASTER_PASS' ) ) {
			$this->WP_CACHE_MASTER_PASS = WP_CACHE_MASTER_PASS;
		}
	}

	/*----------------*/
	private	function myCacheProcess( $cacheFile ) {
		//echo sprintf('File: %s', $cacheFile."<br/>");
		//echo sprintf('Exists: %s', (int)file_exists($cacheFile)."<br/>");
		$result = false;
		if ( file_exists( $cacheFile ) ):
			if ( $content = file_get_contents( $cacheFile, FALSE ) ):
				echo $content;
				$result = true;
			endif;
		endif;
		return $result;
	}
	
	/*----------------*/

	private	function deleteDirectory( $dir ) {
		if ( !file_exists( $dir ) ) {
			return true;
		}

		if ( !is_dir( $dir ) ) {
			return unlink( $dir );
		}

		foreach ( scandir( $dir ) as $item ) {
			if ( $item == '.' || $item == '..' ) {
				continue;
			}

			if ( !$this->deleteDirectory( $dir . DIRECTORY_SEPARATOR . $item ) ) {
				return false;
			}

		}

		return rmdir( $dir );
	}
	
	/*----------------*/
	private function SureRemoveDir( $dir, $DeleteMe ) {
		
		if ( !$dh = @opendir( $dir ) ) return;
		while ( false !== ( $obj = readdir( $dh ) ) ) {
			if ( $obj == '.' || $obj == '..' ) {
				continue;
			}
			$file = $dir . DIRECTORY_SEPARATOR . $obj;
			//echo sprintf('Delete: %s', $file. "<br/>");
			if ( !@unlink( $file  ) ){
				$this->SureRemoveDir( $file , true );
			} 
		}

		closedir( $dh );
		if ( $DeleteMe ) {
			//echo sprintf('Delete: %s', $dir."<br/>");
			@rmdir( $dir );
		}
	}

	/*Delete all directory for default locale*/
	private function clearDefaultLangDir($dir){
		$fileArray = array();
		$cacheFile = $this->cacheHome . DIRECTORY_SEPARATOR . 'index-' . $this->protocol . '.html';
		$cacheFile = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $cacheFile);
		$this->myClearProcess( $cacheFile);
		
		
		if ( !$dh = @opendir( $dir ) ) return;
		while ( false !== ( $obj = readdir( $dh ) ) ) {
			if ( $obj == '.' || $obj == '..' ) continue;
			if (!in_array(strtolower($obj), $this->languages)){
				$obj = $dir . DIRECTORY_SEPARATOR . $obj ;
				$this->deleteDirectory( $obj);
			}
		}
		closedir( $dh );
	}
	
	/*----------------*/
	private function myClearProcess( $cacheFile ) {
		if(is_array($cacheFile)){
			foreach($cacheFile as $file){
				if ( file_exists( $file ) ){
					unlink( $file . '.gz' );
					unlink( $file );
                    continue;
					//echo sprintf('clearOnePage: %s', $file."<br/>");
				} //if (file_exists($cacheFile)):
                $fileWithoutProtocolPostfix = str_replace('index-' . $this->protocol, 'index', $file);
                if (file_exists( $fileWithoutProtocolPostfix )) {
                    unlink( $fileWithoutProtocolPostfix );
                }
			}
		}else{
			if ( file_exists( $cacheFile ) ){
				unlink( $cacheFile . '.gz' );
				unlink( $cacheFile );
				//echo sprintf('clearOnePage: %s', $cacheFile."<br/>");
			} //if (file_exists($cacheFile)):
		}
		

	}
	

	/*----------  Clear all pages ------------*/
	private function clearAllPages() {
		if ($this->lang == '' || $this->lang == 'all'){
			$this->SureRemoveDir( $this->cacheHome, false ); //delete supercache dir
			$this->SureRemoveDir( $this->fvmCacheDir, false ); //delete fvm dir
		}else{
			$languages = explode(',', $this->lang);
			$languages = array_intersect ( $languages, $this->languages);
			foreach ( $languages as $language ) {
				if($language !== $this->defaultLang){
					$cacheFile = $this->cacheHome . DIRECTORY_SEPARATOR . $language  ;
					$this->deleteDirectory($cacheFile );
					//$this->SureRemoveDir( $cacheFile, false );
				}else{
					$this->clearDefaultLangDir($this->cacheHome);
				}
			}
		}
	}

	/*----------  Clear one pages ------------*/
	private function clearOnePage($requestUri) {
		$fileArray = array();
		
		if ($this->lang == '' ){
			$languages = array();
			
		}elseif ($this->lang == 'all' ){
			$languages  = $this->languages;
		}else{
			$languages = explode(',', $this->lang);
			$languages = array_intersect ( $languages, $this->languages);
		}
		
		if(count($languages) > 0){

			$checkRequestUri = explode( DIRECTORY_SEPARATOR, $requestUri );

			if ( in_array( $checkRequestUri[ 1 ], $this->languages ) ) {
				unset($checkRequestUri[ 1 ]);
				$requestUri = implode(DIRECTORY_SEPARATOR, $checkRequestUri);

			}

			foreach ( $languages as $language ) {
				if($language == $this->defaultLang){
					$tmp = DIRECTORY_SEPARATOR . $requestUri;
				}else{
					$tmp = DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $requestUri;
				}
				

				$cacheFile = $this->cacheHome . str_replace( '/', DIRECTORY_SEPARATOR, $tmp ) . DIRECTORY_SEPARATOR . 'index-' . $this->protocol . '.html';
				$cacheFile = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $cacheFile );
				array_push($fileArray, $cacheFile);

			}
		}else{
			$cacheFile = $this->cacheHome . str_replace( '/', DIRECTORY_SEPARATOR, $requestUri ) . DIRECTORY_SEPARATOR . 'index-' . $this->protocol . '.html';
			$cacheFile = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $cacheFile);
			array_push($fileArray, $cacheFile);
		}
		
		$fileArray = array_unique($fileArray);
		
		$this->myClearProcess( $fileArray);
		
	}
	
	/*----------------*/	
	public function process(){
		
		if ( count( $_GET ) > 0 && !isset( $_GET[ 'clear' ] ) ){
			return;
		}


		$requestUri = parse_url( isset($_SERVER[ 'REQUEST_URI' ]) ? $_SERVER[ 'REQUEST_URI' ] : '', PHP_URL_PATH );


		if ( isset($_SERVER[ 'REQUEST_METHOD' ]) && $_SERVER[ 'REQUEST_METHOD' ] === 'GET' && $requestUri ){

			$this->protocol = ( !empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] !== 'off' || $_SERVER[ 'SERVER_PORT' ] == 443 ) ? "https" : "http";
	
			$dir = dirname( __FILE__ ) . '/../../';
			
			$this->fvmCacheDir = defined( 'WP_FVM_CACHE_DIR' ) ? WP_FVM_CACHE_DIR : $dir . 'cache' . DIRECTORY_SEPARATOR . 'fvm' . DIRECTORY_SEPARATOR. 'min'. DIRECTORY_SEPARATOR . $_SERVER[ 'HTTP_HOST' ]; //Second of cache live

			$this->cacheHome = defined( 'WP_CACHE_DIR' ) ? WP_CACHE_DIR : $dir . 'cache' . DIRECTORY_SEPARATOR . 'supercache' . DIRECTORY_SEPARATOR . $_SERVER[ 'HTTP_HOST' ]; //Second of cache live
			
			$cacheFile = $this->cacheHome . str_replace( '/', DIRECTORY_SEPARATOR, $requestUri ) . DIRECTORY_SEPARATOR . 'index-' . $this->protocol . '.html';
		
			$cacheFile = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $cacheFile);

			$clear = isset($_GET[ 'clear' ] ) ? strtoupper( $_GET[ 'clear' ] ) : '';
		
			$this->lang = isset($_GET[ 'langs' ]) ? strtolower( $_GET[ 'langs' ] ) : '';
			
			$userRole = ((isset($_GET[ 'pass' ]) && $_GET[ 'pass' ] == $this->WP_CACHE_PASS) || (isset($_GET[ 'masterpass' ]) && $_GET[ 'masterpass' ] == $this->WP_CACHE_MASTER_PASS))? true : false;
			$masterRole = (isset($_GET[ 'masterpass' ]) && $_GET[ 'masterpass' ] == $this->WP_CACHE_MASTER_PASS) ? true : false;
			
			if ( $userRole || $masterRole){
				switch ( $clear ){
					case 'ONE':
						$this->clearOnePage($requestUri);
					break;
				}
			}
			if ( isset($_GET[ 'masterpass' ]) && $_GET[ 'masterpass' ] == $this->WP_CACHE_MASTER_PASS){
				switch ( $clear ){
					case 'ALL':
						if ($masterRole){
							$this->clearAllPages();
						}
						
					break;
					case 'ONE':
						if($userRole){
							$this->clearOnePage($requestUri);
						}
						
					break;
				}
			}



			/* get cache  */
			$cacheFile = $this->cacheHome . str_replace( '/', DIRECTORY_SEPARATOR, $requestUri ) . DIRECTORY_SEPARATOR . 'index-' . $this->protocol . '.html';
			$cacheFile = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $cacheFile);
			$result = $this->myCacheProcess( $cacheFile );
			//echo sprintf('Step1 %s', (int)$result)."\r\n";
	
			if ( !$result ){
		
				/* Step 2. Check mobile version   */
				$cacheFile = $this->cacheHome . str_replace( '/', DIRECTORY_SEPARATOR, $requestUri ) . DIRECTORY_SEPARATOR . 'index.html';

				$cacheFile = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $cacheFile );

				$result = $this->myCacheProcess( $cacheFile );
		
				//echo sprintf('Step2 %s',(int)$result)."\r\n";
				if ( !$result ){
					/* Step 3. Check mobile version   */
					$cacheFile = $this->cacheHome . str_replace( '/', DIRECTORY_SEPARATOR, $requestUri ) . DIRECTORY_SEPARATOR . 'index-' . $this->protocol . '-mobile.html';

					$cacheFile = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $cacheFile );

					$result = $this->myCacheProcess( $cacheFile );
					//echo sprintf('Step3 %s',(int)$result)."\r\n";
					if ( !$result ){
						/* Step 3. Check mobile version   */
						$cacheFile = $this->cacheHome . str_replace( '/', DIRECTORY_SEPARATOR, $requestUri ) . DIRECTORY_SEPARATOR . 'index-mobile.html';

						$cacheFile = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $cacheFile );

						$result = $this->myCacheProcess( $cacheFile );
						//echo sprintf('Step4 %s',(int)$result)."\r\n";

					}
				}
			}
			if ( $result ){
				//echo('From cache');
				exit;
			}		
		}
	} //end process
} // end class wpMyCache


$wpMyCache = new wpMyCache;

$wpMyCache->process();
