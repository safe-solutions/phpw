<?php
class PHPW {
	private $keywords = ['__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor'];
	private $content = '';
	private $options = [
		'crlf'=>true
	];
	
	private function insert_char($string, $char, $pos) {
		return substr($string, 0, $pos) . $char . substr($string, $pos);
	}
	
	static public function instance($source, $isString = false) {
		$phpw = new self;
		$phpw->content = $isString ? $source : file_get_contents($source);
		return $phpw;
	}
	
	public function options($options) {
		$this->options = array_merge($this->options, $options);
		return $this;
	}
	
	public function compile() {
		$compiled = $this->content;
		$compiled = str_replace(["\r"], "", $compiled);
		$compiled = preg_replace('/\/\/(.*)/', '', $compiled);
		$string_replaces = [];
		$i = 0;
		$compiled = preg_replace_callback('/"([^\\\\"]|\\\\.)*"/U', function($matches) use (&$string_replaces, &$i) {
			$i++;
			$string_replaces[$i] = $matches[0];
			return "^STRING$i^";
		}, $compiled);
		$compiled = preg_replace_callback('/\'([^\\\\\']|\\\\.)*\'/U', function($matches) use (&$string_replaces, &$i) {
			$i++;
			$string_replaces[$i] = $matches[0];
			return "^STRING$i^";
		}, $compiled);
		$compiled = preg_replace('/^@phpw.*\n/U', '', $compiled);
		$compiled = preg_replace('/(\s{1,}|^)(\w{1,})\s*=\s*\{/', "$1class $2 {", $compiled);
		$compiled = preg_replace('/(\s{1,})__\s{1,}/', "$1private ", $compiled);
		$compiled = preg_replace('/(\s{1,})_\s{1,}/', "$1protected ", $compiled);
		$compiled = preg_replace('/\(\s*\(\s*\$(\w*)\s*\,\s*\$(\w*)\s*\)\s*in\s*\$([\w\.]*)\)/', "foreach ($$3 as $$1=>$$2)", $compiled);
		$compiled = preg_replace('/\(\s*\(\s*\$(\w*)\s*\)\s*in\s*\$([\w\.]*)\)/', "foreach ($$2 as $$1)", $compiled);
		$compiled = preg_replace('/\(\s*\(\s*\$(\w*)\s*\,\s*\)\s*in\s*\$([\w\.]*)\)/', "foreach (array_keys($$2) as $$1)", $compiled);
		$compiled = preg_replace('/([ ]*)\n/', "$2\n", $compiled);
		$compiled = preg_replace_callback('/(\w*)[ ]*\(([\w\$\s\,]*)\)\s*\{/', function($matches) {
			if (!in_array($matches[1], $this->keywords)) {
				return "function " . $matches[0];
			}
			else {
				return $matches[0];
			}
		}, $compiled);
		$compiled = preg_replace('/([\w%\)\]\^])(\n|$)(?!\s*[\]\)])/', "$1;\n", $compiled);
		$compiled = preg_replace('/\$(\w*)\.(\w*)/', "$$1->$2", $compiled);
		$compiled = preg_replace('/(\w*)\.(\w*)/', "$1::$2", $compiled);
		$compiled = str_replace('#', '.', $compiled);
		foreach ($string_replaces as $k=>$v) {
			$compiled = str_replace("^STRING$k^", $v, $compiled);
		}
		if ($this->options['crlf']) {
			$compiled = str_replace("\n", "\r\n", $compiled);
		}
		return $compiled;
	}
}

function includex($source, $options = []) {
	eval(PHPW::instance($source)->options($options)->compile());
}