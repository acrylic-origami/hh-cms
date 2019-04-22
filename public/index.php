<?hh // strict
use namespace Facebook\Markdown;
use namespace HH\Lib\{C, Str, Vec};
require_once(__DIR__ . '/../vendor/hh_autoload.hh');
class Main {
	protected Markdown\ParserContext $pctx;
	protected Markdown\RenderContext $rctx;
	protected LamIO\HTMLRenderer $renderer;
	public function __construct() {
		$this->pctx = new Markdown\ParserContext();
		$this->pctx->enableHTML_UNSAFE();
		$this->pctx->getBlockContext()->prependBlockTypes(LamIO\UnparsedBlocks\Aside::class);
		$this->rctx = new Markdown\RenderContext();
		$this->renderer = new LamIO\HTMLRenderer($this->rctx);
	}
	public function recurse(string $path): void {
		foreach(new \DirectoryIterator($path) as $v) {
			if($v->isDir() && $v->getFilename() !== '.' && $v->getFilename() !== '..')
				$this->recurse($path . '/' . $v->getFilename());
			elseif(\strtolower($v->getExtension()) === 'md') {
				// echo $path.': ';
				// echo microtime(true) . '|';
				var_dump(Markdown\parse($this->pctx, file_get_contents($v->getPathname())));
				exit(0);
				// echo microtime(true);
				// echo '<br />';
			}
		}
	}
}
<<__EntryPoint>>
function index(): void {
	// \xhprof_enable(); // \XHPROF_FLAGS_CPU + \XHPROF_FLAGS_MEMORY
	echo PageRenderer::render(new \Index());
	// LamIO\CMS\content_iterator(LamIO\default_renderer_factory(), __DIR__ . '/project_assets/audiolizer');
	// $XHPROF_ROOT = "/tools/xhprof/";
	// include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
	// include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";

	// $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_testing");
	
	// $instance = new Main();
	// $start = \microtime(true);
	// echo $start."<br />";
	// $render_times = vec[0]
	// 	|> Vec\map($$, ($_) ==> {
	// 			$instance->recurse('/Users/derek-lam/Documents/Portfolio/writing/project_assets');
	// 			return (string)(\microtime(true) - $start);
	// 		})
	// 	|> Str\join($$, "\n");
	// \file_put_contents('sample.xhprof', \serialize(\xhprof_disable()));
	// echo <pre>
	// 	{$render_times}
	// </pre>;
}