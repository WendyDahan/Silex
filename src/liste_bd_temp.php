<?php

function getListeBD() {
    $listebd = array();
    $listebd[] = array(
        'id' => 1,
        'album' => 'Garulfo',
        'auteur' => 'Ayroles, Maïorana, Leprévost',
        'editeur' => 'Delcourt',
        'parution' => '2011-05-15'
    );
    $listebd[] = array(
        'id' => 2,
        'album' => 'horologiom',
        'auteur' => 'fabrice Lebeault',
        'editeur' => 'Delcourt',
        'parution' => '2005-01-01'
    );
    $listebd[] = array(
        'id' => 3,
        'album' => 'Le château des étoiles',
        'auteur' => 'Alex Alice',
        'editeur' => 'Rue De Sevres',
        'parution' => '2014-05-01'
    );
    $listebd[] = array(
        'id' => 4,
        'album' => 'Le voyage extraordinaire',
        'auteur' => 'Camboni, Filippi',
        'editeur' => 'Vents d\'Ouest',
        'parution' => '2012-09-01'
    );
    return $listebd;
}
