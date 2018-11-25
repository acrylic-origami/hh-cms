<?hh // strict
namespace LamIO\CMS;
use namespace Facebook\Markdown\Blocks;
use namespace HH\Lib\{C, Str, Vec};
function title_from_AST(Blocks\Document $block): ?Blocks\InlineSequenceBlock {
	$first_nonblank = C\find($block->getChildren(), ($child) ==> !($child instanceof Blocks\BlankLine));
	$title = null;
	if($first_nonblank instanceof Blocks\Heading && $first_nonblank->getLevel() === 1) {
		$title = new Blocks\InlineSequenceBlock($first_nonblank->getHeading());
	}

	return $title;
}