<?php
// mettez dans $path un chemin d'accès vers un projet PHP existant sur votre machine
$path = 'C:\wamp\www\firstsilex';

$metatags = array();

$files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path),
                RecursiveIteratorIterator::CHILD_FIRST
);

$type_src = array('php', 'js', 'ini', 'xml', 'twig', 
    'txt', 'css', 'html', 'phtml', 'sql');

$id = 0;
foreach ($files as $filename => $fileinfo) {
    if ($fileinfo->isFile()) {
        $nb_code_lines = 0;
        $extension = $fileinfo->getExtension();

        if (in_array($extension, $type_src)) {
            $categ_objet = 'source';
            $tmp_file = file($fileinfo->getPathname());
            if (is_array($tmp_file) || count($tmp_file) > 0) {
                $nb_code_lines = count($tmp_file);
            }
        }

        $metatags [] = array(
            'filename' => $fileinfo->getFilename(),
            'filepath' => $fileinfo->getPathname(),
            'extension' => $extension,
            'size' => $fileinfo->getSize(),
            'nb_lines' => $nb_code_lines
        );
        $id++;
    }
}
echo '<pre>';
var_dump($metatags);
