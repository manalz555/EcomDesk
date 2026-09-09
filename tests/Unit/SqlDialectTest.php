<?php

namespace Tests\Unit;

use App\Support\SqlDialect;
use Tests\TestCase;

/**
 * SqlDialect produit des fragments de SQL : ce sont des fonctions pures, donc
 * verifiables sans base de donnees. C'est aussi le seul endroit du projet ou
 * une chaine SQL est assemblee a la main — d'ou l'attention portee a
 * l'echappement, meme si les valeurs proviennent toujours de constantes du
 * domaine et jamais d'une saisie utilisateur.
 */
class SqlDialectTest extends TestCase
{
    public function test_l_ordre_metier_suit_la_liste_fournie_et_non_l_alphabet(): void
    {
        $sql = SqlDialect::orderByValues('priorite', ['faible', 'moyenne', 'haute']);

        $this->assertSame(
            "CASE priorite WHEN 'faible' THEN 1 WHEN 'moyenne' THEN 2 WHEN 'haute' THEN 3 ELSE 4 END",
            $sql
        );
    }

    /** Une valeur absente de la liste est renvoyee en fin de tri, jamais perdue. */
    public function test_une_valeur_inconnue_est_classee_en_dernier(): void
    {
        $sql = SqlDialect::orderByValues('statut', ['nouveau', 'resolu']);

        $this->assertStringEndsWith('ELSE 3 END', $sql);
    }

    /** L'apostrophe est doublee : elle ne peut pas refermer la chaine SQL. */
    public function test_les_apostrophes_sont_echappees(): void
    {
        $sql = SqlDialect::orderByValues('canal', ["l'email"]);

        $this->assertStringContainsString("WHEN 'l''email' THEN 1", $sql);
        $this->assertStringNotContainsString("WHEN 'l'email'", $sql);
    }

    /**
     * La suite s'execute sur SQLite : l'ecart en minutes doit y etre exprime en
     * jours juliens, et non par le TIMESTAMPDIFF propre a MySQL.
     */
    public function test_l_ecart_en_minutes_est_traduit_pour_le_pilote_courant(): void
    {
        $sql = SqlDialect::minutesBetween('a.created_at', 'b.created_at');

        $this->assertStringNotContainsString('TIMESTAMPDIFF', $sql);
        $this->assertStringContainsString('julianday', $sql);
        // 1440 = 24 x 60 : conversion des jours juliens en minutes.
        $this->assertStringContainsString('1440', $sql);
    }
}
