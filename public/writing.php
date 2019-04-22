<?hh // strict
use namespace Facebook\Markdown;
use namespace HH\Lib\{C, Str, Vec};
require_once(__DIR__ . '/../vendor/hh_autoload.hh');
<<__EntryPoint>>
function writing(): void {
	echo PageRenderer::render(new \Writing());
}