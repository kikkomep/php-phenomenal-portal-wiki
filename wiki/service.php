<?php
require('simple_html_dom.php');
require_once('../lib/search/loader.php');
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(60);

$path = "../wiki-html";

function getAllWiki()
{
    global $path;
    getFilenames($path, '');
}

function searchPageContent($term)
{
    $result = array();
    if ($term == '') {
        $result['result'] = 0;
        $result['data'] = array();
    } else {
        global $path;
        $dir = $path . "/phenomenal-h2020.wiki/";
        $result['result'] = 1;
        $temp = getFilenameList($dir);
        $searchResult = array();
        foreach ($temp as $k => $v) {
            if (stripos($v['name'], $term) !== false) {
                $searchResult[] = $v;
            }
        }

        try {
            $docsearch = new File_Search\Document_Search(
                array(__DIR__ . '/../wiki-html/phenomenal-h2020.wiki/'),
                $term
            );
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if (isset($docsearch) && $docsearch != NULL) {
            foreach ($docsearch->getContainingFiles() as $value) {
                foreach ($temp as $k => $v) {
                    if (strcmp($v['link'], pathinfo($value)['filename']) == 0) {
                        $searchResult[] = $v;
                    }
                }
            }
        }
        $searchResult = array_unique($searchResult, SORT_REGULAR);
        $result['data'] = array_values($searchResult);
    }
    echo json_encode($result);
}

function getFilenameList($dir)
{
    return array_merge(
        parseMenuForSearch($dir . 'Tutorials.html'),
        parseMenuForSearch($dir . 'User-Documentation.html'),
        parseMenuForSearch($dir . 'Developer-Documentation.html')
    );
}

function parseMenuForSearch($dir)
{
    $data = array();
    $html = file_get_html($dir);
    foreach ($html->find('li') as $row) {
        $link = extractLinkElement($row);
        if ($link)
            $data[] = extractLinkElement($row);
    }
    return $data;
}


function parseMenuPageContent($folderName, $file_name, $format, $limit)
{
    global $path;
    $dir = $path . "/" . $folderName . "/" . $file_name;
    parseMenuPage($dir, $format, $limit);
}

function parseMenuPage($dir, $format, $limit)
{
    $result = array();
    if (strpos($dir, '../') == true || $dir == '') {
        $result['result'] = 0;
        $result['data'] = '{}';
    } else {
        $result['result'] = 1;
        $data = array();
        $html = file_get_html($dir);
        foreach ($html->find('li') as $row) {
            if ($limit > 0 || $limit == -1) {
                $link = extractLinkElement($row);
                if ($link)
                    $data[] = extractLinkElement($row);
                $limit--;
            }
        }
        $result['data'] = $data;
    }
    echo json_encode($result);
}

function extractLinkElement($row)
{
    $result = null;
    $href = $row->find('a', 0)->href;
    if (!is_null($href)) {
        $result = array();
        $result['id'] = strpos($href, 'http') === 0 ? substr($href, strrpos($href, '/') + 1) : $href;
        $result['link'] = $result['id'] . ".html";
        $result['name'] = $row->plaintext;
    }
    return $result;
}

function parsePageContent($folderName, $file_name, $format, $limit)
{
    global $path;
    $dir = $path . "/" . $folderName . "/" . $file_name;
    parsePage($dir, $format, $limit);
}

function parsePage($dir, $format, $limit)
{
    $result = array();
    if (!file_exists($dir) || strpos($dir, '../') == true || $dir == '') {
        $result['result'] = 0;
        $result['data'] = 'TBD';
    } else {
        $result['result'] = 1;
        $html = file_get_contents($dir);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
        $result['data'] = $html;
    }
    echo json_encode($result);
}


function getContentFromWiki($folderName, $format)
{
    global $path;
    $dir = $path . "/" . $folderName;
    getFilenames($dir, $format);
}

function getFilenames($dir, $format)
{
    $data = createEmptyJSONDataArray();
    if ($format !== 'array') {
        if (is_dir($dir)) {
            $indir = array_filter(scandir($dir), function ($item) {
                return $item[0] !== '.';
            });
            $data['result'] = 1;
            $data['data'] = $indir;
        }
    } else {
        if (is_dir($dir)) {
            $indir = array_filter(scandir($dir), function ($item) {
                return $item[0] !== '.';
            });
            $data['result'] = 1;
            $data['data'] = array_values($indir);
        }
    }
    print_r(json_encode($data));
}

function createEmptyJSONDataArray()
{
    $data = array();
    $data['result'] = 0;
    $data['data'] = json_decode("{}");
    return $data;
}