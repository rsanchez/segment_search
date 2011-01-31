<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'Segment Search',
	'pi_version' =>'1.0.0',
	'pi_author' =>'Rob Sanchez',
	'pi_author_url' => 'http://barrettnewton.com/',
	'pi_description' => 'Search URL segments for use in conditionals.',
	'pi_usage' => Segment_search::usage()
);

class Segment_search
{
	public $return_data = '0';
	
	protected $keywords = array();
	protected $not = FALSE;
	protected $regex = FALSE;
	protected $strict = FALSE;
	
	public function Segment_search() 
	{
		$this->EE = get_instance();
		
		$keyword = $this->EE->TMPL->fetch_param('keyword');
		
		if ($keyword !== '' && $keyword !== FALSE)
		{
			if (substr($keyword, 0 , 4) === 'not ')
			{
				$this->not = TRUE;
				
				$keyword = substr($keyword, 4);
			}
			
			if (substr($keyword, 0, 1) === '=')
			{
				$this->strict = TRUE;
				
				$keyword = substr($keyword, 1);
			}
		}
		else
		{
			return $this->return_data;
		}
		
		$this->keywords = preg_split('/\|/', $keyword);
		
		if (empty($this->keywords) || strlen(implode('', $this->keywords)) < 1)
		{
			return $this->return_data;
		}
		
		if ($this->EE->TMPL->fetch_param('regex'))
		{
			$this->regex = (bool) preg_match('/1|on|yes|y/i', $this->EE->TMPL->fetch_param('regex'));
		}
		
		$this->segments = $this->EE->uri->segment_array();
		
		if ($segments_param = $this->EE->TMPL->fetch_param('segments'))
		{
			if ($segments_param === 'last')
			{
				$this->segments = array(end($this->segments));
			}
			else
			{
				if (substr($segments_param, 0, 4) === 'not ')
				{
					foreach ($this->segments as $key => $value)
					{
						if (in_array($key, preg_split('/\|/', substr($segments_param, 4))))
						{
							unset($this->segments[$key]);
						}
					}
				}
				else
				{
					foreach ($this->segments as $key => $value)
					{
						if ( ! in_array($key, preg_split('/\|/', $segments_param)))
						{
							unset($this->segments[$key]);
						}
					}
				}
			}
		}
		
		if ($this->not && ! $this->regex)
		{
			$this->return_data = '1';
		}
		
		foreach ($this->segments as $segment)
		{
			foreach ($this->keywords as $keyword)
			{
				if ($this->regex)
				{
					if (@preg_match($keyword, $segment))
					{
						$this->return_data = '1';
					}
				}
				else
				{
					if ($this->strict)
					{
						if ($segment === $keyword)
						{
							$this->return_data = ($this->not) ? '0' : '1';
						}
					}
					else
					{
						if (strstr($segment, $keyword) !== FALSE)
						{
							$this->return_data = ($this->not) ? '0' : '1';
						}
					}
				}
			}
		}
	}

	public static function usage() 
	{
  		ob_start();
?>
{if {exp:segment_search keyword="stuff"}}{/if}
{if {exp:segment_search keyword="=stuff"}}{/if}
{if {exp:segment_search keyword="stuff|puff"}}{/if}
{if {exp:segment_search keyword="not stuff|puff"}}{/if}
{if {exp:segment_search keyword="stuff" segments="1|2|3"}}{/if}
{if {exp:segment_search keyword="stuff" segments="not 1|2|3"}}{/if}
{if {exp:segment_search keyword="/\d+/" segments="1|2|3" regex="yes"}}{/if}
{if {exp:segment_search keyword="/P\d+/" segments="last" regex="yes"}}{/if}
	<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}	
}