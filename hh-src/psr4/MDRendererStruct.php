<?hh // strict
namespace LamIO;
use namespace Facebook\Markdown;
type MDRendererStruct<T> = shape('pctx' => Markdown\ParserContext, 'rctx' => Markdown\RenderContext, 'renderer' => Markdown\Renderer<T>);