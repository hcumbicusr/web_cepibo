<?php

require_once '../Config.inc.php';
require_once '../core/Funciones.php';

/**
 * @param Int $total Total de elementos a mostrar
 * @param Int $tpp Total de elementos por pagina a mostrar
 * @param Int $pag_show Paginas a mostrar
 * @param Int $pact Pagina actual
 */
function paginador($total,$tpp,$cat = null,$pag_show = 3,$pact = 1)
{ 
    echo '<ul class="pagination">';
    // solo enteros
    $pact = intval($pact);
    $npag = Funciones::nroPaginas($total, $tpp);
    $ant_sig = Funciones::pagAntSig($npag, $pact);
    $anterior = $ant_sig['anterior'];
    $siguiente = $ant_sig['siguiente'];
    
    //die(var_dump($total));
    
    if ($pact > $npag)
    {
        $pact = $npag;
    }
    if ($pact <= 0)
    {
        $pact = 1;
    }
    
    $links = [];
    //die(var_dump($ant_sig));
    if (!empty($cat)) {
        $cat = "cat_t=$cat&";
    }else {
        $cat = "";
    }
    for ($i = 1; $i <= $npag; $i++) { 
        if ($pact == $i)
        {
            $links[$i] = "<li class='active'><a href='?$cat"."pag=$i' >$i</a></li>";
        }else
        {
            $links[$i] = "<li><a href='?$cat"."pag=$i' >$i</a></li>";
        }
    }
    
    $fin_pag = $i+1;
    
    if($anterior > 0)
    {
        $links[0] = "<li><a href='?$cat"."pag=".($pact-1)."' >&laquo;</a></li>";
    }else
    {
        $links[0] = "";
    }
    
    if($siguiente > 0)
    {
        $links[$fin_pag] = "<li><a href='?$cat"."pag=".($pact+1)."' >&raquo;</a></li>";
    }else
    {
        $links[$fin_pag] = "";
    }
    
    // con esto se muestra las paginas anteriores
    if ($pact > $pag_show)
    {
        echo $links[0];
        for ($j = ($pact - $pag_show); $j < $pact; $j++) {
            echo $links[$j];
        }
    }else
    {
        for ($j = 0; $j < $pact; $j++) {
            echo $links[$j];
        }
    }
    
    // aqui se muestra la pagina actual
    
    echo $links[$pact];
    
    // con esto se muestra las paginas siguientes
    
    $queda_sig = 0;
    // cuantas paginas quedan a partir de la actial
    for ($x = $pact+1; $x < $npag; $x++ )
    {
        $queda_sig++;
    }
    
    //die(var_dump($queda_sig));
    
    if ($queda_sig > $pag_show)
    {
        for ($j = $pact+1; $j < ($pact+1+$pag_show); $j++) {
            echo $links[$j];
        }
        echo $links[$fin_pag];
    }else
    {
        for ($j = $pact+1; $j <= $npag; $j++) {
            echo $links[$j];
        }
    }
    
    echo '</ul>';
}