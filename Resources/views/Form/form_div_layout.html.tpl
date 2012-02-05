{* Widgets *}

{extends 'file:[SmartyBundle]/Form/form_table_layout.html.tpl'}

{function testing}
	<h1>Hi, I'm testing!</h1>
{/function}

{function field_enctype}
	{if $multipart}enctype="multipart/form-data"{/if}
{/function}


