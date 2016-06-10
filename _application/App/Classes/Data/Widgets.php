<?php
namespace App\Classes\Data;
use Core\Classes\Data as CoreData;
/** 
*	Repo for managing Widgets
*	Intended as a starting point for developing application specific DAOs
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class Widgets extends CoreData\AbstractDataBaseRepository {
	public function __construct() {
		parent::__construct('widgets','id','name');
	}
}