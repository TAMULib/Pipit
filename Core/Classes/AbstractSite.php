<?php
namespace Core\Classes;
use Core\Interfaces as Interfaces;
use App\Classes\Data as AppData;
/** 
*	An abstract implementation of the Site interface
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractSite extends CoreObject implements Interfaces\Site {
	private $globalUser;
	private $siteConfig;
	private $viewRenderer;
	private $pages;
	private $inputData;
	protected $systemMessages;
	protected $currentPage;

	public function __construct(&$siteConfig,$pages) {
		$this->siteConfig = $siteConfig;
		$this->setPages($pages);
		$this->generateSanitizedInputData();
		$this->setUser();
	}

	public function getSiteConfig() {
		return $this->siteConfig;
	}

	protected function setUser() {
		//build the user
		if (isset($this->siteConfig['USECAS']) && $this->siteConfig['USECAS']) {
			$this->globalUser = new AppData\UserCAS();
			$casTicket = $this->getSanitizedInputData()['ticket'];
			if (!empty($casTicket)) {
				if ($this->globalUser->processLogIn($casTicket)) {
					header("Location:{$this->siteConfig['PATH_HTTP']}");
				}
			} elseif (!$this->getGlobalUser()->isLoggedIn() && !isset($this->getSanitizedInputData()['action'])) {
				$this->getGlobaluser()->initiateLogIn();
			}
		} else {
			$this->globalUser = new Data\User();
		}
	}

	public function setPages($pages) {
		$this->pages = $pages;
	}

	public function getPages() {
		return $this->pages;
	}

	public function setCurrentPage($page) {
		$this->currentPage = $page;
	}

	public function getCurrentPage() {
		return $this->currentPage;
	}

	public function setViewRenderer($viewRenderer) {
		$this->viewRenderer = $viewRenderer;
	}

	public function getViewRenderer() {
		return $this->viewRenderer;
	}

	abstract public function getControllerClass($controllerName);

	protected function generateSanitizedInputData() {
		if (!empty($_GET['action'])) {
			//restrict any controller actions that alter DB data to POST
			$restrictedActions = array("insert","remove","update");
			if (!in_array($_GET['action'],$restrictedActions)) {
				$data = $_GET;
			}
		} elseif (!empty($_POST['action'])) {
			$data = $_POST;
		} else {
			$data = $_REQUEST;
		}
		$this->sanitizedInputData = $data;
	}

	public function getSanitizedInputData() {
		return $this->sanitizedInputData;
	}

	public function getGlobalUser() {
		return $this->globalUser;
	}

	abstract public function addSystemMessage($message,$type="info");

	abstract public function getSystemMessages();

	abstract public function getDataRepository($repositoryName);
}