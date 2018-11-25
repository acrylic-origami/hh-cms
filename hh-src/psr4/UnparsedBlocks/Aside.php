<?hh // strict
namespace LamIO\UnparsedBlocks;
use type LamIO\Blocks\Aside as ASTNode;
use namespace \Facebook\Markdown\UnparsedBlocks;
use namespace \Facebook\Markdown\UnparsedBlocks\_Private;
use namespace \Facebook\Markdown\Inlines;
use namespace HH\Lib\{C, Str, Vec};

class Aside extends UnparsedBlocks\ContainerBlock<UnparsedBlocks\Block> implements UnparsedBlocks\BlockProducer {
  public static function consume(
    UnparsedBlocks\Context $context,
    UnparsedBlocks\Lines $lines,
  ): ?(Aside, UnparsedBlocks\Lines) {
    $contents = vec[];
    $parsed = null;

    while (!$lines->isEmpty()) {
      $chunk = self::consumePrefixedChunk($context, $lines);
      if ($chunk === null) {
        break;
      }

      list($chunk, $lines) = $chunk;
      $contents = Vec\concat($contents, $chunk);
      $parsed = self::consumeChildren($context, new UnparsedBlocks\Lines($contents));

      if (!self::endsWithParagraph(C\lastx($parsed))) {
        break;
      }

      $chunk = self::consumeLazyChunk($context, $lines);
      if ($chunk === null) {
        break;
      }

      list($chunk, $lines) = $chunk;
      $contents = Vec\concat($contents, $chunk);
      $parsed = null;
    }


    if (C\is_empty($contents)) {
      return null;
    }

    if ($parsed === null) {
      $parsed = self::consumeChildren($context, new UnparsedBlocks\Lines($contents));
    }

    return tuple(new self($parsed), $lines);
  }

  protected static function endsWithParagraph(
    UnparsedBlocks\Block $block,
  ): bool {
    if ($block instanceof UnparsedBlocks\Paragraph) {
      return true;
    }
    if ($block instanceof UnparsedBlocks\ContainerBlock) {
      $last = C\lastx($block->getChildren());
      return self::endsWithParagraph($last);
    }
    return false;
  }

  protected static function consumeLazyChunk(
    UnparsedBlocks\Context $context,
    UnparsedBlocks\Lines $lines,
  ): ?(vec<(int, string)>, UnparsedBlocks\Lines) {
    $contents = vec[];
    while (!$lines->isEmpty()) {
      if (!_Private\is_paragraph_continuation_text($context, $lines)) {
        break;
      }
      list($col, $line, $lines) = $lines->getColumnFirstLineAndRest();
      $contents[] = tuple($col, $line);
    }

    if (C\is_empty($contents)) {
      return null;
    }

    return tuple($contents, $lines);
  }

  protected static function consumePrefixedChunk(
    UnparsedBlocks\Context $_context,
    UnparsedBlocks\Lines $lines,
  ): ?(vec<(int, string)>, UnparsedBlocks\Lines) {
    $contents = vec[];
    while (!$lines->isEmpty()) {
      list($col, $line, $rest) = $lines->getColumnFirstLineAndRest();
      list($_, $line, $n) = UnparsedBlocks\Lines::stripUpToNLeadingWhitespace(
        $line,
        3,
        $col,
      );
      $col = $col + $n;

      $offset = null;
      $prefixes = vec['A>', '|'];
      $any_flag = false;
      foreach($prefixes as $prefix) {
        if(Str\starts_with($line, $prefix)) {
          $any_flag = true;
          if (Str\starts_with($line, $prefix.' ')) {
            $line = Str\slice($line, \strlen($prefix) + 1);
            $col += \strlen($prefix) + 1;
          } else if (Str\starts_with($line, $prefix."\t")) {
            $col += \strlen($prefix);
            $tab_width = 4 - ($col % 4);
            $col += 1; // \t

            $line = Str\slice($line, \strlen($prefix) + 1);

            if ($tab_width === 0) {
              $tab_width = 4;
            }
            $line = Str\repeat(' ', $tab_width - 1).$line;
          } else {
            $line = Str\slice($line, \strlen($prefix));
            $col += \strlen($prefix);
          }
        }
      }
      if(!$any_flag)
          break;

      $contents[] = tuple($col, $line);
      $lines = $rest;
    }

    if (C\is_empty($contents)) {
      return null;
    }
    return tuple($contents, $lines);
  }

  <<__Override>>
  public function withParsedInlines(Inlines\Context $ctx): ASTNode {
    return new ASTNode(
      Vec\map(
        $this->children,
        $child ==> $child->withParsedInlines($ctx),
      ),
    );
  }
}
