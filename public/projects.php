<?hh // strict
use namespace Facebook\Markdown;
use namespace HH\Lib\{C, Str, Vec};
require_once(__DIR__ . '/../vendor/hh_autoload.php');
<<__Entrypoint>>
function projects(): void {
	echo PageRenderer::render(new \Projects());
}