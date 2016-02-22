<?php
namespace TAMU\Seed\Classes\Data;
use TAMU\Seed\Interfaces as Interfaces;
/** 
*	Repo for managing Widgets
*	Intended as a starting point for developing application specific DAOs
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class Widgets extends AbstractDataBaseRepository {
	public function __construct() {
		parent::__construct('widgets','id','name');
	}
}