<?hh // strict
use LamIO\MDRendererStruct;
use namespace Facebook\Markdown;
use Facebook\Markdown\ASTNode;
class MarkdownRenderable implements \XHPUnsafeRenderable {
	public function __construct(
		protected MDRendererStruct<string> $renderer_struct, // TODO: this should only need to take renderer
		protected vec<Markdown\Blocks\Block> $md
	) {}
	public function toHTMLString(): string {
		return $this->renderer_struct['renderer']->render(new Markdown\Blocks\Document($this->md));
	}
}