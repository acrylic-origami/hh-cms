<?hh // strict
namespace LamIO\CMS;
use namespace Facebook\Markdown\Blocks;
use namespace HH\Lib\{C, Str, Vec};
function plaintext_title_from_AST(Blocks\Document $block): string {
	return title_from_AST($block)?->getChildren() ?? []
		|> C\reduce($$, ($acc, $inline) ==> $acc.$inline->getContentAsPlainText(), '');
}