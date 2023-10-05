<?php

/**
 * Dex Booster
 *
 * @package     DexBooster
 * @author      Henri Susanto
 * @copyright   2022 Henri Susanto
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Dex Booster
 * Plugin URI:  https://github.com/susantohenri/dex-booster
 * Description: WordPress Plugin to build datatables server json
 * Version:     1.0.0
 * Author:      Henri Susanto
 * Author URI:  https://github.com/susantohenri/
 * Text Domain: DexBooster
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

add_shortcode('dex-booster', function ($attributes) {
    wp_register_style('datatables', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
    wp_enqueue_style('datatables');

    wp_register_script('datatables', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['jquery']);
    wp_enqueue_script('datatables');

    wp_enqueue_script('dex-booster', plugin_dir_url(__FILE__) . 'dex-booster.js?token=' . time(), null, null, true);

    return "
        <table id='henri-dex-booster'>
            <thead>
                <tr>
                    <th>PAIR</th>
                    <th>TIER</th>
                    <th>APR</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    ";
});

add_action('rest_api_init', function () {
    register_rest_route('dex-booster/v1', '/arbitrum', array(
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function () {
            $path = plugin_dir_path(__FILE__)  . 'data_arbitrum.json';
            $contents = file_get_contents($path);

            $json = json_decode($contents, true);
            $json = array_map(function ($obj) {
                $obj = array_filter($obj, function ($value, $attr) {
                    return in_array($attr, ['Pair', 'Tier', 'apr_mensual']);
                }, ARRAY_FILTER_USE_BOTH);
                return array_values($obj);
            }, $json);

            $data = ['data' => $json];

            $columns = $_POST['columns'];
            $dir = $_POST['order'][0]['dir'];
            $col = $columns[$_POST['order'][0]['column']]['name'];

            $search = urldecode($_POST['search']['value']);
            $rows = [];
            foreach ($data['data'] as $row) {
                if (
                    (empty($search) || (!empty($search) && (stripos($row['key'], $search) !== false || stripos($row['id'], $search) !== false)))
                ) {
                    $rows[] = $row;
                }
            }

            uasort($rows, fn ($a, $b) => ($dir === 'asc') ? $a[$col] <=> $b[$col] : $b[$col] <=> $a[$col]);
            $data_slice = array_slice($rows, $_POST['start'], $_POST['length']);

            return [
                'draw' => intval($_POST['draw']),
                'recordsTotal' => count($data['data']),
                'recordsFiltered' => count($rows),
                'data' => $data_slice
            ];
        }
    ));
});
