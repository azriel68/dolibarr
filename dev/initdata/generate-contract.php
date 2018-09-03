#!/usr/bin/env php
<?php
/* Copyright (C) 2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * ATTENTION DE PAS EXECUTER CE SCRIPT SUR UNE INSTALLATION DE PRODUCTION
 */

/**
 *      \file       dev/initdata/generate-invoice.php
 *		\brief      Script example to inject random customer invoices (for load tests)
 */

// Test si mode batch
$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) == 'cgi') {
	echo "Erreur: Vous utilisez l'interpreteur PHP pour le mode CGI. Pour executer mailing-send.php en ligne de commande, vous devez utiliser l'interpreteur PHP pour le mode CLI.\n";
	exit;
}

// Recupere root dolibarr
//$path=preg_replace('/generate-produit.php/i','',$_SERVER["PHP_SELF"]);
require (__DIR__. '/../../htdocs/master.inc.php');
require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");


/*
 * Parameters
 */

$year = (int)date('Y');
$dates = array (mktime(12,0,0,1,3,$year),
    mktime(12,0,0,1,9,$year),
    mktime(12,0,0,2,13,$year),
    mktime(12,0,0,2,23,$year),
    mktime(12,0,0,3,30,$year),
    mktime(12,0,0,4,3,$year),
    mktime(12,0,0,4,3,$year),
    mktime(12,0,0,5,9,$year),
    mktime(12,0,0,5,1,$year),
    mktime(12,0,0,5,13,$year),
    mktime(12,0,0,5,19,$year),
    mktime(12,0,0,5,23,$year),
    mktime(12,0,0,6,3,$year),
    mktime(12,0,0,6,19,$year),
    mktime(12,0,0,6,24,$year),
    mktime(12,0,0,7,3,$year),
    mktime(12,0,0,7,9,$year),
    mktime(12,0,0,7,23,$year),
    mktime(12,0,0,7,30,$year),
    mktime(12,0,0,8,9,$year),
    mktime(12,0,0,9,23,$year),
    mktime(12,0,0,10,3,$year),
    mktime(12,0,0,11,12,$year),
    mktime(12,0,0,11,13,$year),
    mktime(12,0,0,1,3,($year - 1)),
    mktime(12,0,0,1,9,($year - 1)),
    mktime(12,0,0,2,13,($year - 1)),
    mktime(12,0,0,2,23,($year - 1)),
    mktime(12,0,0,3,30,($year - 1)),
    mktime(12,0,0,4,3,($year - 1)),
    mktime(12,0,0,4,3,($year - 1)),
    mktime(12,0,0,5,9,($year - 1)),
    mktime(12,0,0,5,1,($year - 1)),
    mktime(12,0,0,5,13,($year - 1)),
    mktime(12,0,0,5,19,($year - 1)),
    mktime(12,0,0,5,23,($year - 1)),
    mktime(12,0,0,6,3,($year - 1)),
    mktime(12,0,0,6,19,($year - 1)),
    mktime(12,0,0,6,24,($year - 1)),
    mktime(12,0,0,7,3,($year - 1)),
    mktime(12,0,0,7,9,($year - 1)),
    mktime(12,0,0,7,23,($year - 1)),
    mktime(12,0,0,7,30,($year - 1)),
    mktime(12,0,0,8,9,($year - 1)),
    mktime(12,0,0,9,23,($year - 1)),
    mktime(12,0,0,10,3,($year - 1)),
    mktime(12,0,0,11,12,$year),
    mktime(12,0,0,11,13,$year),
    mktime(12,0,0,12,12,$year),
    mktime(12,0,0,12,13,$year),
);

$ret=$user->fetch('','admin');
if (! $ret > 0)
{
	print 'A user with login "admin" and all permissions must be created to use this script.'."\n";
	exit;
}
$user->getrights();

$prodids = array();
$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."product WHERE tosell=1 AND fk_product_type=1";
$resql = $db->query($sql);
if ($resql)
{
	$num_prods = $db->num_rows($resql);
	$i = 0;
	while ($i < $num_prods)
	{
		$i++;
		$row = $db->fetch_row($resql);
		
		$product=new Product($db);
		$result=$product->fetch( $row[0] );
		
		$prodids[$i] = $product;
	}
}

$i=0;
$result=0;

$sql = "SELECT s.rowid FROM ".MAIN_DB_PREFIX."societe s 
            LEFT OUTER JOIN ".MAIN_DB_PREFIX."contrat c ON (c.fk_soc = s.rowid)
    WHERE s.client in (1, 3) AND c.rowid IS NULL";
$resql = $db->query($sql);

if($resql === false ) {
    var_dump($db);
    exit;
}

$db->query("SET autocommit=0; 
    SET unique_checks=0; 
    SET foreign_key_checks=0;");

if ($resql)
{
    while ($row = $db->fetch_row($resql))
    {
        $fk_soc = $row[0];
        
        $nb_contrat=mt_rand(1,10);

        for($i=0;$i<$nb_contrat;$i++) {

            $object = new Contrat($db);
            $object->socid = $fk_soc;
            $object->date = $dates[mt_rand(1, count($dates)-1)];
            $object->commercial_signature_id=1;
            $object->commercial_suivi_id=1;
            
            $result=$object->create($user);
            if ($result >= 0)
            {
                $nbp = mt_rand(1, 5);
                $xnbp = 0;
                while ($xnbp < $nbp)
                {
                    $prodid = mt_rand(1, $num_prods);
                    $product=$prodids[$prodid];
                    //$desc, $pu_ht, $qty, $txtva, $txlocaltax1, $txlocaltax2, $fk_product, $remise_percent, $date_start, $date_end
                    $lineid=$object->addline($product->description, $product->price, mt_rand(1,5), 0, 0, 0, $product->id, 0, date('Y-m-d',$object->date), date('Y-m-d',strtotime('+'. mt_rand(1,50).'month',$object->date))  );
                    if ($lineid < 0)
                    {
                        dol_print_error($db,$propal->error);
                    }
                    
                    /*if(mt_rand(1,2)==1) {
                        
                        $object->lines[$xnbp]->active_line($user, $object->date);
                    }*/
                    
                    $xnbp++;
                }
                
                $result=$object->validate($user);
                if ($result)
                {
                    print " OK with ref ".$object->ref."\n";;
                }
                else
                {
                    dol_print_error($db,$object->error);
                }
            }
            else
            {
                var_dump($object);exit;
                dol_print_error($db,$object->error);
            }
        }
        
        
    }
}


