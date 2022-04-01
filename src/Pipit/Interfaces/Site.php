<?php
namespace Pipit\Interfaces;
/**
*	An interface defining a Site
*	Sites provide a single, unified application context throughout the execution of the application, providing state information to components, and allowing them to alter the flow of the application without
*	direct knowledge of the inner workings of the other components.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Site {
	/**
	*	Set the ViewRenderer.
	*
	*	@param ViewRenderer $viewRenderer
	*	@return void
	*/
	public function setViewRenderer($viewRenderer);

	/**
	*	Get the ViewRenderer. Components like Controllers can access this method to configure the set ViewRenderer.
	*
	*	@return ViewRenderer The currently set ViewRenderer
	*/
	public function getViewRenderer();

	/**
	*	Define a redirect URL.
	*
	*	@param string $redirectUrl
	*	@return void
	*/
	public function setRedirectUrl($redirectUrl);

	/**
	 * @return boolean
	 */
	public function hasRedirectUrl();

	/**
	 * @return void
	 */
	public function redirect();

	/**
	*	Get the pages defined for the Site.
	*
	*	@return SitePage[]
	*/
	public function getPages();

	/**
	*	Gets the currently requested page
	*	@return \Pipit\Interfaces\SitePage The currently requested SitePage
	*/
	public function getCurrentPage();

	/**
	*	Get a representation of the application user associated with a request.
	*
	*	@return \Pipit\Interfaces\User
	*/
	public function getGlobalUser();

	/**
	*	Get the Controller class associated with the given name
	*
	*	@param string $controllerName
	*	@return string $controllerClass An instantiable string representation of the Controller class name
	*/
	public function getControllerClass($controllerName);

	/**
	*	Provide a sanitized and secured version of user input data (POST,GET,COOKIE,etc) to components
	*	All components should access user input data exclusively through this method.
	*
	*	@return mixed[]
	*/
	public function getSanitizedInputData();

	/**
	*	Push a new message string to the Site's system messages
	*
	*	@param string $message The message
	*	@param string $type The type of the message
	*	@return void
	*/
	public function addSystemMessage($message,$type);

	/**
	*	Get the Site's SystemMessages
	*
	*	@return SystemMessage[]
	*/
	public function getSystemMessages();

	/**
	*	Returns the site configuration
	*	@return mixed[] $siteConfig
	*/
	public function getSiteConfig();
}
