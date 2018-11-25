<?hh // strict
require_once(__DIR__ . '/../../vendor/hh_autoload.php');
<<__Entrypoint>>
function shadow_project(): void {
	/* HH_IGNORE_ERROR[2050] */
	$path = __DIR__ . '/../../public/project_assets' . substr($_SERVER['REQUEST_URI'], strlen('/projects'));
	if(is_dir($path)) {
		$posts = LamIO\CMS\content_iterator(LamIO\default_renderer_factory(), $path);
		if(count($posts) === 0)
			http_response_code(404);
		else {
			// TODO: disambiguation page for multiple markdown articles found
			// TODO: plus, in general revise the class structure and particularly the ownership and passing of the renderer
			// var_dump(__DIR__ . '/../../public/project_assets' . substr($_SERVER['REQUEST_URI'], strlen('/projects')));
			echo PageRenderer::render(new \ShadowProject($posts[0]));
		}
	}
	else
		http_response_code(404);
}