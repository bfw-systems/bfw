<?php
/**
 * Toutes les fonctions utilisé par le système de cache
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @package bfw
 */

/**
 * Ajoute des variables normalement envoyé à la vue à la variable l'affichant
 * 
 * @param string $where : La où ont doit ajouté les variables
 * @param array  $data  : Les variables normalement envoyé à la vue
 */
function BFWCacheAddVars($where, $data)
{
    if($where == 'html')
    {
        global $BFWCacheVars;
        
        foreach($data as $key => $val)
        {
            $BFWCacheVars[$key] = $val;
        }
    }
    elseif($where == 'all')
    {
        global $BFWCacheVars, $BFWCacheBlockVars;
        
        foreach($data as $key => $val)
        {
            $BFWCacheVars[$key] = $val;
        }
        
        foreach($BFWCacheBlockVars as $key => $val)
        {
            foreach($data as $keyD => $valD)
            {
                $BFWCacheBlockVars[$key][$keyD] = $valD;
            }
        }
    }
    else
    {
        global $BFWCacheBlockVars;
        
        foreach($BFWCacheBlockVars as $key => $val)
        {
            foreach($data as $keyD => $valD)
            {
                $BFWCacheBlockVars[$key][$keyD] = $valD;
            }
        }   
    }
}
