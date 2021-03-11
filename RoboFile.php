<?php

include '.tk/RoboFileBase.php';

class RoboFile extends RoboFileBase {
	public function directoriesStructure() {
		return array( 'assets', 'classes' );
	}

	public function fileStructure() {
		return array( 'integration.php', 'composer.json', 'license.txt', 'readme.txt' );
	}

	public function cleanPhpDirectories() {
		return array( 'classes/includes/resources/tgm' );
	}

	public function pluginMainFile() {
		return 'integration';
	}

	public function pluginFreemiusId() {
		return 413;
	}

	public function minifyAssetsDirectories() {
		return array( 'assets' );
	}

	public function minifyImagesDirectories() {
		return array();
	}

	/**
	 * @return array Pair list of sass source directory and css target directory
	 */
	public function sassSourceTarget() {
		return array( array( 'scss/source' => 'assets/css' ) );
	}

	/**
	 * @return string Relative paths from the root folder of the plugin
	 */
	public function sassLibraryDirectory() {
		return 'scss/library';
	}
}