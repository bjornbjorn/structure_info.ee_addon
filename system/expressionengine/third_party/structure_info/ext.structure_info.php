<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package   ExpressionEngine
 * @author    ExpressionEngine Dev Team
 * @copyright Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license   http://expressionengine.com/user_guide/license.html
 * @link    http://expressionengine.com
 * @since   Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Structure Info Extension
 *
 * @package   ExpressionEngine
 * @subpackage  Addons
 * @category  Extension
 * @author    Bjørn Børresen
 * @link    http://lastfriday.no
 */

class Structure_info_ext {

    public $settings    = array();
    public $description   = 'Adds additional structure tags to the standard channel:entries tag';
    public $docs_url    = 'http://wedoaddons.com/addon/structure-info';
    public $name      = 'Structure Info';
    public $settings_exist  = 'n';
    public $version     = '1.1';
    const TAG_PREFIX = 'structure_info:';

    /**
     * Constructor
     *
     * @param   mixed Settings array or empty string if none exist.
     */
    public function __construct($settings = '')
    {
        $this->settings = $settings;
    }

    // ----------------------------------------------------------------------

    /**
     * Activate Extension
     *
     * This function enters the extension into the exp_extensions table
     *
     * @see http://codeigniter.com/user_guide/database/index.html for
     * more information on the db class.
     *
     * @return void
     */
    public function activate_extension()
    {
        // Setup custom settings in this array.
        $this->settings = array();

        $data = array(
            'class' => __CLASS__,
            'method' => 'on_channel_entries_query_result',
            'hook' => 'channel_entries_query_result',
            'settings' => serialize($this->settings),
            'version' => $this->version,
            'enabled' => 'y',
        );

        ee()->db->insert('extensions', $data);

    }

    // ----------------------------------------------------------------------

    /**
     * channel_entries_query_result
     *
     * @param
     * @return
     */
    public function on_channel_entries_query_result($ref, $result)
    {
        $site_pages_all = ee()->config->item('site_pages');
        $site_pages = $site_pages_all[ee()->config->item('site_id')];

        $include_structure_path = ee()->TMPL->fetch_param('include_structure_path') == 'yes';
        $structure_path_separator = ee()->TMPL->fetch_param('structure_path_separator', '>');

        $structure_listing_ids = array();
        if($include_structure_path) {
            require_once PATH_THIRD.'structure/sql.structure.php';
            $structure_sql = new Sql_structure();
            $structure_listing_ids = $structure_sql->get_listing_entry_ids();
        }

        if (ee()->extensions->last_call !== FALSE) {
            $result = ee()->extensions->last_call;
        }

        foreach($result as $result_index => $entry_info) {

            $structure_info = array(
                Structure_info_ext::TAG_PREFIX.'page_uri' => '',
                Structure_info_ext::TAG_PREFIX.'page_last_segment' => '',
            );

            if(isset($site_pages['uris'][$entry_info['entry_id']])) {

                $page_uri = $site_pages['uris'][$entry_info['entry_id']];

                $structure_info[Structure_info_ext::TAG_PREFIX.'page_uri'] = $page_uri;
                $segments = explode('/',$page_uri);
                $last_segment = $segments[count($segments)-1];
                $structure_info[Structure_info_ext::TAG_PREFIX.'page_last_segment'] = $last_segment;

                if($include_structure_path) {
                    $path = $structure_sql->get_single_path($entry_info['entry_id']);

                    $path_str = '';
                    foreach($path as $path_entry) {
                        $path_str .= $path_entry['title'] . ' '.$structure_path_separator.' ';
                    }
                    $path_str = substr($path_str, 0, strlen($path_str)-3);

                    // add the entry to the end if it is a listing (for some reason Structure will
                    // only return the parent for listings ...
                    if (array_key_exists($entry_info['entry_id'], $structure_listing_ids))
                    {
                        $path_str .= ' '.$structure_path_separator.' '.$entry_info['title'];
                    }

                    $structure_info[Structure_info_ext::TAG_PREFIX.'path'] = $path_str;
                }

            }

            $result[$result_index] = array_merge($result[$result_index], $structure_info);
        }

        return $result;
    }


    /**
     * Get
     */


    // ----------------------------------------------------------------------

    /**
     * Disable Extension
     *
     * This method removes information from the exp_extensions table
     *
     * @return void
     */
    public function disable_extension()
    {
        ee()->db->delete('extensions', array('class' => __CLASS__));
    }

    // ----------------------------------------------------------------------

    /**
     * Update Extension
     *
     * This function performs any necessary db updates when the extension
     * page is visited
     *
     * @return  mixed void on update / false if none
     */
    public function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }
    }

    // ----------------------------------------------------------------------
}

/* End of file ext.structure_info.php */
/* Location: /system/expressionengine/third_party/structure_info/ext.structure_info.php */
