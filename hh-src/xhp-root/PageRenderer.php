<?hh // strict
use LamIO\Page;
use namespace HH\Lib\{C, Str, Vec};
class PageRenderer {
	public static function render(Page $page): string {
		return (
			<x:doctype>
				<html>
					<head>
						<meta charset="utf-8" />
						<link rel="icon" href="/img/graphics/favicon.ico" type="image/x-icon" />
						{Vec\map($page->get_style_deps(), ($href) ==> <link rel="stylesheet" type="text/css" href={$href} />)}
						{Vec\map($page->get_js_deps(), ($href) ==> <script type="text/javascript" src={$href} />)}
						<title>{$page->get_title()}</title>
					</head>
					
					<body>
						{$page->render_body()}
					</body>
				</html>
			</x:doctype>
		)->toString();
	}
}
