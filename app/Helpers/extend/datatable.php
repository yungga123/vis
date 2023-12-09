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
     * DataTable default (edit & delete) buttons actions format
     */
	function dt_button_actions(
        array $row, 
        string $id, 
        array $permissions, 
        bool $dropdown = false,
        array $options = [],
    ): string
	{
        $arr    = [
            'edit' => [
                'text'      => '',
                'button'    => 'btn-warning',
                'icon'      => 'fas fa-edit',
                'condition' => 'title="User has no permission to edit record." disabled',
            ],
            'delete' => [
                'text'      => '',
                'button'    => 'btn-danger',
                'icon'      => 'fas fa-trash',
                'condition' => 'title="User has no permission to delete record." disabled',
            ],
        ];
            
        if (check_permissions($permissions, ACTION_EDIT)) {
            $arr['edit']['text']        = $dropdown ? 'Edit' : '';
            $arr['edit']['condition']   = 'onclick="edit('.$row["$id"].')" title="Edit"';
        }
            
        if (check_permissions($permissions, ACTION_DELETE)) {
            $arr['delete']['text']        = $dropdown ? 'Delete' : '';
            $arr['delete']['condition']   = 'onclick="remove('.$row["$id"].')" title="Delete"';
        }
        
        $html = '';
        if (! (in_array('exclude_edit', $options) || isset($options['exclude_edit']))) 
            $html .= dt_button_html($arr['edit'], $dropdown);

        if (! (in_array('exclude_delete', $options) || isset($arr['exclude_delete'], $options))) 
            $html .= dt_button_html($arr['delete'], $dropdown);

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

if (! function_exists('text_badge'))
{
    /**
     * DataTable text badge (text with background color)
     */
	function text_badge(string $color, string $text): string
	{   
        return '<span class="rounded text-sm text-white pl-2 pr-2 pt-1 pb-1 mr-1 bg-'.$color.'">'.$text.'</span>';
	}
}

if (! function_exists('dt_status_color'))
{
    /**
     * DataTable status text label/bagde
     */
	function dt_status_color(string $status): string
    {
        $color   = 'secondary';
        switch (strtolower($status)) {
            case 'pending':
            case 'edit':
                $color = 'warning';                   
                break;
            case 'accepted':
            case 'approved':
            case 'add':
                $color = 'primary';
                break;
            case 'rejected':
                $color = 'secondary';
                break;
            case 'item_out':
            case 'received':
                $color   = 'success';
                break;
            case 'reviewed':
            case 'view':
                $color   = 'info';
                break;
            case 'delete':
                $color   = 'danger';
                break;
            case 'filed':
            case 'print':
                $color = 'dark';
                break;
        }

        return $color;
    }
}

if (! function_exists('dt_status_onchange'))
{
    /**
     * DataTable change status action button
     */
	function dt_status_onchange(int $id, string $changeTo, string $status, string $title = ''): string
    {
        $title  = trim(ucwords($changeTo) . ' '. $title);
        $title  = $changeTo === 'item_out' ? 'Item Out' : $title;
        return <<<EOF
            onclick="change({$id}, '{$changeTo}', '{$status}')" title="{$title}"
        EOF;
    }
}

if (! function_exists('dt_sql_date_format'))
{
    /**
     * DataTable SQL date format
     */
	function dt_sql_date_format(?string $column = null, $format = '%b %e, %Y'): string
    {
        if (! empty($column)) {
            return "DATE_FORMAT({$column}, '{$format}')";
        }

        return $format;
    }
}

if (! function_exists('dt_sql_datetime_format'))
{
    /**
     * DataTable SQL datetime format
     */
	function dt_sql_datetime_format(?string $column = null, $format = '%b %e, %Y at %h:%i %p'): string
    {
        return dt_sql_date_format($column, $format);
    }
}

if (! function_exists('dt_sql_time_format'))
{
    /**
     * DataTable SQL time format
     */
	function dt_sql_time_format(?string $column = null, $format = '%h:%i %p'): string
    {
        return dt_sql_date_format($column, $format);
    }
}

if (! function_exists('dt_sql_concat_client_address'))
{
    /**
     * DataTable SQL client address concatination
     */
	function dt_sql_concat_client_address($alias = ''): string
    {
        $alias = empty($alias) ? '' : $alias .'.';
        return "
            CONCAT(
                IF({$alias}province = '' || {$alias}province IS NULL, '', CONCAT(province, ', ')),
                IF({$alias}city = '' || {$alias}city IS NULL, '', CONCAT({$alias}city, ', ')),
                IF({$alias}barangay = '' || {$alias}barangay IS NULL, '', CONCAT({$alias}barangay, ', ')),
                IF({$alias}subdivision = '', '', {$alias}subdivision)
            ) AS address
        ";
    }
}

if (! function_exists('dt_sql_number_format'))
{
    /**
     * DataTable SQL amount format by round & decimal with comma for thousands
     */
	function dt_sql_number_format(string $column, int $round = 2, int $decimal = 2): string
    {
        return "FORMAT(ROUND(({$column}), {$round}), {$decimal})";
    }
}

if (! function_exists('dt_sql_trim'))
{
    /**
     * DataTable SQL trim column
     */
	function dt_sql_trim(string $column, $alias = ''): string
    {
        $alias = empty($alias) ? '' : ' AS '. $alias;
        return "TRIM({$column}){$alias}";
    }
}

if (! function_exists('dt_empty_col'))
{
    /**
     * DataTable empty first column - 
     * will be used for responsive
     */
	function dt_empty_col(): callable
    {
        return function ($row) { return ''; };
    }
}