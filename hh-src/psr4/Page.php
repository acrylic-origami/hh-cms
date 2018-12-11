<?hh // strict
namespace LamIO;

interface Page {
	public function get_style_deps(): vec<string>;
	public function get_js_deps(): vec<string>;
	public function get_title(): string;
	public function render_body(): \XHPRoot;
}