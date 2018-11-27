<?hh // strict
namespace LamIO;
use namespace Facebook\Markdown;
function default_renderer_factory(): MDRendererStruct<string> {
	$pctx = new Markdown\ParserContext();
	$pctx->enableHTML_UNSAFE();
	$pctx->getBlockContext()->prependBlockTypes(UnparsedBlocks\Aside::class);
	$rctx = new Markdown\RenderContext();
	$rctx->disableNamedExtension('TagFilter');
	$renderer = new HTMLRenderer($rctx);
	return shape('pctx' => $pctx, 'rctx' => $rctx, 'renderer' => $renderer);
}