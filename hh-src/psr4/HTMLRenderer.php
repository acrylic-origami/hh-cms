<?hh // strict
namespace LamIO;
use namespace HH\Lib\{C, Str, Vec};
use Facebook\Markdown\ASTNode;
class HTMLRenderer extends \Facebook\Markdown\HTMLRenderer {
	<<__Override>>
	protected function renderResolvedNode(ASTNode $node): string {
		if($node instanceof Blocks\Aside) {
			return $this->renderAside($node);
		}
		return parent::renderResolvedNode($node);
	}
	protected function renderAside(Blocks\Aside $node): string {
		return $node->getChildren()
			|> $this->renderNodes($$)
			|> "<aside>\n".$$."</aside>\n";
	}
	
}