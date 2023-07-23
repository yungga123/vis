<?php
if (! function_exists('dt_button_html'))
{
    /**
     * DataTable html button format
     */
	function dt_button_html(array $options, bool $dropdown = false): string
	{     
        $wfull  = $dropdown ? 'w-100' : '';  
        $html   = <<<EOF
            <button class="btn btn-sm {$options['button']} {$wfull}" {$options['condition']}>
                <i class="{$options['icon']}"></i> {$options['text']}
            </button>
        EOF;

        return $dropdown 
            ? '<div class="dropdown-item">'. $html .'</div>'
            : $html;
	}
}

if (! function_exists('dt_button_actions'))
{
    /**
     * DataTable buttons actions format
     */
	function dt_button_actions(array $row, string $id, array $permissions, bool $dropdown = false): string
	{
        $options    = [
            'edit' => [
                'text'      => '',
                'button'    => 'btn-warning',
                'icon'      => 'fas fa-edit',
                'condition' => 'title="Cannot edit" disabled',
            ],
            'delete' => [
                'text'      => '',
                'button'    => 'btn-danger',
                'icon'      => 'fas fa-trash',
                'condition' => 'title="Cannot delete" disabled',
            ],
        ];
            
        if (check_permissions($permissions, 'EDIT')) {
            $options['edit']['text']        = $dropdown ? 'Edit' : '';
            $options['edit']['condition']   = 'onclick="edit('.$row["$id"].')" title="Edit"';
        }
            
        if (check_permissions($permissions, 'DELETE')) {
            $options['delete']['text']        = $dropdown ? 'Delete' : '';
            $options['delete']['condition']   = 'onclick="remove('.$row["$id"].')" title="Delete"';
        }

        $html = dt_button_html($options['edit'], $dropdown);
        $html .= dt_button_html($options['delete'], $dropdown);

        return $html;
	}
}

if (! function_exists('dt_buttons_dropdown'))
{
    /**
     * DataTable buttons dropdown html format
     */
	function dt_buttons_dropdown(string $buttons, bool $dropdown = false): string
	{   
        $buttons = $dropdown ? $buttons : '<div class="dropdown-item">'.$buttons.'</div>';
        return <<<EOF
            <div class="">
                <button class="btn btn-info btn-sm dropdown-toggle rounded" type="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-info-circle"></i> Actions</button>
                <div class="dropdown-menu">
                    {$buttons}
                </div>
            </div>
        EOF;
	}
}