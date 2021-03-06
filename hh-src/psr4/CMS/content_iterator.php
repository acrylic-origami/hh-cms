<?hh // strict
namespace LamIO\CMS;
use type LamIO\MDRendererStruct;
use namespace Facebook\Markdown;
use namespace Facebook\Markdown\{Blocks, Inlines};
use namespace HH\Lib\{C, Str, Vec, Dict};
function content_iterator(MDRendererStruct<string> $renderer_struct, string $root_path, string $path = ''): vec<PostBag> {
	$base = "{$root_path}/{$path}";
	return vec(new \DirectoryIterator($base))
		|> C\reduce($$, ($acc, $v) ==> {
			if($v->isDir() && !$v->isDot()) {
				return content_iterator($renderer_struct, $root_path, "{$path}/{$v->getFilename()}")
					|> Vec\concat($acc, $$);
			}
			elseif(\strtolower($v->getExtension()) === 'md') {
				$basename = $v->getBasename('.md');
				$basepath = "{$path}/{$basename}";
				$ast_fullpath = "{$root_path}/{$basepath}.ast";
				if(\file_exists($ast_fullpath) && \filemtime($ast_fullpath) > \filemtime($v->getPathname())) {
					$ast = \unserialize(\file_get_contents($ast_fullpath)) as Markdown\Blocks\Document;
				}
				else {
					$ast = Markdown\parse($renderer_struct['pctx'], \file_get_contents($v->getPathname()));
					\file_put_contents($ast_fullpath, \serialize($ast));
				}
				
				
				list($hero_path, $thumb_path) = vec['hero', 'thumb']
					|> Vec\map($$, ($type) ==> {
						$img_exts = vec['gif', 'png', 'tiff', 'jpg'];
						$rel_bases = vec["{$basepath}_{$type}", "{$path}/{$type}"];
						return Vec\map($rel_bases, ($rel_base) ==> Vec\map($img_exts, ($ext) ==> 
							"{$rel_base}.{$ext}"
						))
							|> Vec\flatten($$)
							|> C\find($$, $rel_path ==> \file_exists("{$root_path}/{$rel_path}"));
					});
				$meta_path = vec["{$basepath}_meta.json", "{$path}/meta.json"]
					|> C\find($$, $rel_path ==> \file_exists("{$root_path}/{$rel_path}"));
				
				$meta = null;
				if($meta_path != null) {
					$meta = \json_decode(\file_get_contents("{$root_path}/{$meta_path}"), true);
				}
				
				return Vec\concat($acc, vec[shape(
					// 'title' => $title,
					'location' => \ltrim($path, '/'),
					'base' => $base,
					'content' => $ast, // new \MarkdownRenderable($renderer_struct, Vec\slice($ast->getChildren(), $content_offset, 5)),
					'ctime' => \filectime("{$root_path}/{$basepath}.md"),
					'mtime' => \filemtime("{$root_path}/{$basepath}.md"),
					'hero' => \is_null($hero_path) ? null : \ltrim($hero_path, '/'),
					'thumb' => \is_null($thumb_path) ? null : \ltrim($thumb_path, '/'),
					'meta' => $meta
				)]);
			}
			else {
				return $acc;
			}
		}, vec[]);
}