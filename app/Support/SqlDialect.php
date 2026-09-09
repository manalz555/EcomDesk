<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Les quelques expressions SQL du projet qui ne s'ecrivent pas pareil selon
 * le SGBD sont regroupees ici, plutot que disseminees dans les controleurs.
 *
 * Le module analytique descend volontairement au niveau SQL pour ses
 * agregations (voir le chapitre 4 du rapport) : faire la moyenne des delais
 * de premiere reponse en PHP obligerait a charger chaque couple
 * conversation/reponse en memoire pour n'en tirer qu'un seul nombre. Le prix
 * a payer est cette dependance au dialecte — isolee dans cette classe, elle
 * reste maitrisee, et l'application redevient testable sur une base legere.
 */
class SqlDialect
{
    /**
     * Ecart en minutes entre deux colonnes datetime, sous forme d'expression SQL.
     *
     * MySQL fournit TIMESTAMPDIFF ; SQLite raisonne en jours juliens (d'ou le
     * facteur 1440 = 24 x 60) ; PostgreSQL passe par un intervalle en secondes.
     */
    public static function minutesBetween(string $debut, string $fin): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "((julianday({$fin}) - julianday({$debut})) * 1440)",
            'pgsql' => "(EXTRACT(EPOCH FROM ({$fin}::timestamp - {$debut}::timestamp)) / 60)",
            default => "TIMESTAMPDIFF(MINUTE, {$debut}, {$fin})",
        };
    }

    /**
     * Ordonnancement selon un ordre metier explicite plutot qu'alphabetique :
     * une priorite « haute » doit passer avant une priorite « faible », ce que
     * l'ordre alphabetique ne donnerait pas.
     *
     * MySQL propose FIELD(), mais un CASE ... WHEN produit le meme resultat et
     * fonctionne sur tous les SGBD — on garde donc un seul chemin de code.
     * Les valeurs sont echappees, bien qu'elles proviennent toujours de
     * constantes du domaine et jamais d'une saisie utilisateur.
     */
    public static function orderByValues(string $colonne, array $valeurs): string
    {
        $cas = '';

        foreach (array_values($valeurs) as $rang => $valeur) {
            $echappee = str_replace("'", "''", (string) $valeur);
            $cas .= " WHEN '{$echappee}' THEN ".($rang + 1);
        }

        // Toute valeur inconnue est renvoyee en fin de tri plutot que d'etre perdue.
        $defaut = count($valeurs) + 1;

        return "CASE {$colonne}{$cas} ELSE {$defaut} END";
    }
}
