<?php
namespace Pipit\Classes\Loaders;
use Pipit\Interfaces\Loader;
use Pipit\Interfaces\Controller;
use Pipit\Interfaces\ViewRenderer;
use Pipit\Interfaces\Site;
use Pipit\Classes\CoreObject;
use Pipit\Classes\Site\CoreSite;

/**
*	The CoreLoader is the default implementation of the Loader interface.
*
*	The CoreLoader is responsible for:
* 		Starting the session
*		Preparing global vars for controller use
*		Managing an implementation of the Site class
*		Using the Site class to get the logged in User
*		Using the Site class to load appropriate controllers and render views
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*
*/

class CoreLoader extends CoreObject implements Loader {
    /** @var mixed[] $config The app configuration */
    private $config;
    /** @var \Pipit\Interfaces\Site $site The Site context */
    private $site;

    /**
     * @param mixed[] $config The app configuration
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * @param mixed[] $config The app configuration
     * @return void
     */
    protected function setConfig($config) {
        $this->config = $config;
    }

    /**
     * @return mixed[]
     */
    protected function getConfig() {
        return $this->config;
    }

    /**
    *	Gets the Site context
    *	@return \Pipit\Interfaces\Site The active Site implementation
    */
    protected function getSite() {
        return $this->site;
    }

    /**
    *	Sets the Site context
    *	@param \Pipit\Interfaces\Site $site The active Site implementation
    *	@return void
    */
    protected function setSite(Site $site) {
        $this->site = $site;
    }

    /**
    *	load() is responsible for taking us from the request to the rendered response.
    *	- Kick off the seesion
    *	- Honor any $config redirect requests
    *	- Hand execution over to a Controller
    *	- Render the view with a ViewRenderer
    */
    public function load() {
        session_start();

        $this->loadSiteClass();
        $this->checkRedirect();
        $this->applyViewRenderer();

        $this->loadController();
        $this->render();

    }

    /**
    *	Check for and execute any $config requested redirects
    *	@return void
    */
    protected function checkRedirect() {
        if (is_string($this->getConfig()['forceRedirectUrl'])) {
            $this->getSite()->setRedirectUrl($this->getConfig()['forceRedirectUrl']);
            $this->getSite()->redirect();
        }
    }

    /**
    *	Looks for a configured Site implementation to utilize, falls back to CoreSite if none are found
    *	@return void
    */
    protected function loadSiteClass() {
        $site = null;
        $config = $this->getConfig();

        if (is_string($config['NAMESPACE_APP']) && is_string($config['SITE_CLASS'])) {
            $className = "{$config['NAMESPACE_APP']}Classes\\{$config['SITE_CLASS']}";
            $site = new $className($config);

            $potentialSiteClass = new $className($config);
            if ($potentialSiteClass instanceof Site) {
                $this->setSite($potentialSiteClass);
                unset($potentialSiteClass);
            }
            $this->getLogger()->debug("Loaded Configured Class: {$className}");
        }
        if (!($this->getSite() instanceof Site)) {
            $coreSite = new CoreSite($config);
            if ($coreSite instanceof Site) {
                $this->setSite($coreSite);
            }
            $this->getLogger()->debug("Loaded CoreSite Class");
        }
    }

    /**
    *	Finds an appropriate ViewRenderer and sets it up for use
    *	@return void
    */
    protected function applyViewRenderer() {
        //set the ViewRenderer
        $config = $this->getConfig();
        $inputData = $this->getSite()->getSanitizedInputData();
        $hasViewRenderer = false;
        if ($viewRendererName = $this->getViewRendererName()) {
            $potentialViewRenderer = new $viewRendererName(
                                            $this->getSite()->getGlobalUser(),
                                            $this->getSite()->getPages(),
                                            $inputData,
                                            (is_array($config['controllerConfig']) && array_key_exists('name', $config['controllerConfig']) ? $config['controllerConfig']['name']:null)
                                        );
            if ($potentialViewRenderer instanceof ViewRenderer) {
                $this->getSite()->setViewRenderer($potentialViewRenderer);
                unset($potentialViewRenderer);
                $hasViewRenderer = true;
            }
        }
        if (!$hasViewRenderer) {
            $this->getLogger()->error("ViewRenderer Class not found");
            exit;
        }
    }

    /**
    *	Provides the fully qualified class name of the ViewRenderer that should be used to render the response
    *	App level extenders of CoreLoader can override this method to use their own criteria to select the ViewRenderer
    *	@return string|null $viewRendererName - The fully qualified class name of the ViewRenderer to be used to render the response
    */
    protected function getViewRendererName() {
        $config = $this->getConfig();
        $inputData = $this->getSite()->getSanitizedInputData();
        $viewRendererName = null;

        $availableCoreRenderers = array("json","csv","html");

        $viewRenderOverride = null;
        if (is_string($config['NAMESPACE_CORE']) && is_string($config['NAMESPACE_APP'])) {
            //legacy support for original GET request of JSONViewRenderer
            if (!empty($inputData['json'])) {
                $viewRenderOverride = "JSONViewRenderer";
            } else if (!empty($inputData['view_renderer']) && in_array($inputData['view_renderer'],$availableCoreRenderers) && is_string($inputData['view_renderer'])) {
                $viewRenderOverride = strtoupper($inputData['view_renderer'])."ViewRenderer";
            }
            if ($viewRenderOverride) {
                $viewRendererName = "{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\{$viewRenderOverride}";
            } else if (is_string($config['VIEW_RENDERER'])) {
                if (class_exists("{$config['NAMESPACE_APP']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}")) {
                    $viewRendererName = "{$config['NAMESPACE_APP']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}";
                } elseif (class_exists("{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}")) {
                    $viewRendererName = "{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}";
                }
            } else {
                $viewRendererName = "{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\HTMLViewRenderer";
            }
        }
        return $viewRendererName;
    }

    /**
    *	Looks for the configured Controller and hands over control by executing its evaluate() method
    *	@return void
    */
    protected function loadController() {
        //try to load the controller
        $config = $this->getConfig();
        $controller = null;
        if (is_array($config['controllerConfig']) && is_string($config['controllerConfig']['name'])) {
            $className = $this->getSite()->getControllerClass($config['controllerConfig']['name']);
            if (class_exists($className)) {
                $site = $this->getSite();
                $controller = new $className($site, $config['controllerConfig']);
                if ($controller instanceof Controller) {
                    $controller->evaluate();
                } else {
                    $controller = null;
                }
            }
        }
        if (!$controller) {
            $this->getLogger()->warn("Did not find Controller Class");
            if (is_string($config['PATH_HTTP'])) {
                $this->getSite()->setRedirectUrl($config['PATH_HTTP']);
            } else {
                exit;
            }
        }
    }

    /**
    *	Asks the ViewRenderer to render the response
    *	@return void
    */
    protected function render() {
        if ($this->getSite()->hasRedirectUrl()) {
            $this->getSite()->redirect();
        }
        //send system messages to the ViewRenderer
        $this->getSite()->getViewRenderer()->registerAppContextProperty("systemMessages", $this->getSite()->getSystemMessages());

        //display the content
        $this->getSite()->getViewRenderer()->renderView();
    }
}
