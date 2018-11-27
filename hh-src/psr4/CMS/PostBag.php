<?hh // strict
namespace LamIO\CMS;
use Facebook\Markdown;
type PostBag = shape(
	"location" => string,
	"base" => string,
	"content" => Markdown\Blocks\Document,
	"ctime" => int,
	"mtime" => int,
	"hero" => ?string,
	"thumb" => ?string,
	"meta" => ?dict<string, mixed>
);